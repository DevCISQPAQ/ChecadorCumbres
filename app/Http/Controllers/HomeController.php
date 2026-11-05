<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Services\AsistenciaService;
use App\Models\Checada;
use App\Models\HorarioEmpleado;
use Carbon\Carbon;
use App\Models\Ausencias;

class HomeController extends Controller
{

    public function __construct(
        private AsistenciaService $asistenciaService
    ) {}

    public function showWelcome()
    {
        return view('welcome');
    }

    public function buscarEmpleado($n_empleado)
    {
        $empleado = Empleado::where('n_empleado', $n_empleado)->first();

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'error' => 'Empleado no encontrado.'
            ], 200);  // <--- Retorna 200 para evitar error 404 en frontend
        }

        try {
            $respuesta = $this->registrarChecada($empleado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar asistencia: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'empleado' => $empleado,
            'asistencia' => $respuesta,
        ]);
    }

    public function registrarChecada($empleado)
    {
        $ahora = now();
        $hoy = $ahora->toDateString();

        $tipo = $this->detectarTipoChecada($empleado, $ahora);

        if (!$tipo) {
            return [
                'success' => false,
                'message' => 'Ya completaste tus checadas del día.',
                'tipo' => $tipo ?? null
            ];
        }


        if ($tipo === 'salida_temprana') {
            return [
                'success' => true,
                'message' => 'Aún no es tu hora de salida. ¿Deseas registrar salida de todas formas?',
                'tipo' => $tipo
            ];
        }

        Checada::create([
            'empleado_id' => $empleado->id,
            'fecha_hora' => $ahora,
            'tipo' => $tipo,
        ]);

        // recalcular asistencia del día (opcional pero recomendado)
        //$this->generarAsistenciaDelDia($empleado, $hoy);
        $this->asistenciaService->generarAsistenciaDelDia($empleado);

        return [
            'success' => true,
            'message' => "Checada registrada: {$tipo}",
            'tipo' => $tipo
        ];
    }


    private function detectarTipoChecada($empleado, $ahora)
    {
        $hoy = $ahora->dayOfWeekIso;

        $horario = HorarioEmpleado::where('empleado_id', $empleado->id)
            ->where('dia_semana', $hoy)
            ->where('activo', true)
            ->first();

        $checadasHoy = Checada::where('empleado_id', $empleado->id)
            ->whereDate('fecha_hora', $ahora->toDateString())
            ->orderBy('fecha_hora')
            ->get();

        $total = $checadasHoy->count();

        $ultimaChecada = $checadasHoy->last();

        if (!$horario) {
            if ($ultimaChecada && $ultimaChecada->tipo === 'salida') {
                return null;
            }
            return $this->toggleEntradaSalidaLibre($empleado, $ahora);
        }

        // Entrada
        if ($total === 0) {

            // $horaSalida = $horario->hora_salida; // "HH:MM"
            //$horaSalida = $this->obtenerHoraSalidaEfectiva($horario);
            $horaSalida = $this->asistenciaService->obtenerHoraSalidaEfectiva($horario);
            if ($ahora->format('H:i') > $horaSalida) {
                return 'salida';
            }

            return 'entrada';
        }


        // Salida (con validación de horario)
        if ($total === 1) {

            // si ya registró salida → no permitir otra
            if ($ultimaChecada && $ultimaChecada->tipo === 'salida') {
                return null;
            }

            // $horaSalida = $horario->hora_salida;
            // $horaSalida = $this->obtenerHoraSalidaEfectiva($horario);
            $horaSalida = $this->asistenciaService->obtenerHoraSalidaEfectiva($horario);
            // si aún no es hora de salida
            if ($ahora->format('H:i') < $horaSalida) {
                return 'salida_temprana';
            }

            return 'salida';
        }

        // ya no permitir más de 2
        return null;
    }

    private function toggleEntradaSalidaLibre($empleado, $ahora)
    {
        $ultima = Checada::where('empleado_id', $empleado->id)
            ->whereDate('fecha_hora', today())
            ->latest()
            ->first();

        if (!$ultima) {
            return 'entrada';
        }

        return match ($ultima->tipo) {
            'entrada' => 'salida',
            'salida' => 'entrada',
            default => 'entrada'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRMAR SALIDA (YA OPCIONAL)
    |--------------------------------------------------------------------------
    */
    public function marcarSalidaConfirmada($n_empleado)
    {
        $empleado = Empleado::where('n_empleado', $n_empleado)->first();
        $ahora = now();
        $hoy = $ahora->toDateString();

        Checada::create([
            'empleado_id' => $empleado->id,
            'fecha_hora' => $ahora,
            'tipo' => 'salida',
        ]);

        //  $this->generarAsistenciaDelDia($empleado, $hoy);
        $this->asistenciaService->generarAsistenciaDelDia($empleado);
        return response()->json([
            'success' => true,
            'message' => 'Salida confirmada.'
        ]);
    }

}
