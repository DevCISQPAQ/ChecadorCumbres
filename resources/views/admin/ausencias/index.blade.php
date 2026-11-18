@extends('layouts.admin')

@section('content')

{{-- ALERTAS DE VALIDACIÓN --}}
@if ($errors->any())
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 5000)"
    x-show="show"
    x-transition
    class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">

    <div class="min-w-0">
        <p class="text-sm font-bold">No se pudo completar la operación</p>
        <ul class="mt-1 list-disc pl-5 text-xs">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    <button type="button" @click="show = false"
        class="ml-auto text-red-500 hover:text-red-700">
        ✕
    </button>
</div>
@endif

{{-- ENCABEZADO --}}
<div class="mb-6">
    <h2 class="text-xl font-bold tracking-tight text-[#0A1033]">
        Gestión de ausencias
    </h2>
    <p class="mt-1 text-sm text-slate-500">
        Administración de vacaciones, permisos e incapacidades del personal.
    </p>
</div>

<div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

    {{-- REGISTRAR AUSENCIA --}}
    <div class="h-fit overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#DCE8FF] text-[#2447FF]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold text-[#0A1033]">
                        Registrar ausencia
                    </h3>
                    <p class="text-xs text-slate-500">
                        Completa los datos del registro.
                    </p>
                </div>
            </div>
        </div>

        <form id="guardar-ausencia"
            action="{{ route('admin.ausencias.store') }}"
            method="POST"
            class="space-y-4 p-5">

            @csrf

            {{-- TIPO --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                    Tipo de ausencia
                </label>

                <select name="tipo" required
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-[#0A1033] outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">

                    <option value="">Seleccionar tipo</option>
                    <option value="vacaciones" {{ old('tipo') == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                    <option value="permiso" {{ old('tipo') == 'permiso' ? 'selected' : '' }}>Permiso</option>
                    <option value="incapacidad" {{ old('tipo') == 'incapacidad' ? 'selected' : '' }}>Incapacidad</option>
                </select>
            </div>

            {{-- BUSCADOR DE EMPLEADO --}}
            <div
                x-data="buscadorEmpleado({{ Js::from($empleados) }})"
                class="relative">

                <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                    Asignar empleado
                </label>

                <input
                    type="text"
                    x-model="buscarEmpleado"
                    placeholder="Buscar por nombre o número..."
                    autocomplete="off"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-[#0A1033] outline-none transition placeholder:text-slate-400 focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">

                {{-- RESULTADOS --}}
                <template x-if="buscarEmpleado.length >= 2">
                    <div class="absolute z-50 mt-1 max-h-52 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl">

                        <template x-for="emp in empleadosFiltrados.slice(0,8)" :key="emp.id">
                            <button
                                type="button"
                                @click="seleccionarEmpleado(emp)"
                                class="flex w-full flex-col border-b border-slate-100 px-4 py-3 text-left transition last:border-0 hover:bg-[#F1F5FF]">

                                <span class="text-sm font-semibold text-[#0A1033]">
                                    <span x-text="emp.nombres"></span>
                                    <span x-text="emp.apellido_paterno"></span>
                                    <span x-text="emp.apellido_materno"></span>
                                </span>

                                <span class="mt-1 text-xs text-slate-500">
                                    No. empleado:
                                    <span x-text="emp.n_empleado"></span>
                                </span>
                            </button>
                        </template>

                        <template x-if="empleadosFiltrados.length === 0">
                            <p class="p-4 text-center text-xs text-slate-500">
                                No se encontraron empleados.
                            </p>
                        </template>
                    </div>
                </template>

                {{-- EMPLEADO SELECCIONADO --}}
                <div x-show="empleadoSeleccionado"
                    x-cloak
                    class="mt-3 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-3">

                    <span class="text-sm text-green-700">✓</span>

                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-green-700">
                            Empleado seleccionado
                        </p>
                        <p class="truncate text-sm font-semibold text-green-800"
                            x-text="empleadoCompleto"></p>
                    </div>
                </div>

                <input type="hidden"
                    name="empleado_id"
                    :value="empleadoSeleccionado?.id">
            </div>

            {{-- FECHAS --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                        Fecha de inicio
                    </label>
                    <input type="date"
                        name="fecha_inicio"
                        value="{{ old('fecha_inicio') }}"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                        Fecha de finalización
                    </label>
                    <input type="date"
                        name="fecha_fin"
                        value="{{ old('fecha_fin') }}"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">
                </div>

            </div>

            {{-- MOTIVO --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                    Observaciones / Motivo
                    <span class="font-normal text-slate-400">(Opcional)</span>
                </label>

                <textarea
                    name="motivo"
                    rows="3"
                    placeholder="Escribe alguna observación..."
                    class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">{{ old('motivo') }}</textarea>
            </div>

            {{-- BOTÓN --}}
            <div class="flex justify-end border-t border-slate-100 pt-4">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#2447FF] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#2447FF]/20 transition hover:bg-[#131F6B] focus:outline-none focus:ring-2 focus:ring-[#2447FF]/30">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>

                    Guardar ausencia
                </button>
            </div>

        </form>
    </div>

    {{-- LISTADO DE AUSENCIAS --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#DCE8FF] text-[#2447FF]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-bold text-[#0A1033]">
                        Ausencias registradas
                    </h3>
                    <p class="text-xs text-slate-500">
                        Vacaciones, permisos e incapacidades.
                    </p>
                </div>
            </div>

            <span class="inline-flex w-fit items-center rounded-lg bg-[#F1F5FF] px-3 py-1.5 text-xs font-semibold text-[#131F6B]">
                Total: {{ $ausencias->count() }} registros
            </span>
        </div>

        {{-- TABLA --}}
        <div class="max-h-[650px] overflow-x-auto overflow-y-auto">

            <table class="min-w-full">

                <thead class="sticky top-0 z-10 bg-[#0A1033]">
                    <tr class="text-[10px] font-semibold uppercase tracking-wider text-[#DCE8FF]">

                        <th class="px-4 py-3 text-left whitespace-nowrap">
                            Empleado
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Tipo
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Inicio
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Fin
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Duración
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Estado
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Acciones
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($ausencias as $ausencia)

                    @php
                    $inicio = \Carbon\Carbon::parse($ausencia->fecha_inicio);
                    $fin = \Carbon\Carbon::parse($ausencia->fecha_fin);
                    $hoy = now()->startOfDay();

                    $dias = $inicio->diffInDays($fin) + 1;

                    if ($fin->lt($hoy)) {
                    $estado = 'Finalizada';
                    $color = 'bg-slate-100 text-slate-700';
                    } elseif ($inicio->lte($hoy) && $fin->gte($hoy)) {
                    $estado = 'En curso';
                    $color = 'bg-[#FFF4D6] text-[#956500]';
                    } else {
                    $estado = 'Próxima';
                    $color = 'bg-green-50 text-[#1E9E5A]';
                    }

                    $tipos = [
                    'vacaciones' => 'Vacaciones',
                    'permiso' => 'Permiso',
                    'incapacidad' => 'Incapacidad'
                    ];

                    $colores = [
                    'vacaciones' => 'bg-[#DCE8FF] text-[#131F6B]',
                    'permiso' => 'bg-[#EDE9FE] text-[#6D28D9]',
                    'incapacidad' => 'bg-[#FFE4E6] text-[#BE123C]'
                    ];
                    @endphp

                    <tr class="transition-colors hover:bg-[#F1F5FF]/70">

                        {{-- EMPLEADO --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="text-xs font-semibold text-[#0A1033]">
                                {{ $ausencia->empleado->nombres ?? '' }}
                                {{ $ausencia->empleado->apellido_paterno ?? '' }}
                                {{ $ausencia->empleado->apellido_materno ?? '' }}
                            </p>

                            <p class="mt-1 text-[10px] text-slate-500">
                                No. empleado:
                                {{ $ausencia->empleado->n_empleado ?? 'N/A' }}
                            </p>
                        </td>

                        {{-- TIPO --}}
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="{{ $colores[$ausencia->tipo] ?? 'bg-slate-100 text-slate-700' }} inline-flex rounded-md px-2.5 py-1 text-[10px] font-bold">
                                {{ $tipos[$ausencia->tipo] ?? $ausencia->tipo }}
                            </span>
                        </td>

                        {{-- INICIO --}}
                        <td class="px-4 py-3 text-center text-xs text-slate-600 whitespace-nowrap">
                            {{ $inicio->format('d/m/Y') }}
                        </td>

                        {{-- FIN --}}
                        <td class="px-4 py-3 text-center text-xs text-slate-600 whitespace-nowrap">
                            {{ $fin->format('d/m/Y') }}
                        </td>

                        {{-- DÍAS --}}
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="font-semibold text-[#131F6B] text-xs">
                                {{ $dias }} días
                            </span>
                        </td>

                        {{-- ESTADO --}}
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="{{ $color }} inline-flex rounded-md px-2.5 py-1 text-[10px] font-bold">
                                {{ $estado }}
                            </span>
                        </td>

                        {{-- ACCIONES --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">

                                {{-- EDITAR --}}
                                <button
                                    type="button"
                                    @click="$dispatch('editar-ausencia', {{ Js::from([
                                        'id' => $ausencia->id,
                                        'url' => route('admin.ausencias.update', $ausencia->id),
                                        'tipo' => $ausencia->tipo,
                                        'fecha_inicio' => $inicio->format('Y-m-d'),
                                        'fecha_fin' => $fin->format('Y-m-d'),
                                        'motivo' => $ausencia->motivo ?? ''
                                    ]) }})"
                                    title="Editar ausencia"
                                    class="rounded-lg bg-[#DCE8FF] px-3 py-1.5 text-[10px] font-bold text-[#131F6B] transition hover:bg-[#5C8DFF] hover:text-white">
                                    Editar
                                </button>

                                {{-- ELIMINAR --}}
                                <form
                                    action="{{ route('admin.ausencias.destroy', $ausencia->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar esta ausencia?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        title="Eliminar ausencia"
                                        class="rounded-lg bg-red-50 px-3 py-1.5 text-[10px] font-bold text-[#D93025] transition hover:bg-red-100">
                                        Eliminar
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#F1F5FF] text-[#5C8DFF]">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="1.7"
                                        d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                </svg>
                            </div>

                            <p class="text-sm font-semibold text-[#0A1033]">
                                No hay ausencias registradas
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Los registros aparecerán aquí cuando agregues una ausencia.
                            </p>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- PIE DE TABLA --}}
        <div class="border-t border-slate-100 bg-[#F1F5FF]/60 px-5 py-3">
            <p class="text-[11px] text-slate-500">
                Mostrando {{ $ausencias->count() }} registros de ausencias.
            </p>
        </div>

    </div>

</div>

{{-- ================================================ --}}
{{-- MODAL EDITAR AUSENCIA --}}
{{-- ================================================ --}}

<div
    x-data="{
        abierto: false,
        id: null,
        url: '',
        tipo: '',
        fecha_inicio: '',
        fecha_fin: '',
        motivo: '',

        abrir(datos) {
            this.id = datos.id;
            this.url = datos.url;
            this.tipo = datos.tipo;
            this.fecha_inicio = datos.fecha_inicio;
            this.fecha_fin = datos.fecha_fin;
            this.motivo = datos.motivo || '';
            this.abierto = true;
        },

        cerrar() {
            this.abierto = false;
        }
    }"
    @editar-ausencia.window="abrir($event.detail)"
    @keydown.escape.window="cerrar()">

    {{-- FONDO DEL MODAL --}}
    <div
        x-show="abierto"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[100] flex items-center justify-center bg-[#030614]/60 px-4 py-6 backdrop-blur-sm">

        {{-- CONTENEDOR --}}
        <div
            x-show="abierto"
            x-transition
            @click.outside="cerrar()"
            class="w-full max-w-lg overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

            {{-- ENCABEZADO --}}
            <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#DCE8FF] text-[#2447FF]">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M16.862 4.487l1.651-1.651a2.121 2.121 0 013 3l-9.193 9.193a4 4 0 01-1.697 1.02l-3.123.89.89-3.123a4 4 0 011.02-1.697l7.452-7.632zM19 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6" />

                        </svg>

                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-[#0A1033]">
                            Editar ausencia
                        </h2>

                        <p class="text-xs text-slate-500">
                            Modifica la información del registro.
                        </p>
                    </div>

                </div>

                <button
                    type="button"
                    @click="cerrar()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-[#0A1033]">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

            {{-- FORMULARIO --}}
            <form
                :action="url"
                method="POST"
                class="space-y-5 p-6">

                @csrf
                @method('PUT')

                {{-- TIPO --}}
                <div>

                    <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                        Tipo de ausencia
                    </label>

                    <select
                        name="tipo"
                        x-model="tipo"
                        required
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-[#0A1033] outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">

                        <option value="vacaciones">
                            Vacaciones
                        </option>

                        <option value="permiso">
                            Permiso
                        </option>

                        <option value="incapacidad">
                            Incapacidad
                        </option>

                    </select>

                </div>

                {{-- FECHAS --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                            Fecha de inicio
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            x-model="fecha_inicio"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">

                    </div>

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                            Fecha de finalización
                        </label>

                        <input
                            type="date"
                            name="fecha_fin"
                            x-model="fecha_fin"
                            required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15">

                    </div>

                </div>

                {{-- MOTIVO --}}
                <div>

                    <label class="mb-1.5 block text-xs font-semibold text-[#0A1033]">
                        Observaciones / Motivo
                    </label>

                    <textarea
                        name="motivo"
                        rows="4"
                        x-model="motivo"
                        placeholder="Escribe alguna observación..."
                        class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-[#2447FF] focus:ring-2 focus:ring-[#2447FF]/15"></textarea>

                </div>

                {{-- BOTONES --}}
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">

                    <button
                        type="button"
                        @click="cerrar()"
                        class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-[#2447FF] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#131F6B] focus:outline-none focus:ring-2 focus:ring-[#2447FF]/30">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection