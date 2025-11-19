@extends('layouts.admin')

@section('module-title','Gestión de Usuarios')
@section('module-subtitle','Administra roles, estados y semilleros')

@section('content')

<div class="container-fluid mt-3 px-3">

    {{-- FILTROS + BOTONES --}}
    <form method="GET" class="card border-0 shadow-sm mb-3 glass-card">
      <div class="card-body">
        <div class="row g-3 align-items-end">

          {{-- Rol --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Rol</label>
            <select name="role" class="form-select">
              <option value="">Todos</option>
              @php 
                $roleSelected = $roleFilter ?? request('role'); 
              @endphp
              @foreach(($roles ?? []) as $value => $label)
                <option value="{{ $value }}" @selected($roleSelected === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          {{-- Semillero --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">Semillero</label>
            <select name="semillero_id" class="form-select">
              <option value="">Todos los semilleros</option>
              @foreach(($semilleros ?? collect()) as $s)
                <option value="{{ $s->id_semillero }}"
                  @selected( (old('semillero_id', $semilleroId ?? request('semillero_id'))) == $s->id_semillero )>
                  {{ $s->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Buscar --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Buscar por nombre</label>
            <input type="text" name="q" class="form-control"
                   value="{{ old('q', $q ?? request('q')) }}"
                   placeholder="Nombre, apellido o email">
          </div>

          {{-- Botones --}}
          <div class="col-12 col-md-2 col-lg-3">
            <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-nuevo-semillero" type="submit">
                <i class="bi bi-search me-1"></i> Buscar
              </button>

              <a href="{{ route('admin.usuarios.index') }}" class="btn btn-accion-ver">
                <i class="bi bi-x-lg me-1"></i> Limpiar
              </a>

              <a href="{{ route('admin.usuarios.create') }}" class="btn btn-nuevo-semillero">
                <i class="bi bi-person-plus me-1"></i> Nuevo
              </a>
            </div>
          </div>

        </div>
      </div>
    </form>

    {{-- TABLA DE USUARIOS --}}
    <div class="card border-0 shadow-sm glass-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 tabla-usuarios">
            <thead>
              <tr>
                <th class="py-3 px-4">Usuario</th>
                <th class="py-3">Rol</th>
                <th class="py-3">Semillero</th>
                <th class="py-3">Estado</th>
                <th class="py-3">Última actividad</th>
                <th class="py-3 text-end pe-4">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse($usuarios as $u)
                @php
                  $nombreCompleto = trim(($u->name ?? '').' '.($u->apellidos ?? ''))
                    ?: ($u->nombre_completo ?? 'Usuario');

                  $activo = $u->estado === 'Activo' || ($u->is_active ?? false);

                  $roleLabel = $u->role_label
                    ?? match($u->role ?? null) {
                        'ADMIN' => 'Líder general',
                        'LIDER_SEMILLERO' => 'Líder de semillero',
                        'APRENDIZ' => 'Aprendiz',
                        default => ($u->role ?? '—'),
                    };

                  $roleColor = match($u->role ?? '') {
                      'ADMIN' => 'bg-danger',
                      'LIDER_SEMILLERO' => 'bg-success',
                      'APRENDIZ' => 'bg-primary',
                      default => 'bg-secondary',
                  };

                  $last = $u->last_login_at ?? $u->updated_at ?? null;
                @endphp

                <tr>
                  <td class="py-3 px-4">
                    <div class="fw-semibold">{{ $nombreCompleto }}</div>
                    <small class="text-muted">{{ $u->email ?? 'Sin correo' }}</small>
                  </td>

                  <td class="py-3">
                    <span class="badge {{ $roleColor }}">{{ $roleLabel }}</span>
                  </td>

                  <td class="py-3">{{ $u->semillero_nombre ?? '—' }}</td>

                  <td class="py-3">
                    <span class="badge {{ $activo ? 'bg-success' : 'bg-secondary' }}">
                      {{ $activo ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>

                  <td class="py-3">
                    {{ $last ? \Carbon\Carbon::parse($last)->locale('es')->diffForHumans() : '—' }}
                  </td>

                  <td class="py-3 text-end pe-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                       class="btn btn-sm btn-accion-editar">
                      <i class="bi bi-pencil me-1"></i> Editar
                    </a>

                    <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Deseas eliminar este usuario?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-accion-eliminar" type="submit">
                        <i class="bi bi-trash me-1"></i> Eliminar
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    No hay usuarios registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- PAGINACIÓN --}}
    @if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
      <div class="mt-3">
        {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    @endif

  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">

<style>
/* Glass para tarjeta de filtros y tabla */
.glass-card {
    background: rgba(255,255,255,0.45) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border-radius: 14px !important;
    border: 1px solid rgba(255,255,255,0.25) !important;
}

/* Mantenemos el fondo transparente para el cuerpo de la tabla */
.glass-card table {
    --bs-table-bg: transparent !important;
    --bs-table-striped-bg: rgba(255,255,255,0.15) !important;
    --bs-table-hover-bg: rgba(255,255,255,0.25) !important;
}
.glass-card tbody tr,
.glass-card tbody td {
    background: transparent !important;
}

/* Encabezado SOLO de esta tabla de usuarios */
.tabla-usuarios thead th {
    background-color: #0b2e4d !important;   /* tu blue-900 */
    color: #ffffff !important;
    font-size: 0.85rem;                     /* más pequeño */
    font-weight: 600;
    padding-top: 0.55rem;
    padding-bottom: 0.55rem;
    letter-spacing: 0.03em;
    border-bottom: 2px solid rgba(255,255,255,0.25) !important;
}

/* Hover un poco más notorio, manteniendo transparencia */
.tabla-usuarios.table-hover tbody tr:hover {
    background-color: rgba(11, 46, 77, 0.28) !important;
    transition: background-color 0.15s ease-in-out;
}
</style>
@endpush
