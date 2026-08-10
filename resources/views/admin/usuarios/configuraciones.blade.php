@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-semibold mb-6 text-gray-800">
        Configuración
    </h2>

    <!-- {{-- MENSAJES --}}
    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
        <ul class="list-disc pl-4 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 p-3 rounded">
        {{ session('success') }}
    </div>
    @endif -->


    {{-- ========================================================= --}}
    {{-- CONFIGURACIONES --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        {{-- ===================================================== --}}
        {{-- AGREGAR DEPARTAMENTO --}}
        {{-- ===================================================== --}}

        <div class="border border-gray-200 rounded-lg p-5">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Agregar departamento
            </h3>

            <form method="POST"
                action="{{ route('admin.departamentos.store') }}">

                @csrf

                <div class="mb-4">

                    <label class="block text-sm font-semibold text-gray-700">
                        Nombre del departamento
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        required
                        class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                </div>

                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="bg-blue-600 cursor-pointer hover:bg-blue-700 text-white px-4 py-2 rounded">

                        Guardar departamento

                    </button>

                </div>

            </form>

        </div>


        {{-- ===================================================== --}}
        {{-- AJUSTE DE HORARIO --}}
        {{-- ===================================================== --}}

        <div class="border border-gray-200 rounded-lg p-5">

            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Ajuste de horario
            </h3>

            <p class="text-sm text-gray-500 mb-4">
                Este ajuste solamente afecta a los empleados cuyo
                horario normal de entrada sea a las 07:30.
            </p>

            <form method="POST"
                action="{{ route('admin.configuracion.horario0730') }}">

                @csrf

                {{-- SWITCH --}}
                <div class="flex items-center justify-between mb-5">

                    <div>
                        <label
                            for="ajuste_horario_0730"
                            class="font-medium text-gray-700">

                            Activar ajuste 07:30

                        </label>

                        <p class="text-xs text-gray-500">
                            Cambia temporalmente la hora de entrada.
                        </p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">

                        <input
                            type="checkbox"
                            name="ajuste_horario_0730"
                            value="1"
                            id="ajuste_horario_0730"
                            class="sr-only peer"
                            {{ ($ajusteActivo ?? '0') == '1' ? 'checked' : '' }}>

                        <div class="w-11 h-6 bg-gray-300 rounded-full peer
                                peer-checked:bg-blue-600
                                after:content-['']
                                after:absolute
                                after:top-[2px]
                                after:left-[2px]
                                after:bg-white
                                after:border-gray-300
                                after:border
                                after:rounded-full
                                after:h-5
                                after:w-5
                                after:transition-all
                                peer-checked:after:translate-x-full
                                peer-checked:after:border-white">
                        </div>

                    </label>

                </div>


                {{-- HORA --}}
                <div class="mb-4">

                    <label class="block text-sm font-semibold text-gray-700">

                        Nueva hora de entrada

                    </label>

                    <div class="flex items-center gap-3 mt-1">

                        <span class="text-sm text-gray-500">
                            07:30 →
                        </span>

                        <input
                            type="time"
                            name="hora_entrada_ajustada_0730"
                            value="{{ $horaAjustada ?? '08:00' }}"
                            required
                            class="flex-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">

                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Los empleados con horarios diferentes a 07:30
                        conservarán su horario normal.
                    </p>

                </div>


                {{-- BOTÓN --}}
                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="bg-green-600 cursor-pointer hover:bg-green-700 text-white px-4 py-2 rounded">

                        Guardar horario

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DEPARTAMENTOS --}}
    {{-- ========================================================= --}}

    <div>

        <h3 class="text-xl font-semibold mb-4 text-gray-800">
            Departamentos registrados
        </h3>

        <div class="max-h-[300px] overflow-y-auto overflow-x-auto">

            <table class="w-full border border-gray-200 text-sm">

                <thead class="sticky top-0 z-10 bg-gray-100 text-gray-700">
                    <tr>
                        <th class="text-left p-3">
                            ID
                        </th>
                        <th class="text-left p-3">
                            Nombre
                        </th>
                        <th class="text-left p-3">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($departamentos as $departamento)

                    <tr class="border-t">

                        <td class="p-3">
                            {{ $departamento->id }}
                        </td>

                        <td class="p-3">
                            {{ $departamento->nombre }}
                        </td>

                        <td class="p-3">

                            <div class="flex gap-2">

                                {{-- EDITAR --}}
                                <a href="#"
                                    class="bg-yellow-100 hover:bg-yellow-200
                                          text-yellow-800 px-3 py-1 rounded text-xs">

                                    Editar

                                </a>


                                {{-- ELIMINAR --}}
                                <form
                                    action="{{ route('admin.departamentos.destroy', $departamento->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar departamento?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-100 hover:bg-red-200
                                               text-red-700 px-3 py-1 rounded text-xs">

                                        Eliminar

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="3"
                            class="p-4 text-center text-gray-500">

                            No hay departamentos registrados

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


</div>

@endsection