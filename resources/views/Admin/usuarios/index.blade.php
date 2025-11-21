@extends('layouts.admin')

@section('module-title','Gestión de Usuarios')
@section('module-subtitle','Administra roles, estados y semilleros')

@section('content')

@php
    $user      = auth()->user();
    $canCreate = $user->canManageModule('usuarios','create');
    $canUpdate = $user->canManageModule('usuarios','update');
    $canDelete = $user->canManageModule('usuarios','delete');
@endphp

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

              @if($canCreate)
                    <button type="button"
                            class="btn btn-nuevo-semillero"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCrearUsuario">
                      <i class="bi bi-person-plus me-1"></i> Nuevo
                    </button>
                  @endif
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
                        'ADMIN'           => 'Líder general',
                        'LIDER_SEMILLERO' => 'Líder de semillero',
                        'APRENDIZ'        => 'Aprendiz',
                        default           => ($u->role ?? '—'),
                    };

                  $roleColor = match($u->role ?? '') {
                      'ADMIN'           => 'bg-danger',
                      'LIDER_SEMILLERO' => 'bg-success',
                      'APRENDIZ'        => 'bg-primary',
                      default           => 'bg-secondary',
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
                    @if($canUpdate)
                      <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                         class="btn btn-sm btn-accion-editar">
                        <i class="bi bi-pencil me-1"></i> Editar
                      </a>
                    @endif

                    @if($canDelete)
                      <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                            method="POST"
                            onsubmit="return confirm('¿Deseas eliminar este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-accion-eliminar" type="submit">
                          <i class="bi bi-trash me-1"></i> Eliminar
                        </button>
                      </form>
                    @endif

                    @if(!$canUpdate && !$canDelete)
                      <span class="text-muted small">Sin permisos</span>
                    @endif
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

@if($canCreate)
  @include('admin.usuarios._modal_crear')
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/usuarios.css') }}">
@endpush
