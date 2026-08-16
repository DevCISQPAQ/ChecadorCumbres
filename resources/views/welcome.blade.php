<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador Cumbres</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" type="image/svg"
        href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192"
        href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>


<body class="min-h-screen flex flex-col bg-slate-100 text-slate-700"
    style="font-family: 'Roboto', sans-serif;">

    <!-- ========================================================= -->
    <!-- HEADER -->
    <!-- ========================================================= -->

    <header>
        <!-- Barra azul -->
        <div class="banner">
            <img src="{{ asset('img/escuworblan.png') }}"
                alt="Cumbres International School"
                class="h-auto w-45 2xl:w-80">
        </div>


        <!-- Fecha y hora -->
        <div class="w-full bg-white shadow-sm">
            <div class="max-w-7xl mx-auto h-[30px] flex items-center justify-center">
                <div class="flex items-center gap-4 text-[#607d9f] text-base font-medium">

                    <!-- Icono calendario -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3.75 9.75h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v12A1.5 1.5 0 0118.75 20.25H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                    <div id="datetime"></div>
                    <div class="h-6 w-px bg-slate-300"></div>
                    <!-- Icono reloj -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <circle cx="12"
                            cy="12"
                            r="8.5" />
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 7.5v5l3.25 2" />
                    </svg>

                    <div id="timeonly"></div>
                </div>
            </div>
        </div>
    </header>



    <!-- ========================================================= -->
    <!-- CONTENIDO PRINCIPAL -->
    <!-- ========================================================= -->

    <main class="flex-grow relative overflow-hidden">
        <!-- Decoración superior derecha -->
        <div class="absolute top-8 right-8 opacity-40 pointer-events-none hidden lg:block">

            <div class="grid grid-cols-6 gap-3">

                @for ($i = 0; $i < 36; $i++)
                    <div class="w-1.5 h-1.5 rounded-full bg-[#b8c9dd]">
            </div>
            @endfor

        </div>
        </div>


        <!-- Decoración inferior -->
        <div class="absolute -bottom-24 -left-20 w-[500px] h-[180px]
                    bg-white/50 rounded-[50%] pointer-events-none">
        </div>

        <div class="absolute -bottom-28 right-[-80px] w-[600px] h-[220px]
                    bg-white/40 rounded-[50%] pointer-events-none">
        </div>

        <section class="relative z-10 max-w-6xl mx-auto px-5 md:px-8 py-10 md:py-4">


            <!-- ================================================= -->
            <!-- TITULO -->
            <!-- ================================================= -->
            <div class="text-center mb-3">
                <h1 class="text-xl md:text-2xl font-black uppercase
                           tracking-wide text-[#12396d]">
                    Registra tu asistencia
                </h1>
                <div class="mx-auto mt-1 h-1 w-32 rounded-full bg-[#ff5900]"></div>
            </div>



            <!-- ================================================= -->
            <!-- TARJETAS PRINCIPALES -->
            <!-- ================================================= -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <!-- ================================================= -->
                <!-- QR -->
                <!-- ================================================= -->

                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgba(31,60,90,0.10)]
                            border border-slate-200 overflow-hidden">

                    <div class="p-4">
                        <!-- Encabezado -->
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-10 h-10 rounded-full
                                        bg-orange-50
                                        flex items-center justify-center
                                        text-[#ff5900]">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-7 h-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 4h5v5H4V4zm11 0h5v5h-5V4zM4 15h5v5H4v-5zm11 2h2m2 0h1m-5 3h2m3 0h2M12 4v3m0 3v2m0 3v5" />
                                </svg>
                            </div>

                            <div>
                                <h2 class="text-lg md:text-xl font-bold
                                           text-[#12396d]">
                                    Escanea aquí tu código QR
                                </h2>

                                <p class="text-xs text-slate-500">
                                    Coloca tu código QR dentro del área
                                    para registrar tu asistencia.
                                </p>
                            </div>
                        </div>

                        <!-- Lector QR -->
                        <div class="relative rounded-xl overflow-hidden
                                    border-2 border-dashed border-[#b7c9dc]
                                    bg-slate-50 min-h-[300px]
                                    flex items-center justify-center">

                            <!-- Decoración esquinas -->
                            <div class="absolute top-4 left-4
                                        w-5 h-5 border-l-2 border-t-2
                                        border-[#ff5900]">
                            </div>
                            <div class="absolute top-4 right-4
                                        w-5 h-5 border-r-2 border-t-2
                                        border-[#ff5900]">
                            </div>
                            <div class="absolute bottom-4 left-4
                                        w-5 h-5 border-l-2 border-b-2
                                        border-[#ff5900]">
                            </div>
                            <div class="absolute bottom-4 right-4
                                        w-5 h-5 border-r-2 border-b-2
                                        border-[#ff5900]">
                            </div>
                            <!-- IMPORTANTE:
                                 Se conserva el ID reader -->

                            <div id="reader" class="w-full  h-auto"></div>
                        </div>
                        <div class="text-center text-xs font-bold
                                    text-[#ff5900] mt-2 uppercase">
                            Escanea aquí tu código QR
                        </div>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- RESULTADO / EMPLEADO -->
                <!-- ================================================= -->

                <div id="cont_result"
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
                    class="bg-white rounded-2xl
                           shadow-[0_8px_30px_rgba(31,60,90,0.10)]
                           border border-slate-200
                           flex flex-col items-center justify-center p-4">
                    <!-- Imagen -->
                    <div class="flex justify-center mb-6">
                        <div class="w-48 h-48 
                                    overflow-hidden rounded-2xl
                                    flex items-center justify-center">
                            <img id="foto-empleado"
                                src="{{ asset('img/escudo-gris.png') }}"
                                alt="Foto empleado"
                                class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Nombre -->
                    <h2 id="nombre-empleado"
                        class="text-xl md:text-2xl font-bold
                               text-[#12396d] text-center">
                    </h2>
                    <!-- Resultado -->
                    <div id="result"
                        x-ref="result"
                        class="mt-4 w-full max-w-md rounded-xl">
                        <p class="text-center font-bold text-lg text-gray-700">
                        </p>
                    </div>
                    <!-- Texto bienvenida -->
                    <div x-show="!hayResultado"
                        x-transition
                        class="mt-4 text-center">
                        <p class="text-xl font-bold text-[#12396d]">
                            Bienvenido
                        </p>
                        <p class="text-slate-500 mt-1">
                            Tu asistencia es importante
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================================================= -->
            <!-- DIVISOR -->
            <!-- ================================================= -->
            <div class="flex items-center gap-6 my-3">
                <div class="flex-1 h-px bg-[#b9c9da]"></div>
                <h2 class="text-center text-base 
                           text-[#607d9f] font-medium">
                    Si no cuenta con código QR ingrese su número de empleado
                </h2>
                <div class="flex-1 h-px bg-[#b9c9da]"></div>
            </div>

            <!-- ================================================= -->
            <!-- NUMERO EMPLEADO -->
            <!-- ================================================= -->
            <div class="flex justify-center">
                <div class="w-full max-w-3xl">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Input -->
                        <div class="relative flex-1">
                            <!-- Icono -->
                            <div class="absolute left-5 top-5
                                        -translate-y-1/2
                                        text-[#7590ad]">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                                </svg>
                            </div>
                            <input id="numEmpleadoInput"
                                type="number"
                                placeholder="Escribe tu número de empleado"
                                class="w-full p-2 pl-14 pr-5
                                       bg-white
                                       border border-slate-200
                                       rounded-xl
                                       shadow-[0_5px_20px_rgba(31,60,90,0.08)]
                                       outline-none
                                       text-lg
                                       text-slate-700
                                       placeholder:text-slate-400
                                       focus:border-[#7392b3]
                                       focus:ring-2
                                       focus:ring-[#7392b3]/20
                                       transition">
                        </div>
                        <!-- Botón -->
                        <button id="btnNumEmp"
                            type="button"
                            class="p-2 px-9
                                   bg-[#082f68]
                                   hover:bg-[#061f47]
                                   text-white
                                   rounded-xl
                                   font-bold
                                   text-lg
                                   shadow-[0_6px_18px_rgba(8,47,104,0.25)]
                                   hover:shadow-[0_8px_22px_rgba(8,47,104,0.30)]
                                   transition-all duration-200
                                   flex items-center justify-center gap-4
                                   sm:min-w-[180px]">
                            <span>
                                Ingresar
                            </span>

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>



    <!-- ========================================================= -->
    <!-- FOOTER -->
    <!-- ========================================================= -->

    <footer class="bg-white border-t border-slate-200">

        <div class="max-w-7xl mx-auto py-2 px-4">

            <h2 class="text-center text-sm md:text-base
                       text-slate-400">

                &copy; {{ date('Y') }}
                Desarrollado e implementado por el Depto.
                de Tecnologías de la Información.

            </h2>

        </div>

    </footer>



    <!-- ========================================================= -->
    <!-- MODAL CONFIRMACIÓN SALIDA -->
    <!-- ========================================================= -->

    <div id="modalConfirmSalida"
        class="hidden fixed inset-0 justify-center items-center
               z-50 bg-black/50 backdrop-blur-sm">
        <div class="bg-white p-7 rounded-2xl
                    max-w-md w-11/12
                    text-center
                    shadow-2xl">
            <p id="mensajeConfirmSalida"
                class="mb-7 text-lg font-semibold text-slate-700">
            </p>
            <div class="flex justify-center gap-3">
                <button id="btnConfirmarSalida"
                    class="px-7 py-2.5
                           bg-green-600
                           text-white
                           rounded-lg
                           font-semibold
                           hover:bg-green-700
                           transition">
                    Sí
                </button>
                <button id="btnCancelarSalida"
                    class="px-7 py-2.5
                           bg-red-600
                           text-white
                           rounded-lg
                           font-semibold
                           hover:bg-red-700
                           transition">
                    No
                </button>

            </div>

        </div>

    </div>



    <!-- ========================================================= -->
    <!-- LOADER -->
    <!-- ========================================================= -->

    <div id="loader"
        style="display: none;"
        class="fixed inset-0 z-[9999]
               bg-black/20 backdrop-blur-[2px]
               items-center justify-center">

        <div class="w-16 h-16
                    border-4
                    border-slate-300
                    border-t-[#ff5900]
                    rounded-full
                    animate-spin">
        </div>

    </div>


</body>

</html>