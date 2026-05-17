@extends('layouts.admin')

@section('content')
<div class="flex justify-between gap-4">
    <h2 class="md:text-xl text-xm font-semibold text-blue-800 mb-6 text-center">Bienvenido(a), {{ Auth::user()->name }}</h2>
    <h2 class="md:text-xl text-xm font-semibold text-gray-800 mb-6 text-center">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</h2>
</div>

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-6 md:space-y-0 space-y-2">
    <div class="bg-white p-1 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Asistencias del dia</h3>
        <p class="text-xl mt-1 text-center font-bold text-green-600 ">{{ $asistenciaE ?? 0 }}</p>
    </div>
    <div class="bg-white p-1 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Retardos del dia</h3>
        <p class="text-xl mt-1 text-center font-bold  text-yellow-500">{{$retardosHoy ?? 0}}</p>
    </div>
    <!-- <div class="bg-white p-1 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Salidas del dia</h3>
        <p class="text-xl mt-1 text-center font-bold text-blue-400">{{ $asistenciaS ?? 0}}</p>
    </div> -->
    <div class="mb-2 md:mb-0 bg-white p-1 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Faltantes del dia</h3>
        <p class="text-xl text-center mt-1 font-bold text-red-600">{{ $faltasHoy ?? 0}}</p>
    </div>
</div>
{{-- Sección adicional --}}

