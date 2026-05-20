<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use App\Models\Configuracion;

use App\Models\Checada;
use App\Models\HorarioEmpleado;
use Carbon\Carbon;

class HomeController extends Controller
{
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
            //$respuesta = $this->agregarAsistencia($empleado);
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

        //
        // $ultimaChecada = Checada::where('empleado_id', $empleado->id)
        //     ->whereDate('fecha_hora', $hoy)
        //     ->latest()
        //     ->first();

        // // evitar duplicados seguidos
        // if ($ultimaChecada && $ultimaChecada->tipo === $tipo) {
        //     return [
        //         'success' => false,
        //         'message' => 'Ya registraste esta acción recientemente.'
        //     ];
        // }

        //


        Checada::create([
            'empleado_id' => $empleado->id,
            'fecha_hora' => $ahora,
            'tipo' => $tipo,
        ]);

        // recalcular asistencia del día (opcional pero recomendado)
        $this->generarAsistenciaDelDia($empleado, $hoy);

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

        // si no tiene horario → sistema libre
        // if (!$horario) {
        //     return $this->toggleEntradaSalidaLibre($empleado, $ahora);
        // }

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

        // 1️⃣ Entrada
        if ($total === 0) {

            $horaSalida = $horario->hora_salida; // "HH:MM"
            if ($ahora->format('H:i') > $horaSalida) {
                return 'salida';
            }

            return 'entrada';
        }


        // 2️⃣ Salida (con validación de horario)
        if ($total === 1) {

            // si ya registró salida → no permitir otra
            if ($ultimaChecada && $ultimaChecada->tipo === 'salida') {
                return null;
            }

            $horaSalida = $horario->hora_salida;

            // si aún no es hora de salida
            if ($ahora->format('H:i') < $horaSalida) {
                return 'salida_temprana';
            }

            return 'salida';
        }

        // 3️⃣ ya no permitir más de 2
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


    private function generarAsistenciaDelDia($empleado, $fecha)
    {

        //1. VACACIONES

        if ($this->estaEnVacaciones($empleado, $fecha)) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'vacaciones',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }


        //2. FESTIVOS

        if ($this->esDiaFestivo($fecha)) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'festivo',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }


        // 3. CHECADAS

        $checadas = \App\Models\Checada::where('empleado_id', $empleado->id)
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();

        $horario = \App\Models\HorarioEmpleado::where('empleado_id', $empleado->id)
            ->where('dia_semana', now()->dayOfWeekIso)
            ->where('activo', true)
            ->first();

        $entrada = $checadas->where('tipo', 'entrada')->first();
        $salida = $checadas->where('tipo', 'salida')->last();


        // 4. FALTA

        // SOLO tiene salida
        if (!$entrada && $salida) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'retardo',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }
        // NO tiene entrada ni salida
        if (!$entrada && !$salida && $horario) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'falta',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }


        // 5. RETARDO

        $estado = 'presente';
        $minutosRetardo = 0;

        if ($horario && $entrada) {

            $limite = \Carbon\Carbon::parse($horario->hora_entrada)
                ->addMinutes($horario->tolerancia_minutos);

            if ($entrada->fecha_hora->gt($limite)) {
                $estado = 'retardo';
                $minutosRetardo = $limite->diffInMinutes($entrada->fecha_hora);
            }
        }


        // 6. HORAS

        $horas = 0;

        if ($entrada && $salida) {
            $horas = abs(
                $entrada->fecha_hora->diffInMinutes($salida->fecha_hora)
            ) / 60;
        }

        \App\Models\Asistencia::updateOrCreate(
            [
                'empleado_id' => $empleado->id,
                'fecha' => $fecha
            ],
            [
                'estado' => $estado,
                'horas_trabajadas' => $horas,
                'minutos_retardo' => $minutosRetardo
            ]
        );
    }


    private function estaEnVacaciones($empleado, $fecha)
    {
        return \App\Models\Vacacion::where('empleado_id', $empleado->id)
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->exists();
    }


    private function esDiaFestivo($fecha)
    {
        return \App\Models\DiaFestivo::whereDate('fecha', $fecha)->exists();
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

        // $checada = Checada::find($id);

        // if (!$checada) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Checada no encontrada.'
        //     ], 404);
        // }

        // $checada->tipo = 'salida';
        // $checada->save();

        Checada::create([
            'empleado_id' => $empleado->id,
            'fecha_hora' => $ahora,
            'tipo' => 'salida',
        ]);

        $this->generarAsistenciaDelDia($empleado, $hoy);
        return response()->json([
            'success' => true,
            'message' => 'Salida confirmada.'
        ]);
    }


}
