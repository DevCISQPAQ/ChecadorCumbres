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
    private function generarAsistenciaDelDia($empleado, $fecha) //validar lo de horas trabajadas
    {
        // CHECADAS
        $checadas = \App\Models\Checada::where('empleado_id', $empleado->id)
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();

        $horario = \App\Models\HorarioEmpleado::where('empleado_id', $empleado->id)
            ->where('dia_semana', \Carbon\Carbon::parse($fecha)->dayOfWeekIso)
            ->first();

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

        // NO tiene horario = libre
        if (!$horario) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'libre',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }

        // Tiene horario pero está desactivado = permiso
        if (!$horario->activo) {

            \App\Models\Asistencia::updateOrCreate(
                [
                    'empleado_id' => $empleado->id,
                    'fecha' => $fecha
                ],
                [
                    'estado' => 'permiso',
                    'horas_trabajadas' => 0,
                    'minutos_retardo' => 0
                ]
            );

            return;
        }


        $entrada = $checadas->where('tipo', 'entrada')->first();
        $salida = $checadas->where('tipo', 'salida')->last();

        //falta
        if (!$entrada && !$salida) {

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


        if ($entrada) {

            $horaEntradaEfectiva = $this->obtenerHoraEntradaEfectiva($horario);

            $limite = \Carbon\Carbon::parse($horaEntradaEfectiva)
                ->addMinutes($horario->tolerancia_minutos);

            // $limite = \Carbon\Carbon::parse($horario->hora_entrada)
            //     ->addMinutes($horario->tolerancia_minutos);

            if ($entrada->fecha_hora->gt($limite)) {

                $estado = 'retardo';

                $minutosRetardo = $limite->diffInMinutes(
                    $entrada->fecha_hora
                );
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
    }*/

    /*
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
    }*/

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

    /*
    private function obtenerHoraEntradaEfectiva($horario)
    {
        if (!$horario) {
            return null;
        }

        $ajusteActivo = Configuracion::where(
            'clave',
            'ajuste_horario_0730'
        )->value('valor');

        $horaAjustada = Configuracion::where(
            'clave',
            'hora_entrada_ajustada_0730'
        )->value('valor');

        // Si el ajuste está activo y el horario original es 07:30
        if (
            $ajusteActivo == '1' &&
            Carbon::parse($horario->hora_entrada)->format('H:i') === '07:30'
        ) {
            return $horaAjustada;
        }

        // Todos los demás conservan su horario original
        return $horario->hora_entrada;
    }

    private function obtenerHoraSalidaEfectiva($horario)
    {
        if (!$horario) {
            return null;
        }

        $ajusteActivo = Configuracion::where(
            'clave',
            'ajuste_horario_salida_1500'
        )->value('valor');

        $horaAjustada = Configuracion::where(
            'clave',
            'hora_salida_ajustada_1500'
        )->value('valor');

        // Si el ajuste está activo y el horario original es 15:00
        if (
            $ajusteActivo == '1' &&
            Carbon::parse($horario->hora_salida)->format('H:i') === '15:00'
        ) {
            return $horaAjustada;
        }

        // Todos los demás conservan su horario original
        return $horario->hora_salida;
    }*/
}
