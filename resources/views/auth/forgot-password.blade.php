@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
<style>
    html, body {
        width: 100%;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden;
    }

    /* Contenedor pantalla completa */
    .fp-full {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;

       
        background-size: cover;

        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 0;
    }

    /* Capa blanca suave */
    .fp-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.55);
        z-index: 1;
    }

    /* Tarjeta */
    .fp-card {
        position: relative;
        z-index: 2;

        width: 100%;
        max-width: 420px;

        background: rgba(255,255,255,0.95);
        border-radius: 10px;
        border-top: 6px solid #77B900;

        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0,0,0,.25);
    }
</style>

<div class="fp-full">
    <div class="fp-overlay"></div>

    <div class="fp-card">

        <h4 class="text-center mb-2" style="color:#0b2e4d; font-weight:600;">
            ¿Olvidaste tu contraseña?
        </h4>

        <p class="text-center mb-4" style="color:#6b7a90;">
            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        @if (session('status'))
            <div class="alert alert-success text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Correo electrónico
                </label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="usuario@correo.com"
                       required>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit"
                    class="btn w-100 text-white fw-semibold"
                    style="background:#77B900;">
                Enviar enlace de recuperación
            </button>
        </form>

    </div>
</div>
@endsection
