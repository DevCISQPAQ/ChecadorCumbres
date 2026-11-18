@extends('layouts.admin')

@section('content')

@if ($errors->any())
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="mb-4 bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg">

    <ul class="list-disc pl-5 text-xs">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

    {{-- REGISTRAR DÍA FESTIVO --}}

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 h-fit">

        <div class="mb-4">
            <h1 class="text-xl font-bold text-gray-800">
                Registrar día festivo
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Agrega días oficiales o no laborables.
            </p>
        </div>

        <form action="{{ route('admin.diasfestivos.store') }}"
            method="POST">

            @csrf

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Nombre del día festivo
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    placeholder="Ejemplo: Navidad"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500">
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ old('fecha') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500">
            </div>

            <div class="mb-4 flex items-center gap-2">

                <input
                    type="checkbox"
                    name="oficial"
                    value="1"
                    checked
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">

                <label class="text-xs text-gray-700">
                    Día festivo oficial
                </label>

            </div>

            <div class="flex justify-end">

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

                    Guardar día festivo

                </button>

            </div>

        </form>
    </div>

    {{-- TABLA DE DÍAS FESTIVOS --}}

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">

        <div class="flex items-center justify-between mb-3">

            <div>
                <h2 class="text-base font-bold text-gray-800">
                    Días festivos registrados
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Listado de días no laborables.
                </p>
            </div>

            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-xs font-semibold">
                Total: {{ $diasFestivos->count() }}
            </span>

        </div>

        <div class="max-h-[450px] overflow-y-auto overflow-x-auto border border-gray-200 rounded-lg">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="sticky top-0 z-10 bg-gray-50">

                    <tr class="text-[11px] uppercase text-gray-500">

                        <th class="px-3 py-2 text-left font-semibold">
                            Nombre
                        </th>

                        <th class="px-3 py-2 text-center font-semibold">
                            Fecha
                        </th>

                        <th class="px-3 py-2 text-center font-semibold">
                            Tipo
                        </th>

                        <th class="px-3 py-2 text-center font-semibold">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse($diasFestivos as $dia)

                    <tr class="hover:bg-gray-50 transition">

                        {{-- NOMBRE --}}
                        <td class="px-3 py-3">

                            <span class="text-xs font-semibold text-gray-800">
                                {{ $dia->nombre }}
                            </span>

                        </td>

                        {{-- FECHA --}}
                        <td class="px-3 py-3 text-center whitespace-nowrap">

                            <span class="text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}
                            </span>

                        </td>

                        {{-- TIPO --}}
                        <td class="px-3 py-3 text-center whitespace-nowrap">

                            @if($dia->oficial)

                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                Oficial
                            </span>

                            @else

                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                No oficial
                            </span>

                            @endif

                        </td>

                        {{-- ACCIONES --}}
                        <td class="px-3 py-3">

                            <div class="flex items-center justify-center gap-1">

                                {{-- EDITAR --}}
                                <button
                                    type="button"
                                    @click="$dispatch('editar-festivo', {{ Js::from([
                                        'id' => $dia->id,
                                        'url' => route('admin.festivos.update', $dia->id),
                                        'nombre' => $dia->nombre,
                                        'fecha' => \Carbon\Carbon::parse($dia->fecha)->format('Y-m-d'),
                                        'oficial' => (bool) $dia->oficial
                                    ]) }})"
                                    class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1.5 rounded-md text-[11px] font-semibold transition">

                                    Editar

                                </button>

                                {{-- ELIMINAR --}}
                                <form
                                    action="{{ route('admin.festivos.destroyFestivos', $dia->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar día festivo?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1.5 rounded-md text-[11px] font-semibold transition">

                                        Eliminar

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center">

                            <p class="text-sm text-gray-500">
                                No hay días festivos registrados.
                            </p>

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- MODAL EDITAR DÍA FESTIVO --}}

<div
    x-data="{
        abierto: false,
        id: null,
        url: '',
        nombre: '',
        fecha: '',
        oficial: false,

        abrir(datos) {
            this.id = datos.id;
            this.url = datos.url;
            this.nombre = datos.nombre;
            this.fecha = datos.fecha;
            this.oficial = datos.oficial;
            this.abierto = true;
        }
    }"
    @editar-festivo.window="abrir($event.detail)">

    <div
        x-show="abierto"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            @click.outside="abierto = false"
            class="bg-white w-full max-w-md rounded-xl shadow-xl p-5">

            {{-- ENCABEZADO --}}
            <div class="flex justify-between items-center mb-4">

                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Editar día festivo
                    </h2>

                    <p class="text-xs text-gray-500">
                        Modifica los datos del día festivo.
                    </p>
                </div>

                <button
                    type="button"
                    @click="abierto = false"
                    class="text-gray-500 hover:text-gray-700 text-2xl">

                    &times;

                </button>

            </div>

            <form :action="url" method="POST">

                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Nombre del día festivo
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        x-model="nombre"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500">

                </div>

                {{-- FECHA --}}
                <div class="mb-3">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        x-model="fecha"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-200 focus:border-green-500">

                </div>

                {{-- OFICIAL --}}
                <div class="mb-5 flex items-center gap-2">

                    <input
                        type="checkbox"
                        name="oficial"
                        value="1"
                        x-model="oficial"
                        class="rounded border-gray-300 text-green-600 focus:ring-green-500">

                    <label class="text-xs text-gray-700">
                        Día festivo oficial
                    </label>

                </div>

                {{-- BOTONES --}}
                <div class="flex justify-end gap-2">

                    <button
                        type="button"
                        @click="abierto = false"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection