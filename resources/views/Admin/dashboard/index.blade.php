@extends('layouts.admin')

@push('styles')
<style>
  .kpi-card { border-radius: 14px; }
  .kpi-value { font-size: 1.8rem; font-weight: 700; }
  .kpi-label { color: #6c757d; }
</style>
@endpush

@section('module-title','Panel de control')
@section('module-subtitle','Resumen del sistema y actividad reciente')

@section('content')
  <div class="row g-3">
    {{-- KPIs --}}
    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Semilleros</div>
          <div id="kpiSemilleros" class="kpi-value">--</div>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Líderes</div>
          <div id="kpiLideres" class="kpi-value">--</div>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Aprendices</div>
          <div id="kpiAprendices" class="kpi-value">--</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Proyectos activos</div>
          <div id="kpiProyectos" class="kpi-value">--</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card kpi-card shadow-sm">
        <div class="card-body">
          <div class="kpi-label">Recursos</div>
          <div id="kpiRecursos" class="kpi-value">--</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Aprendices por semillero (Top 10)</strong></div>
        <div class="card-body"><canvas id="chartAprSem" height="140"></canvas></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Proyectos por estado</strong></div>
        <div class="card-body"><canvas id="chartProjEstado" height="140"></canvas></div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Proyectos creados por mes (últimos 12)</strong></div>
        <div class="card-body"><canvas id="chartProjMes" height="140"></canvas></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Recursos por categoría</strong></div>
        <div class="card-body"><canvas id="chartRecCat" height="140"></canvas></div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  // 1) KPIs
  fetch("{{ route('admin.dashboard.stats') }}")
    .then(r => r.json())
    .then(s => {
      document.getElementById('kpiSemilleros').textContent = s.semilleros ?? 0;
      document.getElementById('kpiLideres').textContent    = s.lideres ?? 0;
      document.getElementById('kpiAprendices').textContent = s.aprendices ?? 0;
      document.getElementById('kpiProyectos').textContent  = s.proyectos ?? 0;
      document.getElementById('kpiRecursos').textContent   = s.recursos ?? 0;
    })
    .catch(console.error);

  // 2) Charts
  fetch("{{ route('admin.dashboard.charts') }}")
    .then(r => r.json())
    .then(d => {
      // Bar: Aprendices por semillero
      new Chart(document.getElementById('chartAprSem'), {
        type: 'bar',
        data: {
          labels: d.aprendicesPorSemillero.labels,
          datasets: [{ label: 'Aprendices', data: d.aprendicesPorSemillero.data }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      // Pie: Proyectos por estado
      new Chart(document.getElementById('chartProjEstado'), {
        type: 'pie',
        data: {
          labels: d.proyectosPorEstado.labels,
          datasets: [{ data: d.proyectosPorEstado.data }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      // Line: Proyectos por mes
      new Chart(document.getElementById('chartProjMes'), {
        type: 'line',
        data: {
          labels: d.proyectosPorMes.labels,
          datasets: [{ label: 'Proyectos', data: d.proyectosPorMes.data, tension: .3, fill: false }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      // Bar: Recursos por categoría
      new Chart(document.getElementById('chartRecCat'), {
        type: 'bar',
        data: {
          labels: d.recursosPorCategoria.labels,
          datasets: [{ label: 'Recursos', data: d.recursosPorCategoria.data }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
      });
    })
    .catch(console.error);
})();
</script>
@endpush
