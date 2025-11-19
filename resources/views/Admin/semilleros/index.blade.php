@extends('layouts.admin')

@section('content')


    {{-- TÍTULO --}}
    <h3 class="fw-bold mb-2" style="color:#2d572c;">
        Gestión de Semilleros
    </h3>

    {{-- BOTÓN NUEVO SEMILLERO --}}
    <button type="button"
            class="btn btn-nuevo-semillero mb-3"
            data-bs-toggle="modal"
            data-bs-target="#modalNuevoSemillero">
        <i class="fa fa-plus me-1"></i> Nuevo semillero
    </button>

    {{-- MODAL CREAR --}}
    @include('Admin.semilleros._modal_crear')

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
                                <button type="button"
                                        class="btn btn-sm btn-accion-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarSemillero"
                                        data-id="{{ $s->id_semillero }}">
                                    EDITAR
                                </button>

                                {{-- ELIMINAR --}}
                                <form action="{{ route('admin.semilleros.destroy',$s->id_semillero) }}"
                                      method="POST"
                                      onsubmit="return confirm('¿Eliminar este semillero?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-accion-eliminar">
                                        ELIMINAR
                                    </button>
                                </form>

                                {{-- VER PROYECTOS --}}
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
@include('Admin.semilleros._modal_editar')

@endsection

{{-- ARCHIVOS EXTERNOS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/semilleros.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">

<style>
/* ================================
   ENCABEZADO DE TABLA (SEMILLEROS)
   ================================ */
.tabla-semilleros thead th {
    background-color: #0b2e4d !important;
    color: #ffffff !important;
    font-size: .85rem;
    font-weight: 600;
    padding-top: .55rem;
    padding-bottom: .55rem;
    border-bottom: 2px solid rgba(255,255,255,0.35) !important;
    letter-spacing: .03em;
}

/* ================================
   CUERPO: MENOS TRANSLÚCIDO + BORDES
   ================================ */

/* Fondo base de las filas (ya no tan transparente) */
.tabla-semilleros tbody tr {
    background-color: rgba(255,255,255,0.75) !important; /* se ve el fondo pero más sólido */
}

/* Celdas sin fondos raros */
.tabla-semilleros tbody td {
    background-color: transparent !important;
}

/* Borde inferior entre filas (un poco grueso y suave) */
.tabla-semilleros > :not(caption) > * > * {
    border-top: none !important;
    border-bottom: 1.5px solid rgba(11,46,77,0.18) !important;
}

/* Bordes generales de la tabla */
.tabla-semilleros {
    border-collapse: collapse !important;
    border-radius: 12px !important;
    overflow: hidden;
    border: 2px solid rgba(0,0,0,0.20) !important; /* borde visible alrededor */
    --bs-table-bg: transparent !important;
}

/* ================================
   HOVER NOTORIO
   ================================ */
.tabla-semilleros.table-hover tbody tr:hover {
    background-color: rgba(11, 46, 77, 0.30) !important; /* azul translúcido */
    color: #ffffff !important;
    transition: background-color .15s ease-in-out;
}

/* Que el texto también se vea bien en hover */
.tabla-semilleros.table-hover tbody tr:hover td {
    color: #ffffff !important;
}

/* ================================
   CONTENEDOR SIN CAJA BLANCA
   ================================ */
.tabla-semilleros-wrapper,
.tabla-semilleros-wrapper .table-responsive {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 !important;
}

/* Evitar que otras tablas metan bordes raros */
.table {
    --bs-table-border-color: transparent !important;
}
</style>
@endpush

