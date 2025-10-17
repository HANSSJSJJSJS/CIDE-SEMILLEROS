<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title','CIDE SEMILLERO')</title>

  {{-- CSS principal del panel --}}
 <link rel="stylesheet" href="{{ asset('css/Style_layouts.css') }}?v={{ filemtime(public_path('css/Style_layouts.css')) }}">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  @stack('styles')
</head>
<body>
  {{-- Header --}}
  <header class="header">
    <div class="user-info">
      <div class="user-avatar">
        {{ strtoupper(Str::substr(Auth::user()->name ?? 'US',0,1)) }}{{ strtoupper(Str::substr(Auth::user()->name ?? 'US',1,1)) }}
      </div>
      <span style="font-weight:600;color:var(--gray-700);">{{ Auth::user()->name ?? 'Usuario' }}</span>
    </div>
    <h1 class="header-title">Bienvenido a CIDE SEMILLERO</h1>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout-btn" type="submit">
        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Cerrar sesión
      </button>
    </form>
  </header>

  {{-- Contenedor principal --}}
  <div class="container">
    {{-- Sidebar --}}
    <aside class="sidebar">
      <nav>
        <button class="nav-item active" onclick="showSection('dashboard', this)">
          <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Dashboard
        </button>

        <button class="nav-item" onclick="showSection('users', this)">
          <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Gestión de usuarios
        </button>

        <button class="nav-item" onclick="showSection('semilleros', this)">
          Gestión de semilleros
        </button>

        <button class="nav-item" onclick="showSection('reports', this)">Reportes</button>
        <button class="nav-item" onclick="showSection('activity', this)">Actividad del sistema</button>
        <button class="nav-item" onclick="showSection('settings', this)">Configuración</button>
      </nav>
    </aside>

    {{-- Main: secciones cargadas como parciales --}}
    <main class="main-content">
      <section id="dashboard" class="content-section active">
        @include('admin.sections.dashboard')
      </section>

      <section id="users" class="content-section">
        @include('admin.sections.users')
      </section>

      <section id="semilleros" class="content-section">
        @include('admin.sections.semillero')
      </section>

      <section id="reports" class="content-section">
        @include('admin.sections.reports')
      </section>

      <section id="activity" class="content-section">
        @include('admin.sections.gestion_usuario')
      </section>

      <section id="settings" class="content-section">
        @include('admin.sections.settings')
      </section>
    </main>
  </div>

  <script>
    function showSection(id, el){
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
      document.getElementById(id)?.classList.add('active');
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      el?.classList.add('active');
    }
    function openModal(id){ document.getElementById(id)?.classList.add('active'); }
    function closeModal(id){ document.getElementById(id)?.classList.remove('active'); }
    window.addEventListener('click', (e)=>{
      if (e.target.classList?.contains('modal')) e.target.classList.remove('active');
    });
  </script>

  @stack('scripts')
</body>
</html>
