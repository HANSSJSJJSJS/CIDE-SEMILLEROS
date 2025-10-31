<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Mis Proyectos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="{{ asset('css/aprendiz.css') }}" rel="stylesheet">
    <style>
        :root{ --header-h: 68px; --gap: 16px; }
        body.main-bg{background:linear-gradient(180deg,#f7fafc 0,#eef7f1 100%)}
        .header-bar{background:#ffffff;border-radius:16px;border:1px solid #e9ecef}
        .header-bar .brand-text{color:#198754}
        /* Sidebar estático para evitar superposición sobre el contenido */
        .sidebar{background:#ffffff;border:1px solid #e9ecef;border-radius:16px; position:static; top:auto}
        .sidebar .nav-link{color:#0f172a;border-radius:12px;padding:.65rem 1rem}
        .sidebar .nav-link:hover{background:#f1f5f9}
        .sidebar .nav-link.active{background:#0d6efd;color:#fff}
        /* El main arranca a la misma altura visual que el sidebar */
        .content-wrapper{border:1px solid #e9ecef;border-radius:16px;background:#fff; margin-top: 0; position:relative; z-index:1}
        .project-card{border:1px solid #e9ecef;border-radius:16px;transition:.2s;position:relative;background:#fff;display:flex;flex-direction:column;height:100%}
        .project-card:hover{box-shadow:0 8px 24px rgba(16,24,40,.08);transform:translateY(-1px)}
        .project-head{background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border-top-left-radius:16px;border-top-right-radius:16px;padding:14px 16px}
        .project-body{padding:16px}
        .project-footer{padding:16px; padding-top:0}
        .badge.status{border-radius:999px;padding:.4rem .75rem;font-weight:700;letter-spacing:.3px}
        .meta-title{font-size:.8rem;color:#64748b;font-weight:700;text-transform:uppercase}
        .btn-pill{border-radius:999px}
        .container-fluid{max-width:1200px; margin:0 auto}
        main.main-col{padding-top: calc(var(--gap));}
    </style>
</head>

<body class="bg-light main-bg">
    <!-- ENCABEZADO -->
<header class="header-bar shadow-sm d-flex justify-content-between align-items-center px-4 py-3" style="height: var(--header-h); margin-bottom: var(--gap);">
    <h1 class="m-0 text-success fw-bold">Bienvenido a <span class="brand-text">CIDE SEMILLERO</span></h1>
    <div class="user-info d-flex align-items-center gap-3">
        <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-dark btn-sm px-3 btn-pill">Cerrar sesión</button>
        </form>
    </div>
</header>

<div class="container-xxl py-4">
  <div class="row gx-4">
    <!-- Sidebar -->
    <aside class="col-12 col-lg-2 mb-4 mb-lg-0">
      <div class="sidebar-card p-3">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('images/logo-sena.png') }}" alt="SENA" style="width:40px;height:40px;object-fit:contain">
          <div>
            <div class="fw-bold" style="font-size:1.05rem;line-height:1.1">Sistema de<br>Gestión</div>
            <small class="text-muted">Semillero</small>
          </div>
        </div>

        <nav class="d-grid gap-2">
          <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-house-fill me-2"></i> Inicio
          </a>
          <a class="side-link {{ request()->routeIs('aprendiz.proyectos.index') ? 'active' : '' }}" href="{{ route('aprendiz.proyectos.index') }}">
            <i class="bi bi-folder-fill me-2"></i> Mis Proyectos
          </a>
          <a class="side-link {{ request()->routeIs('aprendiz.archivos.*') ? 'active' : '' }}" href="{{ route('aprendiz.archivos.index') }}">
            <i class="bi bi-upload me-2"></i> Subir Documentos
          </a>
          <a class="side-link {{ request()->routeIs('aprendiz.perfil.*') ? 'active' : '' }}" href="{{ route('aprendiz.perfil.show') }}">
            <i class="bi bi-person-fill me-2"></i> Mi Perfil
          </a>
          <a class="side-link {{ request()->routeIs('aprendiz.calendario.*') ? 'active' : '' }}" href="{{ route('aprendiz.calendario.index') }}">
            <i class="bi bi-calendar-event-fill me-2"></i> Calendario
          </a>
        </nav>
      </div>
    </aside>

    <!-- Main content -->
    <main class="col-12 col-lg-10">
      <div class="content-wrapper shadow-sm p-4">
        <div class="text-center mb-4">
          <h2 class="fw-bold m-0">Mis Proyectos</h2>
          <p class="text-muted mb-0">Proyectos asignados en el semillero SENA</p>
        </div>

        <div class="filters-bar mb-3">
          <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6 col-xl-4">
              <label class="form-label mb-1">Nombre del proyecto</label>
              <div class="input-icon">
                <i class="bi bi-search"></i>
                <input id="filterName" type="text" class="form-control pill-input" placeholder="Buscar por nombre...">
              </div>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
              <label class="form-label mb-1">Desde</label>
              <input id="filterStart" type="date" class="form-control pill-select">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
              <label class="form-label mb-1">Hasta</label>
              <input id="filterEnd" type="date" class="form-control pill-select">
            </div>
            <div class="col-12 col-md-12 col-xl-2">
              <button id="filterClear" class="btn btn-outline-success btn-pill w-100">Limpiar filtros</button>
            </div>
          </div>
        </div>

        <div class="row g-4 g-xxl-5">
          @forelse ($proyectos as $proyecto)
            @php
              $dataName = strtolower($proyecto->nombre_proyecto ?? '');
              $dataIni  = \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('Y-m-d');
              $dataFin  = \Carbon\Carbon::parse($proyecto->fecha_fin)->format('Y-m-d');
            @endphp
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 col-xxl-3 d-flex project-col" data-name="{{ $dataName }}" data-start="{{ $dataIni }}" data-end="{{ $dataFin }}">
              <div class="project-card shadow-sm w-100">
                <div class="project-head d-flex justify-content-between align-items-center">
                  <h5 class="fw-bold m-0 text-white me-2">{{ $proyecto->nombre_proyecto }}</h5>
                  @php 
                    $estado_raw = strtolower($proyecto->estado ?? '');
                    $estado_norm = str_replace(' ', '_', $estado_raw);
                    $isActive = in_array($estado_norm, ['activo','en_ejecucion']);
                  @endphp
                  <span class="status-chip {{ $isActive ? 'is-active' : '' }}">{{ strtoupper($proyecto->estado) }}</span>
                </div>
                <div class="project-body">
                  <div class="meta-title">Fechas del Proyecto</div>
                  <div class="text-muted small mb-2">Inicio: {{ date('d/m/Y', strtotime($proyecto->fecha_inicio)) }} | Fin: {{ date('d/m/Y', strtotime($proyecto->fecha_fin)) }}</div>
                  <p class="text-muted mb-3">{{ $proyecto->descripcion }}</p>
                  <p class="mb-3 small">
                    <strong>Semillero:</strong> {{ $proyecto->semillero->nombre ?? 'No asignado' }}<br>
                    <strong>Tipo:</strong> {{ $proyecto->tipoProyecto->nombre ?? 'Sin tipo' }}
                  </p>
                </div>
                <div class="project-footer">
                  <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('aprendiz.proyectos.show', $proyecto->id_proyecto) }}" class="btn btn-success btn-pill px-4 fw-semibold">Ver Detalles</a>
                    <a href="{{ route('aprendiz.archivos.create', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-outline-success btn-pill px-4 fw-semibold">Subir Docs</a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12 text-center text-muted py-5">
              <i class="bi bi-kanban fs-1 d-block mb-2"></i>
              No tienes proyectos asignados aún.
            </div>
          @endforelse
        </div>
      </div>
    </main>
  </div>
</div>

<script>
  (function(){
    const nameI = document.getElementById('filterName');
    const startI = document.getElementById('filterStart');
    const endI = document.getElementById('filterEnd');
    const clearB = document.getElementById('filterClear');
    const items = Array.from(document.querySelectorAll('.project-col'));

    function apply(){
      const q = (nameI?.value || '').trim().toLowerCase();
      const ds = startI?.value || '';
      const de = endI?.value || '';
      items.forEach(el => {
        const n = el.getAttribute('data-name') || '';
        const s = el.getAttribute('data-start') || '';
        const e = el.getAttribute('data-end') || '';
        let ok = true;
        if(q) ok = ok && n.includes(q);
        if(ds) ok = ok && (s >= ds);
        if(de) ok = ok && (e <= de);
        el.classList.toggle('d-none', !ok);
      });
    }
    nameI?.addEventListener('input', apply);
    startI?.addEventListener('change', apply);
    endI?.addEventListener('change', apply);
    clearB?.addEventListener('click', (e)=>{ e.preventDefault(); if(nameI) nameI.value=''; if(startI) startI.value=''; if(endI) endI.value=''; apply(); });
  })();
</script>
</body>
</html>
