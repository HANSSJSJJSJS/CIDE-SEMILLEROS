@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">Editar usuario</h1>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
</div>

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

    {{-- Nombre --}}
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control"
             value="{{ old('nombre', $usuario->name) }}" required>
    </div>

    {{-- Apellido --}}
    <div class="col-md-6">
      <label class="form-label">Apellido</label>
      <input type="text" name="apellido" class="form-control"
             value="{{ old('apellido', $usuario->apellidos) }}" required>
    </div>

    {{-- Email --}}
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control"
             value="{{ old('email', $usuario->email) }}" required>
    </div>

    {{-- Rol (lectura por defecto) --}}
    <div class="col-md-6">
      <label class="form-label d-flex align-items-center gap-2">
        Rol <span class="badge text-bg-light">solo lectura</span>
      </label>

      @php
        // Si el controlador pasó $roles, úsalo; si no, arma una lista por defecto:
        $rolesList = isset($roles) ? $roles : [
          'ADMIN'           => 'ADMIN',
          'LIDER_GENERAL'   => 'LIDER GENERAL',
          'LIDER_SEMILLERO' => 'LIDER_SEMILLERO',
          'APRENDIZ'        => 'APRENDIZ',
        ];
        // Normaliza valor mostrado (en BD usas 'LIDER GENERAL' con espacio)
        $rolActual = $usuario->role === 'LIDER GENERAL' ? 'LIDER_GENERAL' : $usuario->role;
      @endphp

      <select class="form-select" disabled>
        @foreach($rolesList as $key => $label)
          <option value="{{ $key }}" @selected($rolActual === $key)>{{ $label }}</option>
        @endforeach
      </select>

      {{-- Si en el futuro quieres habilitar edición, quita "disabled" y añade name="role" --}}
      {{-- <select name="role" class="form-select"> ... </select> --}}
    </div>

    {{-- Semillero (lectura por defecto) --}}
    <div class="col-md-6">
      <label class="form-label d-flex align-items-center gap-2">
        Semillero <span class="badge text-bg-light">solo lectura</span>
      </label>

      @php
        // Si el controlador pasó $semilleros, úsalo; si no, deja un arreglo vacío:
        $semillerosList = isset($semilleros) ? $semilleros : collect();
        // Busca el semillero actual si el controlador lo pasó aparte
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

      {{-- Si en el futuro quieres habilitar edición, quita "disabled" y añade name="semillero_id" --}}
      {{-- <select name="semillero_id" class="form-select"> ... </select> --}}
    </div>

    {{-- Password opcional (si decides habilitarlo en update) --}}
    {{-- 
    <div class="col-md-6">
      <label class="form-label">Password (opcional)</label>
      <input type="password" name="password" class="form-control">
      <div class="form-text">Déjalo vacío para no cambiarlo.</div>
    </div>
    --}}
  </div>

  <div class="card-footer d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-success">Guardar cambios</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
@endsection
