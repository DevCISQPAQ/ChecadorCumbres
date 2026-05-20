<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use App\Models\Checada;
use App\Models\Asistencia;
use App\Models\Vacacion;
use App\Models\DiaFestivo;
use Carbon\Carbon;

class GenerarAsistenciasDiarias extends Command
{
    protected $signature = 'asistencias:generar-diarias';

    protected $description = 'Genera asistencias diarias a partir de checadas';

    public function handle()
    {
        $hoy = Carbon::today();

        $empleados = Empleado::where('activo', true)->get();

        foreach ($empleados as $empleado) {

            $horario = \App\Models\HorarioEmpleado::where('empleado_id', $empleado->id)
                ->where('dia_semana', now()->dayOfWeekIso)
                ->where('activo', true)
                ->first();

            $checadas = Checada::where('empleado_id', $empleado->id)
                ->whereDate('fecha_hora', $hoy)
                ->orderBy('fecha_hora')
                ->get();

            $entradaChecada = $checadas->firstWhere('tipo', 'entrada');
            $salidaChecada  = $checadas->where('tipo', 'salida')->last();

            $entrada = $entradaChecada?->fecha_hora;
            $salida  = $salidaChecada?->fecha_hora;


            // 1. VACACIONES
            $enVacaciones = Vacacion::where('empleado_id', $empleado->id)
                ->where('fecha_inicio', '<=', $hoy)
                ->where('fecha_fin', '>=', $hoy)
                ->exists();

            if ($enVacaciones) {
                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'vacaciones'
                    ]
                );
                continue;
            }

            // 2. FESTIVO
            $esFestivo = DiaFestivo::whereDate('fecha', $hoy)->exists();

            if ($esFestivo) {
                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'festivo'
                    ]
                );
                continue;
            }


            // 4. SIN ENTRADA = FALTA
            if (!$entrada && $horario) {
                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'falta'
                    ]
                );
                continue;
            }


            // 6. RETARDO
            $minutosRetardo = 0;
            $estado = 'libre';

            if ($horario && $entrada) {

                $limite = \Carbon\Carbon::parse($horario->hora_entrada)
                    ->addMinutes($horario->tolerancia_minutos);

                if ($entrada->gt($limite)) {
                    $estado = 'retardo';
                    $minutosRetardo = $limite->diffInMinutes($entrada);
                }
            }


            // 7. HORAS TRABAJADAS
            $horasTrabajadas = null;

            if ($entrada && $salida) {
                $horasTrabajadas = abs(
                    $entrada->diffInMinutes($salida)
                ) / 60;
            }

           // if ($estado != 'libre') {
                // 8. GUARDAR ASISTENCIA
                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => $estado,
                        'hora_entrada' => $entrada,
                        'hora_salida' => $salida,
                        'minutos_retardo' => $minutosRetardo,
                        'horas_trabajadas' => $horasTrabajadas
                    ]
                );
           // }
        }

        $this->info('Asistencias generadas correctamente');
    }
}
