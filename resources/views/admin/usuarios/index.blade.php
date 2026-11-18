@extends('layouts.admin')

@section('content')

@php
$roles = [
0 => 'Usuario',
1 => 'Administrador',
2 => 'Súper Administrador',
];
@endphp

{{-- ENCABEZADO --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Configuraciones
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Administración de usuarios y permisos del sistema
            </p>
        </div>

        <a href="{{ route('admin.configuraciones') }}"
            class="inline-flex justify-center items-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

            Departamentos / Horarios

        </a>

    </div>

</div>


{{-- CONTENEDOR USUARIOS --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-4 border-b border-gray-200">

        <div>
            <h2 class="text-lg font-bold text-gray-800">
                Usuarios registrados
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Consulta y administra las cuentas del sistema
            </p>
        </div>

        <a href="{{ route('admin.usuarios.crear') }}"
            class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">

            + Crear usuario

        </a>

    </div>


    {{-- TABLA --}}
    <div class="overflow-x-auto">

        <div class="max-h-[500px] overflow-y-auto">

            <table class="min-w-full">

                <thead class="sticky top-0 z-10 bg-gray-50 border-b border-gray-200">

                    <tr class="text-[11px] uppercase tracking-wider text-gray-500">

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Nombre
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Correo
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Rol
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Notificaciones
                        </th>

                        <th class="px-4 py-3 text-center whitespace-nowrap">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 text-xs">

                    @foreach ($usuarios as $usuario)

                    @if(auth()->user()->level_user < 2 && $usuario->level_user == 2)
                        @continue
                        @endif

                        <tr class="hover:bg-blue-50/50 transition-colors">

                            {{-- NOMBRE --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">

                                <span class="font-semibold text-gray-800">
                                    {{ $usuario->name }}
                                </span>

                            </td>

                            {{-- CORREO --}}
                            <td class="px-4 py-3 text-center max-w-[220px]">

                                <span class="block truncate text-gray-600"
                                    title="{{ $usuario->email }}">

                                    {{ $usuario->email }}

                                </span>

                            </td>

                            {{-- ROL --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">

                                @if($usuario->level_user == 2)

                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                    {{ $roles[$usuario->level_user] }}
                                </span>

                                @elseif($usuario->level_user == 1)

                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                    {{ $roles[$usuario->level_user] }}
                                </span>

                                @else

                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                    {{ $roles[$usuario->level_user] ?? 'Desconocido' }}
                                </span>

                                @endif

                            </td>

                            {{-- NOTIFICACIONES --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">

                                @if($usuario->yes_notifications)

                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-[10px] font-semibold">
                                    Activadas
                                </span>

                                @else

                                <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-[10px] font-semibold">
                                    Desactivadas
                                </span>

                                @endif

                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-4 py-3 whitespace-nowrap">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDITAR --}}
                                    <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                                        class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">

                                        Editar

                                    </a>

                                    {{-- ELIMINAR --}}
                                    <form
                                        action="{{ route('admin.usuarios.eliminar', $usuario->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">

                                            Eliminar

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                        {{-- CUANDO NO HAY USUARIOS --}}
                        @if($usuarios->isEmpty())

                        <tr>

                            <td colspan="5" class="px-4 py-12 text-center">

                                <p class="text-sm font-semibold text-gray-600">
                                    No hay usuarios registrados
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Puedes crear un usuario utilizando el botón superior.
                                </p>

                            </td>

                        </tr>

                        @endif

                </tbody>

            </table>

        </div>

    </div>

    {{-- PIE --}}
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">

        <p class="text-xs text-gray-500">
            Total de usuarios registrados: {{ $usuarios->count() }}
        </p>

    </div>

</div>

@endsection