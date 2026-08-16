@extends('layouts.admin')

@section('content')

<div class="max-w-8xl mx-auto">

    <form
        id="editar-empleado-form"
        method="POST"
        action="{{ route('admin.empleados.actualizar', $empleado->id) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ================================================= --}}
            {{-- DATOS EMPLEADO --}}
            {{-- ================================================= --}}
            <div class="bg-white rounded shadow p-6">

                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Editar empleado
                </h2>

                {{-- ERRORES --}}
                @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
                    <ul class="list-disc pl-4 text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- FOTO --}}
                <div class="flex justify-center mb-6">

                    <div class="w-32 h-32 rounded-full overflow-hidden border bg-gray-100 flex items-center justify-center">

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

                </div>

                {{-- NUMERO / NOMBRES --}}
                <div class="flex gap-4">

                    <div class="mb-4 w-1/2">

                        <label class="block text-sm font-semibold text-gray-700">
                            Número empleado
                        </label>

                        <input
                            type="text"
                            maxlength="8"
                            pattern="[0-9]*"
                            inputmode="numeric"
                            name="n_empleado"
                            value="{{ old('n_empleado', $empleado->n_empleado) }}"
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
                            value="{{ old('nombres', $empleado->nombres) }}"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                    </div>

                </div>

                {{-- APELLIDOS --}}
                <div class="flex gap-4">

                    <div class="mb-4 w-1/2">

                        <label class="block text-sm font-semibold text-gray-700">
                            Apellido paterno
                        </label>

                        <input
                            type="text"
                            name="apellido_paterno"
                            value="{{ old('apellido_paterno', $empleado->apellido_paterno) }}"
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
                            value="{{ old('apellido_materno', $empleado->apellido_materno) }}"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                    </div>

                </div>

                {{-- DEPARTAMENTO / PUESTO --}}
                <div class="flex gap-4">

                    <div class="mb-4 w-1/2">

                        <label class="block text-sm font-semibold text-gray-700">
                            Departamento
                        </label>

                        <select
                            name="departamento_id"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                            @foreach($departamentos as $departamento)

                            <option
                                value="{{ $departamento->id }}"
                                {{ $empleado->departamento_id == $departamento->id ? 'selected' : '' }}>

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
                            value="{{ old('puesto', $empleado->puesto) }}"
                            required
                            class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                    </div>

                </div>

                {{-- EMAIL --}}
                <div class="mb-4">

                    <label class="block text-sm font-semibold text-gray-700">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $empleado->email) }}"
                        required
                        class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                </div>

                {{-- FOTO --}}
                <div class="mb-4">

                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Cambiar foto
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

            {{-- ================================================= --}}
            {{-- HORARIOS --}}
            {{-- ================================================= --}}
            <div class="bg-white rounded shadow p-6">

                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Horario semanal
                </h2>

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

                <div class="overflow-auto">

                    <table class="w-full border border-gray-200 text-sm">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="border p-2">Día</th>
                                <th class="border p-2">Entrada</th>
                                <th class="border p-2">Salida</th>
                                <!-- <th class="border p-2">Salida comida</th>
                                <th class="border p-2">Regreso comida</th> -->
                                <th class="border p-2">Tol.</th>
                                <th class="border p-2">Activo</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($dias as $numero => $dia)

                            @php
                            $horario = $empleado->horarios
                            ->where('dia_semana', $numero)
                            ->first();
                            @endphp

                            <tr class="hover:bg-gray-50">

                                <td class="border p-2 font-medium">

                                    {{ $dia }}

                                    <input
                                        type="hidden"
                                        name="horarios[{{ $numero }}][dia_semana]"
                                        value="{{ $numero }}">

                                </td>

                                {{-- ENTRADA --}}
                                <td class="border p-1">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_entrada]"
                                        value="{{ isset($horario) ? \Carbon\Carbon::parse($horario->hora_entrada)->format('H:i') : '' }}"
                                        class="w-full border rounded px-2 py-1">

                                </td>

                                {{-- SALIDA --}}
                                <td class="border p-1">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida]"
                                        value="{{ isset($horario) ? \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') : '' }}"
                                        class="w-full border rounded px-2 py-1">

                                </td>

                                {{-- SALIDA COMIDA --}}
                                <!-- <td class="border p-1">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_salida_comida]"
                                        value="{{ $horario->hora_salida_comida ?? '' }}"
                                        class="w-full border rounded px-2 py-1">

                                </td> -->

                                {{-- REGRESO COMIDA --}}
                                <!-- <td class="border p-1">

                                    <input
                                        type="time"
                                        name="horarios[{{ $numero }}][hora_regreso_comida]"
                                        value="{{ $horario->hora_regreso_comida ?? '' }}"
                                        class="w-full border rounded px-2 py-1">

                                </td> -->

                                {{-- TOLERANCIA --}}
                                <td class="border p-1 text-center">

                                    <input
                                        type="number"
                                        min="0"
                                        value="{{ $horario->tolerancia_minutos ?? 10 }}"
                                        name="horarios[{{ $numero }}][tolerancia_minutos]"
                                        class="w-16 border rounded px-2 py-1">

                                </td>

                                {{-- ACTIVO --}}
                                <td class="border p-2 text-center">

                                    <input
                                        type="checkbox"
                                        value="1"
                                        name="horarios[{{ $numero }}][activo]"
                                        {{ isset($horario) && $horario->activo ? 'checked' : '' }}
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
                Actualizar empleado
            </button>

        </div>

    </form>

</div>

@endsection