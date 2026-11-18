<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cheko</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="shortcut icon" type="image/svg"
        href="{{ asset('/img/iconos/favicon.svg') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --vacio: #0A1033;
            --noche: #131F6B;
            --orbita: #2447FF;
            --pulso: #5C8DFF;
            --hielo: #00E0FF;
            --nebulosa: #DCE8FF;
            --niebla: #F1F5FF;

            --ausente: #D93025;
            --atiempo: #1E9E5A;
            --retardo: #F2A900;
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .lector-qr {
            background: var(--niebla);
            border: 2px dashed #5C8DFF;
        }

        .lector-qr::before {
            content: "";
            position: absolute;
            inset: 12px;
            border: 1px solid rgba(36, 71, 255, .10);
            border-radius: 10px;
            pointer-events: none;
        }

        .esquina {
            position: absolute;
            width: 23px;
            height: 23px;
            border-color: var(--orbita);
            border-style: solid;
            z-index: 2;
        }

        .esquina.tl {
            top: 9px;
            left: 9px;
            border-width: 3px 0 0 3px;
        }

        .esquina.tr {
            top: 9px;
            right: 9px;
            border-width: 3px 3px 0 0;
        }

        .esquina.bl {
            bottom: 9px;
            left: 9px;
            border-width: 0 0 3px 3px;
        }

        .esquina.br {
            bottom: 9px;
            right: 9px;
            border-width: 0 3px 3px 0;
        }

        #reader {
            border: none !important;
            width: 100% !important;
        }

        #reader video {
            border-radius: 10px;
        }

        #reader__dashboard_section_csr button,
        #reader button {
            background: var(--orbita) !important;
            color: white !important;
            border: none !important;
            border-radius: 7px !important;
            padding: 8px 14px !important;
            font-weight: 600 !important;
        }

        #reader select {
            border: 1px solid #DCE8FF !important;
            border-radius: 6px !important;
            padding: 6px !important;
        }

        .panel {
            background: #F1F5FF;
            border: 1px solid #DCE8FF;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(10, 16, 51, .05);
        }

        .panel-header {
            background: white;
            border-bottom: 1px solid #DCE8FF;
        }

        .btn-principal {
            background: var(--orbita);
            color: white;
            transition: background .2s, transform .2s;
        }

        .btn-principal:hover {
            background: var(--noche);
        }

        .btn-principal:active {
            transform: scale(.98);
        }

        @media (max-width: 767px) {
            .contenido-principal {
                padding: 18px 14px;
            }

            .paneles {
                grid-template-columns: 1fr;
            }

            .registro-manual {
                flex-direction: column;
                align-items: stretch;
            }

            .registro-manual button {
                width: 100%;
            }
        }
    </style>

</head>


