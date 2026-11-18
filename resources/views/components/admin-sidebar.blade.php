<div class="flex h-full flex-col bg-white">

    {{-- LOGO Y MARCA --}}
    <div class="flex min-h-[105px] items-center justify-center border-b border-slate-100 px-6 py-5">
        <img
            src="{{ asset('img/logos/svg/logo-recuadro.svg') }}"
            alt="Logo Cheko"
            class="w-auto h-auto max-h-[85px] max-w-[220px] object-contain">
    </div>


    {{-- NAVEGACIÓN --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">

        <p class="mb-3 px-3 text-[11px] font-bold uppercase
                  tracking-wider text-slate-400">
            Menú principal
        </p>

        {{-- DASHBOARD --}}
        <a
            href="{{ url('/admin/dashboard') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                   text-sm font-medium transition-all duration-200
                   {{ request()->is('admin/dashboard')
                        ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                        : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z" />
            </svg>

            <span>Dashboard</span>

            @if(request()->is('admin/dashboard'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        {{-- ASISTENCIAS --}}
        <a
            href="{{ route('admin.asistencias') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                   text-sm font-medium transition-all duration-200
                   {{ request()->routeIs('admin.asistencias*')
                        ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                        : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>

            <span>Asistencias</span>

            @if(request()->routeIs('admin.asistencias*'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        {{-- EMPLEADOS --}}
        @if(auth()->user()->level_user)

        <a
            href="{{ route('admin.empleados') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                       text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.empleados*')
                            ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                            : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M17 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12-13a4 4 0 11-8 0 4 4 0 018 0zm3 13v-2a4 4 0 00-3-3.87" />
            </svg>

            <span>Empleados</span>

            @if(request()->routeIs('admin.empleados*'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        @endif

        {{-- ADMINISTRACIÓN --}}
        @if(auth()->user()->level_user >= 1)

        <p class="mb-3 mt-8 px-3 text-[11px] font-bold uppercase
                      tracking-wider text-slate-400">
            Administración
        </p>

        {{-- AUSENCIAS --}}
        <a
            href="{{ route('admin.ausencias.index') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                       text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.ausencias*')
                            ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                            : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zm4 10l2 2 4-4" />
            </svg>

            <span>Ausencias</span>

            @if(request()->routeIs('admin.ausencias*'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        {{-- FESTIVOS --}}
        <a
            href="{{ route('admin.festivos.index') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                       text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.festivos*')
                            ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                            : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zm3 10h2m4 0h2m-8 3h2m4 0h2" />
            </svg>

            <span>Festivos</span>

            @if(request()->routeIs('admin.festivos*'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        {{-- PREFERENCIAS --}}
        <a
            href="{{ route('admin.preferencias') }}"
            class="group flex items-center gap-3 rounded-xl px-3 py-3
                       text-sm font-medium transition-all duration-200
                       {{ request()->routeIs('admin.preferencias*')
                            ? 'bg-[#2447FF] text-white shadow-md shadow-[#2447FF]/20'
                            : 'text-slate-600 hover:bg-[#F1F5FF] hover:text-[#2447FF]' }}">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M12 8a4 4 0 100 8 4 4 0 000-8zm0-5v2m0 14v2m9-9h-2M5 12H3m15.36-6.36l-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0l-1.42-1.42M7.06 7.06L5.64 5.64" />
            </svg>

            <span>Preferencias</span>

            @if(request()->routeIs('admin.preferencias*'))
            <span class="ml-auto h-2 w-2 rounded-full bg-white"></span>
            @endif

        </a>

        @endif

    </nav>

    {{-- PIE DEL SIDEBAR --}}
    <div class="border-t border-slate-100 p-5">

        <div class="flex items-center gap-3 rounded-xl bg-[#F1F5FF] p-3">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-lg bg-white text-[#2447FF] shadow-sm">

                <img
                    src="{{ asset('img/iconos/icono-512.png') }}"
                    alt="Mi icono"
                    class="h-5 w-5 object-contain">

            </div>

            <div class="min-w-0">
                <p class="text-xs font-bold text-[#0A1033]">
                    cheko
                </p>

                <p class="text-[11px] text-slate-500">
                    Sistema de gestión
                </p>
            </div>

        </div>

        <p class="mt-4 text-center text-[10px] text-slate-400">
            © {{ date('Y') }} Panel Administrativo
        </p>

    </div>

</div>