{{-- resources/views/dashboard.blade.php --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sena-green: #39B54A;
        }

        body {
            background: #f8f9fa;
        }

        .top-bar {
            background: var(--sena-green);
            color: white;
            padding: 1rem;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-content {
            padding: 2rem;
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 8px;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-green { background: #39B54A; }
        .stat-orange { background: #F7931E; }
        .stat-teal { background: #00A99D; }

        .steps-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }

        .step-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .step-number {
            background: #39B54A;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Barra Superior -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>📋 Panel del Aprendiz SENA</h1>
            <div>
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm ms-2">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <div class="list-group">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ Request::routeIs('aprendiz.dashboard') ? 'active' : '' }}">
                            🏠 Inicio
                        </a>
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('aprendiz.proyectos.*') ? 'active' : '' }}">
                            📁 Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('aprendiz.archivos.*') ? 'active' : '' }}">
                            📤 Subir Documentos
                        </a>
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action {{ Request::routeIs('profile.*') ? 'active' : '' }}">
                            👤 Mi Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <h2 class="mb-4">Bienvenido(a), {{ Auth::user()->name }}</h2>
                <p class="text-muted">Gestiona tus proyectos y documentos desde tu panel de aprendiz</p>

                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card stat-green">
                            <h3>0</h3>
                            <p>Proyectos Asignados</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-orange">
                            <h3>0</h3>
                            <p>Documentos Pendientes</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-teal">
                            <h3>0</h3>
                            <p>Documentos Completados</p>
                        </div>
                    </div>
                </div>

                <!-- Próximos Pasos -->
                <div class="steps-section">
                    <h4 class="mb-4">Próximos Pasos</h4>

                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h5>Revisa tus Proyectos</h5>
                            <p class="text-muted">Consulta los proyectos asignados y sus requisitos</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h5>Prepara Documentos</h5>
                            <p class="text-muted">Reúne los documentos PDF requeridos para cada proyecto</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h5>Sube Documentos</h5>
                            <p class="text-muted">Carga los archivos en la sección de subida de documentos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
