<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SCIDES - Login</title>
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <img src="{{ asset('images/logo-sena.png') }}" alt="Logo" class="logo">
            <div class="scides-title">SCIDES</div>
        </div>

        <div class="right-panel">
            <div class="leaf" style="top: 10%; right: 15%;"></div>
            <div class="leaf" style="top: 20%; right: 8%;"></div>
            <div class="leaf" style="top: 15%; right: 25%;"></div>
            <div class="leaf" style="top: 25%; right: 18%;"></div>
            <div class="leaf" style="bottom: 15%; left: 10%;"></div>
            <div class="leaf" style="bottom: 25%; left: 18%;"></div>
            <div class="leaf" style="bottom: 20%; left: 5%;"></div>

            <div class="login-box">
                <h2 class="login-title">Inicio de sesión</h2>
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
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="Ingresa tu contraseña">
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
</body>

</html>
