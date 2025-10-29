@extends('layouts.admin')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Lista de Empleados</h2>

<!-- Tarjetas de conteo de empleados por secciones -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-2 md:gap-4 md:space-y-0 space-y-2">
    <!-- Tarjeta para Academia -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Academia</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $academiasCount ?? 0 }}</p>
    </div>
    <!-- Tarjeta para Administrativo -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Administración</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $administrativosCount ?? 0 }}</p>
    </div>
    <!-- Tarjeta para Direccion -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Dirección</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $direccionCount ?? 0 }}</p>
    </div>

    <!-- Tarjeta para Prescolar -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Preescolar</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $preescolarCount ?? 0}}</p>
    </div>

    <!-- Tarjeta para Primaria -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Primaria</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $primariaCount ?? 0 }}</p>
    </div>
    <!-- Tarjeta para Promocion -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Promoción</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $promocionCount ?? 0 }}</p>
    </div>

    <!-- Tarjeta para Secundaria -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Secundaria</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $secundariaCount ?? 0 }}</p>
    </div>
    <!-- Tarjeta para Secundaria -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[100px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-blue-600">Mantenimiento</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $mantenimientoCount ?? 0 }}</p>
    </div>

    <!-- totales -->
    <div class="bg-blue-100 p-2 rounded shadow-lg text-center flex-1 min-w-[200px] sm:basis-[calc(15%-1rem)]">
        <h3 class="text-xl font-semibold text-green-600">Total de empleados</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $totales_empleados ?? 0 }}</p>
    </div>
</div>


<div x-data="{ buscar: '{{ request('buscar', '') }}', editarActivo: false  }">
    <!-- Formulario de búsqueda -->
    <div class="flex flex-col md:flex-row md:flex-wrap md:items-end md:gap-4 space-y-4 md:space-y-0 mb-4 pt-10">
        <!-- Campo de búsqueda -->
        <div class="w-full md:flex-1">
            <form id="buscar-empleado-form" method="GET" action="{{ route('admin.empleados') }}"
                class="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4">

                <input type="text" name="buscar" x-model="buscar" placeholder="Buscar empleado..."
                    class="px-4 py-2 border rounded w-full md:w-1/2 focus:outline-none focus:ring focus:border-blue-300"
                    value="{{ request('buscar') }}" />

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded w-full md:w-auto">Buscar</button>

                @if(request()->has('buscar') && request('buscar') !== '')
                <a href="{{ route('admin.empleados') }}"
                    class="px-4 py-2 bg-red-600 rounded hover:bg-red-400 text-white w-full md:w-auto text-center">
                    Borrar filtros
                </a>
                @endif
            </form>
        </div>
        <!-- Crear empleado -->
        <div class="flex justify-between mb-0 pr-4">
            <a href="{{ route('admin.empleados.crear') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Crear empleado</a>
        </div>

        <!-- Checkbox de activación -->
        @if(auth()->user()->level_user)
        <div class="flex flex-col items-center justify-center min-h-[10px]">
            <label for="toggle" class="inline-flex relative items-center cursor-pointer">
                <input type="checkbox" id="toggle" class="sr-only peer" x-model="editarActivo" />
                <div
                    class="w-11 h-6 bg-gray-300 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300
            peer-checked:bg-green-600 transition-colors duration-300"></div>
                <div
                    class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transform
            peer-checked:translate-x-5 transition-transform duration-300"></div>
            </label>
            <p class="mt-2 text-center">Editar empleado: <strong x-text="editarActivo ? 'ON' : 'OFF'"></strong></p>
        </div>
        @endif
    </div>

    <!-- Tabla de empleado -->
    <div class="overflow-x-auto">
        <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-lg">
            <table class="min-w-full text-left bg-white">
                <thead class="sticky top-0 bg-blue-100 z-10 shadow">
                    <tr>
                        <th class="p-3 text-center">N. Empleado</th>
                        <th class="p-3 text-center">Nombre</th>
                        <th class="p-3 text-center">Departamento</th>
                        <th class="p-3 text-center">Puesto</th>
                        <th class="p-3 text-center">Email</th>
                        <template x-if="editarActivo">
                            <th class="p-3">Acciones</th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleados as $empleado)
                    <tr class="border border-gray-300 rounded-lg hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $empleado->id }}</td>
                        <td class="p-3 text-center">{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $empleado->departamento }}</td>
                        <td class="p-3 text-center">{{ $empleado->puesto }}</td>
                        <td class="p-3 text-center max-w-[200px] truncate">{{ $empleado->email }}</td>
                        <template x-if="editarActivo">
                            <td class="p-3 flex gap-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.empleados.editar', $empleado->id) }}" target="_self"
                                        class="bg-orange-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.empleados.destroy', $empleado->id) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar empleado?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </template>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $empleados->links() }}
    </div>

</div>
@endsection