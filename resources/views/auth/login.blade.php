
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="shortcut icon" type="image/svg" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
    <link rel="shortcut icon" sizes="192x192" href="{{ asset('/img/sello-cumbres-en-blanco-01.png') }}">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm bg-white rounded-xl shadow-md border border-gray-200 p-7">

        {{-- MENSAJES --}}
        @if(session('success') || session('error'))
        <div class="{{ session('success') ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }} border p-3 rounded-lg mb-5 text-sm">
            {{ session('success') ?? session('error') }}
        </div>
        @endif

        {{-- LOGO --}}
        <div class="flex justify-center mb-3">
            <img src="/img/sello-cumbres.svg" alt="Logo" class="h-20">
        </div>

        {{-- TITULO --}}
        <div class="text-center mb-7">
            <h2 class="text-xl font-bold text-gray-800">
                Iniciar sesión
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Ingresa tus credenciales para continuar
            </p>
        </div>

        {{-- ERRORES --}}
        @if ($errors->any())
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORMULARIO --}}
        <form method="POST" action="/admin">
            @csrf

            {{-- CORREO --}}
            <div class="mb-5">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-100
                    focus:border-blue-500 transition"
                    placeholder="ejemplo@correo.com">

            </div>

            {{-- CONTRASEÑA --}}
            <div class="mb-6 relative">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Contraseña
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3 py-2.5 pr-20 border border-gray-300 rounded-lg text-sm
                    focus:outline-none focus:ring-2 focus:ring-blue-100
                    focus:border-blue-500 transition"
                    placeholder="Ingresa tu contraseña">

                <button
                    type="button"
                    id="togglePassword"
                    class="absolute right-3 top-9 text-xs font-medium text-blue-600 hover:text-blue-800 cursor-pointer">

                    Mostrar

                </button>

            </div>

            {{-- BOTÓN --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white
                font-semibold text-sm py-2.5 px-4 rounded-lg
                transition duration-200 cursor-pointer">

                Iniciar sesión

            </button>

        </form>

        {{-- FOOTER --}}
        <div class="border-t border-gray-100 mt-7 pt-4 text-center">
            <p class="text-xs text-gray-400">
                Cumbres International School
            </p>
        </div>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {

            const type = password.getAttribute('type') === 'password'
                ? 'text'
                : 'password';

            password.setAttribute('type', type);

            this.textContent = type === 'password'
                ? 'Mostrar'
                : 'Ocultar';

        });
    </script>

</body>
</html>
