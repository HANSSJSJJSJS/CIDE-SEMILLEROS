@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider_semi/perfil.css') }}">
@endpush

@section('content')
@php($user = auth()->user())
@php($lider = $user?->liderSemillero)
<div class="container mt-4 perfil-modern" style="max-width: 980px;">
    <div class="mb-3">
        <h3 class="fw-bold mb-1 profile-title">Mi Perfil</h3>
        <p class="mb-2 profile-subtitle">Gestiona tu información personal y de contacto</p>
    </div>

    <div class="alert alert-guidance d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2 icon"></i>
        <div>Actualiza tu contraseña para mayor seguridad</div>
    </div>

    <div class="card card-profile p-4 mb-3">
        <div class="card-header">
            <i class="fas fa-user card-icon"></i>
            <span class="card-title section-title">Información Personal</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombres</label>
                <input type="text" class="form-control form-input" value="{{ $user?->name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control form-input" value="{{ $user?->apellidos }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipo de Documento</label>
                <input type="text" class="form-control form-input" value="{{ $lider?->tipo_documento ?? '—' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Número de Documento</label>
                <input type="text" class="form-control form-input" value="{{ $lider?->documento ?? '—' }}" disabled>
            </div>
        </div>
    </div>

    <div class="card card-profile p-4 mb-3">
        <div class="card-header">
            <i class="fas fa-envelope card-icon"></i>
            <span class="card-title section-title">Información de Contacto</span>
        </div>
        <form id="form-contacto" method="POST" action="{{ route('lider_semi.perfil.contacto.update') }}" class="mt-2">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Correo Institucional</label>
                    <div class="input-icon-right">
                        <input type="text" class="form-control form-input with-trailing-icon" value="{{ $lider?->correo_institucional ?? '—' }}" disabled>
                        <span class="trailing-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Correo Personal</label>
                    <div class="input-icon-right">
                        <input id="email" name="email" type="email" class="form-control form-input with-trailing-icon" value="{{ old('email', $user?->email) }}" readonly required>
                        <button type="button" class="trailing-btn toggle-edit" data-target="#email" title="Editar correo" aria-label="Editar correo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-pen"><path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></svg>
                        </button>
                    </div>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <div class="input-icon-right">
                        <input id="telefono" name="telefono" type="text" class="form-control form-input with-trailing-icon" value="{{ old('telefono', $lider?->telefono ?? $user?->telefono ?? '') }}" placeholder="Ej: 3001234567" readonly>
                        <button type="button" class="trailing-btn toggle-edit" data-target="#telefono" title="Editar teléfono" aria-label="Editar teléfono">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="bi bi-pen"><path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></svg>
                        </button>
                    </div>
                    @error('telefono')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </form>
    </div>

    <div class="card card-profile p-4 mb-3">
        <div class="card-header">
            <i class="fas fa-graduation-cap card-icon"></i>
            <span class="card-title section-title">Información Académica</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Especialidad</label>
                <input type="text" class="form-control form-input" value="{{ $lider?->especialidad ?? '—' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol en el Semillero</label>
                <input type="text" class="form-control form-input" value="Líder de Semillero" disabled>
            </div>
        </div>
    </div>

    <div class="card card-warning password-card p-4 mb-3">
        <div class="card-header">
            <i class="fas fa-key card-icon"></i>
            <span class="card-title section-title">Cambio de Contraseña</span>
        </div>
        <form method="POST" action="{{ route('lider_semi.perfil.password.update') }}" class="mt-2">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="current_password" class="form-label">Contraseña Actual</label>
                    <div class="input-icon-right">
                        <input id="current_password" name="current_password" type="password" class="form-control form-input with-trailing-icon" placeholder="Ingresa tu contraseña actual" required>
                        <button type="button" class="trailing-btn toggle-visibility" data-target="#current_password" aria-label="Mostrar/Ocultar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <div class="input-icon-right">
                        <input id="password" name="password" type="password" class="form-control form-input with-trailing-icon" placeholder="Ingresa nueva contraseña" required>
                        <button type="button" class="trailing-btn toggle-visibility" data-target="#password" aria-label="Mostrar/Ocultar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <div class="input-icon-right">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-input with-trailing-icon" placeholder="Confirma la nueva contraseña" required>
                        <button type="button" class="trailing-btn toggle-visibility" data-target="#password_confirmation" aria-label="Mostrar/Ocultar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-3 small password-hint d-flex align-items-start gap-2">
                <i class="fas fa-circle-exclamation mt-1"></i>
                <span>La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.</span>
            </div>
            <button type="submit" class="btn btn-secondary btn-brand-success"><i class="fas fa-key me-1"></i> Cambiar Contraseña</button>
        </form>
    </div>

    <div class="profile-actions button-group">
        <a href="{{ route('lider_semi.dashboard') }}" class="btn btn-outline btn-outline-secondary"><i class="fas fa-xmark me-1"></i> Cancelar</a>
        <button type="button" class="btn btn-primary btn-brand-primary"><i class="fas fa-check me-1"></i> Guardar Cambios</button>
    </div>
</div>
@endsection

<script>
// Habilitar edición al hacer clic en el lápiz (inline para asegurar carga)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-edit');
    if (!btn) return;
    const selector = btn.getAttribute('data-target');
    if (!selector) return;
    const input = document.querySelector(selector);
    if (!input) return;
    input.readOnly = false;
    input.removeAttribute('readonly');
    input.classList.add('is-editing');
    input.focus();
    try {
        const len = input.value.length;
        input.setSelectionRange(len, len);
    } catch (err) {}
});

// Enviar formulario de contacto desde el botón inferior
document.addEventListener('click', function(e){
    const saveBtn = e.target.closest('.btn.btn-primary.btn-brand-primary');
    if (!saveBtn) return;
    const form = document.getElementById('form-contacto');
    if (form) form.submit();
});

// Toggle mostrar/ocultar contraseña
document.addEventListener('click', function(e){
    const btn = e.target.closest('.toggle-visibility');
    if (!btn) return;
    const selector = btn.getAttribute('data-target');
    const input = document.querySelector(selector);
    if (!input) return;
    const isPassword = input.getAttribute('type') === 'password';
    input.setAttribute('type', isPassword ? 'text' : 'password');
    // Alternar ícono bi-eye / bi-eye-slash
    const svg = btn.querySelector('svg');
    if (svg) {
        svg.classList.toggle('bi-eye');
        svg.classList.toggle('bi-eye-slash');
    }
});
</script>

