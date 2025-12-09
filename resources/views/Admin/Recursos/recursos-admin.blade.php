{{-- resources/views/admin/recursos/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('module-title','Recursos para Líderes de Semillero')
@section('module-subtitle','Asigna y gestiona recursos para los líderes de cada semillero')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $user = auth()->user();
    $canCreate = $user->canManageModule('recursos','create');
@endphp

{{-- Variables para JS --}}
<script>
    window.ACT_CAN_CREATE = {{ $canCreate ? 'true' : 'false' }};

    // Endpoints (ajusta rutas según tu proyecto)
    window.ACT_ACTIVIDADES_POR_SEMILLERO_URL = "{{ url('admin/semilleros/:id/actividades') }}";
    window.ACT_SEMILLERO_LIDER_URL          = "{{ url('admin/semilleros/:id/lider') }}";
    window.ACT_STORE_URL                    = "{{ url('admin/actividades-lideres/store') }}";
</script>

{{-- Contenedor de Notificaciones --}}
<div class="notification-container" id="notificationContainer"></div>

<div class="container-fluid mt-4 px-4">

    {{-- HEADER usando .documentos-header y .btn-crear-proyecto --}}
    <div class="d-flex justify-content-between align-items-center mb-4 documentos-header">
        <div>
            <h2>Gestión de Recursos para Líderes</h2>
            <p>Asigna y revisa recursos destinados a los líderes de cada semillero</p>
        </div>

        @if($canCreate)
        <button class="btn btn-light btn-crear-proyecto" id="btnAbrirModalActividad">
            <i class="bi bi-plus-lg me-2"></i>Crear Recurso
        </button>
        @endif
    </div>

    {{-- ALERTA PENDIENTES (usa .alerta-pendientes) --}}
    @php
        $totalPendientes = isset($semilleros) ? $semilleros->sum('actividades_pendientes') : 0;
    @endphp

    @if($totalPendientes > 0)
    <div class="alert alerta-pendientes" role="alert">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
        <strong>Tienes {{ $totalPendientes }} recurso(s) pendiente(s) de seguimiento a líderes</strong>
    </div>
    @endif

    {{-- LISTADO DE SEMILLEROS usando estilos de tarjetas de proyecto --}}
    <div class="mb-5">
        <h4 class="seccion-titulo">Semilleros</h4>
        <div class="row g-4" id="semillerosContainer">
            @forelse($semilleros ?? [] as $semillero)
            <div class="col-md-4">
                <div class="card proyecto-card"><!-- reutilizamos .proyecto-card -->
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="proyecto-titulo">{{ $semillero->nombre }}</h5>
                            <span class="{{ $semillero->estado === 'inactivo' ? 'badge badge-completado' : 'badge badge-activo' }}">
                                {{ $semillero->estado === 'inactivo' ? 'INACTIVO' : 'ACTIVO' }}
                            </span>
                        </div>

                        <p class="proyecto-descripcion">{{ $semillero->descripcion ?: 'Sin descripción' }}</p>

                        {{-- Info del líder --}}
                        <div class="semillero-lider mb-3">
                            <i class="bi bi-person-badge me-2"></i>
                            <strong>Líder:</strong>
                            {{ $semillero->lider_nombre ?? 'Sin líder asignado' }}
                        </div>

                        {{-- Estadísticas reutilizando .proyecto-estadisticas / .estadistica-* --}}
                        <div class="row text-center proyecto-estadisticas">
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $semillero->actividades_total ?? 0 }}</div>
                                <small class="estadistica-label">Recursos</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $semillero->actividades_pendientes ?? 0 }}</div>
                                <small class="estadistica-label">Pendientes</small>
                            </div>
                            <div class="col-4">
                                <div class="estadistica-numero">{{ $semillero->actividades_completadas ?? 0 }}</div>
                                <small class="estadistica-label">Completados</small>
                            </div>
                        </div>

                        {{-- Botones: usamos .btn-ver-entregas para que tome el estilo verde --}}
                        <div class="mt-3 d-grid gap-2">
                            <button
                                class="btn btn-ver-entregas btn-ver-actividades"
                                data-semillero-id="{{ $semillero->id_semillero }}"
                                data-semillero-nombre="{{ $semillero->nombre }}">
                                <i class="bi bi-card-checklist me-2"></i>Ver Recursos
                            </button>

                           @if($semillero->id_lider_semi && $canCreate)
                                <button
                                    class="btn btn-editar-proyecto btn-crear-actividad-card"
                                    data-semillero-id="{{ $semillero->id_semillero }}"
                                    data-semillero-nombre="{{ $semillero->nombre }}">
                                    <i class="bi bi-plus-circle me-2"></i>Crear Recurso para su Líder
                                </button>
                            @else
                                <button class="btn btn-editar-proyecto" disabled
                                    title="Este semillero no tiene líder asignado o no tienes permiso para crear recursos">
                                    <i class="bi bi-exclamation-circle me-2"></i>Sin líder asignado
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="estado-vacio">
                    <i class="bi bi-folder-x"></i>
                    <p>No hay semilleros registrados</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL VER RECURSOS → usa .modal-entregas y .entrega-* --}}
