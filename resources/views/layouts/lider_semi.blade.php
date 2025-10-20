<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Líder de Semillero</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sena-green: #39A900;
            --sena-dark: #2E8900;
            --gray-bg: #f8f9fb;
            --nav-dark: #0D1B2A;
        }

        body {
            background: var(--gray-bg);
            font-family: 'Segoe UI', sans-serif;
        }

        .px-3.mb-4 {
            display: flex;
            flex-direction: column;
            /* coloca el logo y el texto en columna */
            align-items: center;
            /* centra horizontalmente */
            justify-content: center;
            /* centra verticalmente (si tiene altura definida) */
            text-align: center;
            /* centra el texto */
        }

        .logo-sena {
            height: 80px;
            width: 80px;
            margin-bottom: 8px;
            /* separa el logo del texto */
        }

        .sidebar {
            height: 100vh;
            background: #fff;
            border-right: 1px solid #eaeaea;
            padding-top: 1.5rem;
        }

        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .sidebar .nav-link.active {
            background: var(--sena-green);
            color: #fff;
        }

        .sidebar .nav-link:hover {
            background: #e9f7ec;
            color: var(--sena-dark);
        }

        .topbar {
            background: var(--sena-green);
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 0 0 15px 15px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info .avatar {
            background: #fff;
            color: var(--sena-green);
            font-weight: 700;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-metric {
            border: none;
            border-radius: 15px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 col-lg-2 sidebar d-flex flex-column">
                <div class="px-3 mb-4">
                    <img src="{{ asset('images/logo-sena.png') }}" alt="Logo" class="logo-sena">
                    <p class="text-muted small">Sistema de Gestión Semillero</p>
                </div>
                <ul class="nav flex-column px-2">
                    <li><a href="{{ route('lider_semi.dashboard') }}" class="nav-link {{ request()->routeIs('lider_semi.dashboard') ? 'active' : '' }}"><i class="bi bi-house-fill me-2"></i> Dashboard</a>
                    </li>
                    <li><a href="{{ route('lider_semi.semilleros') }}" class="nav-link {{ request()->routeIs('lider_semi.semilleros') ? 'active' : '' }}"><i
                                class="bi bi-hdd-stack me-2"></i>
                            Mis Semilleros</a></li>
                    <li><a href="{{ route('lider_semi.aprendices') }}" class="nav-link {{ request()->routeIs('lider_semi.aprendices') ? 'active' : '' }}"><i class="bi bi-person-video2"></i> Aprendices</a></li>
                    <li><a href="{{ route('lider_semi.documentos') }}" class="nav-link position-relative {{ request()->routeIs('lider_semi.documentos') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text me-2"></i> Documentación
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                        </a></li>
                    <li><a href="{{ route('lider_semi.recursos') }}" class="nav-link {{ request()->routeIs('lider_semi.recursos') ? 'active' : '' }}"><i class="bi bi-journal-text me-2"></i> Recursos</a></li>
                    <li><a href="{{ route('lider_semi.calendario') }}" class="nav-link {{ request()->routeIs('lider_semi.calendario') ? 'active' : '' }}"><i class="bi bi-calendar-event me-2"></i> Calendario</a></li>
                    <li><a href="{{ route('lider_semi.perfil') }}" class="nav-link {{ request()->routeIs('lider_semi.perfil') ? 'active' : '' }}"><i class="bi bi-person-circle me-2"></i> Mi Perfil</a></li>
                </ul>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 col-lg-10 p-0">
                <!-- Top bar -->
                <div class="topbar">
                    <h5 class="fw-bold mb-0">Líder de Semillero</h5>
                    <div class="profile-info">
                        <!-- Icono de notificaciones -->
                        <button class="btn btn-link text-white position-relative me-2" type="button"
                            aria-label="Notificaciones">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-bell" viewBox="0 0 16 16">
                                <path
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                        </button>
                        <div class="avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'JC', 0, 2)) }}
                        </div>
                        <div>
                            <div>{{ Auth::user()->name }}</div>
                            <small>Líder de Semillero</small>
                        </div>


                        <form method="POST" action="{{ route('logout') }}" class="ms-3">
                            @csrf
                            <button class="btn btn-light btn-sm">Cerrar sesión</button>
                        </form>
                    </div>
                </div>

                <div class="p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
