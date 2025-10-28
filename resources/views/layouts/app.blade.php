<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','SCIDES')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{ --scides-green:#34a300; --scides-green-dark:#258000; }
    html,body{height:100%} body{background:var(--scides-green); color:#fff}
    .topbar{border:2px solid #fff;border-bottom:none;border-radius:14px 14px 0 0;padding:.6rem 1rem}
    .board{border:2px solid #fff;border-top:none;border-radius:0 0 14px 14px;min-height:calc(100dvh - 3.5rem);padding:1.25rem}
    .side-frame{background:#fff;border-radius:18px;padding:1rem .9rem;box-shadow:0 8px 30px rgba(0,0,0,.12)}
    .side-inner{background:var(--scides-green-dark);border-radius:12px;padding:1rem}
    .pill-btn{background:#fff;color:#1c1c1c;border:none;border-radius:999px;padding:.55rem .9rem;font-weight:600;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 0 rgba(0,0,0,.15)}
    .pill-btn:hover{transform:translateY(1px);box-shadow:0 4px 0 rgba(0,0,0,.2)}
    .pill-btn:active{transform:translateY(2px);box-shadow:0 2px 0 rgba(0,0,0,.25)}
    .big-title{font-weight:800;font-size:clamp(2rem,2.6rem + 1.2vw,4rem)}
    @media (max-width: 991.98px){ .sidebar-desktop{display:none!important} }
  </style>

  @stack('styles')
</head>
<body>
  <div class="container-xxl mt-3">
    <div class="topbar d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-person-fill" style="font-size:1.5rem"></i>
        <strong>{{ Auth::user()->name ?? 'Usuario' }}</strong>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
          <i class="bi bi-list"></i> Menú
        </button>
        <a href="{{ route('password.change') }}" class="btn btn-light">
          <i class="bi bi-lock-fill me-2"></i> Cambio contraseña
        </a>
        <a href="{{ route('logout') }}" class="btn btn-light"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right me-2"></i> Salida
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
      </div>
    </div>

    <div class="board">
      @yield('content')
    </div>
  </div>

  {{-- Offcanvas (móvil) --}}
  <div class="offcanvas offcanvas-start text-dark" tabindex="-1" id="offcanvasMenu">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title"><i class="bi bi-people-fill me-2"></i> Menú</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="d-grid gap-3">
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('admin.functions') }}">Funciones del administrador</a>
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('lideres.create') }}">Registro aprendiz líderes</a>
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('semilleros.create') }}">Crea grupos semilleros</a>
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('aprendices.create') }}">Crea perfiles de aprendiz</a>
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('semilleros.index') }}">Semilleros de investigación</a>
        <a class="btn btn-outline-success rounded-pill text-start fw-semibold" href="{{ route('grupos.index') }}">Gestión de grupos de investigación</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