<div class="modal-overlay" id="modalActividades">
    <div class="modal-entregas"><!-- clase del CSS -->
        <button class="btn-cerrar-modal" data-close-modal="actividades">×</button>
        <h2 id="tituloModalActividades">Recursos - Semillero</h2>

        <div id="contenedorActividades">
            {{-- Se llena por JS con .entrega-card, .entrega-header, .badge-pendiente, etc. --}}
        </div>
    </div>
</div>

{{-- MODAL CREAR RECURSO → usa .modal-evidencia y clases de formulario --}}
<div class="modal-overlay" id="modalActividadLider">
    <div class="modal-evidencia"><!-- clase del CSS -->
        <h2 id="tituloModalActividad">Crear Recurso para Líder de Semillero</h2>

        <form id="formActividadLider">
            @csrf

            {{-- Semillero --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Semillero</label>
                <select class="form-select-evidencia" id="semillero_id" name="semillero_id" required>
                    <option value="">Selecciona el semillero...</option>
                    @foreach($semilleros ?? [] as $sem)
                        <option value="{{ $sem->id_semillero }}">{{ $sem->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Líder (solo lectura) --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Líder del Semillero</label>
                <input type="text" class="form-control-evidencia"
                       id="lider_nombre" readonly
                       placeholder="Selecciona un semillero para ver su líder"
                       style="background-color:#f5f5f5; cursor:not-allowed;">
                <input type="hidden" id="lider_id" name="lider_id">
                <small id="mensaje-lider" class="text-muted">
                    El líder se cargará automáticamente al seleccionar el semillero.
                </small>
            </div>

            {{-- Título --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Título del Recurso</label>
                <input type="text" class="form-control-evidencia" id="titulo_actividad" name="titulo"
                       placeholder="Ej: Guía de trabajo con el semillero" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Descripción</label>
                <textarea class="form-control-evidencia form-textarea-evidencia"
                          id="descripcion_actividad" name="descripcion"
                          placeholder="Describe brevemente el recurso..." required></textarea>
            </div>

            {{-- Info según tipo (caja verde) --}}
            <div id="info-tipo-actividad"></div>

            {{-- Fecha límite --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Fecha Límite</label>
                <input type="date" class="form-control-evidencia" id="fecha_limite_actividad"
                       name="fecha_limite" required>
                <small class="text-muted">Solo se permiten fechas desde hoy en adelante</small>
            </div>

            {{-- Botones del modal: usan .modal-botones, .btn-cancelar-modal, .btn-guardar-modal --}}
            <div class="modal-botones">
                <button type="button" class="btn-cancelar-modal" id="btnCancelarActividad">Cancelar</button>
                <button type="submit" class="btn-guardar-modal">Guardar Recurso</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/recursos.js') }}"></script>
@endpush
