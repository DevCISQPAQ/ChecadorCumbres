@extends('layouts.admin')

@section('content')

<div class="max-w-xl mx-auto">

    {{-- CONTENEDOR --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        {{-- ENCABEZADO --}}
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">

            <h2 class="text-xl font-bold text-gray-800">
                Crear nuevo usuario
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Registra una nueva cuenta para acceder al sistema.
            </p>

        </div>

        {{-- FORMULARIO --}}
        <div class="p-5">

            {{-- ERRORES --}}
            @if ($errors->any())

            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg">

                <p class="text-sm font-semibold mb-2">
                    Revisa los siguientes errores:
                </p>

                <ul class="list-disc pl-5 text-xs space-y-1">

                    @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

            @endif


            <form id="crear-user-form"
                method="POST"
                action="{{ route('admin.usuarios.guardar') }}">

                @csrf

                {{-- NOMBRES --}}
                <div class="mb-4">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Nombres
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="Ingresa los nombres"
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
                        value="{{ old('last_name') }}"
                        required
                        placeholder="Ingresa los apellidos"
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
                        id="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="ejemplo@correo.com"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                </div>


                {{-- CONTRASEÑA --}}
                <div class="mb-4">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="Ingresa una contraseña"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                </div>

                {{-- ROL --}}
                <div class="mb-4">

                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                        Rol del usuario
                    </label>

                    <select
                        name="level_user"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500">

                        <option value="0" {{ old('level_user', '0') == '0' ? 'selected' : '' }}>
                            Usuario
                        </option>

                        <option value="1" {{ old('level_user') == '1' ? 'selected' : '' }}>
                            Administrador
                        </option>

                        @if(auth()->user()->level_user > 1)

                        <option value="2" {{ old('level_user') == '2' ? 'selected' : '' }}>
                            Súper Administrador
                        </option>

                        @endif

                    </select>

                </div>


                {{-- NOTIFICACIONES --}}
                <div class="mb-5 p-3 bg-gray-50 border border-gray-200 rounded-lg">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="yes_notifications"
                            value="1"
                            {{ old('yes_notifications') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                        <div>

                            <span class="block text-sm font-semibold text-gray-700">
                                Recibir notificaciones
                            </span>

                            <span class="block text-xs text-gray-500">
                                Activar las notificaciones para esta cuenta.
                            </span>

                        </div>

                    </label>

                </div>


                {{-- BOTONES --}}
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">

                    <a
                        href="{{ route('admin.preferencias') }}"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">

                        Cancelar

                    </a>

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">

                        Guardar usuario

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection