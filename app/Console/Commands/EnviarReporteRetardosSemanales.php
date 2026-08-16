<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Asistencia;
use App\Mail\ReporteRetardosMail;
use App\Models\Empleado;

class EnviarReporteRetardosSemanales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar-reporte-retardos-semanales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inicioSemana = now()->startOfWeek(); // lunes
        $finSemana = now()->startOfWeek()->addDays(4); // viernes

        /*--------------------------------------------------------------------------
    | Fechas de lunes a viernes
    |--------------------------------------------------------------------------*/
        $fechasSemana = collect();

        for ($fecha = $inicioSemana->copy(); $fecha->lte($finSemana); $fecha->addDay()) {
            $fechasSemana->push($fecha->copy());
        }

        /*--------------------------------------------------------------------------
    | Días festivos de la semana
    |--------------------------------------------------------------------------*/
        $diasFestivos = \App\Models\DiaFestivo::whereBetween('fecha', [
            $inicioSemana->toDateString(),
            $finSemana->toDateString()
        ])
            ->pluck('fecha')
            ->map(fn($fecha) => \Carbon\Carbon::parse($fecha)->toDateString());
        /*--------------------------------------------------------------------------
    | Empleados activos
    |--------------------------------------------------------------------------*/
        $empleados = Empleado::where('activo', true)
            ->with([
                'asistencias' => function ($query) use ($inicioSemana, $finSemana) {
                    $query->whereBetween('fecha', [
                        $inicioSemana->toDateString(),
                        $finSemana->toDateString()
                    ]);
                },
                'horarios' => function ($query) {
                    $query->where('activo', true);
                }
            ])
            ->get();

        /*|--------------------------------------------------------------------------
    | Retardos
    |--------------------------------------------------------------------------*/
        $retardos = collect();

        /*|--------------------------------------------------------------------------
    | Faltas / empleados sin asistencia
    |--------------------------------------------------------------------------*/

        $empleadosSinAsistencia = collect();
        foreach ($empleados as $empleado) {

            /*|--------------------------------------------------------------------------
        | Horarios del empleado
        |--------------------------------------------------------------------------*/
            $horarios = $empleado->horarios->keyBy('dia_semana');

            /*|--------------------------------------------------------------------------
        | Asistencias del empleado
        |--------------------------------------------------------------------------*/

            $asistencias = $empleado->asistencias->keyBy(function ($asistencia) {
                return \Carbon\Carbon::parse($asistencia->fecha)->toDateString();
            });

            $faltas = collect();

            foreach ($fechasSemana as $fecha) {

                $fechaString = $fecha->toDateString();

                // 1 = lunes, 5 = viernes
                $diaSemana = $fecha->dayOfWeekIso;

                /*
            |--------------------------------------------------------------------------
            | Si es día festivo, no se considera falta
            |--------------------------------------------------------------------------
            */

                if ($diasFestivos->contains($fechaString)) {
                    continue;
                }

                /*|--------------------------------------------------------------------------
            | Si el empleado no tiene horario ese día,
            | significa que es día libre
            |--------------------------------------------------------------------------*/
                if (!isset($horarios[$diaSemana])) {
                    continue;
                }

                /*|--------------------------------------------------------------------------
            | Buscar asistencia de ese día
            |--------------------------------------------------------------------------*/
                $asistencia = $asistencias->get($fechaString);
                /*|--------------------------------------------------------------------------
            | No tiene asistencia
            |--------------------------------------------------------------------------*/

                if (!$asistencia) {
                    $faltas->push([
                        'fecha' => $fechaString,
                        'dia' => $fecha->translatedFormat('l'),
                        'motivo' => 'Sin asistencia',
                    ]);
                    continue;
                }

                /*|--------------------------------------------------------------------------
            | Si explícitamente está marcada como falta
            |--------------------------------------------------------------------------*/
                if ($asistencia->estado === 'falta') {
                    $faltas->push([
                        'fecha' => $fechaString,
                        'dia' => $fecha->translatedFormat('l'),
                        'motivo' => 'Falta',
                    ]);
                }
            }

            /*|--------------------------------------------------------------------------
        | Agregar retardos
        |--------------------------------------------------------------------------*/

            $retardosEmpleado = $empleado->asistencias
                ->where('estado', 'retardo');
            if ($retardosEmpleado->isNotEmpty()) {

                $retardos->put(
                    $empleado->id,
                    $retardosEmpleado
                );
            }

            /*
        |--------------------------------------------------------------------------
        | Agregar empleado si tuvo faltas
        |--------------------------------------------------------------------------
        */
            if ($faltas->isNotEmpty()) {

                $empleadosSinAsistencia->push([
                    'empleado' => $empleado,
                    'faltas' => $faltas,
                ]);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Si no hay nada que reportar
    |--------------------------------------------------------------------------
    */
        if ($retardos->isEmpty() && $empleadosSinAsistencia->isEmpty()) {

            Log::info(
                'No hay retardos ni faltas esta semana.'
            );

            return Command::SUCCESS;
        }

        /*
    |--------------------------------------------------------------------------
    | Usuarios que reciben el reporte
    |--------------------------------------------------------------------------
    */
        $usuarios = User::where('yes_notifications', true)->get();

        /*
    |--------------------------------------------------------------------------
    | Generar PDF
    |--------------------------------------------------------------------------
    */
        $pdf = PDF::loadView(
            'emails.reporte_pdf',
            compact(
                'retardos',
                'empleadosSinAsistencia',
                'inicioSemana',
                'finSemana'
            )
        );
        $pdfContent = $pdf->output();

        /*
    |--------------------------------------------------------------------------
    | Enviar correo
    |--------------------------------------------------------------------------
    */
        foreach ($usuarios as $usuario) {

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
        Log::info(
            'Reporte semanal de retardos y faltas enviado correctamente.'
        );
        return Command::SUCCESS;
    }
}
