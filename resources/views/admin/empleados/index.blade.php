@extends('layouts.admin')

@section('title', 'Directorio de empleados')
@section('page-title', 'Directorio de empleados')

@section('content')

<div
    x-data="{
        buscar: @js(request('buscar', '')),
        editarActivo: false
    }"
    class="space-y-5">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#0A1033]">
                Directorio de empleados
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Administración y consulta del personal de Cumbres
            </p>
        </div>

        <a
            href="{{ route('admin.empleados.crear') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#2447FF] px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#2447FF]/20 transition hover:bg-[#1D39D8]">

            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M12 5v14m-7-7h14" />
            </svg>

            Crear empleado

        </a>

    </div>


    {{-- TARJETAS DE DEPARTAMENTOS --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">

        @foreach($departamentos as $departamento)

        <div class="group rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

            <div class="flex items-center justify-between gap-2">

                <p class="truncate text-xs font-semibold text-slate-500">
                    {{ ucfirst($departamento->nombre) }}
                </p>

                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#DCE8FF] text-[#131F6B]">

                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                    </svg>

                </div>

            </div>

            <h3 class="mt-3 text-2xl font-bold tracking-tight text-[#0A1033]">
                {{ number_format($departamento->empleados_count) }}
            </h3>

            <p class="mt-1 text-[11px] text-slate-400">
                Empleados registrados
            </p>

            <div class="mt-3 h-1 overflow-hidden rounded-full bg-[#DCE8FF]">
                <div class="h-full w-1/2 rounded-full bg-[#5C8DFF]"></div>
            </div>

        </div>

        @endforeach


        {{-- TOTAL DE EMPLEADOS --}}
        <div class="rounded-xl border border-[#2447FF]/15 bg-[#2447FF]/5 p-3 shadow-sm">

            <div class="flex items-center justify-between gap-2">

                <p class="text-xs font-bold text-[#2447FF]">
                    Total empleados
                </p>

                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#2447FF] text-white">

                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m12-13a4 4 0 11-8 0 4 4 0 018 0zm4 13v-2a4 4 0 00-3-3.87" />
                    </svg>

                </div>

            </div>

            <h3 class="mt-3 text-2xl font-bold tracking-tight text-[#2447FF]">
                {{ number_format($totales_empleados) }}
            </h3>

            <p class="mt-1 text-[11px] text-[#2447FF]/70">
                Personal registrado
            </p>

            <div class="mt-3 h-1 overflow-hidden rounded-full bg-[#DCE8FF]">
                <div class="h-full w-full rounded-full bg-[#2447FF]"></div>
            </div>

        </div>
    </div>

    {{-- BÚSQUEDA Y CONTROLES --}}
    <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

            {{-- BUSCADOR --}}
            <form
                id="buscar-empleado-form"
                method="GET"
                action="{{ route('admin.empleados') }}"
                class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-end">

                <div class="min-w-0 flex-1">

                    <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                        Buscar empleado
                    </label>

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">

                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="11" cy="11" r="7" stroke-width="1.8" />
                                <path stroke-linecap="round" stroke-width="1.8"
                                    d="m16 16 4 4" />
                            </svg>

                        </div>

                        <input
                            type="text"
                            name="buscar"
                            x-model="buscar"
                            placeholder="Nombre, número o departamento..."
                            class="w-full rounded-lg border border-slate-200 bg-[#F8FAFF] py-2 pl-9 pr-3 text-xs text-[#030614] outline-none transition placeholder:text-slate-400 focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/10">

                    </div>

                </div>

                <div class="flex shrink-0 gap-2">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#2447FF] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#1D39D8]">

                        Buscar

                    </button>

                    @if(request('buscar'))

                    <a
                        href="{{ route('admin.empleados') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-[#D93025]">

                        Limpiar

                    </a>

                    @endif

                </div>

            </form>


            {{-- MODO DE EDICIÓN --}}
            @if(auth()->user()->level_user)

            <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 lg:border-l lg:border-t-0 lg:pl-5 lg:pt-0">

                <div>
                    <p class="text-xs font-bold text-[#0A1033]">
                        Modo de edición
                    </p>

                    <p class="mt-0.5 text-[11px] text-slate-400">
                        Mostrar acciones administrativas
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2">

                    <label for="toggle"
                        class="relative inline-flex cursor-pointer items-center">

                        <input
                            type="checkbox"
                            id="toggle"
                            class="peer sr-only"
                            x-model="editarActivo">

                        <div class="h-5 w-10 rounded-full bg-slate-300 transition-colors peer-focus:ring-2 peer-focus:ring-[#2447FF]/20 peer-checked:bg-[#1E9E5A]"></div>

                        <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>

                    </label>

                    <span
                        class="min-w-[48px] text-xs font-semibold"
                        :class="editarActivo ? 'text-[#1E9E5A]' : 'text-slate-400'"
                        x-text="editarActivo ? 'Activo' : 'Inactivo'">
                    </span>

                </div>

            </div>

            @endif

        </div>

    </section>


    {{-- TABLA DE EMPLEADOS --}}
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- ENCABEZADO DE TABLA --}}
        <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h3 class="text-sm font-bold text-[#0A1033]">
                    Personal registrado
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    Listado general de empleados de Cumbres
                </p>

            </div>

            <div class="flex items-center gap-2">

                <span class="rounded-lg bg-[#DCE8FF] px-3 py-1.5 text-xs font-semibold text-[#131F6B]">

                    {{ $empleados->total() }} empleados encontrados

                </span>

            </div>

        </div>


        {{-- CONTENEDOR DE TABLA --}}
        <div class="overflow-x-auto">

            <div class="max-h-[550px] overflow-y-auto">

                <table class="min-w-full text-left">

                    {{-- ENCABEZADOS --}}
                    <thead class="sticky top-0 z-10 border-b border-[#131F6B] bg-[#0A1033]">

                        <tr class="text-[10px] font-semibold uppercase tracking-wider text-[#DCE8FF]">

                            <th class="whitespace-nowrap px-4 py-3 text-center">
                                N. Empleado
                            </th>

                            <th class="whitespace-nowrap px-4 py-3">
                                Nombre completo
                            </th>

                            <th class="whitespace-nowrap px-4 py-3">
                                Departamento
                            </th>

                            <th class="whitespace-nowrap px-4 py-3">
                                Puesto
                            </th>

                            <th class="whitespace-nowrap px-4 py-3">
                                Correo electrónico
                            </th>

                            @if(auth()->user()->level_user)

                            <th
                                class="whitespace-nowrap px-4 py-3 text-center"
                                x-show="editarActivo"
                                x-cloak>
                                Acciones
                            </th>

                            @endif

                        </tr>

                    </thead>


                    {{-- REGISTROS --}}
                    <tbody class="divide-y divide-slate-100 text-xs">

                        @forelse($empleados as $empleado)

                        <tr class="transition-colors hover:bg-[#F1F5FF]/70">

                            {{-- NÚMERO DE EMPLEADO --}}
                            <td class="whitespace-nowrap px-4 py-3 text-center">

                                <span class="inline-flex rounded-md bg-[#DCE8FF] px-2.5 py-1 font-bold text-[#131F6B]">

                                    {{ $empleado->n_empleado }}

                                </span>

                            </td>


                            {{-- NOMBRE COMPLETO --}}
                            <td class="whitespace-nowrap px-4 py-3">

                                <div class="font-semibold text-[#0A1033]">

                                    {{ $empleado->nombres }}
                                    {{ $empleado->apellido_paterno }}
                                    {{ $empleado->apellido_materno }}

                                </div>

                            </td>


                            {{-- DEPARTAMENTO --}}
                            <td class="whitespace-nowrap px-4 py-3">

                                @if($empleado->departamento)

                                <span class="inline-flex rounded-md bg-[#F1F5FF] px-2.5 py-1 font-semibold text-[#2447FF]">

                                    {{ $empleado->departamento->nombre }}

                                </span>

                                @else

                                <span class="text-slate-400">
                                    Sin departamento
                                </span>

                                @endif

                            </td>


                            {{-- PUESTO --}}
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">

                                {{ $empleado->puesto ?? 'Sin puesto' }}

                            </td>


                            {{-- CORREO --}}
                            <td class="max-w-[240px] px-4 py-3 text-slate-600">

                                <span
                                    class="block truncate"
                                    title="{{ $empleado->email }}">

                                    {{ $empleado->email ?? 'Sin correo' }}

                                </span>

                            </td>

                            {{-- ACCIONES --}}
                            @if(auth()->user()->level_user)

                            <td
                                class="whitespace-nowrap px-4 py-3"
                                x-show="editarActivo"
                                x-cloak>

                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- PDF VACACIONES --}}
                                    <a
                                        href="{{ route('admin.empleados.vacaciones.pdf', $empleado->id) }}"
                                        target="_blank"
                                        title="Generar PDF de vacaciones"
                                        class="inline-flex items-center justify-center rounded-lg border border-[#1E9E5A]/20 bg-[#1E9E5A]/10 px-2.5 py-1.5 text-[11px] font-bold text-[#1E9E5A] transition hover:bg-[#1E9E5A]/20">

                                        PDF

                                    </a>


                                    {{-- EDITAR --}}
                                    <a
                                        href="{{ route('admin.empleados.editar', $empleado->id) }}"
                                        title="Editar empleado"
                                        class="inline-flex items-center justify-center rounded-lg border border-[#F2A900]/20 bg-[#F2A900]/10 px-2.5 py-1.5 text-[11px] font-bold text-[#8A5A00] transition hover:bg-[#F2A900]/20">

                                        Editar

                                    </a>


                                    {{-- ELIMINAR --}}
                                    <form
                                        action="{{ route('admin.empleados.destroy', $empleado->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este empleado?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Eliminar empleado"
                                            class="inline-flex items-center justify-center rounded-lg border border-[#D93025]/20 bg-[#D93025]/10 px-2.5 py-1.5 text-[11px] font-bold text-[#D93025] transition hover:bg-[#D93025]/20">

                                            Eliminar

                                        </button>

                                    </form>

                                </div>

                            </td>

                            @endif

                        </tr>

                        @empty

                        {{-- SIN RESULTADOS --}}
                        <tr>

                            <td
                                colspan="{{ auth()->user()->level_user ? 6 : 5 }}"
                                class="px-4 py-14 text-center">

                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F1F5FF] text-[#5C8DFF]">

                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="11" cy="11" r="7" stroke-width="1.6" />
                                        <path stroke-linecap="round" stroke-width="1.6"
                                            d="m16 16 4 4M8 11h6" />
                                    </svg>

                                </div>

                                <p class="text-sm font-bold text-[#0A1033]">
                                    No se encontraron empleados
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Intenta cambiar el término de búsqueda.
                                </p>

                                @if(request('buscar'))

                                <a
                                    href="{{ route('admin.empleados') }}"
                                    class="mt-3 inline-flex text-xs font-semibold text-[#2447FF] hover:underline">

                                    Limpiar búsqueda

                                </a>

                                @endif

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINACIÓN --}}
        <div class="border-t border-slate-100 bg-[#F8FAFF] px-4 py-3">

            {{ $empleados->appends(request()->query())->links() }}

        </div>

    </section>

</div>

@endsection