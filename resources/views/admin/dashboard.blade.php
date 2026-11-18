@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')

@php
$tarjetas = [
[
'nombre' => 'Presentes',
'valor' => $resumen->presentes,
'color' => '#1E9E5A',
'fondo' => '#E8F6EE',
'icono' => '✓'
],
[
'nombre' => 'Retardos',
'valor' => $resumen->retardos,
'color' => '#F2A900',
'fondo' => '#FFF6DD',
'icono' => '◷'
],
[
'nombre' => 'Faltas',
'valor' => $resumen->faltas,
'color' => '#D93025',
'fondo' => '#FDECEB',
'icono' => '!'
],
[
'nombre' => 'Permisos',
'valor' => $resumen->permisos,
'color' => '#131F6B',
'fondo' => '#E9ECFA',
'icono' => 'P'
],
[
'nombre' => 'Vacaciones',
'valor' => $resumen->vacaciones,
'color' => '#2447FF',
'fondo' => '#E9EDFF',
'icono' => 'V'
],
[
'nombre' => 'Festivos',
'valor' => $resumen->festivos,
'color' => '#5C8DFF',
'fondo' => '#EDF2FF',
'icono' => 'F'
],
[
'nombre' => 'Días libres',
'valor' => $resumen->libres,
'color' => '#007F91',
'fondo' => '#E3F8FA',
'icono' => 'L'
],
[
'nombre' => 'Incapacidades',
'valor' => $resumen->incapacidades,
'color' => '#6B4AA5',
'fondo' => '#F1ECF8',
'icono' => 'I'
],
];

$coloresBarras = [
'Presente' => '#1E9E5A',
'Retardo' => '#F2A900',
'Falta' => '#D93025',
'Permiso' => '#131F6B',
'Vacaciones' => '#2447FF',
'Festivo' => '#5C8DFF',
'Libre' => '#007F91',
'Incapacidad' => '#6B4AA5',
];

$totalRegistros = $resumen->total_registros;
@endphp


{{-- ================================================= --}}
{{-- ENCABEZADO --}}
{{-- ================================================= --}}

<div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

    <div>

        <div class="mb-2 flex items-center gap-2">

            <span class="h-2 w-2 rounded-full bg-[#2447FF]"></span>

            <span class="text-xs font-bold uppercase tracking-[0.16em] text-[#5C8DFF]">
                Panel administrativo
            </span>

        </div>

        <h2 class="text-[28px] font-bold leading-tight tracking-[-0.02em]
                   text-[#030614] sm:text-[32px]">
            Panel de asistencia
        </h2>

        <p class="mt-2 text-base text-slate-500">
            Bienvenido(a), {{ Auth::user()->name }}.
            Aquí tienes el resumen de asistencia.
        </p>

    </div>

    <div class="flex items-center gap-3 rounded-xl border border-slate-200
                bg-white px-4 py-3 shadow-sm">

        <div class="flex h-10 w-10 items-center justify-center
                    rounded-xl bg-[#E9EDFF] text-[#2447FF]">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

            </svg>

        </div>

        <div>

            <p class="text-xs text-slate-400">
                Fecha actual
            </p>

            <p class="text-sm font-semibold capitalize text-[#0A1033]">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </p>

        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- FILTRO DE FECHAS --}}
{{-- ================================================= --}}

