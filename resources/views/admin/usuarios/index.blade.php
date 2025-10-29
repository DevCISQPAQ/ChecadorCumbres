@extends('layouts.admin') {{-- Usa tu layout base si tienes uno --}}
@section('content')
@php
$roles = [
0 => 'Usuario',
1 => 'Administrador',
2 => 'Súper Administrador',
];
@endphp
<div class="p-6 bg-white rounded shadow mb-4">
    <h2 class="text-xl font-bold mb-5">Configuraciones</h2>
    <a href="{{ route('admin.usuarios.configurar') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Editar Horario</a>
</div>

<div class="p-6 bg-white rounded shadow">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2 sm:gap-0">
        <h2 class="text-xl font-bold">Usuarios</h2>
        <div class="flex  gap-2 sm:gap-3.5">
            <a href="{{ route('admin.usuarios.crear') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Crear Usuario</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="max-h-[500px] overflow-y-auto border border-gray-300 rounded-lg">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 text-center py-2">Nombre</th>
                        <th class="px-4 text-center py-2">Correo</th>
                        <th class="px-4 text-center py-2">Rol</th>
                        <th class="px-4 text-center py-2">Notificaciones</th> 
                        <th class="px-4 text-center py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                    @if(auth()->user()->level_user < 2 && $usuario->level_user == 2)
                        @continue
                        @endif
                        <tr class="border-b">
                            <td class="px-4 text-center py-2">{{ $usuario->name }}</td>
                            <td class="px-4 text-center py-2 max-w-[200px] truncate">{{ $usuario->email }}</td>
                            <td class="px-4 text-center py-2">{{ $roles[$usuario->level_user] ?? 'Desconocido' }}</td>
                            <td class="px-4 text-center py-2">
                                {{ $usuario->yes_notifications ? 'Sí' : 'No' }}
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.usuarios.editar', $usuario->id) }}" class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('admin.usuarios.eliminar', $usuario->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection