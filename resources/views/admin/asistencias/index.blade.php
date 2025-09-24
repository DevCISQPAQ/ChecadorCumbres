@extends('layouts.admin')

@section('content')
<div class="flex justify-between gap-4">
    <h2 class="md:text-2xl text-xm font-semibold text-blue-800 mb-6">Bienvenido(a), {{ Auth::user()->name }}</h2>
    <h2 class="md:text-xl text-xm font-semibold text-gray-800 mb-6">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</h2>
</div>

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:space-y-0 space-y-2">
    <div class="bg-white p-3 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Asistencias del dia</h3>
        <p class="text-3xl mt-2 text-center font-bold text-green-600 ">{{ $asistenciaE ?? 0 }}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Retardos del dia</h3>
        <p class="text-3xl mt-2 text-center font-bold  text-yellow-500">{{$retardosHoy ?? 0}}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Salidas del dia</h3>
        <p class="text-3xl mt-2 text-center font-bold text-blue-400">{{ $asistenciaS ?? 0}}</p>
    </div>
    <div class="bg-white p-3 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Faltantes del dia</h3>
        <p class="text-3xl text-center mt-2 font-bold text-red-600">{{ $cantidadSinAsistencia?? 0}}</p>
    </div>
</div>
{{-- Sección adicional --}}

<div x-data="{ buscar: '{{ request('buscar', '') }}', editarActivo: false  }">
    <!-- Formulario de búsqueda -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4 pt-10">
        <!-- Campo de búsqueda -->
        <div class="w-full md:flex-1">
            <form method="GET" action="{{ route('admin.asistencias') }}" class="w-full">
                <input type="text" name="buscar" x-model="buscar" placeholder="Buscar estudiante..."
                    class="px-4 py-2 border rounded  w-1/2 focus:outline-none focus:ring focus:border-blue-300"
                    value="{{ request('buscar') }}" />

                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded mt-2 md:mt-2">Buscar</button>
            </form>
        </div>

        <!-- Crear empleado -->
        <div class="flex justify-between mb-0 pr-4">
            <a href="" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-blue-700">Crear reporte</a>
        </div>

    </div>

    <!-- Tabla de empleado -->
    <div class="overflow-x-auto">
        <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-lg">
            <table class="min-w-full text-left bg-white">
                <thead class="sticky top-0 bg-gray-500 z-10 shadow">
                    <tr>
                        <th class="p-3 text-center text-white">IdEmpleado</th>
                        <th class="p-3 text-center text-white">Nombre</th>
                        <th class="p-3 text-center text-white">Hora de entrada</th>
                        <th class="p-3 text-center text-white">Hora de salida</th>
                        <th class="p-3 text-center text-white">Retardo</th>
                        <!-- <th class="p-3 text-center text-white">Acciones</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($asistencias as $asistencia)
                    @php
                    $empleado = $asistencia->empleado;
                    @endphp
                    <tr class="border border-gray-300 rounded-lg hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $asistencia->empleado_id }}</td>
                        <td class="p-3 text-center">{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'N/A' }}</td>
                        <td class="p-3 text-center {{ $asistencia->retardo ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                            {{ $asistencia->retardo ? 'Si' : 'No' }}
                        </td>
                        <!-- <td class="p-3 text-center">
                            <a href="" target="_blank"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Ver PDF
                            </a>
                        </td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $asistencias->links() }}
    </div>

</div>




@endsection