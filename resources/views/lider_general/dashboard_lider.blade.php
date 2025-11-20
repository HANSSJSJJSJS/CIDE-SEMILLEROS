<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel del Líder General</title>

  <!-- Bootstrap + Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --sena-green:#39A900;
      --sena-green-dark:#2E8900;
      --light-gray:#f4f6f8;
    }

    body {
      background: var(--light-gray);
      margin: 0;
      height: 100dvh;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .app-green {
      background: var(--sena-green);
      min-height: 100dvh;
      display: flex;
      flex-direction: column;
      padding: 16px;
    }

    /* Navbar */
    .navbar-lider {
      background: #fff;
      border-radius: 12px;
      padding: .8rem 1.2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .user-pill {
      background: #fff;
      color: var(--sena-green);
      border: 2px solid var(--sena-green);
      border-radius: 999px;
      padding: .35rem .9rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: .5rem;
    }

    .top-title {
      color: var(--sena-green);
      font-weight: 800;
      font-size: 1.25rem;
      flex: 1;
      text-align: center;
    }

    .logout-btn {
      background: var(--sena-green);
      color: #fff;
      border: none;
      border-radius: 999px;
      font-weight: 600;
      padding: .45rem .9rem;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      transition: background .2s;
    }
    .logout-btn:hover {
      background: var(--sena-green-dark);
    }

    /* Main */
    .dashboard-main {
      background: var(--sena-green);
      border-radius: 12px;
      flex: 1;
      padding: 16px;
    }

    .side-panel {
      background: #fff;
      border-radius: 16px;
      padding: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,.06);
    }

    .side-inner {
      background: var(--sena-green-dark);
      border-radius: 12px;
      padding: 16px;
    }

    .menu-btn {
      background: #fff;
      color: #111;
      border: none;
      border-radius: 999px;
      padding: .65rem 1rem;
      font-weight: 700;
      text-align: left;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 0 rgba(0,0,0,0.12);
      transition: transform .05s, box-shadow .05s;
    }

    .menu-btn:hover {
      transform: translateY(1px);
      box-shadow: 0 3px 0 rgba(0,0,0,0.2);
    }

    .content-area {
      background: #fff;
      border-radius: 16px;
      min-height: 400px;
      padding: 2rem;
      color: #111;
      box-shadow: 0 4px 10px rgba(0,0,0,.06);
    }

  </style>
</head>

<body>

  <div class="app-green">
    <!-- Navbar -->
    <nav class="navbar-lider">
      <div class="user-pill">
        <i class="bi bi-person-badge-fill"></i>
        {{ Auth::user()->name ?? 'Líder General' }}
      </div>

      <div class="top-title">Panel del Líder General</div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </button>
      </form>
    </nav>

    <!-- Contenido -->
    <main class="dashboard-main">
      <div class="row g-4 flex-grow-1">
        <!-- Panel lateral -->
        <div class="col-lg-4 col-xl-3">
          <aside class="side-panel h-100">
            <div class="side-inner h-100">
              <div class="d-grid gap-3">
                <a class="menu-btn" href="{{ route('admin.usuarios.index') }}">
                  <span>Gestión de Usuarios</span><i class="bi bi-people-fill"></i>
                </a>
                <a class="menu-btn" href="{{ route('admin.semilleros.index') }}">
                  <span>Gestión de Proyectos</span><i class="bi bi-kanban-fill"></i>
                </a>
                <a class="menu-btn" href="{{ route('admin.recursos.index') }}">
                  <span>Documentación</span><i class="bi bi-file-earmark-text-fill"></i>
                </a>
                <a class="menu-btn" href="{{ route('grupos.index') }}">
                  <span>Investigación</span><i class="bi bi-lightbulb-fill"></i>
                </a>
                <a class="menu-btn" href="{{ route('admin.reuniones-lideres.index') }}">
                  <span>Reportes y Avances</span><i class="bi bi-graph-up-arrow"></i>
                </a>
                <a class="menu-btn" href="{{ route('admin.dashboard.charts') }}">
                  <span>Presentaciones</span><i class="bi bi-easel-fill"></i>
                </a>
                <a class="menu-btn" href="{{ route('admin.perfil.edit') }}">
                  <span>Ver Perfil</span><i class="bi bi-person-circle"></i>
                </a>
              </div>
            </div>
          </aside>
        </div>

        <!-- Área principal -->
        <div class="col-lg-8 col-xl-9">
          <section class="content-area h-100">
            <h4 class="fw-bold mb-3 text-success">
              Bienvenido(a) {{ Auth::user()->name ?? 'Líder General' }}
            </h4>
            <p class="text-muted mb-4">
              Desde este panel puedes gestionar usuarios, proyectos, documentación e investigaciones.
              Además, podrás revisar reportes, avances y realizar presentaciones de los semilleros.
            </p>

            <div class="alert alert-success" role="alert">
              <i class="bi bi-shield-check me-2"></i>
              Recuerda: solo tú puedes <strong>inhabilitar proyectos y Permisos</strong>.
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal base para acciones de Líder General (misma estructura que modales Admin) -->
  <div class="modal fade" id="modalLiderGeneral" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="formLiderGeneral" class="modal-content" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalLiderGeneralTitle">Acción de Líder General</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="modalLiderGeneralBody">
            <!-- Contenido dinámico del modal de Líder General -->
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="submit" id="modalLiderGeneralSubmit">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
