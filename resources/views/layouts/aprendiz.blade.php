<!doctype html>
<html lang="es">
<head>
<<<<<<< HEAD
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Panel del Aprendiz')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="{{ asset('css/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/admin-views.css') }}?v={{ time() }}" rel="stylesheet">
  <link href="{{ asset('css/aprendiz.css') }}?v={{ time() }}" rel="stylesheet">

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
          <a href="{{ route('aprendiz.archivos.index') }}"
             class="nav-link {{ request()->routeIs('aprendiz.archivos.*') ? 'active' : '' }}">
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
=======
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel del Aprendiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/lider.css') }}" rel="stylesheet">
    @stack('styles')
    @yield('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 col-lg-2 sidebar d-none d-md-flex flex-column">
            <div class="px-3 mb-4">
                <img src="{{ asset('images/logo-sena.png') }}" alt="Logo" class="logo-sena">
                <p class="text-muted small">Sistema de Gestión Semillero</p>
            </div>
            <ul class="nav flex-column px-2">
                <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-house-fill me-2"></i> Inicio</a></li>
                <li><a href="{{ route('aprendiz.proyectos.index') }}" class="nav-link {{ request()->routeIs('aprendiz.proyectos.*') ? 'active' : '' }}"><i class="bi bi-folder2-open me-2"></i> Mis Proyectos</a></li>
                <li><a href="{{ route('aprendiz.archivos.index') }}" class="nav-link {{ request()->routeIs('aprendiz.archivos.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-arrow-up me-2"></i> Documentación</a></li>
                <li><a href="{{ route('aprendiz.calendario.index') }}" class="nav-link {{ request()->routeIs('aprendiz.calendario.*') ? 'active' : '' }}"><i class="bi bi-calendar-event me-2"></i> Calendario</a></li>
                <li><a href="{{ route('aprendiz.perfil.show') }}" class="nav-link {{ request()->routeIs('aprendiz.perfil.*') ? 'active' : '' }}"><i class="bi bi-person-circle me-2"></i> Mi Perfil</a></li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 col-lg-10 p-0">
            <!-- Top bar -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                        <i class="bi bi-list"></i> Menú
                    </button>
                    <h5 class="fw-bold mb-0">Aprendiz</h5>
                </div>
                <div class="profile-info">
                    <div class="avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AP', 0, 2)) }}
                    </div>
                    <div>
                        <div>{{ Auth::user()->name }}</div>
                        <small>Aprendiz</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ms-3">
                        @csrf
                        <button class="btn btn-navy btn-sm">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <div class="p-4">
                <div class="content-shell">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Offcanvas (móvil) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel"><i class="bi bi-people-fill me-2"></i> Menú</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-grid gap-2">
            <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('dashboard') }}"><i class="bi bi-house-fill me-2"></i> Inicio</a>
            <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('aprendiz.proyectos.index') }}"><i class="bi bi-folder2-open me-2"></i> Mis Proyectos</a>
            <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('aprendiz.archivos.index') }}"><i class="bi bi-file-earmark-arrow-up me-2"></i> Documentación</a>
            <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('aprendiz.calendario.index') }}"><i class="bi bi-calendar-event me-2"></i> Calendario</a>
            <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('aprendiz.perfil.show') }}"><i class="bi bi-person-circle me-2"></i> Mi Perfil</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 7270b2e (avances aprendiz)
</body>
</html>
