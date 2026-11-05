@extends('layouts.admin')

@section('content')

@if ($errors->any())
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="mb-5 bg-red-100 border border-red-400 text-red-700 p-4 rounded">

    <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ================================= --}}
    {{-- REGISTRAR AUSENCIA --}}
    {{-- ================================= --}}

    <div class="bg-white p-6 rounded-xl shadow h-fit">

        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar ausencia
            </h1>

            <p class="text-sm text-gray-500">
                Asigna vacaciones, permisos o incapacidades a un empleado.
            </p>
        </div>

        <form id="guardar-ausencia"
            action="{{ route('admin.ausencias.store') }}"
            method="POST">

            @csrf

            {{-- TIPO DE AUSENCIA --}}
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de ausencia
                </label>

                <select
                    name="tipo"
                    required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    <option value="">Seleccionar tipo</option>

                    <option value="vacaciones">Vacaciones</option>
                    <option value="permiso">Permiso</option>
                    <option value="incapacidad">Incapacidad</option>

                </select>

            </div>

            {{-- EMPLEADO --}}
            <div
                x-data="buscadorEmpleado({{ Js::from($empleados) }})"
                class="mb-4 relative">

                <label class="text-sm font-medium text-gray-700">
                    Asignar empleado
                </label>

                <input
                    type="text"
                    x-model="buscarEmpleado"
                    placeholder="Buscar empleado..."
                    class="w-full border px-3 py-2 mt-1 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400">

                {{-- RESULTADOS --}}
                <template x-if="buscarEmpleado.length >= 2">

                    <div class="absolute z-50 w-full max-h-52 overflow-y-auto border bg-white border-gray-300 rounded-lg shadow">

                        <template
                            x-for="emp in empleadosFiltrados.slice(0,8)"
                            :key="emp.id">

                            <div
                                @click="seleccionarEmpleado(emp)"
                                class="p-3 hover:bg-gray-100 cursor-pointer border-b">

                                <div class="font-medium text-gray-800">

                                    <span x-text="emp.nombres"></span>
                                    <span x-text="emp.apellido_paterno"></span>
                                    <span x-text="emp.apellido_materno"></span>

                                </div>

                                <div class="text-xs text-gray-500">
                                    No. empleado:
                                    <span x-text="emp.n_empleado"></span>
                                </div>

                            </div>

                        </template>

                    </div>

                </template>

                {{-- EMPLEADO SELECCIONADO --}}
                <div
                    x-show="empleadoSeleccionado"
                    class="mt-3 text-sm text-green-600">

                    Empleado seleccionado:

                    <span
                        class="font-semibold"
                        x-text="empleadoCompleto"></span>

                </div>

                {{-- INPUT HIDDEN --}}
                <input
                    type="hidden"
                    name="empleado_id"
                    :value="empleadoSeleccionado?.id">

            </div>

            {{-- FECHAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha inicio
                    </label>

                    <input
                        type="date"
                        name="fecha_inicio"
                        value="{{ old('fecha_inicio') }}"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha fin
                    </label>

                    <input
                        type="date"
                        name="fecha_fin"
                        value="{{ old('fecha_fin') }}"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

            </div>

            {{-- MOTIVO --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones / Motivo
                </label>

                <textarea
                    name="motivo"
                    rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Opcional...">{{ old('motivo') }}</textarea>

            </div>

            {{-- BOTÓN --}}
            <div class="flex justify-end">

                <button
                    type="submit"
                    class="bg-blue-600 cursor-pointer hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    Guardar ausencia

                </button>

            </div>

        </form>

    </div>

    {{-- ================================= --}}
    {{-- LISTADO DE AUSENCIAS --}}
    {{-- ================================= --}}

    <div class="bg-white p-6 rounded-xl shadow">

        <div class="flex items-center justify-between mb-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Ausencias registradas
                </h2>

                <p class="text-sm text-gray-500">
                    Vacaciones, permisos e incapacidades.
                </p>
            </div>

            <span class="text-sm text-gray-500">
                Total: {{ $ausencias->count() }}
            </span>

        </div>

        <div class="max-h-[650px] overflow-y-auto overflow-x-auto border border-gray-200 rounded-xl">

            <table class="min-w-full divide-y divide-gray-200">

                {{-- HEADER --}}
                <thead class="sticky top-0 z-10 bg-gray-50">

                    <tr>

                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Empleado
                        </th>

                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                            Tipo
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Inicio
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Fin
                        </th>

                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                            Días
                        </th>

                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                            Estado
                        </th>

                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                            Acciones
                        </th>

                    </tr>

                </thead>

                {{-- BODY --}}
                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse($ausencias as $ausencia)

                    @php

                    $inicio = \Carbon\Carbon::parse($ausencia->fecha_inicio);
                    $fin = \Carbon\Carbon::parse($ausencia->fecha_fin);
                    $hoy = now()->startOfDay();

                    $dias = $inicio->diffInDays($fin) + 1;

                    if ($fin->lt($hoy)) {
                    $estado = 'Finalizada';
                    $color = 'bg-red-100 text-red-700';
                    } elseif ($inicio->lte($hoy) && $fin->gte($hoy)) {
                    $estado = 'En curso';
                    $color = 'bg-yellow-100 text-yellow-700';
                    } else {
                    $estado = 'Próxima';
                    $color = 'bg-green-100 text-green-700';
                    }

                    $tipos = [
                    'vacaciones' => 'Vacaciones',
                    'permiso' => 'Permiso',
                    'incapacidad' => 'Incapacidad'
                    ];

                    $colores = [
                    'vacaciones' => 'bg-blue-100 text-blue-700',
                    'permiso' => 'bg-purple-100 text-purple-700',
                    'incapacidad' => 'bg-orange-100 text-orange-700'
                    ];

                    @endphp

                    <tr class="hover:bg-gray-50 transition">

                        {{-- EMPLEADO --}}
                        <td class="px-4 py-4">

                            <div class="text-xs font-medium text-gray-800">

                                {{ $ausencia->empleado->nombres }}
                                {{ $ausencia->empleado->apellido_paterno }}
                                {{ $ausencia->empleado->apellido_materno }}

                            </div>

                            <div class="text-xs text-gray-500">
                                No. empleado:
                                {{ $ausencia->empleado->n_empleado }}
                            </div>

                        </td>

                        {{-- TIPO --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap">

                            <span class="{{ $colores[$ausencia->tipo] ?? 'bg-gray-100 text-gray-700' }} px-2 py-1 rounded-full text-xs font-semibold">

                                {{ $tipos[$ausencia->tipo] ?? $ausencia->tipo }}

                            </span>

                        </td>

                        {{-- INICIO --}}
                        <td class="px-4 py-4 text-xs text-gray-600 whitespace-nowrap">
                            {{ $inicio->format('d/m/Y') }}
                        </td>

                        {{-- FIN --}}
                        <td class="px-4 py-4 text-xs text-gray-600 whitespace-nowrap">
                            {{ $fin->format('d/m/Y') }}
                        </td>

                        {{-- DÍAS --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap">

                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $dias }} días
                            </span>

                        </td>

                        {{-- ESTADO --}}
                        <td class="px-4 py-4 text-center">

                            <span class="{{ $color }} px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                {{ $estado }}
                            </span>

                        </td>

                        {{-- ACCIONES --}}
                        <td class="px-4 py-4">

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
                                    class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-xs font-medium transition">

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
                                        class="bg-red-100 hover:bg-red-200 cursor-pointer text-red-700 px-3 py-1 rounded-lg text-xs font-medium transition">

                                        Eliminar

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                            No hay ausencias registradas.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

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
        }
    }"
    @editar-ausencia.window="abrir($event.detail)">

    <div
        x-show="abierto"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            @click.outside="abierto = false"
            class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">

            {{-- ENCABEZADO --}}
            <div class="flex justify-between items-center mb-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Editar ausencia
                </h2>

                <button
                    type="button"
                    @click="abierto = false"
                    class="cursor-pointer text-gray-500 hover:text-gray-700 text-xl">

                    &times;

                </button>

            </div>

            <form
                :action="url"
                method="POST">

                @csrf
                @method('PUT')

                {{-- TIPO DE AUSENCIA --}}
                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de ausencia
                    </label>

                    <select
                        name="tipo"
                        x-model="tipo"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha inicio
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            x-model="fecha_inicio"
                            required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha fin
                        </label>

                        <input
                            type="date"
                            name="fecha_fin"
                            x-model="fecha_fin"
                            required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    </div>

                </div>

                {{-- MOTIVO --}}
                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones / Motivo
                    </label>

                    <textarea
                        name="motivo"
                        rows="4"
                        x-model="motivo"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>

                </div>

                {{-- BOTONES --}}
                <div class="flex justify-end gap-3">

                    <button
                        type="button"
                        @click="abierto = false"
                        class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection