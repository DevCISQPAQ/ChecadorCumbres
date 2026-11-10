@extends('layouts.admin')

@section('content')

@php
$tarjetas = [
['nombre' => 'Presentes', 'valor' => $resumen->presentes, 'color' => 'green', 'icono' => '✓'],
['nombre' => 'Retardos', 'valor' => $resumen->retardos, 'color' => 'amber', 'icono' => '◷'],
['nombre' => 'Faltas', 'valor' => $resumen->faltas, 'color' => 'red', 'icono' => '!'],
['nombre' => 'Permisos', 'valor' => $resumen->permisos, 'color' => 'purple', 'icono' => 'P'],
['nombre' => 'Vacaciones', 'valor' => $resumen->vacaciones, 'color' => 'blue', 'icono' => 'V'],
['nombre' => 'Festivos', 'valor' => $resumen->festivos, 'color' => 'gray', 'icono' => 'F'],
['nombre' => 'Días libres', 'valor' => $resumen->libres, 'color' => 'cyan', 'icono' => 'L'],
['nombre' => 'Incapacidades', 'valor' => $resumen->incapacidades, 'color' => 'pink', 'icono' => 'I'],
];

$estilosTarjetas = [
'green' => ['text' => 'text-green-600', 'icon' => 'bg-green-100 text-green-700'],
'amber' => ['text' => 'text-amber-600', 'icon' => 'bg-amber-100 text-amber-700'],
'red' => ['text' => 'text-red-600', 'icon' => 'bg-red-100 text-red-700'],
'purple' => ['text' => 'text-purple-600', 'icon' => 'bg-purple-100 text-purple-700'],
'blue' => ['text' => 'text-blue-600', 'icon' => 'bg-blue-100 text-blue-700'],
'gray' => ['text' => 'text-gray-600', 'icon' => 'bg-gray-100 text-gray-700'],
'cyan' => ['text' => 'text-cyan-600', 'icon' => 'bg-cyan-100 text-cyan-700'],
'pink' => ['text' => 'text-pink-600', 'icon' => 'bg-pink-100 text-pink-700'],
];

$coloresBarras = [
'Presente' => 'bg-green-500',
'Retardo' => 'bg-amber-500',
'Falta' => 'bg-red-500',
'Permiso' => 'bg-purple-500',
'Vacaciones' => 'bg-blue-500',
'Festivo' => 'bg-gray-500',
'Libre' => 'bg-cyan-500',
'Incapacidad' => 'bg-pink-500',
];
@endphp

{{-- ENCABEZADO --}}

<div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-5">

    <div>
        <h2 class="text-xl font-bold text-gray-800">
            Panel de asistencia
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Bienvenido(a), {{ Auth::user()->name }}
        </p>
    </div>

    <div class="text-sm text-gray-500">
        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
    </div>

</div>


{{-- FILTRO DE FECHAS --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-5">

    <form action="{{ route('dashboard') }}" method="GET"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Fecha inicial
            </label>

            <input type="date" name="desde"
                value="{{ $fechaInicio->format('Y-m-d') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">
                Fecha final
            </label>

            <input type="date" name="hasta"
                value="{{ $fechaFin->format('Y-m-d') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-semibold">
            Filtrar información
        </button>

        <a href="{{ route('dashboard') }}"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold text-center">
            Limpiar filtros
        </a>

        <div class="text-xs text-gray-500 lg:text-right">
            Periodo seleccionado
            <div class="font-semibold text-gray-700 mt-1">
                {{ $fechaInicio->format('d/m/Y') }} -
                {{ $fechaFin->format('d/m/Y') }}
            </div>
        </div>

    </form>

</div>


{{-- TARJETAS DE LOS OCHO ESTADOS --}}

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">

    @foreach($tarjetas as $tarjeta)

    @php
    $estilo = $estilosTarjetas[$tarjeta['color']];
    @endphp

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-3 hover:shadow-md transition">

        <div class="flex items-center justify-between gap-2">

            <p class="text-xs sm:text-sm font-medium text-gray-500">
                {{ $tarjeta['nombre'] }}
            </p>

            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold {{ $estilo['icon'] }}">
                {{ $tarjeta['icono'] }}
            </div>

        </div>

        <h3 class="text-2xl font-bold mt-2 {{ $estilo['text'] }}">
            {{ number_format($tarjeta['valor']) }}
        </h3>

        <p class="text-[10px] text-gray-400 mt-1">
            Registros del periodo
        </p>

    </div>

    @endforeach

</div>


{{-- INDICADORES ADICIONALES --}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">

        <p class="text-xs text-gray-500 font-medium">
            Minutos acumulados de retardo
        </p>

        <h3 class="text-2xl font-bold text-amber-600 mt-1">
            {{ number_format($resumen->minutos_retardo) }}
            <span class="text-xs text-gray-400 font-normal">minutos</span>
        </h3>

    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">

        <p class="text-xs text-gray-500 font-medium">
            Total de horas trabajadas
        </p>

        <h3 class="text-2xl font-bold text-blue-600 mt-1">
            {{ number_format($resumen->horas_trabajadas, 2) }}
            <span class="text-xs text-gray-400 font-normal">horas</span>
        </h3>

    </div>

</div>


{{-- GRÁFICAS: RETARDOS Y FALTAS --}}

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

        <div class="mb-4">
            <h3 class="text-sm font-bold text-gray-800">
                Retardos por departamento
            </h3>

            <p class="text-xs text-gray-400 mt-1">
                Cantidad de retardos registrados
            </p>
        </div>

        <div class="relative w-full h-64">
            <canvas id="graficaRetardos"></canvas>
        </div>

    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

        <div class="mb-4">
            <h3 class="text-sm font-bold text-gray-800">
                Faltas por departamento
            </h3>

            <p class="text-xs text-gray-400 mt-1">
                Cantidad de faltas registradas
            </p>
        </div>

        <div class="relative w-full h-64">
            <canvas id="graficaFaltas"></canvas>
        </div>

    </div>

</div>


{{-- GRÁFICA DE ESTADOS Y RESUMEN --}}

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

        <h3 class="text-sm font-bold text-gray-800">
            Distribución de estados
        </h3>

        <p class="text-xs text-gray-400 mt-1 mb-3">
            Proporción de los ocho estados de asistencia
        </p>

        <div class="relative w-full h-72">
            <canvas id="graficaEstados"></canvas>
        </div>

    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">

        <h3 class="text-sm font-bold text-gray-800">
            Resumen de asistencia
        </h3>

        <p class="text-xs text-gray-400 mt-1 mb-4">
            Cantidad de registros por estado
        </p>

        <div class="space-y-3">

            @foreach($datosEstados as $estado => $total)

            @php
            $porcentaje = $resumen->total_registros > 0
            ? ($total / $resumen->total_registros) * 100
            : 0;
            @endphp

            <div>

                <div class="flex justify-between items-center mb-1">

                    <span class="text-xs font-medium text-gray-600">
                        {{ $estado }}
                    </span>

                    <span class="text-xs font-bold text-gray-800">
                        {{ number_format($total) }}
                    </span>

                </div>

                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">

                    <div class="{{ $coloresBarras[$estado] ?? 'bg-gray-400' }} h-full rounded-full"
                        style="width: {{ min($porcentaje, 100) }}%">
                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>


{{-- EVOLUCIÓN DIARIA --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">

    <h3 class="text-sm font-bold text-gray-800">
        Evolución diaria de asistencias
    </h3>

    <p class="text-xs text-gray-400 mt-1 mb-4">
        Comportamiento diario de los ocho estados
    </p>

    <div class="relative w-full h-80">
        <canvas id="graficaEvolucion"></canvas>
    </div>

</div>


{{-- ESTADOS POR DEPARTAMENTO --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-5">

    <h3 class="text-sm font-bold text-gray-800">
        Estados de asistencia por departamento
    </h3>

    <p class="text-xs text-gray-400 mt-1 mb-4">
        Comparación de todos los estados por área
    </p>

    <div class="relative w-full h-96">
        <canvas id="graficaDepartamentosEstados"></canvas>
    </div>

</div>


{{-- CONTINÚA EN LA PARTE 2 --}}

{{-- CHART.JS --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (typeof Chart === 'undefined') {
            console.error('Chart.js no está cargado.');
            return;
        }

        Chart.defaults.font.family = 'Arial, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#6b7280';

        const retardos = @json($retardos);
        const faltas = @json($faltas);
        const estados = @json($datosEstados);
        const evolucion = @json($evolucionDiaria);
        const datosDepartamentos = @json($estadosPorDepartamento);

        const coloresEstados = [
            '#22c55e',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#3b82f6',
            '#64748b',
            '#06b6d4',
            '#ec4899'
        ];

        const opcionesBarras = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // RETARDOS POR DEPARTAMENTO

        new Chart(document.getElementById('graficaRetardos'), {
            type: 'bar',
            data: {
                labels: retardos.map(item => item.departamento),
                datasets: [{
                    label: 'Retardos',
                    data: retardos.map(item => Number(item.total_retardos)),
                    backgroundColor: '#f59e0b',
                    borderRadius: 5,
                    maxBarThickness: 45
                }]
            },
            options: opcionesBarras
        });

        // FALTAS POR DEPARTAMENTO

        new Chart(document.getElementById('graficaFaltas'), {
            type: 'bar',
            data: {
                labels: faltas.map(item => item.departamento),
                datasets: [{
                    label: 'Faltas',
                    data: faltas.map(item => Number(item.total)),
                    backgroundColor: '#ef4444',
                    borderRadius: 5,
                    maxBarThickness: 45
                }]
            },
            options: opcionesBarras
        });

        // DISTRIBUCIÓN DE ESTADOS

        new Chart(document.getElementById('graficaEstados'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(estados),
                datasets: [{
                    data: Object.values(estados),
                    backgroundColor: coloresEstados,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 14,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // EVOLUCIÓN DIARIA

        const estadosGrafica = [{
                key: 'presente',
                label: 'Presente',
                color: '#22c55e'
            },
            {
                key: 'retardo',
                label: 'Retardo',
                color: '#f59e0b'
            },
            {
                key: 'falta',
                label: 'Falta',
                color: '#ef4444'
            },
            {
                key: 'permiso',
                label: 'Permiso',
                color: '#8b5cf6'
            },
            {
                key: 'vacaciones',
                label: 'Vacaciones',
                color: '#3b82f6'
            },
            {
                key: 'festivo',
                label: 'Festivo',
                color: '#64748b'
            },
            {
                key: 'libre',
                label: 'Libre',
                color: '#06b6d4'
            },
            {
                key: 'incapacidad',
                label: 'Incapacidad',
                color: '#ec4899'
            }
        ];

        new Chart(document.getElementById('graficaEvolucion'), {
            type: 'line',
            data: {
                labels: evolucion.map(item => item.fecha),
                datasets: estadosGrafica.map(estado => ({
                    label: estado.label,
                    data: evolucion.map(item => Number(item[estado.key] ?? 0)),
                    borderColor: estado.color,
                    backgroundColor: estado.color,
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    fill: false
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 13,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // TODOS LOS ESTADOS POR DEPARTAMENTO

        new Chart(document.getElementById('graficaDepartamentosEstados'), {
            type: 'bar',
            data: {
                labels: datosDepartamentos.labels,
                datasets: datosDepartamentos.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 13,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: '#f3f4f6'
                        }
                    }
                }
            }
        });

    });
</script>

@endsection