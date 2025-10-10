<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SCIDES - Inicio de Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>
<body>
    <div class="container">
        <!-- Panel Izquierdo -->
        <div class="left-panel">
            <div class="logo-sena">
                <img src="{{ asset('images/logo-sena.png') }}" alt="Logo SENA">
            </div>
            <div class="scides-title">SCIDES</div>
        </div>
        
        <!-- Panel Derecho -->
        <div class="right-panel" style="background-image: url('{{ asset('images/Loginfondo.png') }}');">
            
            <!-- Formulario -->
            <div class="login-form">
                <h2 class="form-title">Inicio de sesión</h2>
                
                @if($errors->has('error'))
                    <div class="alert alert-error">{{ $errors->first('error') }}</div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Usuario:</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autocomplete="email"
                            autofocus
                            placeholder="Ingresa tu correo"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password">contraseña:</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            autocomplete="current-password"
                            placeholder="Ingresa tu contraseña"
                        >
                    </div>
                    
                    <button type="submit" class="btn-entrar">ENTRAR</button>
                </form>
                
                <div class="forgot-password">
                    <a href="#">¿Olvidaste tú contraseña?</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>