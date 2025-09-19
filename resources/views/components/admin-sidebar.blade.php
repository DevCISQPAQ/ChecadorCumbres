<div x-data="{ sidebarOpen: false }" @click.away="sidebarOpen = false">
    {{-- Título del panel --}}
    <div class="p-6 font-bold text-dm text-[#5D7E8D] border-b border-gray-300 ">
        <div class="flex justify-center mb-3">
            <img src="/img/sello-cumbres.svg" alt="Logo" class="h-24">
        </div>
        Panel de Control de Asistencias
    </div>

    {{-- Navegación --}}
    <nav class="mt-6 flex-1 overflow-y-auto">
        {{-- Solo mostrar este enlace si el usuario es administrador --}}
        @if(auth()->user()->level_user)
        <a href="{{ url('/admin/dashboard') }}"
            class="block py-2.5 px-4 {{ request()->is('admin/dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }} hover:bg-blue-50 transition-colors">
            Dashboard
        </a>
        @endif
        <a href="{{ route('admin.empleados.index') }}"
            class="block py-2.5 px-4 {{ request()->routeIs('admin.empleados.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }} hover:bg-blue-50 transition-colors">
            Empleados
        </a>

        {{-- Solo mostrar este enlace si el usuario es administrador --}}
        @if(auth()->user()->level_user >= 1)
        <a href="{{ route('admin.usuarios') }}"
            class="block py-2.5 px-4 {{ request()->routeIs('admin.usuarios*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700' }} hover:bg-blue-50 transition-colors">
            Usuarios
        </a>
        @endif
    </nav>
    </nav>
    {{-- Pie de menú opcional --}}
    <div class="p-4 text-sm text-gray-500 border-t border-gray-300">
        &copy; {{ date('Y') }} Panel Admin
    </div>
</div>