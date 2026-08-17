<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use App\Models\Checada;
use App\Models\Asistencia;
use App\Models\Vacacion;
use App\Models\DiaFestivo;
use App\Services\AsistenciaService;
use Carbon\Carbon;

class GenerarAsistenciasDiarias extends Command
{
    protected $signature = 'asistencias:generar-diarias';

    protected $description = 'Genera asistencias diarias a partir de checadas';

    public function __construct(
        private AsistenciaService $asistenciaService
    ) {
        parent::__construct();
    }

    public function handle(AsistenciaService $asistenciaService)
    {
        $hoy = Carbon::today();

        $empleados = Empleado::where('activo', true)->get();

        foreach ($empleados as $empleado) {

            $asistencia = $asistenciaService->generarAsistenciaDelDia($empleado);

            $this->info(
                "Empleado {$empleado->id}: {$asistencia->estado} - {$asistencia->fecha}"
            );
        }

        $this->info('Asistencias generadas correctamente');

        return Command::SUCCESS;
    }
}
