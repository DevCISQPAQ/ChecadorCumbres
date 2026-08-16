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

    <!-- ===================================================== -->
    <!-- VACACIONES -->
    <!-- ===================================================== -->

    <div class="bg-white p-6 rounded-xl shadow">

        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar vacaciones
            </h1>

            <p class="text-sm text-gray-500">
                Asigna un periodo de vacaciones a un empleado.
            </p>
        </div>

        <form id="guardar-vacaciones" action="{{ route('admin.vacaciones.store') }}" method="POST">
            @csrf

            <!-- EMPLEADO -->
            <div
                x-data="buscadorEmpleado({{ Js::from($empleados) }})"
                class="mb-4 relative">

                <label class="text-sm font-medium text-gray-700">
                    Asignar empleado
                </label>

                <!-- INPUT -->
                <input
                    type="text"
                    x-model="buscarEmpleado"
                    placeholder="Buscar empleado..."
                    class="w-full border px-3 py-1 mt-1 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400">

                <!-- RESULTADOS -->
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

                <!-- EMPLEADO SELECCIONADO -->
                <div
                    x-show="empleadoSeleccionado"
                    class="mt-3 text-sm text-green-600">

                    Empleado seleccionado:

                    <span
                        class="font-semibold"
                        x-text="empleadoCompleto"></span>

                </div>

                <!-- INPUT HIDDEN -->
                <input
                    type="hidden"
                    name="empleado_id"
                    :value="empleadoSeleccionado?.id">

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">

                <!-- FECHA INICIO -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha inicio
                    </label>

                    <input
                        type="date"
                        name="fecha_inicio"
                        value="{{ old('fecha_inicio') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <!-- FECHA FIN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha fin
                    </label>

                    <input
                        type="date"
                        name="fecha_fin"
                        value="{{ old('fecha_fin') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>
            </div>
            <!-- MOTIVO -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones / Motivo
                </label>

                <textarea
                    name="motivo"
                    rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Opcional...">{{ old('motivo') }}</textarea>
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3">
                <button
                    type="submit"
                    class="bg-blue-600 cursor-pointer hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Guardar vacaciones
                </button>
            </div>

        </form>

        <!-- TABLA VACACIONES -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Vacaciones registradas
                </h2>
                <span class="text-sm text-gray-500">
                    Total: {{ $vacaciones->count() }}
                </span>
                <div class="flex gap-2">
                    <a
                        href="{{ route('admin.vacaciones.excel') }}"
                        target="_blank"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        Descargar Excel
                    </a>
                </div>

            </div>
            <div class="max-h-[300px] overflow-y-auto overflow-x-auto border border-gray-200 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- HEADER -->
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Empleado
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
                            <!-- <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Motivo
                            </th> -->
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <!-- BODY -->
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($vacaciones as $vacacion)
                        @php
                        $inicio = \Carbon\Carbon::parse($vacacion->fecha_inicio);
                        $fin = \Carbon\Carbon::parse($vacacion->fecha_fin);
                        $hoy = now();
                        $dias = $inicio->diffInDays($fin) + 1;
                        if($fin->lt($hoy)){
                        $estado = 'Finalizadas';
                        $color = 'bg-red-100 text-red-700';
                        } elseif($inicio->lte($hoy) && $fin->gte($hoy)){
                        $estado = 'En curso';
                        $color = 'bg-yellow-100 text-yellow-700';
                        } else {
                        $estado = 'Próximas';
                        $color = 'bg-green-100 text-green-700';
                        }
                        @endphp

                        <tr class="hover:bg-gray-50 transition">
                            <!-- EMPLEADO -->
                            <td class="px-4 py-4">
                                <div class="text-xs font-medium text-gray-800">
                                    {{ $vacacion->empleado->nombres }}
                                    {{ $vacacion->empleado->apellido_paterno }}
                                    {{ $vacacion->empleado->apellido_materno }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    No. empleado:
                                    {{ $vacacion->empleado->n_empleado }}
                                </div>
                            </td>
                            <!-- INICIO -->
                            <td class="px-4 py-4 text-xs text-gray-600">
                                {{ $inicio->format('d/m/Y') }}
                            </td>
                            <!-- FIN -->
                            <td class="px-4 py-4 text-xs text-gray-600">
                                {{ $fin->format('d/m/Y') }}
                            </td>
                            <!-- DIAS -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $dias }} días
                                </span>
                            </td>
                            <!-- MOTIVO -->
                            <!-- <td class="px-4 py-4 text-sm text-gray-600 max-w-xs">
                                {{ $vacacion->motivo ?: '-' }}
                            </td> -->
                            <!-- ESTADO -->
                            <td class="px-4 py-4 text-center">
                                <span class="{{ $color }} px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $estado }}
                                </span>

                            </td>
                            <!-- ACCIONES -->
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- EDITAR -->
                                    <button
                                        type="button"
                                        @click="$dispatch('editar-vacacion', {{ Js::from([
                                        'id' => $vacacion->id,
                                         'url' => route('admin.vacaciones.update', $vacacion->id),
                                         'fecha_inicio' => \Carbon\Carbon::parse($vacacion->fecha_inicio)->format('Y-m-d'),
                                         'fecha_fin' => \Carbon\Carbon::parse($vacacion->fecha_fin)->format('Y-m-d'),
                                         'motivo' => $vacacion->motivo ?? ''
                                           ]) }})"
                                        class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                        Editar
                                    </button>

                                    <!-- ELIMINAR -->
                                    <form
                                        action="{{ route('admin.vacaciones.destroyVacaciones', $vacacion->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar vacaciones?')">

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
                                No hay vacaciones registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================== -->
    <!-- DÍAS FESTIVOS -->
    <!-- ===================================================== -->

    <div class="bg-white p-6 rounded-xl shadow h-fit">

        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar día festivo
            </h1>

            <p class="text-sm text-gray-500">
                Agrega días oficiales o no laborables.
            </p>
        </div>

        <form id="guardar-festivo" action="{{ route('admin.diasfestivos.store') }}" method="POST">
            @csrf

            <!-- NOMBRE -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del día festivo
                </label>

                <input
                    type="text"
                    name="nombre"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Ejemplo: Navidad"
                    required>
            </div>

            <!-- FECHA -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    required>
            </div>

            <!-- OFICIAL -->
            <div class="mb-6 flex items-center gap-3">

                <input
                    type="checkbox"
                    name="oficial"
                    value="1"
                    checked
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">

                <label class="text-sm text-gray-700">
                    Día festivo oficial
                </label>

            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3">

                <button
                    type="submit"
                    class="bg-green-600 cursor-pointer hover:bg-green-700 text-white px-5 py-2 rounded-lg">
                    Guardar día festivo
                </button>

            </div>

        </form>

        <!-- TABLA DÍAS FESTIVOS -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Días festivos registrados
                </h2>
                <span class="text-sm text-gray-500">
                    Total: {{ $diasFestivos->count() }}
                </span>
            </div>
            <div class="max-h-[300px] overflow-y-auto overflow-x-auto border border-gray-200 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- HEADER -->
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Nombre
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Fecha
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody class="bg-white divide-y divide-gray-100">

                        @forelse($diasFestivos as $dia)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- NOMBRE -->
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-800 text-sm">
                                    {{ $dia->nombre }}
                                </div>
                            </td>
                            <!-- FECHA -->
                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}
                            </td>
                            <!-- TIPO -->
                            <td class="px-4 py-4 text-center">
                                @if($dia->oficial)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Oficial
                                </span>
                                @else
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    No oficial
                                </span>
                                @endif
                            </td>

                            <!-- ACCIONES -->
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- EDITAR -->
                                    <button
                                        type="button"
                                        @click="$dispatch('editar-festivo', {{ Js::from([
                                        'id' => $dia->id,
                                        'url' => route('admin.festivos.update', $dia->id),
                                        'nombre' => $dia->nombre,
                                        'fecha' => \Carbon\Carbon::parse($dia->fecha)->format('Y-m-d'),
                                         'oficial' => (bool) $dia->oficial
                                          ]) }})"
                                        class="cursor-pointer bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                        Editar
                                    </button>

                                    <!-- ELIMINAR -->
                                    <form
                                        action="{{ route('admin.festivos.destroyFestivos', $dia->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar día festivo?')">
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
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                No hay días festivos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===================================================== --}}
{{-- MODAL EDITAR VACACIONES --}}
{{-- ===================================================== --}}

