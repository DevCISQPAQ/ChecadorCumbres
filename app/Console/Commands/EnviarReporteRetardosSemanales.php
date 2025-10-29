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
        $finSemana = now()->endOfWeek();     // domingo

        // Solo obtener empleados con retardos entre lunes y viernes
        $retardos = Asistencia::with('empleado')
            ->where('retardo', 1)
            ->whereBetween('created_at', [$inicioSemana, $finSemana])
            ->get()
            ->groupBy('empleado_id');

        // Empleados sin asistencia en la semana
        $empleadosSinAsistencia = Empleado::whereDoesntHave('asistencias', function ($query) use ($inicioSemana, $finSemana) {
            $query->whereBetween('created_at', [$inicioSemana, $finSemana]);
        })->get();


        if ($retardos->isEmpty() && $empleadosSinAsistencia->isEmpty()) {
            Log::info('No hay retardos ni empleados sin asistencia esta semana.');
            return;
        }

        $usuarios = User::where('yes_notifications', true)->get();

        $pdf = PDF::loadView('emails.reporte_pdf', compact('retardos', 'empleadosSinAsistencia'));
        $pdfContent = $pdf->output();

        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new ReporteRetardosMail($retardos, $empleadosSinAsistencia, $pdfContent));
        }

        Log::info('Reporte de retardos y asistencias enviado.');
    }
}
