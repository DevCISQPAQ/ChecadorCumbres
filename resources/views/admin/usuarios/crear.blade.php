@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Crear nuevo usuario</h2>

    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 p-3 rounded">
        <ul class="list-disc pl-4 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="crear-user-form" method="POST" action="{{ route('admin.usuarios.guardar') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700">Nombres</label>
            <input type="text" name="name" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
        </div>

         <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700">Apellidos</label>
            <input type="text" name="last_name" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
            <input type="email" name="email" id="email" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700">Contraseña</label>
            <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700">Rol</label>
            <select name="level_user" class="w-full mt-1 px-4 py-2 border rounded focus:ring focus:ring-blue-200">
                <option value="0">Usuario</option>
                <option value="1">Administrador</option>
                @if(auth()->user()->level_user >1)
                <option value="2">Super Administrador</option>
                @endif
            </select>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="yes_notifications" value="1" class="form-checkbox text-blue-600">
                <span class="ml-2 text-sm text-gray-700">Recibir notificaciones</span>
            </label>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('admin.preferencias') }}" class="px-4 py-2 text-gray-600 hover:underline">Cancelar</a>
            <button type="submit" class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection