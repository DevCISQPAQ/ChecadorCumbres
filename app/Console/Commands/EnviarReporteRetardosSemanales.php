<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Asistencia;
use App\Mail\ReporteRetardosMail;

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
        $finSemana = now()->endOfWeek();     // domingo

        // Solo obtener empleados con retardos entre lunes y viernes
        $retardos = \App\Models\Asistencia::with('empleado')
            ->where('retardo', 1)
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->get()
            ->groupBy('empleado_id');

        if ($retardos->isEmpty()) {
            Log::info('No hay retardos esta semana.');
            return;
        }

        $usuarios = \App\Models\User::where('yes_notifications', true)->get();
        // Aquí puedes generar un PDF o preparar la info del reporte
        // $reporte = view('emails.reporte_retardos', compact('retardos'))->render();

        // Enviar el correo
        // Mail::to('ajimenez@cumbresqueretaro.com')->send(new \App\Mail\ReporteRetardosMail($retardos));
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new \App\Mail\ReporteRetardosMail($retardos));
        }

        Log::info('Reporte de retardos enviado.');
    }
}
