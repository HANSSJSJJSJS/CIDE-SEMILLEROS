{{-- resources/views/admin/semilleros/index.blade.php --}}
@extends('layouts.admin')

@section('content')

@php
    $user      = auth()->user();
    $canCreate = $user->canManageModule('semilleros','create');
    $canUpdate = $user->canManageModule('semilleros','update');
    $canDelete = $user->canManageModule('semilleros','delete');
@endphp

{{-- TÍTULO --}}
<h3 class="fw-bold mb-2" style="color:#2d572c;">
    Gestión de Semilleros
</h3>

{{-- BOTÓN NUEVO SEMILLERO --}}
@if($canCreate)
<button type="button"
        class="btn btn-nuevo-semillero mb-3"
        data-bs-toggle="modal"
        data-bs-target="#modalNuevoSemillero">
    <i class="fa fa-plus me-1"></i> Nuevo semillero
</button>
@endif

{{-- MODAL CREAR --}}
@if($canCreate)
    @include('Admin.semilleros._modal_crear')
@endif

{{-- BARRA DE BÚSQUEDA --}}
<form method="GET" action="{{ route('admin.semilleros.index') }}" class="mb-4">
    <div class="search-bar-wrapper">
        <input type="text"
               name="q"
               value="{{ $q }}"
               class="form-control"
               placeholder="Busca por semillero, línea o líder">
        <button type="submit">
            <i class="bi bi-search text-white fs-5"></i>
        </button>
    </div>
</form>

{{-- TABLA (GLASS) --}}
<div class="tabla-semilleros-wrapper">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 tabla-semilleros">
            <thead>
                <tr>
                    <th>Semillero</th>
                    <th>Línea de investigación</th>
                    <th>Líder</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
            @forelse($semilleros as $s)
                <tr>
                    <td>{{ $s->nombre }}</td>
                    <td>{{ $s->linea_investigacion }}</td>
                    <td>{{ $s->lider_nombre ? $s->lider_nombre.' ('.$s->lider_correo.')' : '—' }}</td>

                    <td class="text-center">
                        <div class="acciones-semilleros">

                            {{-- EDITAR --}}
                            @if($canUpdate)
                            <button type="button"
                                    class="btn btn-sm btn-accion-editar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSemillero"
                                    data-id="{{ $s->id_semillero }}">
                                EDITAR
                            </button>
                            @endif

                            {{-- ELIMINAR --}}
                            @if($canDelete)
                            <form action="{{ route('admin.semilleros.destroy',$s->id_semillero) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar este semillero?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-accion-eliminar">
                                    ELIMINAR
                                </button>
                            </form>
                            @endif

                            {{-- VER PROYECTOS (siempre permitido) --}}
                            <a class="btn btn-sm btn-accion-ver"
                               href="{{ route('admin.semilleros.proyectos.index', $s->id_semillero) }}">
                                VER PROYECTOS
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

<div class="mt-3">
    {{ $semilleros->links('pagination::bootstrap-5') }}
</div>

{{-- MODAL EDITAR --}}
@if($canUpdate)
    @include('Admin.semilleros._modal_editar')
@endif

@endsection

{{-- ARCHIVOS EXTERNOS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/semilleros.js') }}"></script>
@endpush
