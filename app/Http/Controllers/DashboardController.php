<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | FILTRO DE FECHAS
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $fechaInicio = isset($validated['desde'])
            ? Carbon::parse($validated['desde'])->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $fechaFin = isset($validated['hasta'])
            ? Carbon::parse($validated['hasta'])->endOfDay()
            : now()->endOfDay();

        $desde = $fechaInicio->toDateString();
        $hasta = $fechaFin->toDateString();

        /*
        |--------------------------------------------------------------------------
        | ESTADOS DE ASISTENCIA
        |--------------------------------------------------------------------------
        */

        $estadosDisponibles = [
            'presente' => 'Presente',
            'retardo' => 'Retardo',
            'falta' => 'Falta',
            'permiso' => 'Permiso',
            'vacaciones' => 'Vacaciones',
            'festivo' => 'Festivo',
            'libre' => 'Libre',
            'incapacidad' => 'Incapacidad',
        ];

        $coloresEstados = [
            'presente' => '#22c55e',
            'retardo' => '#f59e0b',
            'falta' => '#ef4444',
            'permiso' => '#8b5cf6',
            'vacaciones' => '#3b82f6',
            'festivo' => '#64748b',
            'libre' => '#06b6d4',
            'incapacidad' => '#ec4899',
        ];

        /*
        |--------------------------------------------------------------------------
        | CONSULTAR ASISTENCIAS
        |--------------------------------------------------------------------------
        */

        $asistencias = DB::table('asistencias')
            ->join(
                'empleados',
                'asistencias.empleado_id',
                '=',
                'empleados.id'
            )
            ->leftJoin(
                'departamentos',
                'empleados.departamento_id',
                '=',
                'departamentos.id'
            )
            ->whereBetween('asistencias.fecha', [$desde, $hasta])
            ->select(
                'asistencias.empleado_id',
                'asistencias.fecha',
                'asistencias.estado',
                'asistencias.minutos_retardo',
                'asistencias.horas_trabajadas',
                'departamentos.nombre as departamento'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PREPARAR REGISTROS
        |--------------------------------------------------------------------------
        */

        $registros = [];
        $clavesAsistencia = [];

        foreach ($asistencias as $asistencia) {

            $fecha = Carbon::parse($asistencia->fecha)
                ->toDateString();

            $clave = $asistencia->empleado_id . '|' . $fecha;

            $registros[$clave] = [
                'empleado_id' => $asistencia->empleado_id,
                'fecha' => $fecha,
                'departamento' => $asistencia->departamento
                    ?? 'Sin departamento',
                'estado' => $asistencia->estado,
                'minutos_retardo' => (int) $asistencia->minutos_retardo,
                'horas_trabajadas' => (float) (
                    $asistencia->horas_trabajadas ?? 0
                ),
            ];

            $clavesAsistencia[$clave] = true;
        }

        /*
        |--------------------------------------------------------------------------
        | CONSULTAR AUSENCIAS
        |--------------------------------------------------------------------------
        */

        $ausencias = DB::table('ausencias')
            ->join(
                'empleados',
                'ausencias.empleado_id',
                '=',
                'empleados.id'
            )
            ->leftJoin(
                'departamentos',
                'empleados.departamento_id',
                '=',
                'departamentos.id'
            )
            ->whereIn('ausencias.tipo', [
                'permiso',
                'vacaciones',
                'incapacidad',
            ])
            ->where('ausencias.fecha_inicio', '<=', $hasta)
            ->where('ausencias.fecha_fin', '>=', $desde)
            ->select(
                'ausencias.empleado_id',
                'ausencias.tipo',
                'ausencias.fecha_inicio',
                'ausencias.fecha_fin',
                'departamentos.nombre as departamento'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | COMPLETAR AUSENCIAS
        |--------------------------------------------------------------------------
        */

        $prioridadAusencia = [
            'permiso' => 1,
            'vacaciones' => 2,
            'incapacidad' => 3,
        ];

        foreach ($ausencias as $ausencia) {

            $inicio = Carbon::parse($ausencia->fecha_inicio)
                ->startOfDay();

            $fin = Carbon::parse($ausencia->fecha_fin)
                ->startOfDay();

            if ($inicio->lt($fechaInicio)) {
                $inicio = $fechaInicio->copy()->startOfDay();
            }

            if ($fin->gt($fechaFin)) {
                $fin = $fechaFin->copy()->startOfDay();
            }

            for (
                $fecha = $inicio->copy();
                $fecha->lte($fin);
                $fecha->addDay()
            ) {

                $fechaString = $fecha->toDateString();

                $clave = $ausencia->empleado_id . '|' . $fechaString;

                // Nunca reemplazar una asistencia registrada.
                if (isset($clavesAsistencia[$clave])) {
                    continue;
                }

                // Resolver ausencias superpuestas.
                if (isset($registros[$clave])) {

                    $estadoActual = $registros[$clave]['estado'];

                    $prioridadActual =
                        $prioridadAusencia[$estadoActual] ?? 0;

                    $prioridadNueva =
                        $prioridadAusencia[$ausencia->tipo] ?? 0;

                    if ($prioridadNueva <= $prioridadActual) {
                        continue;
                    }
                }

                $registros[$clave] = [
                    'empleado_id' => $ausencia->empleado_id,
                    'fecha' => $fechaString,
                    'departamento' => $ausencia->departamento
                        ?? 'Sin departamento',
                    'estado' => $ausencia->tipo,
                    'minutos_retardo' => 0,
                    'horas_trabajadas' => 0,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INICIALIZAR TOTALES
        |--------------------------------------------------------------------------
        */

        $datosEstados = array_fill_keys(
            array_values($estadosDisponibles),
            0
        );

        $departamentos = [];
        $totalPorDia = [];

        $totalMinutosRetardo = 0;
        $totalHorasTrabajadas = 0;

        /*
        |--------------------------------------------------------------------------
        | AGRUPAR INFORMACIÓN
        |--------------------------------------------------------------------------
        */

        foreach ($registros as $registro) {

            $estado = $registro['estado'];

            if (!isset($estadosDisponibles[$estado])) {
                continue;
            }

            $nombreEstado = $estadosDisponibles[$estado];

            $departamento = $registro['departamento'];

            $fecha = $registro['fecha'];

            // Totales por estado.
            $datosEstados[$nombreEstado]++;

            // Totales diarios.
            if (!isset($totalPorDia[$fecha])) {

                $totalPorDia[$fecha] = array_fill_keys(
                    array_keys($estadosDisponibles),
                    0
                );
            }

            $totalPorDia[$fecha][$estado]++;

            // Inicializar departamento.
            if (!isset($departamentos[$departamento])) {

                $departamentos[$departamento] = array_fill_keys(
                    array_keys($estadosDisponibles),
                    0
                );

                $departamentos[$departamento]['minutos_retardo'] = 0;
            }

            // Totales por departamento.
            $departamentos[$departamento][$estado]++;

            // Minutos de retardo.
            if ($estado === 'retardo') {

                $minutos = $registro['minutos_retardo'];

                $totalMinutosRetardo += $minutos;

                $departamentos[$departamento]['minutos_retardo']
                    += $minutos;
            }

            // Horas trabajadas.
            $totalHorasTrabajadas += $registro['horas_trabajadas'];
        }

        /*
        |--------------------------------------------------------------------------
        | RETARDOS POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        $retardos = collect($departamentos)
            ->map(function ($datos, $nombre) {

                return [
                    'departamento' => $nombre,
                    'total_retardos' => $datos['retardo'],
                    'minutos' => $datos['minutos_retardo'],
                ];
            })
            ->filter(fn($item) => $item['total_retardos'] > 0)
            ->sortByDesc('total_retardos')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FALTAS POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        $faltas = collect($departamentos)
            ->map(function ($datos, $nombre) {

                return [
                    'departamento' => $nombre,
                    'total' => $datos['falta'],
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->sortByDesc('total')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TODOS LOS ESTADOS POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        $estadosPorDepartamento = [
            'labels' => array_keys($departamentos),
            'datasets' => [],
        ];

        foreach ($estadosDisponibles as $clave => $nombre) {

            $estadosPorDepartamento['datasets'][] = [
                'label' => $nombre,

                'data' => collect($departamentos)
                    ->map(fn($datos) => $datos[$clave])
                    ->values()
                    ->all(),

                'backgroundColor' => $coloresEstados[$clave],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | EVOLUCIÓN DIARIA
        |--------------------------------------------------------------------------
        */

        $evolucionDiaria = [];

        for (
            $fecha = $fechaInicio->copy()->startOfDay();
            $fecha->lte($fechaFin);
            $fecha->addDay()
        ) {

            $fechaString = $fecha->toDateString();

            $registroDia = $totalPorDia[$fechaString] ?? [];

            $fila = [
                'fecha' => ucfirst(
                    $fecha->locale('es')->isoFormat('ddd D MMM')
                ),
            ];

            foreach ($estadosDisponibles as $clave => $nombre) {
                $fila[$clave] = $registroDia[$clave] ?? 0;
            }

            $evolucionDiaria[] = $fila;
        }

        /*
        |--------------------------------------------------------------------------
        | RESUMEN GENERAL
        |--------------------------------------------------------------------------
        */

        $resumen = (object) [

            'total_registros' => count($registros),

            'presentes' => $datosEstados['Presente'],

            'retardos' => $datosEstados['Retardo'],

            'faltas' => $datosEstados['Falta'],

            'permisos' => $datosEstados['Permiso'],

            'vacaciones' => $datosEstados['Vacaciones'],

            'festivos' => $datosEstados['Festivo'],

            'libres' => $datosEstados['Libre'],

            'incapacidades' => $datosEstados['Incapacidad'],

            'minutos_retardo' => $totalMinutosRetardo,

            'horas_trabajadas' => $totalHorasTrabajadas,
        ];

        /*
        |--------------------------------------------------------------------------
        | ENVIAR INFORMACIÓN A LA VISTA
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'resumen',
            'datosEstados',
            'retardos',
            'faltas',
            'evolucionDiaria',
            'estadosPorDepartamento'
        ));
    }
}
