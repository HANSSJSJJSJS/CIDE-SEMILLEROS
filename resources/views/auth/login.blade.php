<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SCIDES - Login</title>

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <div class="sena-badge">
        <img src="{{ asset('images/logo-sena.png') }}" alt="Logo SENA">
    </div>
    <div class="container">
        <div class="left-panel">
        </div>

        <div class="right-panel">
            <img src="{{ asset('images/Loginfondo.png') }}" alt="Logo CIDE" class="login-background">

            <div class="login-form">
                <h2 class="login-title">INICIO DE SESIÓN</h2>

                @if ($errors->has('error'))
                    <div class="alert alert-error">{{ $errors->first('error') }}</div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" autofocus placeholder="ejemplo@correo.com">
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Campo de contraseña con iconos -->
                    <div class="form-group password-container">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="Ingresa tu contraseña">
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-entrar">ENTRAR</button>
                </form>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para alternar visibilidad de la contraseña -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            // Cambiar tipo de input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Cambiar icono
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>
