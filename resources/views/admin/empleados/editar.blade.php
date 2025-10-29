@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Editar empleado</h2>

    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
        <ul class="list-disc pl-4 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="editar-empleado-form" method="POST" action="{{ route('admin.empleados.actualizar', $empleado->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <div class="w-32 h-32 mx-auto my-4 flex items-center justify-center border rounded overflow-hidden bg-gray-100">
            <img src="{{ asset('img/empleados/' . $empleado->foto) }}" alt="Foto del empleado" class="h-24 object-contain">
        </div>

        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Numero de empleado</label>
                <input type="text" name="id" value="{{ old('id', $empleado->id) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Nombres</label>
                <input type="text" name="nombres" value="{{ old('nombres', $empleado->nombres) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Apellido paterno</label>
                <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $empleado->apellido_paterno) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Apellido materno</label>
                <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $empleado->apellido_materno) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>

        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Departamento</label>
                <select name="departamento" class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    <option value="Academia" {{ $empleado->departamento === 'Academia' ? 'selected' : '' }}>Academia</option>
                    <option value="Administración" {{ $empleado->departamento === 'Administración' ? 'selected' : '' }}>Administración</option>
                    <option value="Dirección" {{ $empleado->departamento === 'Dirección' ? 'selected' : '' }}>Dirección</option>
                    <option value="Preescolar" {{ $empleado->departamento === 'Preescolar' ? 'selected' : '' }}>Preescolar</option>
                    <option value="Primaria" {{ $empleado->departamento === 'Primaria' ? 'selected' : '' }}>Primaria</option>
                    <option value="Promoción" {{ $empleado->departamento === 'Promoción' ? 'selected' : '' }}>Promoción</option>
                    <option value="Secundaria" {{ $empleado->departamento === 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                    <option value="Mantenimiento" {{ $empleado->departamento === 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                </select>
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Puesto</label>
                <input type="text" name="puesto" value="{{ old('puesto', $empleado->puesto) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>

        </div>

        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Tipo de horario</label>
                <select name="tipo_horario" class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    <option value="Horario Base" {{ $empleado->tipo_horario === 'Horario Base' ? 'selected' : '' }}>Horario Base</option>
                    <option value="Horario Libre" {{ $empleado->tipo_horario === 'Horario Libre' ? 'selected' : '' }}>Horario Libre</option>
                </select>
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $empleado->email) }}" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="foto">Subir foto</label>
            <input
                type="file"
                id="foto"
                name="foto"
                accept="image/*"
                class="block w-full text-sm text-gray-500
           file:mr-4 file:py-2 file:px-4
           file:rounded file:border-0
           file:text-sm file:font-semibold
           file:bg-blue-100 file:text-blue-700
           hover:file:bg-blue-200
           cursor-pointer">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('admin.empleados') }}" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</a>
            <button type="submit" class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Actualizar
            </button>
        </div>
    </form>
</div>
@endsection