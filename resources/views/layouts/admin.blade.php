<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Panel Administrador</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  {{-- si tienes un CSS de admin, ponlo acá --}}
  @stack('styles')
  @yield('styles')
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      {{-- Sidebar admin (enlaces de admin, no del líder) --}}
      <nav class="col-md-2 col-lg-2 sidebar d-flex flex-column">
        <div class="px-3 mb-4">
          <img src="{{ asset('images/logo-sena.png') }}" alt="Logo" class="logo-sena">
          <p class="text-muted small">Panel de Administración</p>
        </div>
        <ul class="nav flex-column px-2">
          <li><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-house-fill me-2"></i> Inicio</a></li>
          <li><a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"><i class="bi bi-people-fill me-2"></i> Usuarios</a></li>
          <li><a href="{{ route('admin.semilleros.index') }}" class="nav-link {{ request()->routeIs('admin.semillers.*') || request()->routeIs('admin.semilleros.*') ? 'active' : '' }}"><i class="bi bi-diagram-3 me-2"></i> Semilleros</a></li>
          {{-- agrega aquí otros módulos de ADMIN --}}
        </ul>
      </nav>

      {{-- Main --}}
      <main class="col-md-10 col-lg-10 p-0">
        <div class="topbar d-flex justify-content-between align-items-center px-3 py-2" style="background:#2d572c;color:#fff;">
          <h5 class="fw-bold mb-0">Administrador</h5>
          <div class="d-flex align-items-center gap-3">
            <div class="avatar bg-light text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
              {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
              @csrf
              <button class="btn btn-light btn-sm">Cerrar sesión</button>
            </form>
          </div>
        </div>

        <div class="p-4">
          @yield('content')  {{-- SOLO SLOT DE CONTENIDO, sin nada del líder --}}
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
