<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Panel del Aprendiz')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_pestaña.png') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  {{-- Estilos base comunes --}}
  <link href="{{ asset('css/common/base.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/common/layout.css') }}?v={{ time() }}" rel="stylesheet">

  {{-- Layout administrativo heredado y vistas --}}
  <link href="{{ asset('css/admin/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/admin/admin-views.css') }}?v={{ time() }}" rel="stylesheet">

  {{-- Estilos específicos del panel del Aprendiz --}}
  <link href="{{ asset('css/aprendiz/aprendiz.css') }}?v={{ time() }}" rel="stylesheet">

  {{-- Loader CSS global --}}
  <link href="{{ asset('css/common/loader.css') }}?v={{ time() }}" rel="stylesheet">


  @stack('styles')
  @yield('styles')
</head>
<body class="adm-body">
  {{-- Loader Overlay --}}
  <div id="pageLoader" class="page-loader" aria-hidden="true" role="status">
    <div class="loader">
      <div class="orbe" style="--index:0"></div>
      <div class="orbe" style="--index:1"></div>
      <div class="orbe" style="--index:2"></div>
      <div class="orbe" style="--index:3"></div>
      <div class="orbe" style="--index:4"></div>
    </div>
    <div class="loader-text">Cargando...</div>
  </div>
  <div class="adm-shell">
    <aside id="admSidebar" class="adm-sidebar">
      <div class="adm-brand brand-large animate__animated animate__flipInX">
        <img src="{{ asset('images/logo-sena.png') }}" alt="Logo SENA" class="brand-logo-lg">
        <div class="brand-system-lg">Sistema de Gestión Semillero</div>
      </div>

      <div class="menu-card">
        <nav class="adm-nav">
          <a href="{{ route('dashboard') }}"
             class="nav-link {{ (request()->routeIs('aprendiz.dashboard*') || request()->routeIs('dashboard')) ? 'active' : '' }}">
            <i class="bi bi-house-fill me-2"></i>
            <span>Inicio</span>
          </a>
          <a href="{{ route('aprendiz.proyectos.index') }}"
             class="nav-link {{ request()->routeIs('aprendiz.proyectos.*') ? 'active' : '' }}">
            <i class="bi bi-folder2-open me-2"></i>
            <span>Mis Proyectos</span>
          </a>
          <a href="{{ route('aprendiz.documentos.index') }}"
             class="nav-link {{ request()->routeIs('aprendiz.documentos.*') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload me-2"></i>
            <span>Subir Documentos</span>
          </a>
          <a href="{{ route('aprendiz.calendario.index') }}"
             class="nav-link {{ request()->routeIs('aprendiz.calendario.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event me-2"></i>
            <span>Calendario</span>
          </a>
          <a href="{{ route('aprendiz.perfil.show') }}"
             class="nav-link {{ request()->routeIs('aprendiz.perfil.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle me-2"></i>
            <span>Mi Perfil</span>
          </a>
        </nav>
      </div>
    </aside>

    <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    <div class="adm-content">
      <header class="adm-topbar">
        <div class="d-flex align-items-center gap-2">
          <button id="sidebarToggle" class="btn btn-outline-light d-lg-none">
            <i class="bi bi-list"></i>
          </button>
          <h1 class="h5 m-0 title-green">Panel del Aprendiz</h1>
        </div>

        <div class="profile-info">
          <div class="position-relative me-2">
            <button id="btnNotificaciones" class="btn btn-link text-white position-relative" type="button" aria-label="Notificaciones" aria-expanded="false">
              <i class="bi bi-bell fs-5"></i>
              <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
            </button>
            <div id="notifDropdown" class="card shadow" style="position:absolute; right:0; top:110%; min-width: 260px; display:none; z-index: 1100;">
              <div class="card-header py-2 px-3">Notificaciones</div>
              <div class="list-group list-group-flush">
                <a href="{{ route('aprendiz.documentos.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" id="notifItemEvidencias">
                  <span>Evidencias nuevas</span>
                  <span id="notifEvidencias" class="badge bg-success">0</span>
                </a>
                <a href="{{ route('aprendiz.calendario.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" id="notifItemReuniones">
                  <span>Reuniones próximas</span>
                  <span id="notifReuniones" class="badge bg-primary">0</span>
                </a>
                <a href="{{ route('aprendiz.documentos.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" id="notifItemRespuestas">
                  <span>Respuestas del líder</span>
                  <span id="notifRespuestas" class="badge bg-info text-dark">0</span>
                </a>
              </div>
            </div>
          </div>

          @php
            $apUser = Auth::user();
            // Intentar usar nombre + apellidos registrados en users
            $apDisplayName = trim(($apUser->nombre ?? '').' '.($apUser->apellidos ?? ''));
            if ($apDisplayName === '') {
                $apDisplayName = trim($apUser->name ?? '');
            }
            if ($apDisplayName === '' && !empty($apUser->email)) {
                $apDisplayName = $apUser->email;
            }
            $apParts = preg_split('/\s+/', $apDisplayName) ?: [];
            $apInitials = '';
            foreach ($apParts as $part) {
                if ($part === '') continue;
                $apInitials .= mb_substr($part, 0, 1);
                if (mb_strlen($apInitials) >= 2) break;
            }
            if ($apInitials === '' && $apDisplayName !== '') {
                $apInitials = mb_substr($apDisplayName, 0, 1);
            }
          @endphp

          <div class="avatar">{{ strtoupper($apInitials ?: 'AP') }}</div>
          <div class="me-2 text-end d-none d-sm-block">
            <div class="fw-semibold">{{ $apDisplayName }}</div>
            <small class="opacity-75">Aprendiz</small>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light btn-sm">
              <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
            </button>
          </form>
        </div>
      </header>

      <main class="adm-page">
        <div class="adm-page-head">
          <h2 class="page-title">@yield('module-title','')</h2>
          <p class="page-subtitle">@yield('module-subtitle','')</p>
        </div>

        <div class="adm-page-body">
          @yield('content')
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  (function () {
    const sidebar   = document.getElementById('admSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const body      = document.body;
    const MQ_LG     = 992;

    const isMobile = () => window.innerWidth < MQ_LG;

    function openSidebar() {
      if (!isMobile()) return;
      sidebar.classList.add('show');
      overlay.classList.add('show');
      toggleBtn?.setAttribute('aria-expanded', 'true');
      body.classList.add('noscroll');
    }
    function closeSidebar() {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
      toggleBtn?.setAttribute('aria-expanded', 'false');
      body.classList.remove('noscroll');
    }
    function toggleSidebar() {
      if (!isMobile()) return;
      sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    }

    toggleBtn?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
    window.addEventListener('resize', () => { if (!isMobile()) closeSidebar(); });
    sidebar?.addEventListener('click', (e) => {
      if (e.target.closest('a.nav-link') && isMobile()) closeSidebar();
    });
  })();
  // Notificaciones (campana)
  (function(){
    const badge = document.getElementById('notifBadge');
    const dd    = document.getElementById('notifDropdown');
    const btn   = document.getElementById('btnNotificaciones');
    const evEl  = document.getElementById('notifEvidencias');
    const reEl  = document.getElementById('notifReuniones');
    if (!btn) return;
    async function fetchSummary(){
      try{
        const resp = await fetch('{{ route('aprendiz.notifications.summary') }}', { headers: { 'Accept':'application/json' } });
        if (!resp.ok) return;
        const data = await resp.json();
        const total = Number(data?.total||0);
        const evid  = Number(data?.evidencias_nuevas||0);
        const reun  = Number(data?.reuniones_nuevas||0);
        const respL = Number(data?.respuestas_nuevas||0);
        if (badge){ badge.textContent = total; badge.style.display = total>0 ? 'inline-block' : 'none'; }
        if (evEl) evEl.textContent = evid;
        if (reEl) reEl.textContent = reun;
        const rsEl = document.getElementById('notifRespuestas');
        if (rsEl) rsEl.textContent = respL;
        btn.setAttribute('aria-label', `Notificaciones (${total})`);
      } catch(_e){}
    }
    fetchSummary();
    setInterval(fetchSummary, 60000); // cada 60s
    btn.addEventListener('click', (e)=>{
      e.preventDefault();
      const show = dd && dd.style.display !== 'block';
      if (dd){ dd.style.display = show ? 'block':'none'; btn.setAttribute('aria-expanded', show ? 'true':'false'); }
    });
    document.addEventListener('click', (e)=>{
      if (!dd || !btn) return;
      if (e.target === btn || btn.contains(e.target)) return;
      if (!dd.contains(e.target)) dd.style.display='none';
    });
  })();
  </script>

  {{-- Loader JS: ocultar al terminar carga --}}
  <script>
    (function(){
      const hide = () => {
        const el = document.getElementById('pageLoader');
        if (!el) return;
        el.classList.add('hidden');
        setTimeout(()=>{ try{ el.remove(); }catch(e){} }, 400);
      };
      if (document.readyState === 'complete') hide();
      window.addEventListener('load', hide);
    })();
  </script>

  @stack('scripts')
  @yield('scripts')
</body>
</html>
