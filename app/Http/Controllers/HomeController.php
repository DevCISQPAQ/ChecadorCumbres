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
        $horariosPorDepartamento = [
            'Preescolar' => '07:30:00',
            'Primaria' => '08:00:00',
            'Secundaria' => '07:45:00',
            // Agrega los que necesites
        ];

        // Hora límite para el departamento del empleado o 07:30 si no está definido
        $horaLimite = $horariosPorDepartamento[$empleado->departamento] ?? '07:30:00';

        $ahora = now(); // Fecha y hora actual (Carbon instance)
        $fechaHoy = $ahora->format('Y-m-d');
        $horaLimiteCompleta = \Carbon\Carbon::parse("$fechaHoy $horaLimite");
        $horaDiez = \Carbon\Carbon::parse("$fechaHoy 09:20:00");

        if ($ahora->lessThan($horaDiez)) {
            // Registro de entrada (antes de las 10am)
            // Verificar si ya existe asistencia para hoy
            $asistenciaExistente = Asistencia::where('empleado_id', $empleado->id)
                ->whereDate('hora_entrada', $fechaHoy)
                ->first();

            if ($asistenciaExistente) {
                // Ya marcó entrada
                return [
                    'success' => false,
                    'message' => 'Ya tienes la entrada marcada para hoy.',
                ];
            }

            // Registrar entrada
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
        } else {
            // Después de las 10 am: actualizar la asistencia existente del día para marcar salida

            // Buscar asistencia del empleado para hoy (la entrada)
            $asistencia = Asistencia::where('empleado_id', $empleado->id)
                ->whereDate('hora_entrada', $fechaHoy)
                ->first();

            if ($asistencia) {
                if ($asistencia->hora_salida) {
                    // Ya marcó salida
                    return [
                        'success' => false,
                        'message' => 'Ya has marcado la salida para hoy.',
                    ];
                }

                $asistencia->hora_salida = $ahora;
                $asistencia->save();

                return [
                    'success' => true,
                    'message' => 'Salida registrada correctamente.',
                ];
            } else {
                // Opcional: si no existe la asistencia de entrada hoy, crear una con solo salida
                Asistencia::create([
                    'empleado_id' => $empleado->id,
                    'hora_entrada' => $ahora,  // Puede ser null o hora actual, según lógica
                    'hora_salida' => $ahora,
                    'retardo' => false,
                ]);
            }
        }
    }
}
