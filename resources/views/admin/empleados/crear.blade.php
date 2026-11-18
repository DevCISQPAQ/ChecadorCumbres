@extends('layouts.admin')

@section('content')

<div class="max-w-7xl mx-auto">

    <form
        id="crear-empleado-form"
        method="POST"
        action="{{ route('admin.empleados.guardar') }}"
        enctype="multipart/form-data">

        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- DATOS DEL EMPLEADO --}}
            <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">

                <div class="mb-5">
                    <h2 class="text-xl font-bold text-gray-800">
                        Crear nuevo empleado
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Información personal y laboral del empleado.
                    </p>
                </div>

                {{-- NÚMERO Y NOMBRES --}}
                <div class="grid grid-cols-2 gap-3 mb-3">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Número de empleado
                        </label>

                        <input
                            type="text"
                            maxlength="8"
                            pattern="[0-9]*"
                            inputmode="numeric"
                            name="n_empleado"
                            value="{{ old('n_empleado') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Nombres
                        </label>

                        <input
                            type="text"
                            name="nombres"
                            value="{{ old('nombres') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                </div>

                {{-- APELLIDOS --}}
                <div class="grid grid-cols-2 gap-3 mb-3">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Apellido paterno
                        </label>

                        <input
                            type="text"
                            name="apellido_paterno"
                            value="{{ old('apellido_paterno') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Apellido materno
                        </label>

                        <input
                            type="text"
                            name="apellido_materno"
                            value="{{ old('apellido_materno') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                </div>

                {{-- DEPARTAMENTO Y PUESTO --}}
                <div class="grid grid-cols-2 gap-3 mb-3">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Departamento
                        </label>

                        <select
                            name="departamento_id"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                            <option value="" disabled selected>
                                Seleccionar
                            </option>

                            @foreach($departamentos as $departamento)
                            <option
                                value="{{ $departamento->id }}"
                                {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                {{ $departamento->nombre }}
                            </option>
                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Puesto
                        </label>

                        <input
                            type="text"
                            name="puesto"
                            value="{{ old('puesto') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>

                </div>

                {{-- CORREO --}}
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                </div>

                {{-- FOTO --}}
                <div class="mb-2">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Fotografía del empleado
                    </label>

                    <input
                        type="file"
                        name="foto"
                        accept="image/*"
                        class="block w-full text-xs text-gray-500 border border-gray-300 rounded-lg
                        file:mr-3 file:py-2 file:px-3
                        file:border-0 file:rounded-l-lg
                        file:bg-blue-50 file:text-blue-700
                        file:font-semibold hover:file:bg-blue-100">

                </div>

            </div>

            {{-- HORARIOS --}}
            <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">

                <div class="mb-5">
                    <h3 class="text-xl font-bold text-gray-800">
                        Horario semanal
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Configura los horarios de entrada y salida.
                    </p>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl">

                    <table class="w-full min-w-[500px] text-sm">

                        <thead class="bg-gray-50">

                            <tr class="text-gray-600">

                                <th class="px-2 py-3 text-left">
                                    Día
                                </th>

                                <th class="px-2 py-3  text-center">
                                    Entrada
                                </th>

                                <th class="px-2 py-3 text-center">
                                    Salida
                                </th>

                                <th class="px-2 py-3 text-center">
                                    Tol. (min)
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @php
                            $dias = [
                            1 => 'Lunes',
                            2 => 'Martes',
                            3 => 'Miércoles',
                            4 => 'Jueves',
                            5 => 'Viernes',
                            6 => 'Sábado',
                            7 => 'Domingo'
                            ];
                            @endphp

                            @foreach($dias as $numero => $dia)

                            <tr class="hover:bg-blue-50/50 transition">

                                <td class="px-2 py-2 font-medium text-gray-700 whitespace-nowrap">

                                    {{ $dia }}

                                    <input
                                        type="hidden"
                                        name="horarios[{{ $numero }}][dia_semana]"
                                        value="{{ $numero }}">

                                </td>

                                <td class="px-1 py-2">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_entrada]"
                                        value="{{ old('horarios.'.$numero.'.hora_entrada') }}"
                                        class="w-full min-w-0 px-1 py-2 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                                </td>

                                <td class="px-1 py-2">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida]"
                                        value="{{ old('horarios.'.$numero.'.hora_salida') }}"
                                        class="w-full min-w-0 px-1 py-2 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                                </td>

                                <td class="px-1 py-2 text-center">

                                    <input
                                        type="number"
                                        value="{{ old('horarios.'.$numero.'.tolerancia_minutos', 6) }}"
                                        min="0"
                                        name="horarios[{{ $numero }}][tolerancia_minutos]"
                                        class="w-14 px-1 py-2 text-xs text-center border border-gray-300 rounded focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <p class="text-[11px] text-gray-400 mt-3">
                    La tolerancia se configura en minutos para cada día.
                </p>

            </div>

        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-2 mt-4 bg-white border border-gray-200 rounded-xl p-4 shadow-sm">

            <a
                href="{{ route('admin.empleados') }}"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">

                Cancelar

            </a>

            <button
                type="submit"
                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">

                Guardar empleado

            </button>

        </div>

    </form>

</div>

@endsection