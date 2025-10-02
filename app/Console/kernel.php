<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Los comandos Artisan del sistema.
     *
     * @var array
     */
    protected $commands = [
        // Aquí registras tus comandos personalizados, ejemplo:
        // \App\Console\Commands\EnviarReporteRetardosSemanales::class,
        \App\Console\Commands\EnviarReporteRetardosSemanales::class,
    ];

    /**
     * Define el programador de tareas.
     */
    protected function schedule(Schedule $schedule)
    {
        // Aquí defines las tareas programadas, ejemplo:
        // $schedule->command('enviar:reporte-retardos')->fridays();
        //  $schedule->command('enviar:reporte-retardos')->fridays()->at('08:00');
    }

    /**
     * Registra los comandos personalizados.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
