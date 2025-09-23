<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador Cumbres</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body style="font-family: 'Roboto', sans-serif; font-style: italic;" class="min-h-screen flex flex-col">

    <header>
        <div class="banner">
            <img src="{{ asset('img/escuworblan.png') }}" alt="Logo" class="h-auto w-45 2xl:w-80">
        </div>
    </header>

    <main class="flex-grow">
        <div class="pb-8">
            <div class="banner-time">
                <div id="datetime" class="text-date"></div>
                <div id="timeonly" class="text-time"></div>
            </div>
            <h1 class="titleHome">Registra tu asistencia</h1>
        </div>

        <section class="bg-white">
            <div class="content-scanResult">
                <!-- Div lector QR -->
                <div class="content-QR">
                    <div id="reader" class="w-full h-auto"></div>
                </div>
                <!-- Div de resultado -->
                <div class="content-Result">
                    <div class="flex justify-center mb-4">
                        <img id="foto-empleado" src="{{ asset('img/escudo-gris.png') }}" alt="Logo" class="w-44 2xl:w-60 h-auto pt-7">
                    </div>
                    <h2 class="text-result" id="nombre-empleado">
                        <!-- Bienvenido <span id="nombre-empleado">Lalo</span> -->
                    </h2>
                    <div id="result"  class="mt-auto w-full">
                        <p class="text-center font-bold text-lg text-gray-700"> </p>
                    </div>
                </div>
            </div>
            <div class="pt-6 w-full flex justify-center">
                <div>
                    <h1 class="text-numEmpl">
                        Si no cuenta con código QR ingrese su numero de empleado
                    </h1>
                    <div class="flex gap-3 pt-4">
                        <input id="numEmpleadoInput" type="number" class="imput-numEmp" placeholder="Escribe tu numero de empleado" />
                        <button id="btnNumEmp" class="btn-numEmp" type="button"> <!-- type="button" evita submit -->
                            Ingresar
                        </button>
                    </div>
                </div>
            </div>

        </section>
    </main>

    <footer>
        <h2 class=" text-gray-600/50 text-center pb-1 italic">&copy; {{ date('Y') }} Desarrollado e implementado por el Depto. de Tecnologías de la Información.</h2>
    </footer>
</body>

</html>