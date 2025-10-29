<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use App\Models\Configuracion;

class HomeController extends Controller
{
    public function showWelcome()
    {
        return view('welcome');
    }

    public function buscarEmpleado($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'error' => 'Empleado no encontrado.'
            ], 200);  // <--- Retorna 200 para evitar error 404 en frontend
        }

        try {
            $respuesta = $this->agregarAsistencia($empleado);
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

    public function agregarAsistencia($empleado)
    {
        $ahora = now();
        $fechaHoy = $ahora->format('Y-m-d');
        $horaLimiteSalida = \Carbon\Carbon::parse($fechaHoy . ' ' . Configuracion::getValor('hora_limite_salida', '11:30:00'));

        if ($this->esHorarioLibre($empleado)) {
            return $this->agregarAsistenciaHorarioLibre($empleado, $ahora);
        }

        // Para horario base u otros
        return $this->agregarAsistenciaHorarioBase($empleado, $ahora, $horaLimiteSalida, $fechaHoy);
    }

    private function esHorarioLibre($empleado)
    {
        return strtolower($empleado->tipo_horario) === 'horario libre';
    }

    private function agregarAsistenciaHorarioLibre($empleado, $ahora)
    {

        $asistencia = $this->obtenerAsistenciaHoy($empleado);

        if (!$asistencia) {
            return $this->registrarEntrada($empleado, $ahora);
        }

        if ($asistencia && is_null($asistencia->hora_salida)) {
            return $this->validarSalidaHorarioLibre($asistencia, $ahora);
        }

        return [
            'success' => false,
            'message' => 'Ya tienes registrada la entrada y salida para hoy.',
        ];
    }

    private function validarSalidaHorarioLibre($asistencia, $ahora)
    {
        $horaEntrada = \Carbon\Carbon::parse($asistencia->hora_entrada);
        $minutosDesdeEntrada = $horaEntrada->diffInMinutes($ahora);

        if ($minutosDesdeEntrada < 60) {
            return [
                'success' => false,
                'confirmar_salida' => true,
                'message' => 'Ya has marcado tu entrada. ¿Quieres marcar tu salida?',
                'asistencia_id' => $asistencia->id,
            ];
        }

        // Más de una hora, marcar salida automáticamente
        return $this->registrarSalida($asistencia, $ahora);
    }

    private function agregarAsistenciaHorarioBase($empleado, $ahora, $horaLimiteSalida, $fechaHoy)
    {
        if ($ahora->lessThan($horaLimiteSalida)) {
            $asistencia = $this->obtenerAsistenciaHoy($empleado);
            if ($asistencia) {
                if (!empty($asistencia->hora_entrada) && empty($asistencia->hora_salida)) {
                    return [
                        'success' => false,
                        'confirmar_salida' => true,
                        'message' => 'Ya has marcado tu entrada. ¿Quieres marcar tu salida?',
                        'asistencia_id' => $asistencia->id,
                    ];
                }
                if (!empty($asistencia->hora_entrada) && !empty($asistencia->hora_salida)) {
                    return [
                        'success' => false,
                        'message' => 'Ya tienes tu entrada y salida marcadas para hoy.',
                    ];
                }
            }

            $horaLimiteCompleta = \Carbon\Carbon::parse($fechaHoy . ' ' . Configuracion::getValor('hora_limite_entrada', '07:35:00'));
            return $this->registrarEntrada($empleado, $ahora, $horaLimiteCompleta);
        }

        $asistencia = $this->obtenerAsistenciaHoy($empleado);

        if ($asistencia) {
            if ($this->yaTieneSalidaHoy($asistencia)) {
                return [
                    'success' => false,
                    'message' => 'Ya has marcado la salida para hoy.',
                ];
            }

            return $this->registrarSalida($asistencia, $ahora);
        }

        return $this->crearSalidaSinEntrada($empleado, $ahora);
    }

    private function obtenerAsistenciaHoy($empleado)
    {
        return Asistencia::where('empleado_id', $empleado->id)
            ->where(function ($query) {
                $query->whereDate('hora_entrada', today())
                    ->orWhereDate('hora_salida', today());
            })
            ->first();
    }

    private function registrarEntrada($empleado, $ahora, $horaLimiteCompleta = null)
    {
        $retardo = false;

        if ($horaLimiteCompleta) {
            $retardo = $ahora->greaterThan($horaLimiteCompleta);
        }

        Asistencia::create([
            'empleado_id' => $empleado->id,
            'hora_entrada' => $ahora,
            'hora_salida' => null,
            'retardo' => $retardo,
        ]);

        return [
            'success' => true,
            'message' => 'Entrada registrada correctamente.',
        ];
    }

    private function yaTieneSalidaHoy($asistencia)
    {
        return !is_null($asistencia->hora_salida);
    }

    private function registrarSalida($asistencia, $ahora)
    {
        $asistencia->hora_salida = $ahora;
        $asistencia->save();

        return [
            'success' => true,
            'message' => 'Salida registrada correctamente.',
        ];
    }

    private function crearSalidaSinEntrada($empleado, $ahora)
    {

        $retardo = false;
        $horaE = $ahora;

        if (strtolower($empleado->tipo_horario) === 'horario base') {
            $horaE = null;
            $retardo = true; 
        }

        Asistencia::create([
            'empleado_id' => $empleado->id,
            'hora_entrada' => $horaE, 
            'hora_salida' => $ahora,
            'retardo' => $retardo,
        ]);

        return [
            'success' => true,
            'message' => 'Salida registrada correctamente.',
        ];
    }

    public function marcarSalidaConfirmada($id)
    {
        $asistencia = Asistencia::find($id);

        if (!$asistencia) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de asistencia no encontrado.'
            ], 404);
        }

        if ($asistencia->hora_salida !== null) {
            return response()->json([
                'success' => false,
                'message' => 'La salida ya fue registrada previamente.'
            ], 400);
        }

        $ahora = now();
        $asistencia->hora_salida = $ahora;
        $asistencia->save();

        return response()->json([
            'success' => true,
            'message' => 'Salida registrada correctamente.',
        ]);
    }
}
