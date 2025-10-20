@extends('layouts.lider_semi')

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold mb-3">Documentación</h4>
    <p class="text-muted">Gestión y seguimiento de documentos asociados a proyectos.</p>
    <div class="card p-4">
        <div class="text-muted">Contenido en construcción.</div>
    </div>
    <div class="mt-3">
        <a href="{{ route('lider_semi.dashboard') }}" class="btn btn-outline-secondary">Volver al Dashboard</a>
    </div>
</div>
@endsection