<div x-data="{ abierto: false, id: null, url: '', fecha_inicio: '', fecha_fin: '', motivo: '',
    abrir(datos) {
        this.id = datos.id;
        this.url = datos.url;
        this.fecha_inicio = datos.fecha_inicio;
        this.fecha_fin = datos.fecha_fin;
        this.motivo = datos.motivo || '';

        this.abierto = true;
    }
     }"
    @editar-vacacion.window="abrir($event.detail)">

    <div
        x-show="abierto"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            @click.outside="abierto = false"
            class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">

            <div class="flex justify-between items-center mb-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Editar vacaciones
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
                            class="w-full border-gray-300 rounded-lg shadow-sm">

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
                            class="w-full border-gray-300 rounded-lg shadow-sm">

                    </div>

                </div>

                <div class="mb-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones / Motivo
                    </label>

                    <textarea
                        name="motivo"
                        rows="4"
                        x-model="motivo"
                        class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>

                </div>

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

{{-- ===================================================== --}}
{{-- MODAL EDITAR DÍA FESTIVO --}}
{{-- ===================================================== --}}

<div x-data="{
    abierto: false,
    id: null,
    url: '',
    nombre: '',
    fecha: '',
    oficial: false,

    abrir(datos) {
        this.id = datos.id;
        this.url = datos.url;
        this.nombre = datos.nombre;
        this.fecha = datos.fecha;
        this.oficial = datos.oficial;
        this.abierto = true;
    }}"
    @editar-festivo.window="abrir($event.detail)">

    <div
        x-show="abierto"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            @click.outside="abierto = false"
            x-transition
            class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">

            <div class="flex justify-between items-center mb-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Editar día festivo
                </h2>

                <button
                    type="button"
                    @click="abierto = false"
                    class="text-gray-500 hover:text-gray-700 text-xl">

                    &times;

                </button>

            </div>

            <form
                :action="url"
                method="POST">


                @csrf
                @method('PUT')

                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del día festivo
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        x-model="nombre"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm">

                </div>

                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        x-model="fecha"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm">

                </div>

                <div class="mb-6 flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="oficial"
                        value="1"
                        x-model="oficial"
                        class="rounded border-gray-300 text-green-600">

                    <label class="text-sm text-gray-700">
                        Día festivo oficial
                    </label>

                </div>

                <div class="flex justify-end gap-3">

                    <button
                        type="button"
                        @click="abierto = false"
                        class="bg-gray-100 cursor-pointer hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="bg-green-600 cursor-pointer hover:bg-green-700 text-white px-5 py-2 rounded-lg">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection