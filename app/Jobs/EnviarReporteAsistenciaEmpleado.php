<?php

namespace App\Jobs;

use App\Models\Empleado;
use App\Models\DiaFestivo;
use App\Mail\ReporteAsistenciaEmpleadoMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarReporteAsistenciaEmpleado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 180, 300];

    protected $empleadoId;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct(
        $empleadoId,
        $fechaInicio,
        $fechaFin
    ) {
        $this->empleadoId = $empleadoId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function handle()
    {
        Carbon::setLocale('es');
        /*
        |--------------------------------------------------------------------------
        | Fechas del reporte
        |--------------------------------------------------------------------------
        */

        $fechaInicio = Carbon::parse($this->fechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($this->fechaFin)->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Obtener empleado y relaciones
        |--------------------------------------------------------------------------
        */

        $empleado = Empleado::with([

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
                ])->orderBy('fecha_hora', 'asc');
            }

        ])->find($this->empleadoId);

        /*
        |--------------------------------------------------------------------------
        | Validar empleado
        |--------------------------------------------------------------------------
        */

        if (!$empleado || !$empleado->activo) {
            Log::warning("Empleado {$this->empleadoId} inexistente o inactivo.");
            return;
        }

        if (empty($empleado->email)) {
            Log::warning("Empleado {$empleado->id} sin correo.");
            return;
        }

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

            $esFestivo = in_array($fechaString, $diasFestivos);

            /*
            |--------------------------------------------------------------------------
            | Obtener entrada y salida
            |--------------------------------------------------------------------------
            */

            $entrada = $checadas
                ->where('tipo', 'entrada')
                ->sortBy('fecha_hora')
                ->first();

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
                    'dia' => ucfirst($fecha->translatedFormat('l')),
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
                    'dia' => ucfirst($fecha->translatedFormat('l')),
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
                    'dia' => ucfirst($fecha->translatedFormat('l')),
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
                'dia' => ucfirst($fecha->translatedFormat('l')),
                'estado' => 'sin registro',
                'hora_entrada' => $horaEntrada,
                'hora_salida' => $horaSalida,
                'minutos_retardo' => 0,
                'horas_trabajadas' => null,
                'observaciones' => null,
                'tipo' => 'sin_registro',

            ]);
        } // FIN DEL FOR: aquí termina el recorrido de los 15 días.

        /*
        |--------------------------------------------------------------------------
        | Generar PDF individual
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'emails.reporte_asistencia_individual',
            [
                'empleado' => $empleado,
                'asistencias' => $registrosReporte,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
            ]
        )->setPaper('a4', 'portrait');

        $pdfContent = $pdf->output();

        /*
        |--------------------------------------------------------------------------
        | Enviar correo
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

        /*
        |--------------------------------------------------------------------------
        | Registrar envío exitoso
        |--------------------------------------------------------------------------
        */

        Log::info(
            "Reporte enviado al empleado {$empleado->id}"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Registrar trabajo fallido después de agotar los intentos
    |--------------------------------------------------------------------------
    */

    public function failed(\Throwable $exception)
    {
        Log::error(
            "Falló el reporte del empleado {$this->empleadoId}: "
                . $exception->getMessage()
        );
    }
}
