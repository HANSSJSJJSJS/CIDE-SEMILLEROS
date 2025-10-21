@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Perfil del Aprendiz</h1>

    <div class="card">
        <div class="card-header">
            Datos Personales
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $aprendiz->nombre }}</p>
            <p><strong>Rol:</strong> {{ $aprendiz->rol }}</p>
            <p><strong>Estado:</strong> {{ $aprendiz->activo ? 'Activo' : 'Inactivo' }}</p>
        </div>
    </div>

    <div class="mt-4">
        <h2>Documentación y Recursos</h2>
        <ul>
            @foreach($recursos as $recurso)
                <li>
                    <a href="{{ route('archivos.show', $recurso->id) }}">{{ $recurso->nombre }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
