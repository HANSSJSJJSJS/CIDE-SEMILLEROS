@extends('layouts.lider_semi')

@section('content')
<style>
  .dash-wrap{background:#f5f8fb;padding:1rem 0}
  .dash-title{font-weight:800;color:#0f172a}
  .metric-card{border:1px solid #e2e8f0;border-radius:16px;padding:18px;background:#fff;box-shadow:0 6px 20px rgba(15,23,42,.06);position:relative}
  .metric-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:8px}
  .metric-kpi{font-size:28px;font-weight:800;color:#0f172a;margin:0}
  .metric-sub{color:#64748b;margin:0}
  .metric-pill{display:inline-block;margin-top:6px;font-size:12px;padding:4px 10px;border-radius:999px}
  .pill-green{background:#e6f6ed;color:#22c55e}
  .pill-yellow{background:#fff7e6;color:#f59e0b}
  .list-card{border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 6px 20px rgba(15,23,42,.06)}
  .item{display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;background:#fff}
  .item+.item{margin-top:10px}
  .avatar{width:36px;height:36px;border-radius:999px;background:#e2f7ee;color:#0f766e;font-weight:800;display:flex;align-items:center;justify-content:center}
  .status-pill{font-size:12px;padding:4px 10px;border-radius:999px;background:#ecfdf5;color:#16a34a}
  .bar{height:6px;background:#e2e8f0;border-radius:999px;overflow:hidden}
  .bar>span{display:block;height:100%;background:#22c55e}
  .actions-card{border:1px solid #e2e8f0;border-radius:16px;background:#fff;box-shadow:0 6px 20px rgba(15,23,42,.06)}
  .btn-block{display:block;width:100%;border-radius:12px;padding:12px 16px;font-weight:700}
  .btn-green{background:#22c55e;color:#fff}
  .btn-green:hover{filter:brightness(1.05)}
  .btn-dark{background:#0f172a;color:#fff}
  .btn-outline-blue{background:#fff;border:2px solid #2563eb;color:#2563eb}
  .btn-outline-blue:hover{background:#eff6ff}
</style>

<div class="container-fluid dash-wrap">
  <h3 class="dash-title mb-1">¡Bienvenido, {{ strtolower(Auth::user()->name) }}! 👋</h3>
  <p class="text-muted mb-4">Aquí está el resumen de tus semilleros y actividades recientes.</p>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="metric-card">
        <div class="metric-icon" style="background:#e6f6ed;color:#16a34a"><i class="bi bi-people-fill"></i></div>
        <p class="metric-kpi">{{ $semilleros->count() }}</p>
        <p class="metric-sub">Semilleros Activos</p>
        <span class="metric-pill pill-green">+2 este mes</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="metric-card">
        <div class="metric-icon" style="background:#e0f2fe;color:#2563eb"><i class="bi bi-person-badge"></i></div>
        <p class="metric-kpi">{{ $totalAprendices }}</p>
        <p class="metric-sub">Aprendices Totales</p>
        <span class="metric-pill" style="background:#e0f2fe;color:#2563eb">+8 este mes</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="metric-card">
        <div class="metric-icon" style="background:#fff7e6;color:#f59e0b"><i class="bi bi-file-earmark-check"></i></div>
        <p class="metric-kpi">{{ max(0, ($totalAprendices - $documentosPendientes)) }}</p>
        <p class="metric-sub">Documentos Revisados</p>
        <span class="metric-pill pill-yellow">{{ $documentosPendientes }} pendientes</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="metric-card">
        <div class="metric-icon" style="background:#e6f6ed;color:#22c55e"><i class="bi bi-graph-up-arrow"></i></div>
        <p class="metric-kpi">{{ (int) $progresoPromedio }}%</p>
        <p class="metric-sub">Progreso Promedio</p>
        <span class="metric-pill pill-green">+12%</span>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="list-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="fw-bold mb-0">Actividad de Aprendices</h5>
          <a href="#" class="small text-primary">Ver todos →</a>
        </div>
        @php
          $items = $actividadReciente ?? [];
        @endphp
        @forelse($items as $act)
          @php
            $titulo = $act['titulo'] ?? 'Actividad';
            preg_match('/^([A-Za-zÁÉÍÓÚÜÑ][^\s]*)\s+([A-Za-zÁÉÍÓÚÜÑ])?/u', $titulo, $m);
            $ini = strtoupper((($m[1] ?? 'A')[0] ?? 'A') . ($m[2] ?? ''));
            $status = $act['tipo'] === 'documento' ? 'Pendiente' : 'Activo';
            $statusClass = $status === 'Pendiente' ? 'pill-yellow' : 'pill-green';
            $pct = $status === 'Pendiente' ? 35 : 78;
          @endphp
          <div class="item">
            <div class="avatar">{{ $ini }}</div>
            <div class="flex-fill">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">{{ $titulo }}</div>
                <span class="status-pill {{ $statusClass }}">{{ $status }}</span>
              </div>
              <div class="text-muted small">{{ $act['descripcion'] ?? '' }}</div>
              <div class="d-flex align-items-center gap-2 mt-2">
                <div class="bar flex-fill"><span style="width: {{ $pct }}%"></span></div>
                <small class="text-muted">{{ $act['tiempo'] ?? '' }}</small>
              </div>
            </div>
          </div>
        @empty
          <div class="text-muted p-3">No hay actividad reciente.</div>
        @endforelse
      </div>
    </div>

    <div class="col-lg-4">
      <div class="actions-card p-4">
        <h5 class="fw-bold mb-3">Acciones Rápidas</h5>
        <div class="d-grid gap-3">
          <a href="{{ route('lider_semi.documentos') }}" class="btn-block btn-green"><i class="bi bi-plus-circle me-2"></i> Crear Evidencia</a>
          <a href="{{ route('lider_semi.semilleros') }}" class="btn-block btn-dark"><i class="bi bi-person-plus me-2"></i> Asignar Aprendiz</a>
          <a href="{{ route('lider_semi.calendario') }}" class="btn-block btn-outline-blue"><i class="bi bi-calendar-plus me-2"></i> Programar Reunión</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
