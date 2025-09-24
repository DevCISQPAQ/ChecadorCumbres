<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;

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
            return response()->json(['error' => 'Empleado no encontrado.'], 404);
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
        $ahora = now(); // Fecha y hora actual (Carbon instance)
        $fechaHoy = $ahora->format('Y-m-d');
        $horaLimiteCompleta = \Carbon\Carbon::parse("$fechaHoy" . $this->obtenerHoraLimite($empleado));
        $horaLimiteSalida = \Carbon\Carbon::parse("$fechaHoy 11:30:00");


        if ($ahora->lessThan($horaLimiteSalida)) {
            // Intento de marcar entrada
            if ($this->yaTieneEntradaHoy($empleado)) {
                return [
                    'success' => false,
                    'message' => 'Ya tienes la entrada marcada para hoy.',
                ];
            }

            return $this->registrarEntrada($empleado, $ahora, $horaLimiteCompleta);
        }

        //
        // Intento de marcar salida
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

        // No hay entrada, crear asistencia solo con salida
        return $this->crearSalidaSinEntrada($empleado, $ahora);
    }

    private function obtenerHoraLimite($empleado)
    {
        $horarios = [
            'Preescolar' => '07:30:00',
            'Primaria' => '08:00:00',
            'Secundaria' => '07:45:00',
            // Agrega los que necesites
        ];

        return $horarios[$empleado->departamento] ?? '07:30:00';
    }

    private function yaTieneEntradaHoy($empleado)
    {
        return Asistencia::where('empleado_id', $empleado->id)
            ->whereDate('hora_entrada', today())
            ->exists();
    }

    private function obtenerAsistenciaHoy($empleado)
    {
        return Asistencia::where('empleado_id', $empleado->id)
            // ->whereDate('hora_entrada', today())
            // ->first();
        ->whereDate('hora_entrada', today())
        ->orWhere(function ($query) use ($empleado) {
            $query->where('empleado_id', $empleado->id)
                  ->whereDate('hora_salida', today());
        })
        ->first();
    }

    private function registrarEntrada($empleado, $ahora, $horaLimiteCompleta)
    {
        $retardo = $ahora->greaterThan($horaLimiteCompleta);

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
        Asistencia::create([
            'empleado_id' => $empleado->id,
            'hora_entrada' => null, // puedes usar null si prefieres
            'hora_salida' => $ahora,
            'retardo' => true,
        ]);

        return [
            'success' => true,
            'message' => 'Salida registrada correctamente.',
        ];
    }
}
