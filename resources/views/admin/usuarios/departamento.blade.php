@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-semibold mb-4 text-gray-800">
        Departamentos
    </h2>

    {{-- MENSAJES --}}
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
    @endif

    {{-- FORM CREAR --}}
    <form method="POST" action="{{ route('admin.departamentos.store') }}" class="mb-6">
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
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Guardar
            </button>
        </div>
    </form>

    {{-- TABLA --}}
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="text-left p-3">ID</th>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($departamentos as $departamento)
                    <tr class="border-t">
                        <td class="p-3">{{ $departamento->id }}</td>

                        <td class="p-3">
                            {{ $departamento->nombre }}
                        </td>

                        <td class="p-3 flex gap-2">

                            {{-- EDITAR --}}
                            <a href="#"
                               class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded text-xs">
                                Editar
                            </a>

                            {{-- ELIMINAR --}}
                            <form action="{{ route('admin.departamentos.destroy', $departamento->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar departamento?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded text-xs">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">
                            No hay departamentos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection