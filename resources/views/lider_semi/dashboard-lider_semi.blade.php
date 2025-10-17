<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel - Líder de Semillero</title>

  <!-- Bootstrap e Iconos -->
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
      min-height: 350px;
      padding: 2rem;
      color: #111;
      box-shadow: 0 4px 10px rgba(0,0,0,.06);
    }

  </style>
</head>

<body>

  <div class="app-green">
    <!-- NAV SUPERIOR -->
    <nav class="navbar-lider">
      <div class="user-pill">
        <i class="bi bi-person-badge-fill"></i>
        {{ Auth::user()->name ?? 'Líder de Semillero' }}
      </div>

      <div class="top-title">Panel del Líder de Semillero</div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </button>
      </form>
    </nav>

    <!-- CUERPO PRINCIPAL -->
    <main class="dashboard-main">
      <div class="row g-4 flex-grow-1">

        <!-- PANEL LATERAL -->
        <div class="col-lg-4 col-xl-3">
          <aside class="side-panel h-100">
            <div class="side-inner h-100">
              <div class="d-grid gap-3">
                <a class="menu-btn" href="{{ route('semilleros.index', [], false) }}">
                  <span>Mis Semilleros</span><i class="bi bi-chevron-right"></i>
                </a>
                <a class="menu-btn" href="{{ route('aprendices.index', [], false) }}">
                  <span>Aprendices del Grupo</span><i class="bi bi-chevron-right"></i>
                </a>
                <a class="menu-btn" href="#">
                  <span>Subir Documentación</span><i class="bi bi-chevron-right"></i>
                </a>
                <a class="menu-btn" href="#">
                  <span>Ver Recursos</span><i class="bi bi-chevron-right"></i>
                </a>
                <a class="menu-btn" href="{{ route('profile.edit', [], false) }}">
                  <span>Editar Perfil</span><i class="bi bi-chevron-right"></i>
                </a>
              </div>
            </div>
          </aside>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="col-lg-8 col-xl-9">
          <section class="content-area h-100">
            <h4 class="fw-bold mb-3 text-success">Bienvenido(a), {{ Auth::user()->name ?? 'Líder' }}</h4>
            <p class="text-muted mb-4">
              Desde este panel puedes gestionar los semilleros que lideras, revisar los avances de tus aprendices y administrar la documentación correspondiente.
            </p>

            <div class="alert alert-success d-flex align-items-center" role="alert">
              <i class="bi bi-clipboard-data me-2"></i>
              <div>
                Recuerda mantener actualizada la información de tu semillero y los documentos de avance.
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
