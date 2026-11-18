@extends('layouts.admin')

@section('title', 'Asistencias')
@section('page-title', 'Control de asistencias')

@section('content')

@php
$hayFiltros = collect(request()->only([
'buscar',
'fecha_inicio',
'fecha_fin',
'departamento',
'estado',
'hora_entrada',
'hora_salida'
]))->filter(fn($value) => $value !== null && $value !== '')->isNotEmpty();

$estadisticas = [
[
'titulo' => 'Asistencias',
'valor' => $asistenciaE ?? 0,
'color' => 'text-[#1E9E5A]',
'fondo' => 'bg-[#1E9E5A]/10',
'borde' => 'border-l-[#1E9E5A]',
],
[
'titulo' => 'Retardos',
'valor' => $retardosHoy ?? 0,
'color' => 'text-[#B77900]',
'fondo' => 'bg-[#F2A900]/10',
'borde' => 'border-l-[#F2A900]',
],
[
'titulo' => 'Faltas',
'valor' => $faltasHoy ?? 0,
'color' => 'text-[#D93025]',
'fondo' => 'bg-[#D93025]/10',
'borde' => 'border-l-[#D93025]',
],
[
'titulo' => 'Vacaciones',
'valor' => $vacacionesHoy ?? 0,
'color' => 'text-[#2447FF]',
'fondo' => 'bg-[#2447FF]/10',
'borde' => 'border-l-[#2447FF]',
],
[
'titulo' => 'Permisos',
'valor' => $permisosHoy ?? 0,
'color' => 'text-[#5C8DFF]',
'fondo' => 'bg-[#5C8DFF]/15',
'borde' => 'border-l-[#5C8DFF]',
],
[
'titulo' => 'Incapacidades',
'valor' => $incapacidadHoy ?? 0,
'color' => 'text-[#131F6B]',
'fondo' => 'bg-[#DCE8FF]',
'borde' => 'border-l-[#131F6B]',
],
[
'titulo' => 'Libres',
'valor' => $libresHoy ?? 0,
'color' => 'text-[#0A1033]',
'fondo' => 'bg-[#DCE8FF]/70',
'borde' => 'border-l-[#5C8DFF]',
],
];

$estados = [
'presente' => [
'texto' => 'Presente',
'clase' => 'bg-[#1E9E5A]/10 text-[#167A46]'
],
'retardo' => [
'texto' => 'Retardo',
'clase' => 'bg-[#F2A900]/15 text-[#936000]'
],
'falta' => [
'texto' => 'Falta',
'clase' => 'bg-[#D93025]/10 text-[#D93025]'
],
'vacaciones' => [
'texto' => 'Vacaciones',
'clase' => 'bg-[#2447FF]/10 text-[#2447FF]'
],
'permiso' => [
'texto' => 'Permiso',
'clase' => 'bg-[#5C8DFF]/15 text-[#2447FF]'
],
'incapacidad' => [
'texto' => 'Incapacidad',
'clase' => 'bg-[#131F6B]/10 text-[#131F6B]'
],
'libre' => [
'texto' => 'Libre',
'clase' => 'bg-[#DCE8FF] text-[#131F6B]'
],
'festivo' => [
'texto' => 'Festivo',
'clase' => 'bg-slate-100 text-slate-700'
],
'fin_semana' => [
'texto' => 'Fin de semana',
'clase' => 'bg-slate-100 text-slate-700'
],
];
@endphp

