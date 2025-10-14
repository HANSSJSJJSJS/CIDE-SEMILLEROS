<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-semibold text-gray-800 dark:text-gray-100">
            {{ config('app.name', 'Laravel') }}
        </a>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    👤 {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                        Cerrar sesión
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Registrarse
                </a>
            @endguest
        </div>
    </div>
</nav>
