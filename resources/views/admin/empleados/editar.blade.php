@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto">

    <form
        id="editar-empleado-form"
        method="POST"
        action="{{ route('admin.empleados.actualizar', $empleado->id) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

            {{-- ========================================= --}}
            {{-- DATOS DEL EMPLEADO --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-xl shadow p-4">

                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Editar empleado
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Modifica la información personal y laboral del empleado.
                    </p>
                </div>

                {{-- ERRORES --}}
                @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">

                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
                @endif

                {{-- FOTO ACTUAL --}}
                <div class="flex flex-col items-center mb-7">

                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-100 bg-gray-50 flex items-center justify-center shadow-sm">

                        @if($empleado->foto)

                        <img
                            src="{{ asset('img/empleados/' . $empleado->foto) }}"
                            alt="Foto empleado"
                            class="w-full h-full object-cover">

                        @else

                        <span class="text-gray-400 text-sm">
                            Sin foto
                        </span>

                        @endif

                    </div>

                    <p class="text-sm font-semibold text-gray-700 mt-3">
                        Fotografía del empleado
                    </p>

                    <p class="text-xs text-gray-400">
                        Puedes actualizarla más adelante.
                    </p>

                </div>

                {{-- NÚMERO / NOMBRES --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Número de empleado
                        </label>

                        <input
                            type="text"
                            maxlength="8"
                            pattern="[0-9]*"
                            inputmode="numeric"
                            name="n_empleado"
                            value="{{ old('n_empleado', $empleado->n_empleado) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                    </div>

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombres
                        </label>

                        <input
                            type="text"
                            name="nombres"
                            value="{{ old('nombres', $empleado->nombres) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                    </div>

                </div>

                {{-- APELLIDOS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Apellido paterno
                        </label>

                        <input
                            type="text"
                            name="apellido_paterno"
                            value="{{ old('apellido_paterno', $empleado->apellido_paterno) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                    </div>

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Apellido materno
                        </label>

                        <input
                            type="text"
                            name="apellido_materno"
                            value="{{ old('apellido_materno', $empleado->apellido_materno) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                    </div>

                </div>

                {{-- DEPARTAMENTO / PUESTO --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Departamento
                        </label>

                        <select
                            name="departamento_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                            @foreach($departamentos as $departamento)

                            <option
                                value="{{ $departamento->id }}"
                                {{ old('departamento_id', $empleado->departamento_id) == $departamento->id ? 'selected' : '' }}>

                                {{ $departamento->nombre }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Puesto
                        </label>

                        <input
                            type="text"
                            name="puesto"
                            value="{{ old('puesto', $empleado->puesto) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                    </div>

                </div>

                {{-- CORREO --}}
                <div class="mb-4">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $empleado->email) }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                </div>

                {{-- CAMBIAR FOTO --}}
                <div class="mt-5 border-t border-gray-100 pt-5">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cambiar fotografía
                    </label>

                    <input
                        type="file"
                        name="foto"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        cursor-pointer">

                    <p class="text-xs text-gray-400 mt-2">
                        Selecciona una imagen nueva si deseas actualizar la fotografía actual.
                    </p>

                </div>

            </div>

            {{-- ========================================= --}}
            {{-- HORARIO SEMANAL --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-xl shadow p-4">

                <div class="mb-6">

                    <h2 class="text-xl font-bold text-gray-800">
                        Horario semanal
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Configura las horas de entrada, salida y tolerancia.
                    </p>

                </div>

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

                <div class="overflow-x-auto border border-gray-200 rounded-xl">

                    <table class="w-full min-w-[500px] text-sm">

                        {{-- ENCABEZADO --}}
                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Día
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Entrada
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Salida
                                </th>

                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Tol.
                                </th>

                            </tr>

                        </thead>

                        {{-- CONTENIDO --}}
                        <tbody class="divide-y divide-gray-100">

                            @foreach($dias as $numero => $dia)

                            @php
                            $horario = $empleado->horarios
                            ->where('dia_semana', $numero)
                            ->first();
                            @endphp

                            <tr class="hover:bg-gray-50 transition">

                                {{-- DÍA --}}
                                <td class="px-3 py-3">

                                    <span class="font-medium text-gray-700">
                                        {{ $dia }}
                                    </span>

                                    <input
                                        type="hidden"
                                        name="horarios[{{ $numero }}][dia_semana]"
                                        value="{{ $numero }}">

                                </td>

                                {{-- ENTRADA --}}
                                <td class="px-2 py-2">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_entrada]"
                                        value="{{ old('horarios.'.$numero.'.hora_entrada', isset($horario) ? \Carbon\Carbon::parse($horario->hora_entrada)->format('H:i') : '') }}"
                                        class="w-full min-w-24 px-2 py-2 border border-gray-300 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                                </td>

                                {{-- SALIDA --}}
                                <td class="px-2 py-2">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida]"
                                        value="{{ old('horarios.'.$numero.'.hora_salida', isset($horario) ? \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') : '') }}"
                                        class="w-full min-w-24 px-2 py-2 border border-gray-300 rounded-lg text-gray-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                                </td>

                                {{-- TOLERANCIA --}}
                                <td class="px-2 py-2">

                                    <input
                                        type="number"
                                        min="0"
                                        value="{{ old('horarios.'.$numero.'.tolerancia_minutos', $horario->tolerancia_minutos ?? 6) }}"
                                        name="horarios[{{ $numero }}][tolerancia_minutos]"
                                        class="w-16 mx-auto block px-2 py-2 border border-gray-300 rounded-lg text-center text-gray-700 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-4 bg-blue-50 border border-blue-100 rounded-lg p-3">

                    <p class="text-xs text-blue-700">
                        <strong>Nota:</strong> La tolerancia se expresa en minutos. Los horarios pueden modificarse individualmente para cada día.
                    </p>

                </div>

            </div>

        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-3 mt-6 bg-white p-5 rounded-xl shadow">

            <a
                href="{{ route('admin.empleados') }}"
                class="px-5 py-2.5 rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium transition">

                Cancelar

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-sm transition cursor-pointer">

                Actualizar empleado

            </button>

        </div>

    </form>

</div>

@endsection