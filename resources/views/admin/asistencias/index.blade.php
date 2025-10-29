@extends('layouts.admin')

@section('content')
<div class="flex justify-between gap-4">
    <h2 class="md:text-2xl text-xm font-semibold text-blue-800 mb-6 text-center">Bienvenido(a), {{ Auth::user()->name }}</h2>
    <h2 class="md:text-xl text-xm font-semibold text-gray-800 mb-6 text-center">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</h2>
</div>

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-4 md:space-y-0 space-y-2">
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
    <div class="mb-2 md:mb-0 bg-white p-3 rounded-lg shadow">
        <h3 class="text-sm text-center font-semibold text-gray-700">Faltantes del dia</h3>
        <p class="text-3xl text-center mt-2 font-bold text-red-600">{{ $cantidadSinAsistencia ?? 0}}</p>
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
}" class="pt-10">
    <!-- Formulario de filtros -->
    <form id="filtrosForm" method="GET" action="{{ route('admin.asistencias') }}"
        class="flex flex-col md:flex-row flex-wrap md:items-end md:gap-4 space-y-4 md:space-y-0">

        <div class="w-full md:w-auto">
            <label class="block mb-1 font-semibold">Buscar nombre o apellido</label>
            <input type="text" name="buscar" x-model="buscar" placeholder="Buscar..."
                class="border rounded px-3 py-2 w-full md:w-64" />
        </div>

        <div class="w-full sm:w-1/2 md:w-auto">
            <label class="block mb-1 font-semibold">Fecha inicio</label>
            <input type="date" name="fecha_inicio" x-model="fecha_inicio"
                class="border rounded px-3 py-2 w-full" />
        </div>

        <div class="w-full sm:w-1/2 md:w-auto">
            <label class="block mb-1 font-semibold">Fecha fin</label>
            <input type="date" name="fecha_fin" x-model="fecha_fin"
                class="border rounded px-3 py-2 w-full" />
        </div>

        <!-- grupo de select filtrar -->
        <div class=" flex flex-col md:flex-row gap-4">
            <!-- 2 columnas SOLO en móvil -->
            <div class="w-full flex flex-row gap-4">
                <!-- Departamento -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold">Departamento</label>
                    <select name="departamento" x-model="departamento" class="border rounded px-3 py-2 w-full">
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
                    <label class="block mb-1 font-semibold">Retardo</label>
                    <select name="retardo" x-model="retardo" class="border rounded px-3 py-2 w-full">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <!-- Hora entrada + salida (solo se agrupan en móviles) -->
            <div class="w-full flex flex-row gap-4 md:w-auto">
                <!-- Hora de entrada -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold">Hora de entrada</label>
                    <select name="hora_entrada" x-model="hora_entrada" class="border rounded px-3 py-2 w-full">
                        <option value="">Todos</option>
                        <option value="1">Con hora</option>
                        <option value="0">Sin hora</option>
                    </select>
                </div>
                <!-- Hora de salida -->
                <div class="w-1/2 md:w-32">
                    <label class="block mb-1 font-semibold">Hora de salida</label>
                    <select name="hora_salida" x-model="hora_salida" class="border rounded px-3 py-2 w-full">
                        <option value="">Todos</option>
                        <option value="1">Con hora</option>
                        <option value="0">Sin hora</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Boton filtrar -->
        <div class="w-full sm:w-1/2 md:w-auto">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
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

    <div class="flex justify-end mb-1 pr-4 pt-5">
        <form method="GET" action="{{ route('admin.asistencias.reporte') }}" target="_blank">
            <!-- Envía los filtros actuales como inputs ocultos -->
            <input type="hidden" name="buscar" value="{{ request('buscar') }}">
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="departamento" value="{{ request('departamento') }}">
            <input type="hidden" name="retardo" value="{{ request('retardo') }}">
            <input type="hidden" name="hora_entrada" value="{{ request('hora_entrada') }}">
            <input type="hidden" name="hora_salida" value="{{ request('hora_salida') }}">

            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Crear reporte
            </button>
        </form>
    </div>
    <!-- Tabla de asistencias -->
    <div class="overflow-x-auto">
        <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-lg">
            <table class="min-w-full bg-white">
                <thead class="sticky top-0 bg-gray-700 text-white">
                    <tr>
                        <th class="p-3 text-center">N. Empleado</th>
                        <th class="p-3 text-center">Nombre</th>
                        <th class="p-3 text-center">Departamento</th>
                        <th class="p-3 text-center">Hora de entrada</th>
                        <th class="p-3 text-center">Hora de salida</th>
                        <th class="p-3 text-center">Retardo</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Empleado)
                    @forelse($asistencias as $empleado)
                    <tr class="border border-gray-300 hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $empleado->id }}</td>
                        <td class="p-3 text-center">{{ $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno }}</td>
                        <td class="p-3 text-center">{{ $empleado->departamento }}</td>
                        <td class="p-3 text-center text-red-600 font-semibold">Sin registro</td>
                        <td class="p-3 text-center text-red-600 font-semibold">Sin registro</td>
                        <td class="p-3 text-center text-red-600 font-semibold">Sin registro</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">No se encontraron registros.</td>
                    </tr>
                    @endforelse
                    @else
                    @forelse ($asistencias as $asistencia)
                    @php $empleado = $asistencia->empleado; @endphp
                    <tr class="border border-gray-300 hover:bg-gray-50">
                        <td class="p-3 text-center">{{ $asistencia->empleado_id ?? 0}}</td>
                        <td class="p-3 text-center">{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $empleado->departamento ?? 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'N/A' }}</td>
                        <td class="p-3 text-center {{ $asistencia->retardo ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                            {{ $asistencia->retardo ? 'Sí' : 'No' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">No se encontraron registros.</td>
                    </tr>
                    @endforelse
                    @endif

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Si hay query string y queremos ocultarla tras carga:
        if (window.location.search.length) {
            // opcional: conservas historial (replaceState) sin query
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>