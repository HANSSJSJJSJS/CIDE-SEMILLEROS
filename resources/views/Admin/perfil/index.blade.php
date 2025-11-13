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

<<<<<<< HEAD
    <div class="mb-3">
        <h3 class="fw-bold mb-1 profile-title">Mi Perfil</h3>
        <p class="mb-2 profile-subtitle">Gestiona tu información personal y de contacto</p>
=======
    @php($CURRENT_ROLE = strtoupper(str_replace([' ', '-'], '_', auth()->user()->role ?? '')))
    {{-- Datos de cuenta --}}
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Datos de cuenta</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.perfil.update') }}" novalidate>
          @csrf
          @method('PUT')

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre de usuario</label>
              <input
                type="text"
                name="name"
                value="{{ old('name', auth()->user()->name) }}"
                class="form-control @error('name') is-invalid @enderror"
                @if($CURRENT_ROLE==='LIDER_INTERMEDIARIO') disabled @endif
                required
              >
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="form-hint mt-1">Cómo aparecerás en el sistema.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input
                type="email"
                name="email"
                value="{{ old('email', auth()->user()->email) }}"
                class="form-control @error('email') is-invalid @enderror"
                @if($CURRENT_ROLE==='LIDER_INTERMEDIARIO') disabled @endif
                required
              >
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <div class="form-hint mt-1">Usado para iniciar sesión y notificaciones.</div>
            </div>
          </div>

          @unless($CURRENT_ROLE==='LIDER_INTERMEDIARIO')
            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-save me-1"></i> Guardar cambios
              </button>
              <a class="btn btn-outline-secondary" href="{{ url()->current() }}">Cancelar</a>
            </div>
          @endunless
        </form>
      </div>
>>>>>>> PreFu
    </div>

    {{-- QUITADO: contenedor interno .card card-profile p-4 mb-3 --}}
    {{-- Ahora todo cuelga directamente y se separa con .content-stack --}}
    <div class="content-stack">

<<<<<<< HEAD
        {{-- Alertas de éxito / error --}}
        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-1">
                    <i class="bi bi-exclamation-octagon me-2"></i>Corrige lo siguiente:
                </div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
=======
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
>>>>>>> PreFu
            </div>
        @endif

<<<<<<< HEAD
        {{-- Datos de cuenta --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Datos de cuenta</h5>
=======
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
>>>>>>> PreFu
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.perfil.update') }}" novalidate>
                    @csrf
                    @method('PUT')

<<<<<<< HEAD
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de usuario</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', auth()->user()->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-hint mt-1">Cómo aparecerás en el sistema.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', auth()->user()->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-hint mt-1">Usado para iniciar sesión y notificaciones.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save me-1"></i> Guardar cambios
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ url()->current() }}">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Cambiar contraseña</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.perfil.password.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Contraseña actual</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    required
                                    id="current_password">
                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="current_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                    minlength="8"
                                    id="password">
                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Confirmar contraseña</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    required
                                    minlength="8"
                                    id="password_confirmation">
                                <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-lock me-1"></i> Cambiar contraseña
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ url()->current() }}">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

    </div> {{-- /.content-stack --}}

</div> {{-- /.container --}}
=======
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
>>>>>>> PreFu
@endsection
