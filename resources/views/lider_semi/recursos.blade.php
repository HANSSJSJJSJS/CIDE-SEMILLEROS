@extends('layouts.lider_semi')

@section('title','Recursos')
@section('module-title','Recursos del Semillero')
@section('module-subtitle','Sube y gestiona plantillas y manuales')

@section('content')
<div class="container-fluid">
  <div class="row g-4">
    <div class="col-12">
      <h4 class="mb-3">Proyectos Activos</h4>
      <div class="row g-4 mb-4">
        @forelse(($activos ?? []) as $p)
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="mb-0">{{ $p['nombre'] }}</h5>
                <span class="badge bg-secondary">ACTIVO</span>
              </div>
              <div class="text-muted mb-3">{{ $p['descripcion'] ?: 'Sin descripción' }}</div>
              <div class="row text-center mb-3">
                <div class="col-4">
                  <div class="fw-bold">{{ $p['entregas'] }}</div>
                  <small class="text-muted">Entregas</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold">{{ $p['pendientes'] }}</div>
                  <small class="text-muted">Pendientes</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold">{{ $p['aprobadas'] }}</div>
                  <small class="text-muted">Aprobadas</small>
                </div>
              </div>
              <a href="{{ route('lider_semi.documentos') }}" class="btn btn-success w-100">Ver Entregas</a>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12">
          <div class="text-muted">No hay proyectos activos</div>
        </div>
        @endforelse
      </div>

      <h4 class="mb-3">Proyectos Completados</h4>
      <div class="row g-4">
        @forelse(($completados ?? []) as $p)
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="mb-0">{{ $p['nombre'] }}</h5>
                <span class="badge bg-success">COMPLETADO</span>
              </div>
              <div class="text-muted mb-3">{{ $p['descripcion'] ?: 'Sin descripción' }}</div>
              <div class="row text-center mb-3">
                <div class="col-4">
                  <div class="fw-bold">{{ $p['entregas'] }}</div>
                  <small class="text-muted">Entregas</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold">{{ $p['pendientes'] }}</div>
                  <small class="text-muted">Pendientes</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold">{{ $p['aprobadas'] }}</div>
                  <small class="text-muted">Aprobadas</small>
                </div>
              </div>
              <a href="{{ route('lider_semi.documentos') }}" class="btn btn-success w-100">Ver Entregas</a>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12">
          <div class="text-muted">No hay proyectos completados</div>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection