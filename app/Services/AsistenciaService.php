<?php

namespace App\Services;

use App\Models\Asistencia;
use App\Models\Checada;
use App\Models\Configuracion;
use App\Models\DiaFestivo;
use App\Models\Empleado;
use App\Models\HorarioEmpleado;
use App\Models\Ausencias;
use Carbon\Carbon;

class AsistenciaService
{
    /**
     * Genera o actualiza la asistencia de un empleado para una fecha.
     *
     * Esta función debe ser utilizada tanto por:
     * - El comando CRON
     * - El registro de checada
     */
    // public function generarAsistenciaDelDia(Empleado $empleado, $fecha): Asistencia
    // {


    public function generarAsistenciaDelDia(Empleado $empleado, $fecha = null): Asistencia
    {
        $fecha = $fecha
            ? Carbon::parse($fecha)->toDateString()
            : now()->toDateString();

        // =====================================================
        // HORARIO
        // =====================================================

        $diaSemana = Carbon::parse($fecha)->dayOfWeekIso;

        $horario = HorarioEmpleado::where('empleado_id', $empleado->id)
            ->where('dia_semana', $diaSemana)
            ->first();

        // =====================================================
        // AUSENCIAS: PRIORIDAD
        // =====================================================

        $ausencia = Ausencias::where('empleado_id', $empleado->id)
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->first();

        if ($ausencia) {
            return $this->guardarAsistencia(
                $empleado,
                $fecha,
                $ausencia->tipo,
                0,
                0
            );
        }

        // =====================================================
        // FESTIVO
        // =====================================================

        $esFestivo = DiaFestivo::whereDate('fecha', $fecha)
            ->exists();

        if ($esFestivo) {
            return $this->guardarAsistencia(
                $empleado,
                $fecha,
                'festivo',
                0,
                0
            );
        }

        // =====================================================
        // CHECADAS
        // =====================================================

        $checadas = Checada::where('empleado_id', $empleado->id)
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();

        $entrada = $checadas
            ->where('tipo', 'entrada')
            ->first();

        $salida = $checadas
            ->where('tipo', 'salida')
            ->last();

        // =====================================================
        // SI TIENE CHECADAS
        // =====================================================

        if ($entrada || $salida) {

            $estado = 'presente';
            $minutosRetardo = 0;

            if ($entrada && $horario && $horario->activo) {

                $horaEntradaEfectiva = $this->obtenerHoraEntradaEfectiva(
                    $horario
                );

                $limite = Carbon::parse($fecha . ' ' . $horaEntradaEfectiva)
                    ->addMinutes($horario->tolerancia_minutos);

                if ($entrada->fecha_hora->gt($limite)) {

                    $estado = 'retardo';

                    $minutosRetardo = (int) $limite->diffInMinutes(
                        $entrada->fecha_hora
                    );
                }
            }

            $horasTrabajadas = 0;

            if ($entrada && $salida) {

                $horasTrabajadas = abs(
                    $entrada->fecha_hora->diffInMinutes(
                        $salida->fecha_hora
                    )
                ) / 60;
            }

            return $this->guardarAsistencia(
                $empleado,
                $fecha,
                $estado,
                $minutosRetardo,
                $horasTrabajadas
            );
        }

        // =====================================================
        // LIBRE
        // =====================================================

        if (!$horario || !$horario->activo) {

            return $this->guardarAsistencia(
                $empleado,
                $fecha,
                'libre',
                0,
                0
            );
        }

        // =====================================================
        // FALTA
        // =====================================================

        return $this->guardarAsistencia(
            $empleado,
            $fecha,
            'falta',
            0,
            0
        );
    }



    /**
     * Guarda o actualiza una asistencia.
     */
    private function guardarAsistencia(
        Empleado $empleado,
        string $fecha,
        string $estado,
        int $minutosRetardo = 0,
        float $horasTrabajadas = 0
    ): Asistencia {

        return Asistencia::updateOrCreate(
            [
                'empleado_id' => $empleado->id,
                'fecha' => $fecha
            ],
            [
                'estado' => $estado,
                'minutos_retardo' => $minutosRetardo,
                'horas_trabajadas' => $horasTrabajadas
            ]
        );
    }

    /**
     * Obtiene la hora de entrada efectiva.
     *
     * Permite modificar el horario 07:30 mediante configuración.
     */
    public function obtenerHoraEntradaEfectiva($horario)
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

        if (
            $ajusteActivo == '1' &&
            Carbon::parse($horario->hora_entrada)->format('H:i') === '07:30'
        ) {
            return $horaAjustada;
        }

        return $horario->hora_entrada;
    }

    /**
     * Obtiene la hora de salida efectiva.
     *
     * Permite modificar el horario 15:00 mediante configuración.
     */
    public function obtenerHoraSalidaEfectiva($horario)
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

        if (
            $ajusteActivo == '1' &&
            Carbon::parse($horario->hora_salida)->format('H:i') === '15:00'
        ) {
            return $horaAjustada;
        }

        return $horario->hora_salida;
    }
}
