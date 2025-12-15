@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('content')

<div class="container mt-4">

    {{-- TÍTULO DE LA SECCIÓN --}}
    <div class="d-flex justify-content-between align-items-center mb-4 documentos-header">
        <div>
            <h2>Multimedia del Semillero</h2>
            <p class="text-muted">Consulta los videos, documentos y contenido multimedia proporcionado por el administrador.</p>
        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="alert alert-info rounded-3 p-3">
        Esta sección está en construcción. Aquí se listarán los recursos multimedia del semillero.
        <br>
        <small class="text-muted">Muy pronto podrás visualizar videos, manuales y contenido interactivo.</small>
    </div>

</div>

@endsection
