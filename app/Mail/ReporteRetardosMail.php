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

     public function __construct($retardos, $empleadosSinAsistencia)
    {
        $this->retardos = $retardos;
        $this->empleadosSinAsistencia = $empleadosSinAsistencia;
    }

     public function build()
    {
        return $this->subject('Reporte Semanal de Retardos y Asistencias')
                    ->view('emails.reporte_retardos');
    }
}
