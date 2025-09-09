<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador Cumbres</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="bg-[#00205C] m-0 h-auto flex items-center justify-center">
            <img src="{{ asset('img/escuworblan.png') }}" alt="Logo" class="h-auto w-64">
        </div>
    </header>

    <main>
        <div class="pt-10">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center text-[#5E7B96] uppercase">Registra tu asistencia</h1>
        </div>
    </main>

</body>

</html>