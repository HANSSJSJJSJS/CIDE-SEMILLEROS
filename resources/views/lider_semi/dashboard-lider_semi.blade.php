@extends('layouts.lider_semi')

@section('content')
<div class="container-fluid">
  <h3 class="fw-bold mb-4">¡Bienvenido, {{ Auth::user()->name }}! 👋</h3>
  <p class="text-muted">Aquí está el resumen de tus semilleros y actividades recientes.</p>

  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-people-fill fs-3 text-success"></i>
        <h3 class="fw-bold mt-2">5</h3>
        <p class="text-muted">Semilleros Activos</p>
        <small class="text-success">+2 este mes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-person-badge fs-3 text-primary"></i>
        <h3 class="fw-bold mt-2">47</h3>
        <p class="text-muted">Aprendices Totales</p>
        <small class="text-primary">+8 este mes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-file-earmark-check fs-3 text-warning"></i>
        <h3 class="fw-bold mt-2">24</h3>
        <p class="text-muted">Documentos Revisados</p>
        <small class="text-danger">3 pendientes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-graph-up-arrow fs-3 text-purple"></i>
        <h3 class="fw-bold mt-2">87%</h3>
        <p class="text-muted">Progreso Promedio</p>
        <small class="text-success">+12%</small>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Actividad de Aprendices</h5>
        <div class="text-muted text-center" style="height:200px;">(Gráfico o tabla aquí)</div>
      </div>
      <div class="card p-4 mt-4">
        <h5 class="fw-bold mb-3">Actividad Reciente</h5>
        <div class="list-group">
          <div class="list-group-item border-0">
            <i class="bi bi-person-plus text-success me-2"></i>
            <strong>María González</strong> se unió al Semillero de IA
            <div class="text-muted small">Hace 5 minutos</div>
          </div>
          <div class="list-group-item border-0">
            <i class="bi bi-file-earmark-arrow-up text-primary me-2"></i>
            <strong>Carlos Pérez</strong> subió un documento <em>(Proyecto Final - Desarrollo Web.pdf)</em>
            <div class="text-muted small">Hace 1 hora</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Acciones Rápidas</h5>
        <div class="d-grid gap-3">
          <button class="btn btn-success"><i class="bi bi-plus-circle me-2"></i> Crear Semillero</button>
          <button class="btn btn-dark"><i class="bi bi-person-plus me-2"></i> Agregar Aprendiz</button>
          <button class="btn btn-outline-success"><i class="bi bi-upload me-2"></i> Subir Documento</button>
          <button class="btn btn-outline-primary"><i class="bi bi-calendar-plus me-2"></i> Programar Reunión</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
