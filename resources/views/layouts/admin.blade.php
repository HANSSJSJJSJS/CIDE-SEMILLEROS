<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Panel del Administrador')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  {{-- NUEVO tema admin --}}
  {{-- CSS del layout (estructura) --}}
<link href="{{ asset('css/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
{{-- CSS de vistas (componentes reutilizables) --}}
<link href="{{ asset('css/admin-views.css') }}?v={{ time() }}" rel="stylesheet">


  @stack('styles')
  @yield('styles')
</head>
<body class="adm-body">
  <div class="adm-shell">
    {{-- Sidebar --}}
   <aside id="admSidebar" class="adm-sidebar">
  {{-- Brand más grande, texto debajo --}}
  <div class="adm-brand brand-large">
    <img src="{{ asset('images/logo-sena.png') }}" alt="Logo SENA" class="brand-logo-lg">
    <div class="brand-system-lg">Sistema de Gestión Semillero</div>
  </div>

  {{-- Contenedor semitransparente para el menú --}}
  <div class="menu-card">
    <nav class="adm-nav">
      <a href="{{ route('admin.dashboard') }}"
         class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-fill me-2"></i> <span>Inicio</span>
      </a>

      <a href="{{ route('admin.usuarios.index') }}"
         class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
        <i class="bi bi-people-fill me-2"></i> <span>Gestión de Usuarios</span>
      </a>

      <a href="{{ route('admin.semilleros.index') }}"
         class="nav-link {{ request()->routeIs('admin.semilleros.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3 me-2"></i> <span>Gestión de semilleros</span>
      </a>

      <a href="{{ route('admin.reuniones-lideres.index') }}"
      class="nav-link {{ request()->routeIs('admin.reuniones-lideres.*') ? 'active' : '' }}">
      <i class="bi bi-calendar-event me-2"></i> <span>Reuniones</span>
    </a>

      <a href="{{ route('lider_semi.recursos') }}"
         class="nav-link {{ request()->routeIs('lider_semi.recursos') ? 'active' : '' }}">
        <i class="bi bi-journal-text me-2"></i> <span>Recursos</span>
      </a>

      <a href="{{ route('lider_semi.perfil') }}"
         class="nav-link {{ request()->routeIs('lider_semi.perfil') ? 'active' : '' }}">
        <i class="bi bi-person-circle me-2"></i> <span>Mi Perfil</span>
      </a>
    </nav>
  </div>
</aside>

      <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    {{-- Contenido --}}
    <div class="adm-content">
      {{-- Top bar --}}
      <header class="adm-topbar">
        <div class="d-flex align-items-center gap-2">
          <button id="sidebarToggle" class="btn btn-outline-light d-lg-none">
            <i class="bi bi-list"></i>
          </button>
          <h1 class="h5 m-0 title-green">Líder General</h1>
        </div>

        <div class="profile-info">
          <button class="btn btn-link text-white position-relative me-2" type="button" aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
          </button>

          <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}</div>
          <div class="me-2 text-end d-none d-sm-block">
            <div class="fw-semibold">{{ Auth::user()->name }}</div>
            <small class="opacity-75">Líder General</small>
          </div>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light btn-sm">
              <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
            </button>
          </form>
        </div>
      </header>

      {{-- Área de página con fondo decorativo --}}
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

  <!-- 👇 Reemplaza tu script anterior por este -->
  <script>
  (function () {
    const sidebar   = document.getElementById('admSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const body      = document.body;
    const MQ_LG     = 992; // breakpoint Bootstrap lg

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

    // Cerrar al hacer click en un link del menú (en móvil)
    sidebar?.addEventListener('click', (e) => {
      if (e.target.closest('a.nav-link') && isMobile()) closeSidebar();
    });
  })();
  </script>

  @stack('scripts')
  @yield('scripts')
</body>
</html>
