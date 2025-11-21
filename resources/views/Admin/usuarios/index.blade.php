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

<div class="container-fluid mt-3 px-3 gestion-usuarios-wrapper">

  {{-- FILTROS + BARRA DE BÚSQUEDA --}}
  <form method="GET" class="mb-3">
    <div class="row g-3 align-items-end">

      {{-- Rol --}}
      <div class="col-md-3 col-lg-3">
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
      <div class="col-md-4 col-lg-4">
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

      {{-- Barra de búsqueda grande --}}
      <div class="col-md-5 col-lg-5">
        <label class="form-label fw-semibold">Buscar por nombre, apellido o correo</label>
        <div class="search-bar-wrapper">
          <input type="text"
                 name="q"
                 class="form-control"
                 value="{{ old('q', $q ?? request('q')) }}"
                 placeholder="Ej. Juan Pérez, correo@correo.com">
          <button type="submit">
            <i class="bi bi-search text-white"></i>
          </button>
        </div>
      </div>

      {{-- Botones secundarios --}}
      <div class="col-12 d-flex flex-wrap gap-2 justify-content-between justify-content-md-end mt-2">
        <div>
          <a href="{{ route('admin.usuarios.index') }}" class="btn btn-accion-ver">
            <i class="bi bi-x-lg me-1"></i> Limpiar filtros
          </a>
        </div>

        <div class="d-flex flex-wrap gap-2">
          @if($canCreate)
            <button type="button"
                    class="btn btn-nuevo-semillero"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearUsuario">
              <i class="bi bi-person-plus me-1"></i> Nuevo usuario
            </button>
          @endif
        </div>
      </div>

    </div>
  </form>

  {{-- TABLA DE USUARIOS (mismo estilo que semilleros) --}}
  <div class="tabla-semilleros-wrapper mt-2">
    <div class="table-responsive">
      <table class="table table-hover mb-0 tabla-semilleros tabla-usuarios">
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
                    'ADMIN'               => 'Líder general',
                    'LIDER_SEMILLERO'     => 'Líder de semillero',
                    'LIDER_INVESTIGACION' => 'Líder de investigación',
                    'APRENDIZ'            => 'Aprendiz',
                    default               => ($u->role ?? '—'),
                };

              $roleColor = match($u->role ?? '') {
                  'ADMIN'               => 'bg-danger',
                  'LIDER_SEMILLERO'     => 'bg-success',
                  'LIDER_INVESTIGACION' => 'bg-warning text-dark',
                  'APRENDIZ'            => 'bg-primary',
                  default               => 'bg-secondary',
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

              <td class="py-3 text-end pe-4">
                <div class="acciones-semilleros">

                  {{-- EDITAR --}}
                  @if($canUpdate)
                    <button type="button"
                            class="btn btn-accion-editar"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarUsuario"
                            data-id="{{ $u->id }}"
                            data-nombre="{{ $u->name }}"
                            data-apellido="{{ $u->apellidos }}"
                            data-email="{{ $u->email }}"
                            data-role="{{ $u->role }}"
                            data-semillero-id="{{ $u->semillero_id ?? '' }}"
                            data-update-url="{{ route('admin.usuarios.update', $u->id) }}">
                      <i class="bi bi-pencil me-1"></i> Editar
                    </button>
                  @endif

                  {{-- DAR / QUITAR PERMISOS SOLO LÍDER INVESTIGACIÓN --}}
                  @if($u->role === 'LIDER_INVESTIGACION')
                    <form method="POST"
                          action="{{ route('admin.usuarios.togglePermisosInvestigacion', $u->id) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar los permisos de este líder de investigación?');">
                      @csrf

                      @if($u->li_tiene_permisos)
                        <button type="submit" class="btn btn-accion-ver">
                          <i class="bi bi-shield-x me-1"></i> Quitar permisos
                        </button>
                      @else
                        <button type="submit" class="btn btn-accion-ver">
                          <i class="bi bi-shield-check me-1"></i> Dar permisos
                        </button>
                      @endif
                    </form>
                  @endif

                  {{-- ELIMINAR --}}
                  @if($canDelete)
                    <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Deseas eliminar este usuario?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-accion-eliminar" type="submit">
                        <i class="bi bi-trash me-1"></i> Eliminar
                      </button>
                    </form>
                  @endif

                  @if(!$canUpdate && !$canDelete && $u->role !== 'LIDER_INVESTIGACION')
                    <span class="text-muted small">Sin permisos</span>
                  @endif
                </div>
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

  {{-- PAGINACIÓN --}}
  @if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-3">
      {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif

</div>

{{-- MODALES --}}
@if($canCreate)
  @include('admin.usuarios._modal_crear')
@endif

@if($canUpdate)
  @include('admin.usuarios._modal_editar')
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/usuarios.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/usuarios.js') }}"></script>
@endpush
