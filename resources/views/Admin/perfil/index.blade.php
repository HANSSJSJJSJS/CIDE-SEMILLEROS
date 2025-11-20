@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
<style>
  /* Espaciado entre bloques principales dentro del contenido */
  .content-stack > * + * { margin-top: 1.25rem; } /* ~20px */
</style>
@endpush

@push('scripts')
    {{-- Scripts originales del index --}}
@endpush

@section('content')
<div class="container mt-4 perfil-modern" style="max-width: 980px;">

    <div class="mb-3">
        <h3 class="fw-bold mb-1 profile-title">Mi Perfil</h3>
        <p class="mb-2 profile-subtitle">Gestiona tu información personal y de contacto</p>
    </div>

    {{-- QUITADO: contenedor interno .card card-profile p-4 mb-3 --}}
    {{-- Ahora todo cuelga directamente y se separa con .content-stack --}}
    <div class="content-stack">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Contraseña actual</label>
              <div class="input-group">
                <input
                  type="password"
                  name="current_password"
                  class="form-control @error('current_password') is-invalid @enderror"
                  @if($CURRENT_ROLE==='LIDER_INTERMEDIARIO') disabled @endif
                  required
                  id="current_password"
                >
                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="current_password">
                  <i class="bi bi-eye"></i>
                </button>
                @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Nueva contraseña</label>
              <div class="input-group">
                <input
                  type="password"
                  name="password"
                  class="form-control @error('password') is-invalid @enderror"
                  @if($CURRENT_ROLE==='LIDER_INTERMEDIARIO') disabled @endif
                  required
                  minlength="8"
                  id="password"
                >
                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="password">
                  <i class="bi bi-eye"></i>
                </button>
                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>
              <div class="form-hint mt-1">Mínimo 8 caracteres, ideal mezclar mayúsculas, números y símbolos.</div>
            </div>
        @endif

            <div class="col-md-4">
              <label class="form-label">Confirmar nueva contraseña</label>
              <div class="input-group">
                <input
                  type="password"
                  name="password_confirmation"
                  class="form-control"
                  @if($CURRENT_ROLE==='LIDER_INTERMEDIARIO') disabled @endif
                  required
                  id="password_confirmation"
                >
                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="password_confirmation">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div id="pc-match" class="form-hint mt-1"></div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.perfil.update') }}" novalidate>
                    @csrf
                    @method('PUT')

          @unless($CURRENT_ROLE==='LIDER_INTERMEDIARIO')
            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-key-fill me-1"></i> Actualizar contraseña
              </button>
              <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
            </div>
          @endunless
        </form>
      </div>
    </div>
  </div>
@endsection
