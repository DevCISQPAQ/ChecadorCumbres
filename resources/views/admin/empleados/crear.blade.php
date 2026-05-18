@extends('layouts.admin')

@section('content')

<div class="max-w-8xl mx-auto">

    <form
        id="crear-empleado-form"
        method="POST"
        action="{{ route('admin.empleados.guardar') }}"
        enctype="multipart/form-data">

        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ========================================= --}}
            {{-- DATOS EMPLEADO --}}
            {{-- ========================================= --}}
            <div class="bg-white p-6 rounded shadow">

                <h2 class="text-2xl font-semibold mb-6 text-gray-800">
                    Crear nuevo empleado
                </h2>

                @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
                    <ul class="list-disc pl-4 text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Número / nombres --}}
                <div class="flex gap-4">
                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Número de empleado
                        </label>

                        <input
                            type="text"
                            maxlength="8"
                            pattern="[0-9]*"
                            inputmode="numeric"
                            name="n_empleado"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Nombres
                        </label>

                        <input
                            type="text"
                            name="nombres"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>
                </div>

                {{-- Apellidos --}}
                <div class="flex gap-4">
                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Apellido paterno
                        </label>

                        <input
                            type="text"
                            name="apellido_paterno"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Apellido materno
                        </label>

                        <input
                            type="text"
                            name="apellido_materno"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>
                </div>

                {{-- Departamento / puesto --}}
                <div class="flex gap-4">

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Departamento
                        </label>

                        <select
                            name="departamento_id"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                            <option value="" disabled selected>
                                Seleccione una opción
                            </option>

                            @foreach($departamentos as $departamento)
                            <option value="{{ $departamento->id }}">
                                {{ $departamento->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Puesto
                        </label>

                        <input
                            type="text"
                            name="puesto"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>
                </div>

                {{-- Horario / email --}}
                <div class="flex gap-4">

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Tipo de horario
                        </label>

                        <select
                            name="tipo_horario"
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                            <option value="" disabled selected>
                                Seleccione una opción
                            </option>

                            <option value="Horario Base">
                                Horario Base
                            </option>

                            <option value="Horario Libre">
                                Horario Libre
                            </option>
                        </select>
                    </div>

                    <div class="mb-4 w-1/2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    </div>
                </div>

                {{-- Foto --}}
                <div class="mb-6">
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-1">

                        Subir foto
                    </label>

                    <input
                        type="file"
                        name="foto"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-100 file:text-blue-700
                        hover:file:bg-blue-200
                        cursor-pointer">
                </div>

            </div>

            {{-- ========================================= --}}
            {{-- HORARIOS --}}
            {{-- ========================================= --}}
            <div class="bg-white p-6 rounded shadow">

                <h3 class="text-2xl font-semibold mb-6 text-gray-800">
                    Horario semanal
                </h3>

                <div class="overflow-auto">

                    <table class="w-full border border-gray-200 text-sm">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="p-2 border">Día</th>
                                <th class="p-2 border">Entrada</th>
                                <th class="p-2 border">Salida</th>
                                <th class="p-2 border">Comida</th>
                                <th class="p-2 border">Regreso</th>
                                <th class="p-2 border">Tol.</th>
                                <th class="p-2 border">Activo</th>
                            </tr>

                        </thead>

                        <tbody>

                            @php
                            $dias = [
                                1 => 'Lun',
                                2 => 'Mar',
                                3 => 'Mié',
                                4 => 'Jue',
                                5 => 'Vie',
                                6 => 'Sáb',
                                7 => 'Dom'
                            ];
                            @endphp

                            @foreach($dias as $numero => $dia)

                            <tr class="hover:bg-gray-50">

                                <td class="border p-2 font-medium">
                                    {{ $dia }}

                                    <input
                                        type="hidden"
                                        name="horarios[{{ $numero }}][dia_semana]"
                                        value="{{ $numero }}">
                                </td>

                                <td class="border p-1">
                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_entrada]"
                                        class="w-full border rounded px-1 py-1">
                                </td>

                                <td class="border p-1">
                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida]"
                                        class="w-full border rounded px-1 py-1">
                                </td>

                                <td class="border p-1">
                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida_comida]"
                                        class="w-full border rounded px-1 py-1">
                                </td>

                                <td class="border p-1">
                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_regreso_comida]"
                                        class="w-full border rounded px-1 py-1">
                                </td>

                                <td class="border p-1">
                                    <input
                                        type="number"
                                        value="10"
                                        min="0"
                                        name="horarios[{{ $numero }}][tolerancia_minutos]"
                                        class="w-16 border rounded px-1 py-1">
                                </td>

                                <td class="border p-2 text-center">
                                    <input
                                        type="checkbox"
                                        checked
                                        value="1"
                                        name="horarios[{{ $numero }}][activo]"
                                        class="w-4 h-4">
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end mt-6">

            <a
                href="{{ route('admin.empleados') }}"
                class="px-4 py-2 text-gray-600 hover:underline">

                Cancelar
            </a>

            <button
                type="submit"
                class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">

                Guardar empleado
            </button>

        </div>

    </form>

</div>

@endsection