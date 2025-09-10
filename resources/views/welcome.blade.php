<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador Cumbres</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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
                    <p id="result" class="text-center mt-4 font-bold text-lg text-gray-700">
                        Esperando escaneo...
                    </p>
                </div>
                <!-- Div 2 -->
                <div class="content-Result">
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('img/escudo-gris.png') }}" alt="Logo" class="w-44 2xl:w-60 h-auto">
                    </div>
                    <h2 class="text-result">
                        Bienvenido: <span>Lalo</span>
                    </h2>
                </div>
            </div>
            <div class="pt-6 w-full flex justify-center">
                <div>
                    <h1 class="text-numEmpl">
                        Si no cuenta con código QR ingrese su numero de empleado</h1>
                    <div class="flex gap-3 pt-4">
                        <input type="number" class="imput-numEmp" placeholder="Escribe tu numero de empleado" />
                        <button class="btn-numEmp">
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