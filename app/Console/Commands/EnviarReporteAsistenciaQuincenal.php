<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use App\Jobs\EnviarReporteAsistenciaEmpleado;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnviarReporteAsistenciaQuincenal extends Command
{
    // protected $signature = 'app:enviar-reporte-asistencia-quincenal';
    protected $signature = 'app:enviar-reporte-asistencia-quincenal
                        {--fecha= : Fecha para simular el periodo Y-m-d}';

    protected $description = 'Programa los reportes quincenales de asistencia';

    public function handle()
    {
        // $fechaInicio = now()->subDays(14)->startOfDay();
        // $fechaFin = now()->endOfDay();

        // $hoy = now();

        // if ($hoy->day == 1) {

        //     // Primera quincena de envío:
        //     // Del día 16 del mes anterior
        //     // hasta el último día del mes anterior.

        //     $fechaInicio = $hoy->copy()
        //         ->subMonthNoOverflow()
        //         ->day(16)
        //         ->startOfDay();

        //     $fechaFin = $hoy->copy()
        //         ->subMonthNoOverflow()
        //         ->endOfMonth()
        //         ->endOfDay();
        // } elseif ($hoy->day == 15) {

        //     // Segunda quincena de envío:
        //     // Del día 1 al día 15 del mes actual.

        //     $fechaInicio = $hoy->copy()
        //         ->startOfMonth()
        //         ->startOfDay();

        //     $fechaFin = $hoy->copy()
        //         ->day(15)
        //         ->endOfDay();
        // } else {

        //     $this->error('Este comando solamente debe ejecutarse los días 1 y 15.');

        //     return Command::FAILURE;
        // }

        $hoy = $this->option('fecha')
            ? Carbon::parse($this->option('fecha'))
            : now();

        if ($hoy->day == 1) {

            $fechaInicio = $hoy->copy()
                ->subMonthNoOverflow()
                ->day(16)
                ->startOfDay();

            $fechaFin = $hoy->copy()
                ->subMonthNoOverflow()
                ->endOfMonth()
                ->endOfDay();
        } elseif ($hoy->day == 15) {

            $fechaInicio = $hoy->copy()
                ->startOfMonth()
                ->startOfDay();

            $fechaFin = $hoy->copy()
                ->day(15)
                ->endOfDay();
        } else {

            $this->error('La fecha debe corresponder al día 1 o 15.');

            return Command::FAILURE;
        }


        $total = 0;

        Empleado::where('activo', true)
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->select('id')
            ->chunkById(50, function ($empleados) use (
                $fechaInicio,
                $fechaFin,
                &$total
            ) {
                foreach ($empleados as $empleado) {

                    EnviarReporteAsistenciaEmpleado::dispatch(
                        $empleado->id,
                        $fechaInicio->toDateString(),
                        $fechaFin->toDateString()
                    );

                    $total++;
                }
            });

        Log::info(
            "Se programaron {$total} reportes quincenales."
        );

        $this->info("Se programaron {$total} reportes.");

        return Command::SUCCESS;
    }
}
