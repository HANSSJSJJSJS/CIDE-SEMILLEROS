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

    {{-- FILTROS --}}
    <form id="formFiltroUsuarios" method="GET" class="mb-3">
        <div class="row gy-3 gx-3">

            {{-- 1) BARRA DE BÚSQUEDA --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Buscar por nombre, apellido o correo</label>
                <div class="search-bar-wrapper w-100">
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

            <div class="d-none d-md-block col-md-6"></div>

            {{-- 2) SELECT ROL --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Rol</label>
                <select name="role" class="form-select">
                    <option value="">Todos</option>
                    @php $roleSelected = $roleFilter ?? request('role'); @endphp
                    @foreach(($roles ?? []) as $value => $label)
                        <option value="{{ $value }}" @selected($roleSelected === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3) SELECT SEMILLERO --}}
            <div class="col-12 col-md-3">
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

            {{-- 4) BOTONES FILTRAR / LIMPIAR --}}
            <div class="col-12 col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-accion-ver">
                        <i class="bi bi-search"></i> Filtrar
                    </button>

                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-accion-ver">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </a>
                </div>
            </div>

            {{-- 5) BOTÓN NUEVO USUARIO --}}
            <div class="col-12 mt-2 d-flex justify-content-start">
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
    </form>

    {{-- TABLA DE USUARIOS --}}
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
                        $nombreCompleto = trim(($u->nombre ?? '').' '.($u->apellidos ?? ''));
                        if ($nombreCompleto === '') {
                            $nombreCompleto = 'Usuario';
                        }
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
                            <small class="text-muted">{{ $u->email }}</small>
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
                                        class="btn btn-accion-editar btn-editar-usuario"
                                        data-user-id="{{ $u->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarUsuario">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            @endif

                                                            {{-- PERMISOS INVESTIGACIÓN --}}
                                @if(auth()->user()->role === 'ADMIN'
                                    && $u->role === 'LIDER_INVESTIGACION'
                                    && auth()->id() !== $u->id)

                                    <form method="POST"
                                          action="{{ route('admin.usuarios.togglePermisosInvestigacion', $u->id) }}"
                                          class="needs-confirmation"
                                          data-message="¿Seguro que quieres cambiar los permisos de este líder de investigación?"
                                          data-confirm-text="Sí, cambiar permisos"
                                          data-cancel-text="Cancelar">
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

                                                            {{-- ELIMINAR + VER DATOS --}}
                                @if($canDelete)
                                    <form method="POST"
                                        action="{{ route('admin.usuarios.destroy', $u->id) }}"
                                        class="form-eliminar-usuario"
                                        data-user-name="{{ $nombreCompleto }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-accion-eliminar btn-eliminar-usuario">
                                            <i class="bi bi-trash me-1"></i> Eliminar
                                        </button>

                                        <button type="button"
                                                class="btn btn-accion-ver btn-ver-usuario"
                                                data-user-id="{{ $u->id }}">
                                            <i class="bi bi-eye me-1"></i> Ver datos
                                        </button>
                                    </form>
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

{{-- MODAL VER DATOS (uno solo, reutilizable) --}}
@include('admin.usuarios._modal_ver')

{{-- MODAL CREAR --}}
@if($canCreate)
    @include('admin.usuarios._modal_crear')
@endif

{{-- 🔹 UN SOLO MODAL EDITAR USUARIO GLOBAL --}}
@if($canUpdate)
    @include('admin.usuarios._modal_editar')
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/usuarios.css') }}">
@endpush

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function swalSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: msg
            });
        }

        function swalError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg
            });
        }
    </script>

    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <script> swalSuccess(@json(session('success'))); </script>
    @endif

    @if(session('error'))
        <script> swalError(@json(session('error'))); </script>
    @endif

    {{-- JS de la pantalla --}}
    <script src="{{ asset('js/admin/usuarios.js') }}"></script>
@endpush
