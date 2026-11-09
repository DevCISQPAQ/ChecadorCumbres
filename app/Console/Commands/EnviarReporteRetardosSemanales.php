<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Asistencia;
use App\Mail\ReporteRetardosMail;
use App\Models\DiaFestivo;
use App\Models\Empleado;
use Carbon\Carbon;

class EnviarReporteRetardosSemanales extends Command
{
    protected $signature = 'app:enviar-reporte-retardos-semanales';

    protected $description = 'Envía el reporte semanal de asistencia, retardos y faltas a los administradores';

    public function handle()
    {
        $inicioSemana = now()->startOfWeek();
        $finSemana = $inicioSemana->copy()->addDays(4);

        // Fechas de lunes a viernes
        $fechasSemana = collect();

        for (
            $fecha = $inicioSemana->copy();
            $fecha->lte($finSemana);
            $fecha->addDay()
        ) {
            $fechasSemana->push($fecha->copy());
        }

        // Días festivos
        $diasFestivos = DiaFestivo::whereBetween('fecha', [
            $inicioSemana->toDateString(),
            $finSemana->toDateString()
        ])
            ->pluck('fecha')
            ->map(fn($fecha) => Carbon::parse($fecha)->toDateString());

        // Empleados activos
        $empleados = Empleado::where('activo', true)
            ->with([
                'asistencias' => function ($query) use ($inicioSemana, $finSemana) {
                    $query->whereBetween('fecha', [
                        $inicioSemana->toDateString(),
                        $finSemana->toDateString()
                    ])->orderBy('fecha', 'asc');
                },
                'horarios' => function ($query) {
                    $query->where('activo', true);
                }
            ])
            ->get();

        $retardos = collect();
        $empleadosSinAsistencia = collect();

        // Nuevo: todos los registros de asistencia por empleado
        $asistenciasPorEmpleado = collect();

        foreach ($empleados as $empleado) {

            $horarios = $empleado->horarios->keyBy('dia_semana');

            $asistencias = $empleado->asistencias->keyBy(function ($asistencia) {
                return Carbon::parse($asistencia->fecha)->toDateString();
            });

            $faltas = collect();

            // Guardar todos los estados registrados
            if ($empleado->asistencias->isNotEmpty()) {

                $asistenciasPorEmpleado->put(
                    $empleado->id,
                    [
                        'empleado' => $empleado,
                        'asistencias' => $empleado->asistencias
                    ]
                );
            }

            // Revisar faltas de lunes a viernes
            foreach ($fechasSemana as $fecha) {

                $fechaString = $fecha->toDateString();
                $diaSemana = $fecha->dayOfWeekIso;

                // No contar festivos
                if ($diasFestivos->contains($fechaString)) {
                    continue;
                }

                // Si no tiene horario, es día libre
                if (!isset($horarios[$diaSemana])) {
                    continue;
                }

                $asistencia = $asistencias->get($fechaString);

                // No tiene registro de asistencia
                if (!$asistencia) {

                    $faltas->push([
                        'fecha' => $fechaString,
                        'dia' => $fecha->translatedFormat('l'),
                        'motivo' => 'Sin asistencia',
                    ]);

                    continue;
                }

                // Falta registrada explícitamente
                if ($asistencia->estado === 'falta') {

                    $faltas->push([
                        'fecha' => $fechaString,
                        'dia' => $fecha->translatedFormat('l'),
                        'motivo' => 'Falta',
                    ]);
                }
            }

            // Registrar retardos
            $retardosEmpleado = $empleado->asistencias
                ->where('estado', 'retardo');

            if ($retardosEmpleado->isNotEmpty()) {
                $retardos->put(
                    $empleado->id,
                    $retardosEmpleado
                );
            }

            // Registrar empleados con faltas
            if ($faltas->isNotEmpty()) {

                $empleadosSinAsistencia->push([
                    'empleado' => $empleado,
                    'faltas' => $faltas,
                ]);
            }
        }

        // Si no existe ningún registro que reportar
        if (
            $retardos->isEmpty() &&
            $empleadosSinAsistencia->isEmpty() &&
            $asistenciasPorEmpleado->isEmpty()
        ) {
            Log::info('No hay registros de asistencia esta semana.');

            return Command::SUCCESS;
        }

        // Usuarios que reciben el reporte
        $usuarios = User::where('yes_notifications', true)->get();

        if ($usuarios->isEmpty()) {
            Log::warning('No hay usuarios configurados para recibir notificaciones.');

            return Command::SUCCESS;
        }

        // Generar PDF general
        $pdf = PDF::loadView(
            'emails.reporte_pdf',
            compact(
                'retardos',
                'empleadosSinAsistencia',
                'asistenciasPorEmpleado',
                'inicioSemana',
                'finSemana'
            )
        );

        $pdfContent = $pdf->output();

        // Enviar correo a administradores
        foreach ($usuarios as $usuario) {

            if (!$usuario->email) {
                continue;
            }

            Mail::to($usuario->email)->send(
                new ReporteRetardosMail(
                    $retardos,
                    $empleadosSinAsistencia,
                    $pdfContent,
                    $inicioSemana,
                    $finSemana
                )
            );
        }

        Log::info('Reporte semanal de asistencia enviado correctamente.');

        return Command::SUCCESS;
    }
}
