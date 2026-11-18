@extends('layouts.admin')

@section('content')

<div class="max-w-xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- ENCABEZADO --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">
            Editar usuario
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Modifica la información y los permisos del usuario.
        </p>
    </div>

    <div class="p-6">

        {{-- ERRORES --}}
        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg">
            <ul class="list-disc pl-4 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORMULARIO --}}
        <form id="editar-user-form"
            method="POST"
            action="{{ route('admin.usuarios.actualizar', $usuario->id) }}">

            @csrf
            @method('PUT')

            {{-- NOMBRES --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Nombres
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $usuario->name) }}"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
            </div>

            {{-- APELLIDOS --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Apellidos
                </label>

                <input
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $usuario->last_name) }}"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
            </div>

            {{-- CORREO --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $usuario->email) }}"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
            </div>

            {{-- CONTRASEÑA --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Nueva contraseña
                </label>

                <input
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                <p class="text-[11px] text-gray-400 mt-1">
                    Déjalo en blanco si no deseas cambiarla.
                </p>
            </div>

            {{-- ROL --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Rol del usuario
                </label>

                <select
                    name="level_user"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                    <option value="0" {{ old('level_user', $usuario->level_user) == 0 ? 'selected' : '' }}>
                        Usuario
                    </option>

                    <option value="1" {{ old('level_user', $usuario->level_user) == 1 ? 'selected' : '' }}>
                        Administrador
                    </option>

                    @if(auth()->user()->level_user > 1)
                    <option value="2" {{ old('level_user', $usuario->level_user) == 2 ? 'selected' : '' }}>
                        Súper Administrador
                    </option>
                    @endif

                </select>
            </div>

            {{-- NOTIFICACIONES --}}
            <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded-lg">

                <label class="flex items-center gap-2 cursor-pointer">

                    <input
                        type="checkbox"
                        name="yes_notifications"
                        value="1"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        {{ old('yes_notifications', $usuario->yes_notifications) ? 'checked' : '' }}>

                    <span class="text-xs font-medium text-gray-700">
                        Recibir notificaciones
                    </span>

                </label>

            </div>

            {{-- BOTONES --}}
            <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">

                <a
                    href="{{ route('admin.preferencias') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">

                    Cancelar

                </a>

                <button
                    type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">

                    Actualizar usuario

                </button>

            </div>

        </form>

    </div>

</div>

@endsection