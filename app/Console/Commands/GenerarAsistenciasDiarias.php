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

        $empleados = Empleado::where('activo', 1)->get();

        foreach ($empleados as $empleado) {

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

            // 3. CHECADAS DEL DÍA
            $checadas = Checada::where('empleado_id', $empleado->id)
                ->whereDate('fecha_hora', $hoy)
                ->orderBy('fecha_hora')
                ->get();

            $entrada = $checadas->firstWhere('tipo', 'entrada')?->fecha_hora;
            $salida  = $checadas->where('tipo', 'entrada')->last()?->fecha_hora;

            // 4. SIN ENTRADA = FALTA
            if (!$entrada) {
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

            // 5. HORARIO DEL EMPLEADO (EJEMPLO SIMPLE)
            $horarioEntrada = Carbon::parse($hoy->format('Y-m-d') . ' 08:00:00');
            $tolerancia = 10; // minutos

            // 6. RETARDO
            $minutosRetardo = 0;
            $estado = 'presente';

            if ($entrada->gt($horarioEntrada->copy()->addMinutes($tolerancia))) {
                $estado = 'retardo';
                $minutosRetardo = $horarioEntrada->diffInMinutes($entrada);
            }

            // 7. HORAS TRABAJADAS
            $horasTrabajadas = null;

            if ($salida) {
                $horasTrabajadas = Carbon::parse($entrada)
                    ->diffInMinutes(Carbon::parse($salida)) / 60;
            }

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
        }

        $this->info('Asistencias generadas correctamente');
    }
}