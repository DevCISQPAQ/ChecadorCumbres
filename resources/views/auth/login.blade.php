<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        @if(session('success') || session('error'))
        <div class="{{ session('success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} p-3 rounded mb-4">
            {{ session('success') ?? session('error') }}
        </div>
        @endif
        <div class="flex justify-center mb-3">
            <img src="/img/sello-cumbres.svg" alt="Logo" class="h-20">
        </div>
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Iniciar Sesión</h2>

        @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="/admin">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Correo electrónico</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="ejemplo@correo.com">
            </div>

            <div class="mb-6 relative">
                <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="********">

                <!-- Botón para mostrar/ocultar -->
                <button type="button" id="togglePassword"
                    class="absolute right-3 top-9 text-gray-600 hover:text-gray-900 focus:outline-none">
                    Mostrar
                </button>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Ingresar
            </button>
        </form>
    </div>
</body>
</html>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        // Cambiar tipo del input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Cambiar texto del botón
        this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
    });
</script>