@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Crear nuevo empleado</h2>
    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
        <ul class="list-disc pl-4 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="crear-empleado-form" method="POST" action="{{ route('admin.empleados.guardar') }}" enctype="multipart/form-data">
        @csrf

        <div class="flex justify-between gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Numeró de empleado</label>
                <input type="number" name="id" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Nombres</label>
                <input type="text" name="nombres" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div class="flex justify-between gap-4">
            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Apellido paterno</label>
                <input type="text" name="apellido_paterno" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Apellido materno</label>
                <input type="text" name="apellido_materno" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div class="flex justify-between gap-4">
            <div class="mb-4  w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Departamento</label>
                <select name="departamento" class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="Academia">Academia</option>
                    <option value="Administración">Administración</option>
                    <option value="Dirección">Dirección</option>
                    <option value="Preescolar">Preescolar</option>
                    <option value="Primaria">Primaria</option>
                    <option value="Promoción">Promoción</option>
                    <option value="Secundaria">Secundaria</option>
                    <option value="Mantenimiento">Mantenimiento</option>


                </select>
            </div>

            <div class="mb-4  w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Puesto</label>
                <input type="text" name="puesto" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
            </div>
        </div>

        <div class="flex justify-between gap-4">

            <div class="mb-4  w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Tipo de horario</label>
                <select name="tipo_horario" class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="Horario Base">Horario Base</option>
                    <option value="Horario Libre">Horario Libre</option>
                </select>
            </div>

            <div class="mb-4 w-1/2">
                <label class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
                <input type="email" name="email" id="email" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
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
                Guardar
            </button>
        </div>
    </form>
</div>

@endsection