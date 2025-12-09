@extends('layouts.admin')

@push('styles')
<style>
  :root {
    --blue-900: #0b2e4d;
  }

  .kpi-card { border-radius: 14px; }
  .kpi-value { font-size: 1.8rem; font-weight: 700; }
  .kpi-label { color: #6c757d; }

  .card-header {
    background-color: var(--blue-900) !important;
    color: #fff !important;
    border-bottom: none;
  }

  .dashboard-chart-card { min-height: 260px; }
  canvas { max-height: 260px; }
</style>
@endpush

@section('module-title','Panel de control')
@section('module-subtitle','Resumen del sistema y actividad reciente')

@section('content')
<div class="container dash-admin">

  {{-- TARJETAS KPI --}}
  <div class="row g-3">
    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body text-center">
          <div class="kpi-label">Semilleros</div>
          <div id="kpiSemilleros" class="kpi-value">--</div>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body text-center">
          <div class="kpi-label">Líderes</div>
          <div id="kpiLideres" class="kpi-value">--</div>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body text-center">
          <div class="kpi-label">Aprendices</div>
          <div id="kpiAprendices" class="kpi-value">--</div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card kpi-card shadow-sm">
        <div class="card-body text-center">
          <div class="kpi-label">Proyectos</div>
          <div id="kpiProyectos" class="kpi-value">--</div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card kpi-card shadow-sm">
        <div class="card-body text-center">
          <div class="kpi-label">Recursos</div>
          <div id="kpiRecursos" class="kpi-value">--</div>
        </div>
      </div>
    </div>
  </div>

  {{-- GRÁFICA 1: Aprendices por semillero (Barras) --}}
  <div class="card shadow-sm mt-4 dashboard-chart-card">
    <div class="card-header">
      <strong>Aprendices por semillero</strong>
    </div>
    <div class="card-body">
      <canvas id="chartAprendicesSem"></canvas>
    </div>
  </div>

  {{-- GRÁFICA 2: Proyectos recientes por semillero (Donut) --}}
  <div class="card shadow-sm mt-4 dashboard-chart-card">
    <div class="card-header">
      <strong>Proyectos recientes por semillero</strong>
    </div>
    <div class="card-body">
      <canvas id="chartProyectosSem"></canvas>
    </div>
  </div>

  <div class="row mt-4">

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><strong>Estado de los proyectos</strong></div>
            <div class="card-body">
                <canvas id="chartEstadoProyectos" height="240"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><strong>Top 5 semilleros con más proyectos</strong></div>
            <div class="card-body">
                <canvas id="chartTopSemilleros" height="240"></canvas>
            </div>
        </div>
    </div>

  </div>

  <div class="card shadow-sm mt-4 mb-4">
    <div class="card-header">
      <strong>Actividad de líderes</strong>
    </div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-dark">
          <tr>
            <th>Líder</th>
            <th>Línea de investigación</th>
            <th>Último ingreso</th>
            <th>Hace cuánto</th>
          </tr>
        </thead>
        <tbody id="tablaActividadLideres"></tbody>
      </table>
    </div>
  </div>

</div>

@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="{{ asset('js/admin/dashboard.js') }}?v={{ time() }}"></script>
@endpush