<section class="mb-7 rounded-2xl border border-slate-200
                bg-white p-5 shadow-sm sm:p-6">

    <div class="mb-5 flex items-center gap-3">

        <div class="flex h-10 w-10 items-center justify-center
                    rounded-xl bg-[#E9EDFF] text-[#2447FF]">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />

            </svg>

        </div>

        <div>

            <h3 class="text-base font-semibold text-[#0A1033]">
                Filtrar asistencia
            </h3>

            <p class="text-sm text-slate-500">
                Selecciona el periodo que deseas consultar.
            </p>

        </div>

    </div>

    <form action="{{ route('dashboard') }}" method="GET"
        class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-5">

        <div>

            <label for="desde"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-[0.12em] text-slate-500">
                Fecha inicial
            </label>

            <input
                type="date"
                id="desde"
                name="desde"
                value="{{ $fechaInicio->format('Y-m-d') }}"
                class="w-full rounded-xl border border-slate-200 bg-white
                       px-3 py-3 text-sm text-[#0A1033] outline-none
                       transition focus:border-[#2447FF]
                       focus:ring-2 focus:ring-[#2447FF]/10">

        </div>

        <div>

            <label for="hasta"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-[0.12em] text-slate-500">
                Fecha final
            </label>

            <input
                type="date"
                id="hasta"
                name="hasta"
                value="{{ $fechaFin->format('Y-m-d') }}"
                class="w-full rounded-xl border border-slate-200 bg-white
                       px-3 py-3 text-sm text-[#0A1033] outline-none
                       transition focus:border-[#2447FF]
                       focus:ring-2 focus:ring-[#2447FF]/10">

        </div>

        <button
            type="submit"
            class="flex items-center justify-center gap-2 rounded-xl
                   bg-[#2447FF] px-4 py-3 text-sm font-semibold text-white
                   shadow-sm transition hover:bg-[#131F6B]">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-5-5m2-6a7 7 0 11-14 0 7 7 0 0114 0z" />

            </svg>

            Aplicar filtros

        </button>

        <a href="{{ route('dashboard') }}"
            class="flex items-center justify-center gap-2 rounded-xl
                   border border-slate-200 bg-white px-4 py-3
                   text-sm font-semibold text-slate-600
                   transition hover:bg-[#F1F5FF] hover:text-[#2447FF]">

            Limpiar filtros

        </a>

        <div class="rounded-xl bg-[#F1F5FF] px-4 py-3">

            <p class="text-xs text-slate-500">
                Periodo seleccionado
            </p>

            <p class="mt-1 text-sm font-semibold tabular-nums text-[#0A1033]">

                {{ $fechaInicio->format('d/m/Y') }}
                -
                {{ $fechaFin->format('d/m/Y') }}

            </p>

        </div>

    </form>

</section>


{{-- ================================================= --}}
{{-- TARJETAS DE ESTADOS --}}
{{-- ================================================= --}}

<div class="mb-7">

    <div class="mb-4 flex items-center justify-between">

        <div>
            <h3 class="text-xl font-semibold tracking-[-0.02em] text-[#0A1033]">
                Resumen de asistencia
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Registros clasificados por estado.
            </p>

        </div>

        <span class="hidden rounded-lg bg-[#E9EDFF] px-3 py-2
                     text-xs font-semibold text-[#2447FF] sm:inline-block">
            8 indicadores
        </span>

    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 xl:grid-cols-4">

        @foreach($tarjetas as $tarjeta)

        <div class="group rounded-2xl border border-slate-200
                        bg-white p-2 shadow-sm transition duration-200
                        hover:-translate-y-0.5 hover:shadow-md sm:p-3">

            <div class="flex items-center justify-between gap-2">

                <p class="text-sm font-medium text-slate-500">
                    {{ $tarjeta['nombre'] }}
                </p>

                <div
                    style="background-color: {{ $tarjeta['fondo'] }};
                               color: {{ $tarjeta['color'] }};"
                    class="flex h-5 w-5 shrink-0 items-center
                               justify-center rounded-xl text-lg font-bold">

                    {{ $tarjeta['icono'] }}

                </div>

            </div>

            <div class="mt-1">

                <h4
                    style="color: {{ $tarjeta['color'] }};"
                    class="text-3xl font-bold tracking-[-0.02em]
                               tabular-nums sm:text-[34px]">

                    {{ number_format($tarjeta['valor']) }}

                </h4>

                <p class="mt-1 text-xs text-slate-400">
                    Registros del periodo
                </p>

            </div>

        </div>

        @endforeach

    </div>

</div>

{{-- ================================================= --}}
{{-- INDICADORES DE TIEMPO --}}
{{-- ================================================= --}}

<div class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2">

    {{-- MINUTOS DE RETARDO --}}

    <div class="relative overflow-hidden rounded-2xl border border-slate-200
                bg-white p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    Minutos acumulados de retardo
                </p>

                <div class="mt-3 flex items-baseline gap-2">

                    <h3 class="text-3xl font-bold tracking-[-0.02em]
                               tabular-nums text-[#F2A900]">

                        {{ number_format($resumen->minutos_retardo) }}

                    </h3>

                    <span class="text-sm text-slate-400">
                        minutos
                    </span>

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Tiempo total acumulado en el periodo.
                </p>

            </div>

            <div class="flex h-12 w-12 items-center justify-center
                        rounded-xl bg-[#FFF6DD] text-[#F2A900]">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <circle cx="12" cy="12" r="9"
                        stroke-width="1.8" />

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 7v5l3 2" />

                </svg>

            </div>

        </div>

        <div class="absolute bottom-0 left-0 h-1 w-full bg-[#FFF6DD]">

            <div class="h-full w-1/3 bg-[#F2A900]"></div>

        </div>

    </div>


    {{-- HORAS TRABAJADAS --}}

    <div class="relative overflow-hidden rounded-2xl border border-slate-200
                bg-white p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    Total de horas trabajadas
                </p>

                <div class="mt-3 flex items-baseline gap-2">

                    <h3 class="text-3xl font-bold tracking-[-0.02em]
                               tabular-nums text-[#2447FF]">

                        {{ number_format($resumen->horas_trabajadas, 2) }}

                    </h3>

                    <span class="text-sm text-slate-400">
                        horas
                    </span>

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Horas registradas durante el periodo.
                </p>

            </div>

            <div class="flex h-12 w-12 items-center justify-center
                        rounded-xl bg-[#E9EDFF] text-[#2447FF]">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <circle cx="12" cy="12" r="9"
                        stroke-width="1.8" />

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 7v5h4" />

                </svg>

            </div>

        </div>

        <div class="absolute bottom-0 left-0 h-1 w-full bg-[#E9EDFF]">

            <div class="h-full w-1/3 bg-[#2447FF]"></div>

        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- GRÁFICAS DE RETARDOS Y FALTAS --}}
{{-- ================================================= --}}

<div class="mb-7">

    <div class="mb-4">

        <h3 class="text-xl font-semibold tracking-[-0.02em] text-[#0A1033]">
            Indicadores por departamento
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Consulta los retardos y las faltas registrados en cada área.
        </p>

    </div>


    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">


        {{-- RETARDOS POR DEPARTAMENTO --}}

        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-5 flex items-center justify-between">

                <div>

                    <div class="flex items-center gap-2">

                        <span class="h-2.5 w-2.5 rounded-full bg-[#F2A900]"></span>

                        <h4 class="text-base font-semibold text-[#0A1033]">
                            Retardos por departamento
                        </h4>

                    </div>

                    <p class="mt-2 text-sm text-slate-400">
                        Cantidad de retardos registrados.
                    </p>

                </div>

                <div class="rounded-lg bg-[#FFF6DD] px-3 py-2
                            text-xs font-bold text-[#9A6900]">

                    RETARDOS

                </div>

            </div>

            <div class="relative h-64 w-full">

                <canvas id="graficaRetardos"></canvas>

            </div>

        </div>


        {{-- FALTAS POR DEPARTAMENTO --}}

        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-5 flex items-center justify-between">

                <div>

                    <div class="flex items-center gap-2">

                        <span class="h-2.5 w-2.5 rounded-full bg-[#D93025]"></span>

                        <h4 class="text-base font-semibold text-[#0A1033]">
                            Faltas por departamento
                        </h4>

                    </div>

                    <p class="mt-2 text-sm text-slate-400">
                        Cantidad de faltas registradas.
                    </p>

                </div>

                <div class="rounded-lg bg-[#FDECEB] px-3 py-2
                            text-xs font-bold text-[#D93025]">

                    FALTAS

                </div>

            </div>

            <div class="relative h-64 w-full">

                <canvas id="graficaFaltas"></canvas>

            </div>

        </div>

    </div>

</div>

{{-- ================================================= --}}
{{-- DISTRIBUCIÓN DE ESTADOS Y RESUMEN --}}
{{-- ================================================= --}}

<div class="mb-7">

    <div class="mb-4">

        <h3 class="text-xl font-semibold tracking-[-0.02em] text-[#0A1033]">
            Análisis de asistencia
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Distribución y comportamiento de los registros del periodo.
        </p>

    </div>


    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">


        {{-- DISTRIBUCIÓN DE ESTADOS --}}

        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-4">

                <h4 class="text-base font-semibold text-[#0A1033]">
                    Distribución de estados
                </h4>

                <p class="mt-1 text-sm text-slate-400">
                    Proporción de los ocho estados de asistencia.
                </p>

            </div>

            <div class="relative h-72 w-full">

                <canvas id="graficaEstados"></canvas>

            </div>

        </div>


        {{-- RESUMEN VISUAL --}}

        <div class="rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-5">

                <h4 class="text-base font-semibold text-[#0A1033]">
                    Resumen de asistencia
                </h4>

                <p class="mt-1 text-sm text-slate-400">
                    Cantidad y porcentaje de registros por estado.
                </p>

            </div>


            <div class="space-y-5">

                @foreach($datosEstados as $estado => $total)

                @php

                $porcentaje = $resumen->total_registros > 0
                ? ($total / $resumen->total_registros) * 100
                : 0;

                $colorEstado = $coloresBarras[$estado] ?? '#5C8DFF';

                @endphp


                <div>

                    <div class="mb-2 flex items-center justify-between gap-3">

                        <div class="flex items-center gap-2">

                            <span
                                style="background-color: {{ $colorEstado }};"
                                class="h-2.5 w-2.5 shrink-0 rounded-full">
                            </span>

                            <span class="text-sm font-medium text-slate-600">
                                {{ $estado }}
                            </span>

                        </div>


                        <div class="flex items-center gap-3">

                            <span class="text-xs text-slate-400">
                                {{ number_format($porcentaje, 1) }}%
                            </span>

                            <span class="min-w-[45px] text-right text-sm
                                             font-bold tabular-nums text-[#0A1033]">

                                {{ number_format($total) }}

                            </span>

                        </div>

                    </div>


                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">

                        <div
                            style="width: {{ min($porcentaje, 100) }}%;
                                       background-color: {{ $colorEstado }};"
                            class="h-full rounded-full transition-all duration-500">
                        </div>

                    </div>

                </div>

                @endforeach

            </div>


            <div class="mt-6 border-t border-slate-100 pt-4">

                <div class="flex items-center justify-between">

                    <span class="text-sm font-medium text-slate-500">
                        Total de registros
                    </span>

                    <span class="text-xl font-bold tabular-nums text-[#0A1033]">

                        {{ number_format($resumen->total_registros) }}

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- EVOLUCIÓN DIARIA --}}
{{-- ================================================= --}}

<div class="mb-7 rounded-2xl border border-slate-200
            bg-white p-5 shadow-sm sm:p-6">


    <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

        <div>

            <h3 class="text-xl font-semibold tracking-[-0.02em] text-[#0A1033]">
                Evolución diaria de asistencias
            </h3>

            <p class="mt-1 text-sm text-slate-400">
                Comportamiento diario de los ocho estados de asistencia.
            </p>

        </div>


        <div class="rounded-lg bg-[#E9EDFF] px-3 py-2">

            <span class="text-xs font-semibold text-[#2447FF]">
                ANÁLISIS DIARIO
            </span>

        </div>

    </div>


    <div class="relative h-80 w-full">

        <canvas id="graficaEvolucion"></canvas>

    </div>


    <div class="mt-4 flex items-start gap-2 rounded-xl bg-[#F1F5FF] p-3">

        <svg xmlns="http://www.w3.org/2000/svg"
            class="mt-0.5 h-4 w-4 shrink-0 text-[#5C8DFF]"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">

            <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 110-20 10 10 0 010 20z" />

        </svg>

        <p class="text-xs leading-relaxed text-slate-500">
            Puedes pasar el cursor sobre las líneas de la gráfica para
            consultar los valores registrados en cada fecha.
        </p>

    </div>

</div>


{{-- ================================================= --}}
{{-- ESTADOS POR DEPARTAMENTO --}}
{{-- ================================================= --}}

<div class="mb-7 rounded-2xl border border-slate-200
            bg-white p-5 shadow-sm sm:p-6">

    <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

        <div>

            <h3 class="text-xl font-semibold tracking-[-0.02em] text-[#0A1033]">
                Estados de asistencia por departamento
            </h3>

            <p class="mt-1 text-sm text-slate-400">
                Comparación de todos los estados por área.
            </p>

        </div>

        <div class="rounded-lg bg-[#E9EDFF] px-3 py-2">

            <span class="text-xs font-semibold text-[#2447FF]">
                COMPARATIVA
            </span>

        </div>

    </div>


    <div class="relative h-96 w-full">

        <canvas id="graficaDepartamentosEstados"></canvas>

    </div>

</div>


{{-- ================================================= --}}
{{-- CONFIGURACIÓN DE GRÁFICAS --}}
{{-- ================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (typeof Chart === 'undefined') {
            console.error('Chart.js no está cargado.');
            return;
        }

        // CONFIGURACIÓN GLOBAL

        Chart.defaults.font.family = "'IBM Plex Sans', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        Chart.defaults.plugins.tooltip.backgroundColor = '#0A1033';
        Chart.defaults.plugins.tooltip.titleFont = {
            family: "'IBM Plex Sans', sans-serif",
            size: 11,
            weight: '600'
        };

        Chart.defaults.plugins.tooltip.bodyFont = {
            family: "'IBM Plex Sans', sans-serif",
            size: 11
        };

        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;


        // DATOS DE LARAVEL

        const retardos = @json($retardos);
        const faltas = @json($faltas);
        const estados = @json($datosEstados);
        const evolucion = @json($evolucionDiaria);
        const datosDepartamentos = @json($estadosPorDepartamento);


        // COLORES INSTITUCIONALES

        const coloresEstados = [
            '#1E9E5A', // Presente
            '#F2A900', // Retardo
            '#D93025', // Falta
            '#131F6B', // Permiso
            '#2447FF', // Vacaciones
            '#5C8DFF', // Festivo
            '#007F91', // Libre
            '#6B4AA5' // Incapacidad
        ];


        // CONFIGURACIÓN BASE DE BARRAS

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
                        color: '#EDF1F7',
                        drawBorder: false
                    },

                    border: {
                        display: false
                    }
                },

                x: {
                    grid: {
                        display: false
                    },

                    border: {
                        display: false
                    }
                }

            }
        };


        // =================================================
        // RETARDOS POR DEPARTAMENTO
        // =================================================

        new Chart(document.getElementById('graficaRetardos'), {

            type: 'bar',

            data: {

                labels: retardos.map(item => item.departamento),

                datasets: [{

                    label: 'Retardos',

                    data: retardos.map(item => Number(item.total_retardos)),

                    backgroundColor: '#F2A900',

                    hoverBackgroundColor: '#D99A00',

                    borderRadius: 7,

                    maxBarThickness: 45

                }]

            },

            options: opcionesBarras

        });


        // =================================================
        // FALTAS POR DEPARTAMENTO
        // =================================================

        new Chart(document.getElementById('graficaFaltas'), {

            type: 'bar',

            data: {

                labels: faltas.map(item => item.departamento),

                datasets: [{

                    label: 'Faltas',

                    data: faltas.map(item => Number(item.total)),

                    backgroundColor: '#D93025',

                    hoverBackgroundColor: '#B42318',

                    borderRadius: 7,

                    maxBarThickness: 45

                }]

            },

            options: opcionesBarras

        });


        // =================================================
        // DISTRIBUCIÓN DE ESTADOS
        // =================================================

        new Chart(document.getElementById('graficaEstados'), {

            type: 'doughnut',

            data: {

                labels: Object.keys(estados),

                datasets: [{

                    data: Object.values(estados),

                    backgroundColor: coloresEstados,

                    borderWidth: 4,

                    borderColor: '#ffffff',

                    hoverOffset: 8,

                    spacing: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '68%',

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            usePointStyle: true,

                            pointStyle: 'circle',

                            padding: 16,

                            font: {
                                size: 11,
                                weight: '500'
                            }

                        }

                    }

                }

            }

        });


        // =================================================
        // EVOLUCIÓN DIARIA
        // =================================================

        const estadosGrafica = [

            {
                key: 'presente',
                label: 'Presente',
                color: '#1E9E5A'
            },

            {
                key: 'retardo',
                label: 'Retardo',
                color: '#F2A900'
            },

            {
                key: 'falta',
                label: 'Falta',
                color: '#D93025'
            },

            {
                key: 'permiso',
                label: 'Permiso',
                color: '#131F6B'
            },

            {
                key: 'vacaciones',
                label: 'Vacaciones',
                color: '#2447FF'
            },

            {
                key: 'festivo',
                label: 'Festivo',
                color: '#5C8DFF'
            },

            {
                key: 'libre',
                label: 'Libre',
                color: '#007F91'
            },

            {
                key: 'incapacidad',
                label: 'Incapacidad',
                color: '#6B4AA5'
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

                    borderWidth: 2.5,

                    tension: 0.35,

                    pointRadius: 3,

                    pointHoverRadius: 6,

                    pointBackgroundColor: '#ffffff',

                    pointBorderWidth: 2,

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

                            padding: 15,

                            font: {
                                size: 11,
                                weight: '500'
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
                            color: '#EDF1F7',
                            drawBorder: false
                        },

                        border: {
                            display: false
                        }

                    },

                    x: {

                        grid: {
                            display: false
                        },

                        border: {
                            display: false
                        }

                    }

                }

            }

        });


        // =================================================
        // ESTADOS POR DEPARTAMENTO
        // =================================================

        new Chart(document.getElementById('graficaDepartamentosEstados'), {

            type: 'bar',

            data: {

                labels: datosDepartamentos.labels,

                datasets: datosDepartamentos.datasets.map((dataset, index) => ({

                    ...dataset,

                    borderRadius: 3,

                    borderSkipped: false,

                    backgroundColor: coloresEstados[index] ?? '#5C8DFF'

                }))

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

                            padding: 15,

                            font: {
                                size: 11,
                                weight: '500'
                            }

                        }

                    }

                },

                scales: {

                    x: {

                        stacked: true,

                        grid: {
                            display: false
                        },

                        border: {
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
                            color: '#EDF1F7',
                            drawBorder: false
                        },

                        border: {
                            display: false
                        }

                    }

                }

            }

        });

    });
</script>

@endsection