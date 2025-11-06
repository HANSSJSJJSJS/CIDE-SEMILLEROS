@extends('layouts.aprendiz')

@section('title','Panel del Aprendiz')
@section('module-title','Panel del Aprendiz')
@section('module-subtitle','Resumen y accesos rápidos')

@section('content')
<div class="container">
    <h1>Bienvenido, {{ $aprendiz->nombre }}</h1>
    <p>Rol: {{ $aprendiz->rol }}</p>
    <p>Estado: {{ $aprendiz->activo ? 'Activo' : 'Inactivo' }}</p>

    <h2>Datos Personales</h2>
    <ul>
        <li>Correo: {{ $aprendiz->email }}</li>
        <li>Teléfono: {{ $aprendiz->telefono }}</li>
        <li>Fecha de Registro: {{ $aprendiz->created_at->format('d/m/Y') }}</li>
    </ul>

    <h2>Reportes y Avances</h2>
    <div>
        @foreach($reportes as $reporte)
            <div>
                <h3>{{ $reporte->titulo }}</h3>
                <p>{{ $reporte->descripcion }}</p>
                <a href="{{ route('reportes.show', $reporte->id) }}">Ver Detalles</a>
            </div>
        @endforeach
    </div>

    <h2>Documentación y Recursos</h2>
    <div>
        @foreach($recursos as $recurso)
            <div>
                <h3>{{ $recurso->titulo }}</h3>
                <a href="{{ route('recursos.download', $recurso->id) }}">Descargar PDF</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
