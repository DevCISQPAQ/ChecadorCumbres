<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Empleado;
use App\Models\DiaFestivo;
use App\Mail\ReporteAsistenciaEmpleadoMail;
use Carbon\Carbon;

class EnviarReporteAsistenciaQuincenal extends Command
{
    protected $signature = 'app:enviar-reporte-asistencia-quincenal';

    protected $description = 'Envía reportes individuales de asistencia de los últimos 15 días';

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | Periodo de los últimos 15 días, incluyendo hoy
        |--------------------------------------------------------------------------
        */

        $fechaInicio = now()->subDays(14)->startOfDay();
        $fechaFin = now()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Obtener días festivos
        |--------------------------------------------------------------------------
        */

        $diasFestivos = DiaFestivo::whereBetween('fecha', [
            $fechaInicio->toDateString(),
            $fechaFin->toDateString()
        ])
            ->pluck('fecha')
            ->map(fn($fecha) => Carbon::parse($fecha)->toDateString())
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Obtener empleados activos
        |--------------------------------------------------------------------------
        */

        $empleados = Empleado::where('activo', true)
            ->with([
                'departamento',

                'asistencias' => function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('fecha', [
                        $fechaInicio->toDateString(),
                        $fechaFin->toDateString()
                    ]);
                },

                'horarios' => function ($query) {
                    $query->where('activo', true);
                },

                'checadas' => function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('fecha_hora', [
                        $fechaInicio,
                        $fechaFin
                    ])
                        ->orderBy('fecha_hora', 'asc');
                }
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Procesar cada empleado
        |--------------------------------------------------------------------------
        */

        foreach ($empleados as $empleado) {

            try {

                if (empty($empleado->email)) {

                    Log::warning(
                        "Empleado {$empleado->id} sin correo electrónico."
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Organizar información del empleado
                |--------------------------------------------------------------------------
                */

                $horarios = $empleado->horarios->keyBy('dia_semana');

                $asistencias = $empleado->asistencias->keyBy(function ($asistencia) {
                    return Carbon::parse($asistencia->fecha)->toDateString();
                });

                $checadasPorFecha = $empleado->checadas->groupBy(function ($checada) {
                    return Carbon::parse($checada->fecha_hora)->toDateString();
                });

                $registrosReporte = collect();

                /*
                |--------------------------------------------------------------------------
                | Recorrer los 15 días
                |--------------------------------------------------------------------------
                */

                for (
                    $fecha = $fechaInicio->copy();
                    $fecha->lte($fechaFin);
                    $fecha->addDay()
                ) {

                    $fechaString = $fecha->toDateString();

                    $diaSemana = $fecha->dayOfWeekIso;

                    $asistencia = $asistencias->get($fechaString);

                    $checadas = $checadasPorFecha->get(
                        $fechaString,
                        collect()
                    );

                    $tieneHorario = isset($horarios[$diaSemana]);

                    $esFestivo = in_array(
                        $fechaString,
                        $diasFestivos
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Obtener entrada y salida
                    |--------------------------------------------------------------------------
                    */

                    // Primera checada de entrada
                    $entrada = $checadas
                        ->where('tipo', 'entrada')
                        ->sortBy('fecha_hora')
                        ->first();

                    // Última checada de salida
                    $salida = $checadas
                        ->where('tipo', 'salida')
                        ->sortByDesc('fecha_hora')
                        ->first();

                    $horaEntrada = $entrada
                        ? Carbon::parse($entrada->fecha_hora)->format('H:i')
                        : null;

                    $horaSalida = $salida
                        ? Carbon::parse($salida->fecha_hora)->format('H:i')
                        : null;

                    /*
                    |--------------------------------------------------------------------------
                    | Si existe asistencia
                    |--------------------------------------------------------------------------
                    */

                    if ($asistencia) {

                        $registrosReporte->push([

                            'fecha' => $fecha->copy(),

                            'dia' => $fecha->translatedFormat('l'),

                            'estado' => $asistencia->estado,

                            'hora_entrada' => $horaEntrada,

                            'hora_salida' => $horaSalida,

                            'minutos_retardo' => $asistencia->minutos_retardo ?? 0,

                            'horas_trabajadas' => $asistencia->horas_trabajadas,

                            'observaciones' => $asistencia->observaciones,

                            'tipo' => 'registro',

                        ]);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Día festivo
                    |--------------------------------------------------------------------------
                    */

                    if ($esFestivo) {

                        $registrosReporte->push([

                            'fecha' => $fecha->copy(),

                            'dia' => $fecha->translatedFormat('l'),

                            'estado' => 'festivo',

                            'hora_entrada' => $horaEntrada,

                            'hora_salida' => $horaSalida,

                            'minutos_retardo' => 0,

                            'horas_trabajadas' => null,

                            'observaciones' => null,

                            'tipo' => 'no_laborable',

                        ]);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Día libre
                    |--------------------------------------------------------------------------
                    */

                    if (!$tieneHorario) {

                        $registrosReporte->push([

                            'fecha' => $fecha->copy(),

                            'dia' => $fecha->translatedFormat('l'),

                            'estado' => 'libre',

                            'hora_entrada' => $horaEntrada,

                            'hora_salida' => $horaSalida,

                            'minutos_retardo' => 0,

                            'horas_trabajadas' => null,

                            'observaciones' => null,

                            'tipo' => 'no_laborable',

                        ]);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Día sin registro
                    |--------------------------------------------------------------------------
                    */

                    $registrosReporte->push([

                        'fecha' => $fecha->copy(),

                        'dia' => $fecha->translatedFormat('l'),

                        'estado' => 'sin registro',

                        'hora_entrada' => $horaEntrada,

                        'hora_salida' => $horaSalida,

                        'minutos_retardo' => 0,

                        'horas_trabajadas' => null,

                        'observaciones' => null,

                        'tipo' => 'sin_registro',

                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Generar PDF individual
                |--------------------------------------------------------------------------
                */

                $pdf = PDF::loadView(
                    'emails.reporte_asistencia_individual',
                    [
                        'empleado' => $empleado,
                        'asistencias' => $registrosReporte,
                        'fechaInicio' => $fechaInicio,
                        'fechaFin' => $fechaFin,
                    ]
                )->setPaper('a4', 'landscape');

                $pdfContent = $pdf->output();

                /*
                |--------------------------------------------------------------------------
                | Enviar correo al empleado
                |--------------------------------------------------------------------------
                */

                Mail::to($empleado->email)->send(
                    new ReporteAsistenciaEmpleadoMail(
                        $empleado,
                        $registrosReporte,
                        $pdfContent,
                        $fechaInicio,
                        $fechaFin
                    )
                );

                Log::info(
                    "Reporte quincenal enviado correctamente al empleado {$empleado->id}."
                );
            } catch (\Throwable $e) {

                Log::error(
                    "Error al enviar reporte al empleado {$empleado->id}: "
                        . $e->getMessage()
                );

                continue;
            }
        }

        Log::info(
            'Finalizó el envío de reportes individuales quincenales.'
        );

        return Command::SUCCESS;
    }
}
