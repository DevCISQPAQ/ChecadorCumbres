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

            // =====================================================
            // HORARIO
            // =====================================================

            $horario = \App\Models\HorarioEmpleado::where(
                'empleado_id',
                $empleado->id
            )
                ->where(
                    'dia_semana',
                    $hoy->dayOfWeekIso
                )
                ->first();

            // =====================================================
            // VACACIONES
            // =====================================================

            $enVacaciones = Vacacion::where(
                'empleado_id',
                $empleado->id
            )
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
                        'estado' => 'vacaciones',
                        'minutos_retardo' => 0,
                        'horas_trabajadas' => 0
                    ]
                );

                continue;
            }

            // =====================================================
            // FESTIVO
            // =====================================================

            $esFestivo = DiaFestivo::whereDate(
                'fecha',
                $hoy
            )->exists();

            if ($esFestivo) {

                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'festivo',
                        'minutos_retardo' => 0,
                        'horas_trabajadas' => 0
                    ]
                );

                continue;
            }

            // =====================================================
            // LIBRE
            // =====================================================

            if (!$horario) {

                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'libre',
                        'minutos_retardo' => 0,
                        'horas_trabajadas' => 0
                    ]
                );

                continue;
            }

            // =====================================================
            // PERMISO
            // =====================================================

            if (!$horario->activo) {

                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'permiso',
                        'minutos_retardo' => 0,
                        'horas_trabajadas' => 0
                    ]
                );

                continue;
            }

            // =====================================================
            // CHECADAS
            // =====================================================

            $checadas = Checada::where(
                'empleado_id',
                $empleado->id
            )
                ->whereDate('fecha_hora', $hoy)
                ->orderBy('fecha_hora')
                ->get();

            $entradaChecada = $checadas
                ->firstWhere('tipo', 'entrada');

            $salidaChecada = $checadas
                ->where('tipo', 'salida')
                ->last();

            $entrada = $entradaChecada?->fecha_hora;

            $salida = $salidaChecada?->fecha_hora;

            // =====================================================
            // FALTA
            // =====================================================

            if (!$entrada && !$salida) {

                Asistencia::updateOrCreate(
                    [
                        'empleado_id' => $empleado->id,
                        'fecha' => $hoy
                    ],
                    [
                        'estado' => 'falta',
                        'minutos_retardo' => 0,
                        'horas_trabajadas' => 0
                    ]
                );

                continue;
            }

            // =====================================================
            // RETARDO
            // =====================================================

            $estado = 'presente';

            $minutosRetardo = 0;

            if ($entrada) {

                $limite = Carbon::parse(
                    $horario->hora_entrada
                )
                    ->addMinutes(
                        $horario->tolerancia_minutos
                    );

                if ($entrada->gt($limite)) {

                    $estado = 'retardo';

                    $minutosRetardo = $limite
                        ->diffInMinutes($entrada);
                }
            }

            // =====================================================
            // HORAS TRABAJADAS
            // =====================================================

            $horasTrabajadas = 0;

            if ($entrada && $salida) {

                $horasTrabajadas = abs(
                    $entrada->diffInMinutes($salida)
                ) / 60;
            }

            // =====================================================
            // GUARDAR
            // =====================================================

            Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $hoy
                ],
                [
                    'estado' => $estado,
                    'minutos_retardo' => $minutosRetardo,
                    'horas_trabajadas' => $horasTrabajadas
                ]
            );
        }

        $this->info('Asistencias generadas correctamente');
    }
}
