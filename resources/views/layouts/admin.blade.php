<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración') | Cumbres</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/png"
        href="{{ asset('img/sello-cumbres-en-blanco-01.png') }}">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    x-data="{
        sidebarOpen: false,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        closeSidebar() {
            this.sidebarOpen = false;
        }
    }"
    @keydown.escape.window="closeSidebar()"
    class="h-screen overflow-hidden bg-[#F1F5FF] text-[#030614] antialiased">

    <div class="flex h-full w-full">

        {{-- SIDEBAR --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex h-full w-72 shrink-0
                   flex-col border-r border-slate-200 bg-white
                   shadow-xl transition-transform duration-300 ease-in-out
                   md:static md:z-30 md:translate-x-0 md:shadow-none">

            <x-admin-sidebar />

        </aside>

        {{-- OVERLAY MOVIL --}}
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            @click="closeSidebar()"
            class="fixed inset-0 z-40 bg-[#030614]/30 backdrop-blur-sm md:hidden">
        </div>

        {{-- CONTENEDOR --}}
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

            {{-- HEADER --}}
            <header class="relative z-20 flex min-h-[76px] shrink-0
                           items-center justify-between border-b border-slate-200
                           bg-white px-4 py-3 sm:px-6 lg:px-8">

                {{-- IZQUIERDA --}}
                <div class="flex min-w-0 items-center gap-3">

                    <button
                        type="button"
                        @click="toggleSidebar()"
                        aria-label="Abrir menú"
                        class="flex h-10 w-10 shrink-0 items-center justify-center
                               rounded-xl text-[#0A1033] transition
                               hover:bg-[#F1F5FF] focus:outline-none
                               focus:ring-2 focus:ring-[#2447FF]/30 md:hidden">

                        <svg x-show="!sidebarOpen"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <svg x-show="sidebarOpen"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate text-base font-bold text-[#0A1033] sm:text-lg">
                            @yield('page-title', 'Control de Asistencias')
                        </h1>

                        <p class="hidden text-xs text-slate-500 sm:block">
                            Panel de administración
                        </p>
                    </div>

                </div>

                {{-- DERECHA --}}
                <div class="flex shrink-0 items-center gap-3 sm:gap-5">

                    {{-- USUARIO --}}
                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-[#DCE8FF] text-sm font-bold
                                    text-[#131F6B]">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden min-w-0 sm:block">
                            <p class="max-w-[160px] truncate text-sm font-semibold text-[#0A1033]">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                Administrador
                            </p>
                        </div>

                    </div>

                    <div class="hidden h-9 w-px bg-slate-200 sm:block"></div>

                    {{-- CERRAR SESIÓN --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="flex items-center gap-2 rounded-xl
                                   border border-slate-200 bg-white px-3 py-2
                                   text-sm font-semibold text-slate-600
                                   transition hover:border-red-200
                                   hover:bg-red-50 hover:text-[#D93025]">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>

                            <span class="hidden sm:inline">Salir</span>
                        </button>
                    </form>

                </div>

            </header>

            {{-- CONTENIDO --}}
            <main class="flex-1 overflow-y-auto px-4 py-5 sm:px-6 sm:py-6 lg:px-8">

                {{-- ALERTAS --}}
                @if(session('success') || session('error') || $errors->any())

                @php
                $isSuccess = session()->has('success');
                $message = session('success')
                ?? session('error')
                ?? $errors->first();
                @endphp

                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition
                    class="mb-6 flex items-center justify-between gap-3
                        rounded-xl border p-4
                        {{ $isSuccess
                            ? 'border-green-200 bg-green-50 text-[#166534]'
                            : 'border-red-200 bg-red-50 text-[#991B1B]' }}">

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium">
                            {{ $message }}
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="show = false"
                        class="shrink-0 opacity-60 hover:opacity-100"
                        aria-label="Cerrar alerta">
                        ✕
                    </button>

                </div>

                @endif

                {{-- VISTAS --}}
                @yield('content')

            </main>

        </div>

    </div>

    {{-- LOADER --}}
    <div
        id="loader"
        style="display:none;"
        class="fixed inset-0 z-[9999] items-center justify-center
               bg-[#030614]/20 backdrop-blur-sm">

        <div class="flex flex-col items-center gap-4 rounded-2xl
                    bg-white p-8 shadow-xl">

            <div class="h-12 w-12 animate-spin rounded-full border-4
                        border-[#DCE8FF] border-t-[#2447FF]">
            </div>

            <p class="text-sm font-semibold text-[#0A1033]">
                Procesando...
            </p>

        </div>

    </div>

</body>

</html>