@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Editar horario</h2>

    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
        <ul class="list-disc pl-4 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.usuarios.data') }}">
        @csrf


        <div class="mb-5">
            <label for="hora_limite_entrada" class="block text-xl font-medium text-gray-700">
                Hora límite de entrada
            </label>
            <p class=" text-xs text-gray-500">Este horario sera indicador de retardo.</p>
            <input type="time" name="hora_limite_entrada" id="hora_limite_entrada"
                value="{{ old('hora_limite_entrada', $config['hora_limite_entrada'] ?? '') }}"
                class="mt-2 py-3 text-xl block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        @if(auth()->user()->level_user >= 2)
        <div class="mb-5">
            <label for="hora_limite_salida" class="block text-xl font-medium text-gray-700">
                Hora de marcado como salida
            </label>
            <p class=" text-xs text-gray-500">Se usa para establecer la hora en que el chequeo del empleado se marca como salida.</p>
            <input type="time" name="hora_limite_salida" id="hora_limite_salida"
                value="{{ old('hora_limite_salida', $config['hora_limite_salida'] ?? '') }}"
                class="mt-2 py-3 text-xl block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        @endif

        <div class="flex justify-end">
            <a href="{{ route('admin.preferencias') }}" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</a>
            <button type="submit" class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection