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
        <div class="right-panel">
            <!-- Decoración de hojas -->
            <div class="leaves-decoration">
                <svg class="leaf leaf-top" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <g opacity="0.4">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                        <path d="M110,30 Q135,55 110,80" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <path d="M90,30 Q65,55 90,80" fill="none" stroke="#ffffff" stroke-width="2"/>
                    </g>
                    <g opacity="0.4" transform="translate(60, 20)">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                    </g>
                    <g opacity="0.4" transform="translate(120, 10)">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                    </g>
                </svg>
                
                <svg class="leaf leaf-bottom" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <g opacity="0.4">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                        <path d="M110,30 Q135,55 110,80" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <path d="M90,30 Q65,55 90,80" fill="none" stroke="#ffffff" stroke-width="2"/>
                    </g>
                    <g opacity="0.4" transform="translate(-60, 30)">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                    </g>
                    <g opacity="0.4" transform="translate(30, 50)">
                        <path d="M100,20 Q140,60 100,100 Q60,60 100,20" fill="#ffffff"/>
                    </g>
                </svg>
            </div>
            
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