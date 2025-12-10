<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Panel del Líder de Semillero')</title>
      <link rel="icon" type="image/png" href="{{ asset('images/logo_pestaña.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Estilos base comunes --}}
    <link href="{{ asset('css/common/base.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/common/layout.css') }}?v={{ time() }}" rel="stylesheet">

    {{-- Layout administrativo heredado y vistas --}}
    <link href="{{ asset('css/admin/admin-layout.css') }}?v={{ time() }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/admin/admin-views.css') }}?v={{ time() }}" rel="stylesheet"> --}}

    {{-- Estilos específicos del panel de Líder de Semillero --}}
    <link href="{{ asset('css/lider_semi/lider.css') }}?v={{ time() }}" rel="stylesheet">
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
          <a href="{{ route('lider_semi.dashboard') }}" class="nav-link {{ request()->routeIs('lider_semi.dashboard*') ? 'active' : '' }}">
            <i class="bi bi-house-fill me-2"></i>
            <span>Inicio</span>
          </a>
          <a href="{{ route('lider_semi.semilleros') }}" class="nav-link {{ request()->routeIs('lider_semi.semilleros*') ? 'active' : '' }}">
            <i class="bi bi-hdd-stack me-2"></i> <span>Mis Proyectos</span>
          </a>
          <a href="{{ route('lider_semi.aprendices') }}" class="nav-link {{ request()->routeIs('lider_semi.aprendices*') ? 'active' : '' }}">
            <i class="bi bi-person-video2 me-2"></i> <span>Aprendices</span>
          </a>
          <a href="{{ route('lider_semi.documentos') }}" class="nav-link {{ request()->routeIs('lider_semi.documentos*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text me-2"></i> <span>Documentación</span>
          </a>
         <a href="{{ route('lider_semi.recursos.index') }}" class="nav-link {{ request()->routeIs('lider_semi.recursos.*') ? 'active' : '' }}">
          <i class="bi bi-archive me-2"></i> <span>Recursos</span> </a>
          <a href="{{ route('lider_semi.calendario') }}" class="nav-link {{ request()->routeIs('lider_semi.calendario*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event me-2"></i> <span>Calendario</span>
          </a>
          <a href="{{ route('lider_semi.perfil') }}" class="nav-link {{ request()->routeIs('lider_semi.perfil*') ? 'active' : '' }}">
            <i class="bi bi-person-circle me-2"></i> <span>Mi Perfil</span>
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
          <h1 class="h5 m-0 title-green">Líder de Semillero</h1>
        </div>

        <div class="profile-info">
          <button class="btn btn-link text-white position-relative me-2" type="button" aria-label="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
          </button>
          @php
            $lsUser = Auth::user();
            $lsDisplayName = trim($lsUser->name ?? '');
            if (class_exists('Illuminate\\Support\\Facades\\Schema') && class_exists('Illuminate\\Support\\Facades\\DB')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('lideres_semillero')) {
                    $lsProfile = \Illuminate\Support\Facades\DB::table('lideres_semillero')->where('id_usuario', $lsUser->id)->first();
                    if ($lsProfile) {
                        $lsDisplayName = trim(($lsProfile->nombres ?? '').' '.($lsProfile->apellidos ?? '')) ?: $lsDisplayName;
                    }
                }
            }
            if ($lsDisplayName === '' && !empty($lsUser->email)) {
                $lsDisplayName = $lsUser->email;
            }
            $lsParts = preg_split('/\s+/', $lsDisplayName) ?: [];
            $lsInitials = '';
            foreach ($lsParts as $part) {
                if ($part === '') continue;
                $lsInitials .= mb_substr($part, 0, 1);
                if (mb_strlen($lsInitials) >= 2) break;
            }
            if ($lsInitials === '' && $lsDisplayName !== '') {
                $lsInitials = mb_substr($lsDisplayName, 0, 1);
            }
          @endphp

          <div class="avatar">{{ strtoupper($lsInitials ?: 'LS') }}</div>
          <div class="me-2 text-end d-none d-sm-block">
            <div class="fw-semibold">{{ $lsDisplayName }}</div>
            <small class="opacity-75">Líder de Semillero</small>
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
</body>

</html>