<body class="min-h-screen flex flex-col bg-white text-[#0A1033]">

    {{-- HEADER --}}
    <header class="bg-[#0A1033] shadow-md">

        <div class="max-w-[1400px] mx-auto px-8 py-3 flex items-center justify-between">

            <img src="{{ asset('img/logos/svg/logo-recuadro.svg') }}"
                alt="cheko"
                class="w-44 h-auto">

            <div class="flex items-center gap-8 text-white">

                <div class="flex items-center gap-2">

                    <svg class="w-5 h-5 text-[#00E0FF]" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13H4V6a1 1 0 011-1z" />

                    </svg>

                    <span id="datetime" class="text-sm font-medium text-[#DCE8FF]"></span>

                </div>

                <div class="h-6 w-px bg-white/20"></div>

                <div class="flex items-center gap-2">

                    <svg class="w-5 h-5 text-[#00E0FF]" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">

                        <circle cx="12" cy="12" r="9" stroke-width="1.8" />

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 7v5l3 2" />

                    </svg>

                    <span id="timeonly" class="text-xl font-bold text-white"></span>

                </div>

            </div>

        </div>

        <div class="h-1 bg-[#00E0FF]"></div>

    </header>


    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 flex flex-col justify-center contenido-principal px-8 py-5">

        <div class="max-w-[1400px] w-full mx-auto">

            {{-- TITULO --}}
            <div class="flex items-center justify-between mb-5">

                <div>

                    <h1 class="text-2xl font-bold text-[#0A1033]">
                        Registro de asistencia
                    </h1>

                    <p class="text-sm text-[#5C8DFF] mt-1">
                        Escanea tu código QR o ingresa tu número de empleado.
                    </p>

                </div>

                <div class="flex items-center gap-2 bg-[#F1F5FF] border border-[#DCE8FF] rounded-lg px-4 py-2">

                    <span class="w-2.5 h-2.5 bg-[#1E9E5A] rounded-full"></span>

                    <span class="text-sm font-semibold text-[#0A1033]">
                        Sistema activo
                    </span>

                </div>

            </div>


            {{-- PANELES --}}
            <div class="paneles grid grid-cols-2 gap-5">


                {{-- LECTOR QR --}}
                <section class="panel overflow-hidden">

                    <div class="panel-header px-5 py-3 flex items-center gap-3">

                        <div class="w-9 h-9 rounded-lg bg-[#DCE8FF] flex items-center justify-center">

                            <svg class="w-6 h-6 text-[#2447FF]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h2v2h-2zm4 0h1v5h-5v-2" />

                            </svg>

                        </div>

                        <div>

                            <h2 class="font-bold text-[#0A1033]">
                                Escaneo QR
                            </h2>

                            <p class="text-xs text-[#5C8DFF]">
                                Acerca tu código al lector.
                            </p>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="lector-qr relative rounded-xl min-h-[310px] flex items-center justify-center overflow-hidden">

                            <div class="esquina tl"></div>
                            <div class="esquina tr"></div>
                            <div class="esquina bl"></div>
                            <div class="esquina br"></div>

                            {{-- CONSERVAR ID --}}
                            <div id="reader" class="w-full h-auto"></div>

                        </div>

                        <p class="text-center text-xs text-[#5C8DFF] mt-3 font-medium">
                            Coloca el código QR frente a la cámara.
                        </p>

                    </div>

                </section>



                {{-- RESULTADO EMPLEADO --}}
                <section id="cont_result"
                    x-data="{ hayResultado: false }"
                    x-init="
                    const observer = new MutationObserver(() => {
                        hayResultado = $refs.result.innerText.trim() !== '';
                    });

                    observer.observe($refs.result, {
                        childList: true,
                        subtree: true,
                        characterData: true
                    });"
                    class="panel overflow-hidden flex flex-col">


                    <div class="panel-header px-5 py-3 flex items-center gap-3">

                        <div class="w-9 h-9 rounded-lg bg-[#DCE8FF] flex items-center justify-center">

                            <svg class="w-6 h-6 text-[#2447FF]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M15 7.5a3 3 0 11-6 0 3 3 0 016 0zM4.5 20a7.5 7.5 0 0115 0" />

                            </svg>

                        </div>

                        <div>

                            <h2 class="font-bold text-[#0A1033]">
                                Información del empleado
                            </h2>

                            <p class="text-xs text-[#5C8DFF]">
                                Resultado del registro.
                            </p>

                        </div>

                    </div>


                    <div class="flex-1 flex flex-col items-center justify-center p-5">

                        <div class="w-40 h-40 rounded-xl border border-[#DCE8FF] bg-white overflow-hidden flex items-center justify-center">

                            <img id="foto-empleado"
                                src="{{ asset('img/iconos/icono-1024.png') }}"
                                alt="Foto empleado"
                                class="w-full h-full object-cover">

                        </div>


                        {{-- CONSERVAR ID --}}
                        <h2 id="nombre-empleado"
                            class="text-xl font-bold text-[#0A1033] text-center mt-4">
                        </h2>


                        {{-- CONSERVAR ID --}}
                        <div id="result"
                            x-ref="result"
                            class="mt-3 w-full max-w-md rounded-lg">

                            <p class="text-center text-[#0A1033] font-semibold"></p>

                        </div>


                        <div x-show="!hayResultado"
                            x-transition
                            class="mt-4 text-center">

                            <div class="w-12 h-1 bg-[#00E0FF] rounded-full mx-auto mb-3"></div>

                            <h3 class="text-lg font-bold text-[#0A1033]">
                                ¡Bienvenido!
                            </h3>

                            <p class="text-sm text-[#5C8DFF] mt-1">
                                Tu asistencia es importante.
                            </p>

                        </div>

                    </div>

                </section>

            </div>



            {{-- REGISTRO MANUAL --}}
            <section class="mt-5 bg-[#F1F5FF] border border-[#DCE8FF] rounded-xl shadow-sm p-5">

                <div class="registro-manual flex items-center justify-between gap-6">

                    <div class="flex items-center gap-3 min-w-[260px]">

                        <div class="w-10 h-10 bg-[#DCE8FF] rounded-lg flex items-center justify-center">

                            <svg class="w-6 h-6 text-[#2447FF]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                            </svg>

                        </div>

                        <div>

                            <h3 class="font-bold text-[#0A1033]">
                                Registro manual
                            </h3>

                            <p class="text-xs text-[#5C8DFF]">
                                Ingresa tu número de empleado.
                            </p>

                        </div>

                    </div>


                    <div class="flex flex-1 gap-3">

                        {{-- CONSERVAR ID --}}
                        <input id="numEmpleadoInput"
                            type="number"
                            inputmode="numeric"
                            placeholder="Número de empleado"
                            class="flex-1 min-w-0 bg-white border border-[#DCE8FF] rounded-lg px-5 py-3 text-base text-[#0A1033] placeholder:text-[#5C8DFF] outline-none focus:border-[#2447FF] focus:ring-2 focus:ring-[#5C8DFF]/20 transition">


                        {{-- CONSERVAR ID --}}
                        <button id="btnNumEmp"
                            type="button"
                            class="btn-principal font-bold px-8 py-3 rounded-lg flex items-center justify-center gap-3">

                            Ingresar

                            <svg class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 12h14m-6-6l6 6-6 6" />

                            </svg>

                        </button>

                    </div>

                </div>

            </section>

        </div>

    </main>



    {{-- FOOTER --}}
    <footer class="bg-[#0A1033] py-3 px-6">

        <p class="text-center text-xs text-[#DCE8FF]">

            &copy; {{ date('Y') }} cheko.

        </p>

    </footer>



    {{-- MODAL CONFIRMACION SALIDA --}}
    <div id="modalConfirmSalida"
        class="hidden fixed inset-0 justify-center items-center z-50 bg-[#0A1033]/60 backdrop-blur-sm">

        <div class="bg-white p-7 rounded-xl max-w-md w-11/12 text-center shadow-2xl border-t-4 border-[#2447FF]">

            <p id="mensajeConfirmSalida"
                class="mb-7 text-lg font-semibold text-[#0A1033]">
            </p>

            <div class="flex justify-center gap-3">

                <button id="btnConfirmarSalida"
                    class="px-7 py-2.5 bg-[#2447FF] text-white rounded-lg font-semibold hover:bg-[#131F6B] transition">
                    Sí
                </button>

                <button id="btnCancelarSalida"
                    class="px-7 py-2.5 bg-[#DCE8FF] text-[#0A1033] rounded-lg font-semibold hover:bg-[#F1F5FF] transition">
                    No
                </button>

            </div>

        </div>

    </div>

    {{-- LOADER --}}
    <div id="loader"
        style="display: none;"
        class="fixed inset-0 z-[9999] bg-[#0A1033]/20 backdrop-blur-[2px] items-center justify-center">

        <div class="w-14 h-14 border-4 border-[#DCE8FF] border-t-[#2447FF] rounded-full animate-spin"></div>

    </div>

</body>

</html>