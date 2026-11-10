
@extends('layouts.admin')

@section('content')

<div x-data="{
    buscar: '{{ addslashes(request('buscar', '')) }}',
    editarActivo: false
}">

    {{-- ENCABEZADO --}}

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">

        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Directorio de empleados
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Administración y consulta del personal
            </p>
        </div>

        <a href="{{ route('admin.empleados.crear') }}"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition">

            <span class="text-lg">+</span>
            Crear empleado

        </a>

    </div>


    {{-- TARJETAS POR DEPARTAMENTO --}}

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-2 mb-4">

        @foreach($departamentos as $departamento)

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-2 hover:shadow-md transition">

            <div class="flex justify-between items-center gap-2">

                <p class="text-xs font-medium text-gray-500 truncate">
                    {{ ucfirst($departamento->nombre) }}
                </p>

                <div class="w-6 h-6 flex-shrink-0 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                    D
                </div>

            </div>

            <h3 class="text-xl font-bold text-gray-800 mt-2">
                {{ number_format($departamento->empleados_count) }}
            </h3>

            <p class="text-[10px] text-gray-400 mt-1">
                Empleados registrados
            </p>

        </div>

        @endforeach


        {{-- TOTAL DE EMPLEADOS --}}

        <div class="bg-blue-50 border border-blue-100 rounded-xl shadow-sm p-2">

            <div class="flex justify-between items-center gap-2">

                <p class="text-xs font-semibold text-blue-700">
                    Total de empleados
                </p>

                <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                    T
                </div>

            </div>

            <h3 class="text-xl font-bold text-blue-700 mt-2">
                {{ number_format($totales_empleados) }}
            </h3>

            <p class="text-[10px] text-blue-500 mt-1">
                Personal registrado
            </p>

        </div>

    </div>


    {{-- BÚSQUEDA Y CONTROLES --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">

            {{-- BUSCADOR --}}

            <form id="buscar-empleado-form"
                  method="GET"
                  action="{{ route('admin.empleados') }}"
                  class="flex flex-col sm:flex-row gap-2 flex-1">

                <div class="flex-1">

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        Buscar empleado
                    </label>

                    <input
                        type="text"
                        name="buscar"
                        x-model="buscar"
                        placeholder="Nombre, número de empleado, departamento..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                        value="{{ request('buscar') }}"
                    >

                </div>

                <div class="flex gap-2 sm:items-end">

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

                        Buscar

                    </button>

                    @if(request('buscar'))

                    <a href="{{ route('admin.empleados') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">

                        Limpiar

                    </a>

                    @endif

                </div>

            </form>


            {{-- ACTIVAR EDICIÓN --}}

            @if(auth()->user()->level_user)

            <div class="flex items-center gap-3 border-t lg:border-t-0 lg:border-l border-gray-200 pt-3 lg:pt-0 lg:pl-4">

                <div>

                    <p class="text-xs font-semibold text-gray-700">
                        Modo de edición
                    </p>

                    <p class="text-[10px] text-gray-400">
                        Mostrar acciones administrativas
                    </p>

                </div>

                <label for="toggle"
                       class="inline-flex relative items-center cursor-pointer">

                    <input
                        type="checkbox"
                        id="toggle"
                        class="sr-only peer"
                        x-model="editarActivo"
                    >

                    <div class="w-10 h-5 bg-gray-300 rounded-full peer-focus:ring-2 peer-focus:ring-blue-200 peer-checked:bg-green-600 transition-colors"></div>

                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transform peer-checked:translate-x-5 transition-transform"></div>

                </label>

                <span
                    class="text-xs font-semibold"
                    :class="editarActivo ? 'text-green-600' : 'text-gray-400'"
                    x-text="editarActivo ? 'Activo' : 'Inactivo'">
                </span>

            </div>

            @endif

        </div>

    </div>


    {{-- TABLA DE EMPLEADOS --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        {{-- ENCABEZADO DE TABLA --}}

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-4 py-3 border-b border-gray-200">

            <div>

                <h3 class="text-sm font-bold text-gray-800">
                    Personal registrado
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Listado de empleados de la institución
                </p>

            </div>

            <span class="text-xs text-gray-500">
                {{ $empleados->total() }} empleados encontrados
            </span>

        </div>


        {{-- CONTENIDO --}}

        <div class="overflow-x-auto">

            <div class="max-h-[550px] overflow-y-auto">

                <table class="min-w-full text-left">

                    <thead class="sticky top-0 z-10 bg-gray-50 border-b border-gray-200">

                        <tr class="text-[11px] uppercase tracking-wider text-gray-500">

                            <th class="px-4 py-3 text-center whitespace-nowrap">
                                N. Empleado
                            </th>

                            <th class="px-4 py-3 whitespace-nowrap">
                                Nombre completo
                            </th>

                            <th class="px-4 py-3 whitespace-nowrap">
                                Departamento
                            </th>

                            <th class="px-4 py-3 whitespace-nowrap">
                                Puesto
                            </th>

                            <th class="px-4 py-3 whitespace-nowrap">
                                Correo electrónico
                            </th>

                            @if(auth()->user()->level_user)

                            <th class="px-4 py-3 text-center whitespace-nowrap"
                                x-show="editarActivo"
                                x-cloak>
                                Acciones
                            </th>

                            @endif

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100 text-xs">

                        @forelse($empleados as $empleado)

                        <tr class="hover:bg-blue-50/50 transition-colors">

                            {{-- NÚMERO --}}

                            <td class="px-4 py-3 text-center whitespace-nowrap">

                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-md font-semibold">
                                    {{ $empleado->n_empleado }}
                                </span>

                            </td>


                            {{-- NOMBRE --}}

                            <td class="px-4 py-3 whitespace-nowrap">

                                <div class="font-semibold text-gray-800">

                                    {{ $empleado->nombres }}
                                    {{ $empleado->apellido_paterno }}
                                    {{ $empleado->apellido_materno }}

                                </div>

                            </td>


                            {{-- DEPARTAMENTO --}}

                            <td class="px-4 py-3 whitespace-nowrap">

                                @if($empleado->departamento)

                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md font-medium">

                                    {{ $empleado->departamento->nombre }}

                                </span>

                                @else

                                <span class="text-gray-400">
                                    Sin departamento
                                </span>

                                @endif

                            </td>


                            {{-- PUESTO --}}

                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">

                                {{ $empleado->puesto }}

                            </td>


                            {{-- CORREO --}}

                            <td class="px-4 py-3 text-gray-600 max-w-[220px]">

                                <span class="block truncate"
                                      title="{{ $empleado->email }}">

                                    {{ $empleado->email ?? 'Sin correo' }}

                                </span>

                            </td>


                            {{-- ACCIONES --}}

                            @if(auth()->user()->level_user)

                            <td class="px-4 py-3 whitespace-nowrap"
                                x-show="editarActivo"
                                x-cloak>

                                <div class="flex items-center justify-center gap-2">

                                    {{-- VACACIONES PDF --}}

                                    <a href="{{ route('admin.empleados.vacaciones.pdf', $empleado->id) }}"
                                       target="_blank"
                                       title="Generar PDF de vacaciones"
                                       class="bg-green-50 hover:bg-green-100 text-green-700 px-2.5 py-1.5 rounded-lg font-semibold transition">

                                        PDF

                                    </a>


                                    {{-- EDITAR --}}

                                    <a href="{{ route('admin.empleados.editar', $empleado->id) }}"
                                       title="Editar empleado"
                                       class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg font-semibold transition">

                                        Editar

                                    </a>


                                    {{-- ELIMINAR --}}

                                    <form action="{{ route('admin.empleados.destroy', $empleado->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar este empleado?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Eliminar empleado"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-lg font-semibold transition">

                                            Eliminar

                                        </button>

                                    </form>

                                </div>

                            </td>

                            @endif

                        </tr>

                        @empty

                        <tr>

                            <td colspan="{{ auth()->user()->level_user ? 6 : 5 }}"
                                class="px-4 py-12 text-center">

                                <div class="text-gray-400 text-3xl mb-2">
                                    <span>◎</span>
                                </div>

                                <p class="text-sm font-semibold text-gray-600">
                                    No se encontraron empleados
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Intenta cambiar el término de búsqueda.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINACIÓN --}}

        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">

            {{ $empleados->appends(request()->query())->links() }}

        </div>

    </div>

</div>

@endsection
