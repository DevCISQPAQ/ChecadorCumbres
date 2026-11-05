@extends('layouts.admin')

@section('content')

@if ($errors->any())
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition
    class="mb-5 bg-red-100 border border-red-400 text-red-700 p-4 rounded">
    <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- ===================================================== -->
    <!-- DÍAS FESTIVOS -->
    <!-- ===================================================== -->

    <div class="bg-white p-6 rounded-xl shadow h-fit">

        <div class="mb-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Registrar día festivo
            </h1>

            <p class="text-sm text-gray-500">
                Agrega días oficiales o no laborables.
            </p>
        </div>

        <form id="guardar-festivo" action="{{ route('admin.diasfestivos.store') }}" method="POST">
            @csrf

            <!-- NOMBRE -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del día festivo
                </label>

                <input
                    type="text"
                    name="nombre"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Ejemplo: Navidad"
                    required>
            </div>

            <!-- FECHA -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    required>
            </div>

            <!-- OFICIAL -->
            <div class="mb-6 flex items-center gap-3">

                <input
                    type="checkbox"
                    name="oficial"
                    value="1"
                    checked
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">

                <label class="text-sm text-gray-700">
                    Día festivo oficial
                </label>

            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3">

                <button
                    type="submit"
                    class="bg-green-600 cursor-pointer hover:bg-green-700 text-white px-5 py-2 rounded-lg">
                    Guardar día festivo
                </button>

            </div>

        </form>
    </div>

    <!-- TABLA DÍAS FESTIVOS -->
    <div class="bg-white p-6 rounded-xl shadow">
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Días festivos registrados
                </h2>
                <span class="text-sm text-gray-500">
                    Total: {{ $diasFestivos->count() }}
                </span>
            </div>
            <div class="max-h-[400px] overflow-y-auto overflow-x-auto border border-gray-200 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- HEADER -->
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Nombre
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Fecha
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody class="bg-white divide-y divide-gray-100">

                        @forelse($diasFestivos as $dia)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- NOMBRE -->
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-800 text-sm">
                                    {{ $dia->nombre }}
                                </div>
                            </td>
                            <!-- FECHA -->
                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}
                            </td>
                            <!-- TIPO -->
                            <td class="px-4 py-4 text-center">
                                @if($dia->oficial)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Oficial
                                </span>
                                @else
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    No oficial
                                </span>
                                @endif
                            </td>

                            <!-- ACCIONES -->
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- EDITAR -->
                                    <button
                                        type="button"
                                        @click="$dispatch('editar-festivo', {{ Js::from([
                                        'id' => $dia->id,
                                        'url' => route('admin.festivos.update', $dia->id),
                                        'nombre' => $dia->nombre,
                                        'fecha' => \Carbon\Carbon::parse($dia->fecha)->format('Y-m-d'),
                                         'oficial' => (bool) $dia->oficial
                                          ]) }})"
                                        class="cursor-pointer bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                        Editar
                                    </button>

                                    <!-- ELIMINAR -->
                                    <form
                                        action="{{ route('admin.festivos.destroyFestivos', $dia->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar día festivo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="bg-red-100 hover:bg-red-200 cursor-pointer text-red-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                No hay días festivos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- MODAL EDITAR DÍA FESTIVO --}}
{{-- ===================================================== --}}

<div x-data="{
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
    }}"
    @editar-festivo.window="abrir($event.detail)">

    <div
        x-show="abierto"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div
            @click.outside="abierto = false"
            x-transition
            class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">

            <div class="flex justify-between items-center mb-5">

                <h2 class="text-xl font-bold text-gray-800">
                    Editar día festivo
                </h2>

                <button
                    type="button"
                    @click="abierto = false"
                    class="text-gray-500 hover:text-gray-700 text-xl">

                    &times;

                </button>

            </div>

            <form
                :action="url"
                method="POST">


                @csrf
                @method('PUT')

                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del día festivo
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        x-model="nombre"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm">

                </div>

                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        x-model="fecha"
                        required
                        class="w-full border-gray-300 rounded-lg shadow-sm">

                </div>

                <div class="mb-6 flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="oficial"
                        value="1"
                        x-model="oficial"
                        class="rounded border-gray-300 text-green-600">

                    <label class="text-sm text-gray-700">
                        Día festivo oficial
                    </label>

                </div>

                <div class="flex justify-end gap-3">

                    <button
                        type="button"
                        @click="abierto = false"
                        class="bg-gray-100 cursor-pointer hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="bg-green-600 cursor-pointer hover:bg-green-700 text-white px-5 py-2 rounded-lg">

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@endsection