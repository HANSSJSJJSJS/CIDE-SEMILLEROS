{{-- resources/views/admin/recursos/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('module-title','Centro de Recursos')
@section('module-subtitle','Plantillas, manuales y otros documentos')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $user = auth()->user();
    $canCreate = $user->canManageModule('recursos','create');
    $canDelete = $user->canManageModule('recursos','delete');
@endphp

{{-- Variables para JS --}}
<script>
    window.REC_CAN_CREATE = {{ $canCreate ? 'true' : 'false' }};
    window.REC_CAN_DELETE = {{ $canDelete ? 'true' : 'false' }};
    window.REC_LISTAR_URL = "{{ route('admin.recursos.listar') }}";
    window.REC_STORE_URL  = "{{ route('admin.recursos.store') }}";
    window.REC_DELETE_URL = "{{ url('admin/recursos/:id') }}";
</script>

<div class="rec-head">

    {{-- Buscador --}}
    <div class="input-group" style="max-width:480px;">
        <span class="input-group-text">🔍</span>
        <input id="recSearch" type="search" class="form-control"
               placeholder="Buscar recurso (nombre o descripción)">
    </div>

    {{-- Categoría --}}
    <select id="recFilter" class="form-select" style="max-width:220px;">
        <option value="">Todas las categorías</option>
        <option value="plantillas">Plantillas</option>
        <option value="manuales">Manuales</option>
        <option value="otros">Otros</option>
    </select>

    {{-- Botón subir --}}
    @if($canCreate)
        <button class="btn btn-success " data-bs-toggle="modal" data-bs-target="#recUploadModal">
            <i class="bi bi-upload"></i> Subir recurso
        </button>
    @endif
</div>

<div id="recContainer"></div>

{{-- Modal de subir recurso – solo si tiene permiso --}}
@if($canCreate)
    @include('admin.recursos._modal_subir')
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/admin/recursos.js') }}"></script>
@endpush
