@extends('layouts.admin')

@push('styles')
<style>
  :root {
    --blue-900: #0b2e4d; /* tu color personalizado */
  }

  .kpi-card { border-radius: 14px; }
  .kpi-value { font-size: 1.8rem; font-weight: 700; }
  .kpi-label { color: #6c757d; }

  /* 🔵 Encabezados azul oscuro personalizados */
  .card-header {
    background-color: var(--blue-900) !important;
    color: #fff !important;
    border-bottom: none;
  }

  /* Opcional: resaltar texto dentro del header */
  .card-header strong {
    color: #fff !important;
  }
</style>
@endpush

@section('module-title','Panel de control')
@section('module-subtitle','Resumen del sistema y actividad reciente')

@section('content')

  {{-- Tarjetas KPI --}}
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

  {{-- TABLA 1 — Aprendices por semillero --}}
  <div class="card shadow-sm mt-4">
    <div class="card-header"><strong>Aprendices por semillero</strong></div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-dark">
          <tr>
            <th>Semillero</th>
            <th>Total de aprendices</th>
          </tr>
        </thead>
        <tbody id="tablaAprendicesSem"></tbody>
      </table>
    </div>
  </div>

  {{-- TABLA 2 — Proyectos por semillero (últimos 5) --}}
  <div class="card shadow-sm mt-4">
    <div class="card-header"><strong>Proyectos por semillero (últimos 5)</strong></div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-dark">
          <tr>
            <th>Semillero</th>
            <th>Últimos proyectos</th>
          </tr>
        </thead>
        <tbody id="tablaProyectosSem"></tbody>
      </table>
    </div>
  </div>

  {{-- TABLA 3 — Actividad de líderes --}}
  <div class="card shadow-sm mt-4 mb-4">
    <div class="card-header"><strong>Actividad de líderes</strong></div>
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

@endsection


@push('scripts')
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

  // 2) Tablas
  fetch("{{ route('admin.dashboard.charts') }}")
    .then(r => r.json())
    .then(d => {
      // ---- TABLA 1: Aprendices por semillero
      let htmlApr = "";
      d.tablaAprendicesSem.forEach(row => {
        htmlApr += `
          <tr>
            <td>${row.semillero}</td>
            <td>${row.total_aprendices}</td>
          </tr>`;
      });
      document.getElementById('tablaAprendicesSem').innerHTML = htmlApr;

      // ---- TABLA 2: Proyectos por semillero
      let htmlProj = "";
      d.tablaProyectosSem.forEach(row => {
        const lista = row.proyectos.length
          ? row.proyectos.map(p => `<div>• ${p}</div>`).join('')
          : '<em>Sin proyectos</em>';

        htmlProj += `
          <tr>
            <td>${row.semillero}</td>
            <td>${lista}</td>
          </tr>`;
      });
      document.getElementById('tablaProyectosSem').innerHTML = htmlProj;

      // ---- TABLA 3: Actividad de líderes
      let htmlLid = "";
      d.actividadLideres.forEach(row => {
        htmlLid += `
          <tr>
            <td>${row.lider}</td>
            <td>${row.linea ?? '-'}</td>
            <td>${row.last_login ?? '<em>Sin registro</em>'}</td>
            <td>${row.last_login_humano}</td>
          </tr>`;
      });
      document.getElementById('tablaActividadLideres').innerHTML = htmlLid;
    })
    .catch(console.error);

})();
</script>
@endpush
