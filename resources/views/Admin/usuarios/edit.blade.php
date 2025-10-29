@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">Editar usuario</h1>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
</div>

{{-- Mensajes de estado --}}
@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="card shadow-sm">
  @csrf
  @method('PUT')

  <div class="card-body row g-3">

    {{-- =========================================
         CAMPOS PRINCIPALES
    ========================================== --}}
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control"
             value="{{ old('nombre', $usuario->name) }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Apellido</label>
      <input type="text" name="apellido" class="form-control"
             value="{{ old('apellido', $usuario->apellidos) }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Correo</label>
      <input type="email" name="email" class="form-control"
             value="{{ old('email', $usuario->email) }}" required>
    </div>

    {{-- =========================================
         ROL (solo lectura)
    ========================================== --}}
    <div class="col-md-6">
      <label class="form-label d-flex align-items-center gap-2">
        Rol <span class="badge text-bg-light">solo lectura</span>
      </label>

      @php
        // Lista de roles visibles: claves = valor real en BD, etiquetas = texto bonito
        $rolesList = [
          'ADMIN'           => 'Líder general',
          'LIDER_SEMILLERO' => 'Líder semillero',
          'APRENDIZ'        => 'Aprendiz',
        ];

        // Normaliza: si el usuario tiene role='ADMIN', el label será “Líder general”
        $rolActual = $usuario->role;
      @endphp

      {{-- Deshabilitado (solo mostrar, no editar) --}}
      <select class="form-select" disabled>
        @foreach($rolesList as $key => $label)
          <option value="{{ $key }}" @selected($rolActual === $key)>
            {{ $label }}
          </option>
        @endforeach
      </select>

      {{-- 
        Si en el futuro deseas permitir edición de rol, 
        elimina “disabled” y añade name="role"
      --}}
    </div>

    {{-- =========================================
         SEMILLERO (solo lectura)
    ========================================== --}}
    <div class="col-md-6">
      <label class="form-label d-flex align-items-center gap-2">
        Semillero <span class="badge text-bg-light">solo lectura</span>
      </label>

      @php
        // Lista de semilleros si el controlador la pasó
        $semillerosList = isset($semilleros) ? $semilleros : collect();
        $semilleroIdActual = old('semillero_id', $usuario->semillero_id ?? null);
      @endphp

      <select class="form-select" disabled>
        <option value="">— Ninguno —</option>
        @foreach($semillerosList as $s)
          <option value="{{ $s->id_semillero }}" @selected($semilleroIdActual == $s->id_semillero)>
            {{ $s->nombre }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- =========================================
         CONTRASEÑA (opcional, si habilitas cambio)
    ========================================== --}}
    {{-- 
    <div class="col-md-6">
      <label class="form-label">Contraseña (opcional)</label>
      <input type="password" name="password" class="form-control">
      <div class="form-text">Déjala vacía para no cambiarla.</div>
    </div>
    --}}
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-success">Guardar cambios</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
@endsection