<div x-data="{
    buscar: '{{ request('buscar', '') }}',
    fecha_inicio: '{{ request('fecha_inicio', '') }}',
    fecha_fin: '{{ request('fecha_fin', '') }}',
    departamento: '{{ request('departamento', '') }}',
    retardo: '{{ request('retardo', '') }}',
    hora_entrada: '{{ request('hora_entrada', '') }}',
    hora_salida: '{{ request('hora_salida', '') }}'
}" class="pt-5">
    <!-- Formulario de filtros -->
    <form id="filtrosForm" method="GET" action="{{ route('admin.asistencias') }}"
        class="flex flex-col md:flex-row flex-wrap md:items-end md:gap-4 space-y-2 md:space-y-0">

        <div class="w-full md:w-auto">
            <label class="block mb-1 font-semibold text-sm">Buscar nombre o apellido</label>
            <input type="text" name="buscar" x-model="buscar" placeholder="Buscar..."
                class="border rounded px-3 py-1 w-full md:w-64 text-sm" />
        </div>

        <div class="w-full sm:w-1/2 md:w-auto">
            <label class="block mb-1 font-semibold text-sm">Fecha inicio</label>
            <input type="date" name="fecha_inicio" x-model="fecha_inicio"
                class="border rounded px-3 py-1 w-full text-sm" />
        </div>

        <div class="w-full sm:w-1/2 md:w-auto">
            <label class="block mb-1 font-semibold text-sm">Fecha fin</label>
            <input type="date" name="fecha_fin" x-model="fecha_fin"
                class="border rounded px-3 py-1 w-full text-sm" />
        </div>

        <!-- grupo de select filtrar -->
        <div class=" flex flex-col md:flex-row gap-4">
            <!-- 2 columnas SOLO en móvil -->
            <div class="w-full flex flex-row gap-2">
                <!-- Departamento -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold text-sm">Departamento</label>
                    <select name="departamento" x-model="departamento" class="border rounded px-3 py-1 w-full text-sm">
                        <option value="">Todos</option>
                        <option value="academia" {{ request('departamento') == 'academia' ? 'selected' : '' }}>Academia</option>
                        <option value="administracion" {{ request('departamento') == 'administracion' ? 'selected' : '' }}>Administración</option>
                        <option value="direccion" {{ request('departamento') == 'direccion' ? 'selected' : '' }}>Dirección</option>
                        <option value="preescolar" {{ request('departamento') == 'preescolar' ? 'selected' : '' }}>Preescolar</option>
                        <option value="primaria" {{ request('departamento') == 'primaria' ? 'selected' : '' }}>Primaria</option>
                        <option value="promocion" {{ request('departamento') == 'promocion' ? 'selected' : '' }}>Promoción</option>
                        <option value="secundaria" {{ request('departamento') == 'secundaria' ? 'selected' : '' }}>Secundaria</option>
                        <option value="mantenimiento" {{ request('departamento') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>
                <!-- Retardo -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold text-sm">Retardo</label>
                    <select name="retardo" x-model="retardo" class="border rounded px-3 py-1 w-full text-sm">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <!-- Hora entrada + salida (solo se agrupan en móviles) -->
            <div class="w-full flex flex-row gap-2 md:w-auto">
                <!-- Hora de entrada -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold text-sm">Hora de entrada</label>
                    <select name="hora_entrada" x-model="hora_entrada" class="border rounded px-3 py-1 w-full text-sm">
                        <option value="">Todos</option>
                        <option value="1">Con hora</option>
                        <option value="0">Sin hora</option>
                    </select>
                </div>
                <!-- Hora de salida -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold text-sm">Hora de salida</label>
                    <select name="hora_salida" x-model="hora_salida" class="border rounded px-3 py-1 w-full text-sm">
                        <option value="">Todos</option>
                        <option value="1">Con hora</option>
                        <option value="0">Sin hora</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Boton filtrar -->
        <div class="w-full sm:w-1/2 md:w-auto">
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 w-full">
                Filtrar
            </button>
        </div>

        @if(request()->hasAny(['buscar', 'fecha_inicio', 'fecha_fin', 'departamento', 'retardo', 'hora_entrada', 'hora_salida']) && collect(request()->only(['buscar', 'fecha_inicio', 'fecha_fin','departamento', 'retardo', 'hora_entrada', 'hora_salida']))->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty())
        <div class="w-full sm:w-1/2 md:w-auto">
            <a href="{{ route('admin.asistencias') }}"
                class="block text-center px-4 py-2 bg-red-600 hover:bg-red-400 text-white rounded w-full">
                Borrar filtros
            </a>
        </div>
        @endif
    </form>

    <div class="flex justify-end gap-2 mb-1 pr-4 pt-5"">
        <form method=" GET" action="{{ route('admin.asistencias.reporte.pdf') }}" target="_blank">
        <!-- Envía los filtros actuales como inputs ocultos -->
        <input type="hidden" name="buscar" value="{{ request('buscar') }}">
        <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
        <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
        <input type="hidden" name="departamento" value="{{ request('departamento') }}">
        <input type="hidden" name="retardo" value="{{ request('retardo') }}">
        <input type="hidden" name="hora_entrada" value="{{ request('hora_entrada') }}">
        <input type="hidden" name="hora_salida" value="{{ request('hora_salida') }}">

        <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-blue-700">
            Crear reporte
        </button>
        </form>

        <form method="GET" action="{{ route('admin.asistencias.reporte.excel') }}">
            <!-- Envía los filtros actuales como inputs ocultos -->
            <input type="hidden" name="buscar" value="{{ request('buscar') }}">
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="departamento" value="{{ request('departamento') }}">
            <input type="hidden" name="retardo" value="{{ request('retardo') }}">
            <input type="hidden" name="hora_entrada" value="{{ request('hora_entrada') }}">
            <input type="hidden" name="hora_salida" value="{{ request('hora_salida') }}">

            <button type="submit"
                class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
                Exportar Excel
            </button>
        </form>
    </div>


    <!-- Tabla de asistencias -->
    <div class="overflow-x-auto">
        <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-lg">
            <table class="min-w-full bg-white">
                <thead class="sticky top-0 bg-gray-700 text-white">
                    <tr class="text-xs">
                        <th class="p-2 text-center">N. Empleado</th>
                        <th class="p-2 text-center">Nombre</th>
                        <th class="p-2 text-center">Departamento</th>
                        @if($hayFiltros)
                        <th class="p-2 text-center">Fecha</th>
                        @endif
                        <th class="p-2 text-center">Hora de entrada</th>
                        <th class="p-2 text-center">Hora de salida</th>
                        <th class="p-2 text-center">Retardo</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @forelse ($asistencias as $asistencia)
                    @php
                    $empleado = $asistencia->empleado;

                    // Obtener checadas del día
                    $checadas = $asistencia->checadas->filter(function ($checada) use ($asistencia) {
                    return \Carbon\Carbon::parse($checada->fecha_hora)->toDateString()
                    === \Carbon\Carbon::parse($asistencia->fecha)->toDateString();
                    });
                    $entrada = $checadas->where('tipo', 'entrada')->first();
                    $salida = $checadas->where('tipo', 'salida')->last();
                    @endphp
                    <tr class="border border-gray-300 hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $empleado->n_empleado ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
                        @if($hayFiltros)
                        <td class="p-3 text-center">
                            {{ $entrada ? \Carbon\Carbon::parse($entrada->fecha_hora)->format('d/m/Y') : 'N/A' }}
                        </td>
                        @endif
                        <td class="p-3 text-center {{ !$entrada ? 'text-red-600 font-semibold' : '' }}">
                            {{ $entrada ? \Carbon\Carbon::parse($entrada->fecha_hora)->format('H:i') : 'Sin registro' }}
                        </td>
                        <td class="p-3 text-center {{ !$salida ? 'text-red-600 font-semibold' : '' }}">
                            {{ $salida ? \Carbon\Carbon::parse($salida->fecha_hora)->format('H:i') : 'Sin registro' }}
                        </td>
                        <td class="p-3 text-center font-semibold
                        @if(is_null($entrada) && is_null($salida)) text-red-600
                        @elseif($asistencia->minutos_retardo > 0) text-red-600
                        @else text-green-600
                        @endif
                    ">
                            @if(is_null($entrada) && is_null($salida))
                            Sin registro
                            @elseif($asistencia->minutos_retardo > 0)
                            Sí
                            @else
                            No
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $hayFiltros ? 7 : 6 }}" class="text-center p-4">No se encontraron registros.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        @if(!$hayFiltros)
        {{ $asistencias->links() }}
        @endif
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Si hay query string y queremos ocultarla tras carga:
        if (window.location.search.length) {
            // opcional: conservas historial (replaceState) sin query
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>