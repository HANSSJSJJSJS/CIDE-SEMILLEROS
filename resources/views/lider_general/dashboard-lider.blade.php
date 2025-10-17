{{-- resources/views/dashboard.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SCIDES · Dashboard Lider General</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      /* Verde SENA aprox */
      --sena-green: #2BB673;
      --sena-green-dark:#1f8f59;
      --light-gray:#f4f6f8;
    }

    body{ background: var(--light-gray); }

    /* CONTENEDOR PADRE */
    .app-wrap{
      max-width: 1280px;
      margin: 24px auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,.06);
      overflow: hidden;
    }

    /* NAV SUPERIOR */
    .topbar{
      background: #fff;
      border-bottom: 1px solid #e9ecef;
      padding: .75rem 1rem;
    }
    .user-pill{
      background: #fff;
      color: var(--sena-green);
      border: 2px solid var(--sena-green);
      border-radius: 999px;
      padding: .35rem .8rem;
      font-weight: 700;
      display: inline-flex; align-items: center; gap:.5rem;
    }
    .top-title{
      font-weight: 800;
      color: #111;
      text-transform: lowercase; /* respeta tu “bienvenidso a” en minúsculas */
      letter-spacing: .3px;
    }

    /* LAYOUT PRINCIPAL */
    .main-area{
      padding: 1.25rem;
      background: #fff;
    }

    /* PANEL LATERAL */
    .side-outer{
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 16px;
      padding: 10px;
    }
    .side-inner{
      background: var(--sena-green);
      border-radius: 12px;
      padding: 14px;
    }
    .menu-btn{
      background: #fff;
      color: #111;
      border: none;
      border-radius: 999px;
      padding: .65rem .9rem;
      font-weight: 700;
      width: 100%;
      text-align: left;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 4px 0 rgba(0,0,0,.12);
      transition: transform .05s ease, box-shadow .05s ease;
    }
    .menu-btn:hover{ transform: translateY(1px); box-shadow: 0 3px 0 rgba(0,0,0,.18); }
    .menu-btn:active{ transform: translateY(2px); box-shadow: 0 2px 0 rgba(0,0,0,.2); }

    /* CONTENIDO (a la derecha del panel) */
    .content-card{
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 16px;
      min-height: 320px;
      padding: 24px;
    }

    @media (max-width: 991.98px){
      .center-on-mobile{
        justify-content: center !important;
      }
    }
  </style>
</head>
<body>

  <!-- CONTENEDOR PADRE -->
  <div class="app-wrap">

    <!-- NAV -->
    <nav class="topbar">
      <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2 center-on-mobile">
            <span class="user-pill">
              <i class="bi bi-person-fill"></i>
              {{ Auth::user()->name ?? 'Usuario' }}
            </span>
          </div>

          <!-- Título centrado visualmente (se centra en mobile con utilidades) -->
          <div class="d-none d-lg-flex justify-content-center flex-grow-1 position-absolute start-0 end-0">
            <h5 class="top-title m-0">Bienvenido al Modulo Lider General</h5>
          </div>
          <div class="d-flex d-lg-none flex-grow-1 justify-content-center">
            <h5 class="top-title m-0">BIENVENIDO</h5>
          </div>

          <!-- Espaciador derecho (vacío porque no pediste acciones a la derecha) -->
          <div style="width:140px"></div>
        </div>
      </div>
    </nav>

    <!-- MAIN -->
    <div class="main-area">
      <div class="row g-3">
        <!-- PANEL IZQUIERDO -->
        <div class="col-lg-4 col-xl-3">
          <div class="side-outer">
            <div class="side-inner">
              <div class="d-grid gap-3">
                <a class="menu-btn" href="{{ route('usuarios.index', [], false) }}">
                  <span>Gestión de usuarios</span>
                  <i class="bi bi-chevron-right"></i>
                </a>

                <a class="menu-btn" href="{{ route('semilleros.index', [], false) }}">
                  <span>Gestión de semilleros</span>
                  <i class="bi bi-chevron-right"></i>
                </a>

                <a class="menu-btn" href="{{ route('profile.edit', [], false) }}">
                  <span>Gestión de cuenta</span>
                  <i class="bi bi-chevron-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- CONTENIDO DERECHA -->
        <div class="col-lg-8 col-xl-9">
          <div class="content-card">
            {{-- Aquí tu contenido dinámico de cada módulo --}}
            <h4 class="mb-3">Panel principal</h4>
            <p class="text-muted mb-0">Selecciona una opción del menú de la izquierda.</p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
