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

{{-- ========================== --}}
{{-- VARIABLES JS GLOBALES --}}
{{-- ========================== --}}
<script>
    window.ACT_CAN_CREATE = {{ $canCreate ? 'true' : 'false' }};

    // URLs dinámicas correctas (se usan con buildUrl() en JS)
    window.ACT_ACTIVIDADES_POR_SEMILLERO_URL = "/admin/recursos/semillero/:id";
    window.ACT_SEMILLERO_LIDER_URL = "/admin/recursos/semillero/:id/lider";

    // URLs base (para concatenar manualmente)
   window.ACT_STORE_URL = "{{ route('admin.recursos.semillero.store') }}";
    window.URL_PROYECTOS_SEMILLERO = "/admin/recursos/proyectos"; 
    window.URL_MULTIMEDIA = "{{ route('admin.recursos.multimedia') }}";
</script>

{{-- Contenedor de Notificaciones --}}
<div class="notification-container" id="notificationContainer"></div>

<div class="container-fluid mt-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 documentos-header">

        <div>
            <h2>Gestión de Recursos para Líderes</h2>
            <p>Asigna y revisa recursos destinados a los líderes de cada semillero</p>
        </div>

        <div class="d-flex gap-2">

            @if($canCreate)
            <button class="btn btn-light btn-crear-proyecto" id="btnAbrirModalActividad">
                <i class="bi bi-plus-lg me-2"></i>Crear Recurso
            </button>
            @endif

            <a href="{{ route('admin.recursos.multimedia') }}" class="btn-multimedia">
                <i class="bi bi-collection-play"></i> Multimedia
            </a>

        </div>
    </div>

    {{-- ALERTA PENDIENTES --}}
    @php
        $totalPendientes = $semilleros->sum('actividades_pendientes');
    @endphp

    @if($totalPendientes > 0)
    <div class="alert alerta-pendientes">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
        <strong>Tienes {{ $totalPendientes }} recurso(s) pendiente(s) de seguimiento</strong>
    </div>
    @endif

    {{-- LISTADO DE SEMILLEROS --}}
    <div class="mb-5">
        <h4 class="seccion-titulo">Semilleros</h4>

        <div class="row g-4" id="semillerosContainer">
            @forelse($semilleros as $semillero)
                @include('admin.recursos._card_semillero')
            @empty
                <div class="col-12 text-center estado-vacio">
                    <i class="bi bi-folder-x"></i>
                    <p>No hay semilleros registrados</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- MODALES --}}
@include('admin.recursos.modal-ver-recursos')
@include('admin.recursos.modal-universal')

@endsection

@push('scripts')
<script src="{{ asset('js/admin/recursos.js') }}"></script>
@endpush
