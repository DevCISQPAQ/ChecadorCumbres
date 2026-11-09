<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Empleado;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReporteAsistenciaEmpleadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empleado;
    public $asistencias;
    public $pdfContent;
    public $fechaInicio;
    public $fechaFin;

    public function __construct(
        Empleado $empleado,
        Collection $asistencias,
        $pdfContent,
        Carbon $fechaInicio,
        Carbon $fechaFin
    ) {
        $this->empleado = $empleado;
        $this->asistencias = $asistencias;
        $this->pdfContent = $pdfContent;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function build()
    {
        return $this->subject('Tu reporte de asistencia - Últimos 15 días')
            ->view('emails.reporte_asistencia_individual_mail')
            ->attachData(
                $this->pdfContent,
                'reporte_asistencia_' . $this->empleado->id . '.pdf',
                [
                    'mime' => 'application/pdf'
                ]
            );
    }
}