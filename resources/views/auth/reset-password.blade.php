@extends('layouts.guest')

@section('title', 'Restablecer contraseña')

@section('content')
<div class="fp-full">
    <div class="fp-overlay"></div>

    <div class="fp-card" style="max-width:760px; width:100%;">
        <div class="pf-block perfil-admin">

            <div class="pf-head pass">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill fs-5"></i>
                    <strong>Restablecer contraseña</strong>
                </div>
                <span class="pf-badge" style="background:#e7f5d9;color:#2f6b1c;">
                    Seguridad
                </span>
            </div>

            <div class="p-3 p-md-4">
                <p class="text-muted mb-3">
                    Ingresa tu nueva contraseña para recuperar el acceso a tu cuenta.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/reset-password" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label pf-label">Correo electrónico</label>
                        <input type="email"
                               name="email"
                               class="form-control pf-input"
                               value="{{ old('email', $email ?? '') }}"
                               readonly>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label pf-label">Nueva contraseña *</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       id="pf_password"
                                       class="form-control pf-input"
                                       required>
                                <button class="btn btn-outline-secondary toggle-pass"
                                        type="button"
                                        data-target="pf_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label pf-label">Confirmar contraseña *</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       id="pf_password_confirmation"
                                       class="form-control pf-input"
                                       required>
                                <button class="btn btn-outline-secondary toggle-pass"
                                        type="button"
                                        data-target="pf_password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3 py-2 px-3">
                        La contraseña debe tener:
                        <ul class="mb-1">
                            <li>Mínimo 10 caracteres</li>
                            <li>Al menos una mayúscula</li>
                            <li>Al menos un número</li>
                        </ul>

                        <div class="progress pf-progress mt-2">
                            <div class="progress-bar" id="pf_strength_bar"></div>
                        </div>
                        <div class="mt-1" id="pf_strength_msg"></div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn pf-btn" type="submit">
                            <i class="bi bi-lock-fill me-1"></i>
                            Cambiar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
