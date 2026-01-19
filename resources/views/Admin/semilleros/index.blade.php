{{-- resources/views/admin/semilleros/index.blade.php --}}
@extends('layouts.admin')

@section('content')

@php
    $user      = auth()->user();
    $canCreate = $user->canManageModule('semilleros','create');
    $canUpdate = $user->canManageModule('semilleros','update');
    $canDelete = $user->canManageModule('semilleros','delete');
@endphp

{{-- =========================
   CONTENIDO PRINCIPAL
   ========================= --}}
<div class="semilleros-wrapper">

    {{-- CABECERA --}}
    <div class="semilleros-header mb-3">
        <div class="row g-3 align-items-center">

            <div class="col-12 col-lg-7">
                <h3 class="fw-bold mb-1" style="color:#2d572c;">Gestión de Semilleros</h3>
                <p class="text-muted mb-0">
                    Administra los semilleros, líneas de investigación y líderes.
                </p>
            </div>


        </div>

        {{-- BARRA DE BÚSQUEDA --}}
        <div class="mt-3">
            <form method="GET" action="{{ route('admin.semilleros.index') }}" id="formFiltroSemilleros">
                <label class="form-label fw-semibold mb-2">
                    Buscar semillero, línea o líder
                </label>

                <div class="search-bar-wrapper">
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           class="form-control"
                           placeholder="Ej. GEDS, videojuegos, Juan Pérez">

                    <button type="submit">
                        <i class="bi bi-search text-white fs-5"></i>
                    </button>
                </div>
             <div class="col-12 col-lg-5 d-flex justify-content-start">
                @if($canCreate)
                    <button type="button"
                            class="btn btn-nuevo-semillero mt-2 mt-lg-0"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNuevoSemillero">
                        <i class="bi bi-people-fill me-1"></i>
                        Nuevo semillero
                    </button>
                @endif
            </div>

            </form>
        </div>
    </div>

    
           

    {{-- TABLA --}}
    <div class="tabla-semilleros-wrapper mt-2">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tabla-semilleros">

                <thead>
                    <tr>
                        <th class="py-3 px-4">Semillero</th>
                        <th class="py-3">Línea de investigación</th>
                        <th class="py-3">Líder</th>
                        <th class="py-3 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($semilleros as $s)
                        <tr>

                            {{-- NOMBRE --}}
                            <td class="py-3 px-4">
                                <div class="fw-semibold">{{ $s->nombre }}</div>
                            </td>

                            {{-- LÍNEA --}}
                            <td class="py-3">
                                {{ $s->linea_investigacion ?: '—' }}
                            </td>

                            {{-- LÍDER --}}
                            <td class="py-3">
                                @if($s->lider_nombre)
                                    {{ $s->lider_nombre }}
                                    <small class="text-muted d-block">
                                        {{ $s->lider_correo }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="py-3 text-center">
                                <div class="acciones-semilleros">

                                    {{-- EDITAR --}}
                                    @if($canUpdate)
                                        <button type="button"
                                                class="btn btn-accion-editar btn-editar-semillero"
                                                data-id="{{ $s->id_semillero }}"
                                                data-edit-url="{{ route('admin.semilleros.edit.ajax', $s->id_semillero) }}"
                                                data-update-url="{{ route('admin.semilleros.update', $s->id_semillero) }}">
                                            <i class="bi bi-pencil me-1"></i>
                                            Editar
                                        </button>
                                    @endif

                                    {{-- ELIMINAR --}}
                                    @if($canDelete)
                                        <button type="button"
                                                class="btn btn-accion-eliminar btn-eliminar-semillero"
                                                data-url="{{ route('admin.semilleros.destroy',$s->id_semillero) }}"
                                                data-nombre="{{ $s->nombre }}">
                                            <i class="bi bi-trash me-1"></i>
                                            Eliminar
                                        </button>
                                    @endif

                                    {{-- VER PROYECTOS --}}
                                    <a class="btn btn-accion-ver"
                                       href="{{ route('admin.semilleros.proyectos.index', $s->id_semillero) }}">
                                        <i class="bi bi-folder2-open me-1"></i>
                                        Ver proyectos
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No hay registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $semilleros->links('pagination::bootstrap-5') }}
    </div>

</div> {{-- /.semilleros-wrapper --}}

{{-- =========================
   MODALES (FUERA DEL WRAPPER)
   ========================= --}}

{{-- MODAL CREAR --}}
@if($canCreate)
    @include('Admin.semilleros._modal_crear')
@endif

{{-- MODAL EDITAR --}}
@if($canUpdate)
    @include('Admin.semilleros._modal_editar')
@endif

@endsection

{{-- =========================
   ESTILOS Y SCRIPTS
   ========================= --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin/semilleros.js') }}"></script>

    @if(session('success'))
        <script>
            swalSuccess(@json(session('success')));
        </script>
    @endif

    @if(session('error'))
        <script>
            swalError(@json(session('error')));
        </script>
    @endif
@endpush
