<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReporteRetardosMail extends Mailable
{
    use Queueable, SerializesModels;

    public $retardos;
    public $empleadosSinAsistencia;
    public $pdfContent;

    public $inicioSemana;
    public $finSemana;

    public function __construct(
        $retardos,
        $empleadosSinAsistencia,
        $pdfContent,
        $inicioSemana,
        $finSemana
    ) {
        $this->retardos = $retardos;
        $this->empleadosSinAsistencia = $empleadosSinAsistencia;
        $this->pdfContent = $pdfContent;

        $this->inicioSemana = $inicioSemana;
        $this->finSemana = $finSemana;
    }

    public function build()
    {
        return $this->subject(
            'Reporte Semanal de Retardos y Asistencias'
        )
            ->view('emails.reporte_retardos')
            ->attachData(
                $this->pdfContent,
                'ReporteRetardos.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