<div class="space-y-3">

    {{-- RESUMEN --}}
    <section>
        <div class="mb-2">
            <h2 class="text-lg font-bold text-[#0A1033]">
                Resumen de asistencia
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Indicadores generales del día.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-7">

            @foreach($estadisticas as $estadistica)

            <div class="rounded-xl border border-slate-200 border-l-4 {{ $estadistica['borde'] }} bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                <p class="text-xs font-semibold text-slate-500">
                    {{ $estadistica['titulo'] }}
                </p>

                <div class="mt-1 flex items-center justify-between gap-2">

                    <p class="text-2xl font-bold {{ $estadistica['color'] }}">
                        {{ $estadistica['valor'] }}
                    </p>

                    <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $estadistica['fondo'] }}">

                        <span class="text-lg font-bold {{ $estadistica['color'] }}">
                            {{ $estadistica['valor'] }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </section>


    {{-- FILTROS --}}
    <section
        x-data="{
        buscar: @js(request('buscar', '')),
        fecha_inicio: @js(request('fecha_inicio', '')),
        fecha_fin: @js(request('fecha_fin', '')),
        departamento: @js(request('departamento', '')),
        hora_entrada: @js(request('hora_entrada', '')),
        estado: @js(request('estado', '')),
        hora_salida: @js(request('hora_salida', ''))}"
        class="rounded-xl border border-slate-200 bg-white p-4 mb-2 shadow-sm">

        {{-- ENCABEZADO --}}
        <div class="mb-2 flex items-center gap-2.5">

            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2447FF]/10 text-[#2447FF]">

                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M3 4h18M6 8h12m-9 4h6m-4 4h2m-1 0v4" />
                </svg>

            </div>

            <div>
                <h3 class="text-sm font-bold text-[#0A1033]">
                    Filtros de búsqueda
                </h3>

                <p class="text-[11px] text-slate-500">
                    Filtra los registros de asistencia.
                </p>
            </div>

        </div>

        {{-- FORMULARIO --}}
        <form
            id="filtrosForm"
            method="GET"
            action="{{ route('admin.asistencias') }}"
            class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7">

            {{-- BUSCAR --}}
            <div class="col-span-2 sm:col-span-2 xl:col-span-1">

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Empleado
                </label>

                <input
                    type="text"
                    name="buscar"
                    x-model="buscar"
                    placeholder="Nombre o apellido..."
                    class="w-full rounded-lg border border-slate-200 bg-[#F8FAFF] px-3 py-2 text-xs text-[#030614] outline-none transition placeholder:text-slate-400 focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

            </div>

            {{-- FECHA INICIO --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Fecha inicio
                </label>

                <input
                    type="date"
                    name="fecha_inicio"
                    x-model="fecha_inicio"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

            </div>

            {{-- FECHA FIN --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Fecha fin
                </label>

                <input
                    type="date"
                    name="fecha_fin"
                    x-model="fecha_fin"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

            </div>

            {{-- DEPARTAMENTO --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Departamento
                </label>

                <select
                    name="departamento"
                    x-model="departamento"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

                    <option value="">Todos</option>

                    @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">
                        {{ $departamento->nombre }}
                    </option>
                    @endforeach

                </select>

            </div>

            {{-- ESTADO --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Estado
                </label>

                <select
                    name="estado"
                    x-model="estado"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

                    <option value="">Todos</option>
                    <option value="presente">Presente</option>
                    <option value="retardo">Retardo</option>
                    <option value="falta">Falta</option>
                    <option value="vacaciones">Vacaciones</option>
                    <option value="permiso">Permiso</option>
                    <option value="incapacidad">Incapacidad</option>
                    <option value="libre">Libre</option>
                    <option value="festivo">Festivo</option>

                </select>

            </div>

            {{-- HORA ENTRADA --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Entrada
                </label>

                <select
                    name="hora_entrada"
                    x-model="hora_entrada"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

                    <option value="">Todos</option>
                    <option value="1">Con hora</option>
                    <option value="0">Sin hora</option>

                </select>

            </div>

            {{-- HORA SALIDA --}}
            <div>

                <label class="mb-1 block text-[11px] font-semibold text-[#0A1033]">
                    Salida
                </label>

                <select
                    name="hora_salida"
                    x-model="hora_salida"
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-[#F8FAFF] px-2 py-2 text-xs outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

                    <option value="">Todos</option>
                    <option value="1">Con hora</option>
                    <option value="0">Sin hora</option>

                </select>

            </div>

            {{-- BOTONES --}}
            <div class="col-span-2 flex flex-wrap justify-end gap-2 sm:col-span-3 lg:col-span-4 xl:col-span-7 pt-1">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#2447FF] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#1D39D8]">

                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.5-4.5m2-5.5a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                    </svg>

                    Filtrar

                </button>

                @if(request()->query())

                <a
                    href="{{ route('admin.asistencias') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-[#D93025]">

                    Limpiar filtros

                </a>

                @endif

            </div>

        </form>

    </section>
</div>

{{-- TABLA Y EXPORTACIONES --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- ENCABEZADO TABLA --}}
    <div class="flex flex-col gap-4 border-b border-slate-100 p-2 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h3 class="font-bold text-[#0A1033]">
                Registro de asistencias
            </h3>

            <p class="mt-1 text-xs text-slate-500">
                Consulta las entradas, salidas y estados de los empleados.
            </p>
        </div>

        {{-- EXPORTACIONES --}}
        <div class="flex flex-col gap-2 sm:flex-row">

            {{-- PDF --}}
            <form method="GET"
                action="{{ route('admin.asistencias.reporte.pdf') }}"
                target="_blank">

                @foreach(request()->only([
                'buscar',
                'fecha_inicio',
                'fecha_fin',
                'departamento',
                'retardo',
                'hora_entrada',
                'hora_salida',
                'estado'
                ]) as $key => $value)

                <input type="hidden" name="{{ $key }}" value="{{ $value }}">

                @endforeach

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-[#0A1033] transition hover:border-[#2447FF] hover:bg-[#F1F5FF] sm:w-auto">

                    <svg class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v3h14v-3" />

                    </svg>

                    Crear reporte PDF

                </button>

            </form>

            {{-- EXCEL --}}
            <form method="GET"
                action="{{ route('admin.asistencias.reporte.excel') }}">

                @foreach(request()->only([
                'buscar',
                'fecha_inicio',
                'fecha_fin',
                'departamento',
                'retardo',
                'hora_entrada',
                'hora_salida',
                'estado'
                ]) as $key => $value)

                <input type="hidden" name="{{ $key }}" value="{{ $value }}">

                @endforeach

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#1E9E5A] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#167A46] sm:w-auto">

                    <svg class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 3v4m8-4v4M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zm4 10l2 2 4-4" />

                    </svg>

                    Exportar Excel

                </button>

            </form>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="max-h-[600px] overflow-auto">

        <table class="min-w-[850px] w-full border-collapse text-sm">

            {{-- CABECERA --}}
            <thead class="sticky top-0 z-10 bg-[#0A1033] text-white">

                <tr class="text-xs uppercase tracking-wide">

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        N. Empleado
                    </th>

                    <th class="whitespace-nowrap px-4 py-4 text-left font-semibold">
                        Nombre
                    </th>

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        Departamento
                    </th>

                    @if($hayFiltros)

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        Fecha
                    </th>

                    @endif

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        Hora de entrada
                    </th>

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        Hora de salida
                    </th>

                    <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">
                        Estado
                    </th>

                </tr>

            </thead>

            {{-- CUERPO --}}
            <tbody class="divide-y divide-slate-100">

                @forelse($asistencias as $asistencia)

                @php

                $empleado = $asistencia->empleado;

                // Obtener checadas correspondientes al día
                $checadas = $asistencia->checadas->filter(function ($checada) use ($asistencia) {

                return \Carbon\Carbon::parse($checada->fecha_hora)->toDateString()
                === \Carbon\Carbon::parse($asistencia->fecha)->toDateString();

                });

                // Entrada y salida
                $entrada = $checadas
                ->where('tipo', 'entrada')
                ->first();

                $salida = $checadas
                ->where('tipo', 'salida')
                ->last();

                // Fecha de asistencia
                $fecha = \Carbon\Carbon::parse($asistencia->fecha);

                // Identificar fin de semana
                $esFinSemana = in_array($fecha->dayOfWeekIso, [6, 7]);

                // Texto de entrada
                $entradaTexto = $asistencia->estado === 'libre' && !$entrada
                ? ($esFinSemana ? 'Fin de semana' : 'Libre')
                : ($entrada
                ? \Carbon\Carbon::parse($entrada->fecha_hora)->format('H:i')
                : 'Sin registro');

                // Texto de salida
                $salidaTexto = $asistencia->estado === 'libre' && !$salida
                ? ($esFinSemana ? 'Fin de semana' : 'Libre')
                : ($salida
                ? \Carbon\Carbon::parse($salida->fecha_hora)->format('H:i')
                : 'Sin registro');

                // Estado visual
                $estado = $estados[$asistencia->estado] ?? [
                'texto' => 'Sin registro',
                'clase' => 'bg-slate-100 text-slate-600'
                ];

                @endphp

                <tr class="transition hover:bg-[#F1F5FF]/70">

                    {{-- NÚMERO EMPLEADO --}}
                    <td class="whitespace-nowrap px-4 py-4 text-center font-semibold text-[#131F6B]">

                        {{ $empleado->n_empleado ?? 'N/A' }}

                    </td>

                    {{-- NOMBRE --}}
                    <td class="px-4 py-4 font-semibold text-[#0A1033]">

                        {{ $empleado
                                    ? $empleado->nombres . ' ' .
                                      $empleado->apellido_paterno . ' ' .
                                      $empleado->apellido_materno
                                    : 'N/A'
                                }}

                    </td>

                    {{-- DEPARTAMENTO --}}
                    <td class="px-4 py-4 text-center text-slate-600">

                        {{ $empleado->departamento->nombre ?? 'N/A' }}

                    </td>

                    {{-- FECHA --}}
                    @if($hayFiltros)

                    <td class="whitespace-nowrap px-4 py-4 text-center text-slate-600">

                        {{ $fecha->format('d/m/Y') }}

                    </td>

                    @endif

                    {{-- ENTRADA --}}
                    <td class="whitespace-nowrap px-4 py-4 text-center">

                        <span class="{{ $entrada ? 'font-semibold text-[#0A1033]' : 'text-slate-400' }}">

                            {{ $entradaTexto }}

                        </span>

                    </td>

                    {{-- SALIDA --}}
                    <td class="whitespace-nowrap px-4 py-4 text-center">

                        <span class="{{ $salida ? 'font-semibold text-[#0A1033]' : 'text-slate-400' }}">

                            {{ $salidaTexto }}

                        </span>

                    </td>

                    {{-- ESTADO --}}
                    <td class="whitespace-nowrap px-4 py-4 text-center">

                        <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-bold {{ $estado['clase'] }}">

                            {{ $estado['texto'] }}

                        </span>

                    </td>

                </tr>

                @empty

                {{-- SIN REGISTROS --}}
                <tr>

                    <td
                        colspan="{{ $hayFiltros ? 7 : 6 }}"
                        class="px-4 py-16 text-center">

                        <div class="flex flex-col items-center gap-3">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F1F5FF] text-[#5C8DFF]">

                                <svg class="h-7 w-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.6"
                                        d="M8 7h8m-8 4h8m-8 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />

                                </svg>

                            </div>

                            <div>

                                <p class="font-semibold text-[#0A1033]">
                                    No se encontraron registros
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    No hay asistencias que coincidan con la búsqueda.
                                </p>

                            </div>

                        </div>

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINACIÓN --}}
    <div class="border-t border-slate-100 p-4">

        {{ $asistencias->links() }}

    </div>

</section>

</div>

@endsection