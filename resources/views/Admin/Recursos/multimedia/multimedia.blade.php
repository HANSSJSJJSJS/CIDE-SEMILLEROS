@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('module-title','Multimedia')
@section('module-subtitle','Gestión de recursos digitales')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- VARIABLES PARA JS --}}
<script>
    window.MultimediaConfig = {
        storeUrl: "{{ route('admin.recursos.multimedia.store') }}",
        deleteBaseUrl: "{{ url('/admin/recursos/multimedia') }}",
        semilleros: @json($semilleros),
        userRole: "{{ auth()->user()->role }}"
    };
</script>

{{-- CONTENEDOR DE NOTIFICACIONES --}}
<div class="notification-container" id="notificationContainer"></div>

<div class="recursos-wrapper">

<div class="container-fluid mt-4 px-4">

{{-- MODAL --}}
@include('admin.recursos.multimedia._modal_multimedia')

{{-- HEADER --}}
<div class="documentos-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-2">Multimedia222</h2>
        <p class="text-muted mb-0">
            Imágenes, documentos, presentaciones y otros archivos
        </p>
    </div>

    <div class="d-flex gap-2">
        {{-- 🔙 VOLVER A RECURSOS --}}
        <a href="{{ route('admin.recursos.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left"></i> Volver a Recursos
        </a>

        {{-- ⬆️ SUBIR MULTIMEDIA --}}
        <button id="btnAbrirModalMultimedia" class="btn-multimedia">
            <i class="bi bi-upload"></i> Subir multimedia
        </button>
    </div>
</div>

{{-- SECCIÓN PLANTILLAS --}}
<div class="mb-5">
    <h4 class="seccion-titulo">Plantillas / Presentaciones</h4>
    <div id="contenedorPlantillas" class="row g-4"></div>
    <hr class="my-5">
</div>

{{-- SECCIÓN MANUALES --}}
<div class="mb-5">
    <h4 class="seccion-titulo">Manuales</h4>
    <div id="contenedorManuales" class="row g-4"></div>
    <hr class="my-5">
</div>

{{-- SECCIÓN OTROS --}}
<div class="mb-5">
    <h4 class="seccion-titulo">Otros archivos</h4>
    <div id="contenedorOtros" class="row g-4"></div>
</div>

</div> {{-- .container-fluid --}}

</div> {{-- .recursos-wrapper --}}

@endsection

@push('scripts')
<script src="{{ asset('js/admin/multimedia.js') }}"></script>
@endpush
