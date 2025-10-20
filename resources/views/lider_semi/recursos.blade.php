@extends('layouts.lider_semi')

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold mb-3">Recursos</h4>
    <p class="text-muted">Materiales, guías y recursos de apoyo para el semillero.</p>
    <div class="card p-4">
        <div class="text-muted">Contenido en construcción.</div>
    </div>
    <div class="mt-3">
        <a href="{{ route('lider_semi.dashboard') }}" class="btn btn-outline-secondary">Volver al Dashboard</a>
    </div>
</div>
@endsection

