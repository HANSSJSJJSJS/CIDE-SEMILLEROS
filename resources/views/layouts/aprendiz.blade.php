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

  <link href="{{ asset('css/global/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/admin/admin-views.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/aprendiz/aprendiz.css') }}?v={{ time() }}" rel="stylesheet">


  @stack('styles')
  @yield('styles')
</head>
<body class="adm-body">
  <div class="adm-shell">
    <aside id="admSidebar" class="adm-sidebar">
      <div class="adm-brand brand-large">
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
          <button class="btn btn-link text-white position-relative me-2" type="button" aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
          </button>

          <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AP', 0, 2)) }}</div>
          <div class="me-2 text-end d-none d-sm-block">
            <div class="fw-semibold">{{ Auth::user()->name }}</div>
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
  </script>

  @stack('scripts')
  @yield('scripts')
</body>
</html>
