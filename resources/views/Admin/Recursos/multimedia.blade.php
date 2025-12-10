@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('module-title','Multimedia de Recursos')
@section('module-subtitle','Imágenes, videos, presentaciones y otros archivos')

{{-- TOKEN CSRF --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

{{-- ====================== --}}
{{-- VARIABLES JS GLOBALES --}}
{{-- ====================== --}}
<script>
    window.MULTIMEDIA_STORE_URL = "{{ route('admin.recursos.multimedia.store') }}";
    window.MULTIMEDIA_DELETE_URL = "/admin/recursos/multimedia"; // + "/{id}"
    window.SEMILLEROS_LIST = @json($semilleros);
</script>

{{-- MODAL DE SUBIR MULTIMEDIA --}}
@include('admin.recursos._modal_multimedia', ['semilleros' => $semilleros])

<div class="container-fluid mt-4 px-4">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 documentos-header">
        <div>
            <h2>Multimedia</h2>
            <p>Gestión de imágenes, videos, presentaciones, manuales y otros archivos</p>
        </div>

        {{-- BOTÓN SUBIR MULTIMEDIA --}}
        <button id="btnAbrirModalMultimedia" class="btn-multimedia">
            <i class="bi bi-upload"></i> Subir multimedia
        </button>
    </div>

    {{-- SECCIONES DE MULTIMEDIA --}}
    <div class="mb-5">

        {{-- PLANTILLAS --}}
        <h4 class="seccion-titulo">Plantillas / Presentaciones</h4>
        <div id="contenedorPlantillas" class="row g-4"></div>

        <hr class="my-5">

        {{-- MANUALES --}}
        <h4 class="seccion-titulo">Manuales</h4>
        <div id="contenedorManuales" class="row g-4"></div>

        <hr class="my-5">

        {{-- OTROS --}}
        <h4 class="seccion-titulo">Otros archivos</h4>
        <div id="contenedorOtros" class="row g-4"></div>

    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/multimedia.js') }}"></script>
@endpush
