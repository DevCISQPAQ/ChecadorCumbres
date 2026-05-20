@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Registrar vacaciones
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Asigna un periodo de vacaciones a un empleado.
        </p>
    </div>

    @if ($errors->any())
    <div class="mb-5 bg-red-100 border border-red-400 text-red-700 p-4 rounded">
        <ul class="list-disc pl-5 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.vacaciones.store') }}" method="POST">
        @csrf

        <!-- EMPLEADO -->
        <!-- BUSCADOR EMPLEADO -->
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
        <!-- FECHA INICIO -->
        <div class="mb-5">
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
        <div class="mb-5">
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

        <!-- MOTIVO -->
        <div class="mb-6">
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
            <a
                href="{{ route('admin.preferencias') }}"
                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Cancelar
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                Guardar vacaciones
            </button>
        </div>
    </form>

</div>


@endsection