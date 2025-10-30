@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('content')
@php($user = auth()->user())
@php($lider = $user?->liderSemillero)
<div class="container mt-4" style="max-width: 980px;">
    <div class="mb-3">
        <h3 class="fw-bold mb-1 profile-title">Mi Perfil</h3>
        <p class="mb-2 profile-subtitle">Gestiona tu información personal y de contacto.</p>
        <hr class="hr-soft">
    </div>

    <div class="alert alert-guidance d-flex align-items-center" role="alert">
        <i class="fas fa-info-circle me-2 icon"></i>
        <div>Sólo puedes editar tu nombre y contraseña. Los demás datos son de sólo lectura.</div>
    </div>

    <div class="card card-profile p-4 mb-3">
        <h5 class="mb-3 section-title"><i class="fas fa-user me-2 icon"></i>Información Personal</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombres</label>
                <input type="text" class="form-control" value="{{ $user?->name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control" value="{{ $user?->apellidos }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipo de Documento</label>
                <input type="text" class="form-control" value="{{ $lider?->tipo_documento ?? '—' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Número de Documento</label>
                <input type="text" class="form-control" value="{{ $lider?->documento ?? '—' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol en el sistema</label>
                <input type="text" class="form-control" value="{{ $user?->role ?? '—' }}" disabled>
            </div>
        </div>
    </div>

    <div class="card card-profile p-4 mb-3">
        <h5 class="mb-3 section-title"><i class="fas fa-envelope me-2 icon"></i>Información de Contacto</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Correo Institucional</label>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ $lider?->correo_institucional ?? '—' }}" disabled>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo en la cuenta</label>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ $user?->email }}" disabled>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-profile p-4 mb-3">
        <h5 class="mb-3 section-title"><i class="fas fa-graduation-cap me-2 icon"></i>Información Académica</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Especialidad</label>
                <input type="text" class="form-control" value="{{ $lider?->especialidad ?? '—' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rol en el Semillero</label>
                <input type="text" class="form-control" value="Líder de Semillero" disabled>
            </div>
        </div>
    </div>

    <div class="card card-warning p-4 mb-3">
        <h5 class="mb-3 section-title"><i class="fas fa-key me-2"></i>Cambio de Contraseña</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Contraseña Actual</label>
                <input type="password" class="form-control" placeholder="Ingresa tu contraseña actual">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control" placeholder="Ingresa nueva contraseña">
            </div>
            <div class="col-md-4">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" placeholder="Confirma la nueva contraseña">
            </div>
        </div>
        <div class="mt-3 small text-muted d-flex align-items-start gap-2">
            <i class="fas fa-info-circle mt-1"></i>
            <span>La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.</span>
        </div>
    </div>

    <div class="profile-actions">
        <a href="{{ route('lider_semi.dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancelar</a>
        <button type="button" class="btn btn-brand-success"><i class="fas fa-key me-1"></i> Cambiar Contraseña</button>
        <button type="button" class="btn btn-brand-primary"><i class="fas fa-lock me-1"></i> Guardar Cambios</button>
    </div>
</div>
@endsection

