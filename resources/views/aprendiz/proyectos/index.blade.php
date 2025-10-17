<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Mis Proyectos</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sena-green: #39B54A;
        }

        .top-bar {
            background: var(--sena-green);
            color: white;
            padding: 1rem;
        }

        .sidebar {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .project-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress {
            height: 0.5rem;
        }

        .btn-sena {
            background-color: var(--sena-green);
            color: white;
        }

        .btn-sena:hover {
            background-color: #2d9d3b;
            color: white;
        }

        .btn-outline-sena {
            border-color: var(--sena-green);
            color: var(--sena-green);
        }

        .btn-outline-sena:hover {
            background-color: var(--sena-green);
            color: white;
        }
    </style>
</head>
<body class="bg-light">
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
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                            🏠 Inicio
                        </a>
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action active">
                            📁 Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action">
                            📤 Subir Documentos
                        </a>
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action">
                            👤 Mi Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <h2 class="mb-3">Mis Proyectos</h2>
                <p class="text-muted mb-4">Proyectos asignados en el semillero SENA</p>

                <!-- Proyecto 1 -->
                <div class="project-card">
                    <h4>Proyecto de Desarrollo Web</h4>
                    <span class="badge bg-success mb-2">En progreso</span>
                    <p>Desarrollo de una aplicación web responsiva con tecnologías modernas</p>

                    <div class="mb-2">Progreso</div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: 60%"></div>
                    </div>

                    <div class="mb-3">Documentos: 2/3</div>

                    <div>
                        <a href="{{ route('aprendiz.proyectos.show', 1) }}" class="btn btn-sena">Ver Detalles</a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="btn btn-outline-sena">Subir Docs</a>
                    </div>
                </div>

                <!-- Proyecto 2 -->
                <div class="project-card">
                    <h4>Proyecto de Base de Datos</h4>
                    <span class="badge bg-warning text-dark mb-2">Pendiente</span>
                    <p>Diseño e implementación de una base de datos relacional</p>

                    <div class="mb-2">Progreso</div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: 30%"></div>
                    </div>

                    <div class="mb-3">Documentos: 1/4</div>

                    <div>
                        <a href="{{ route('aprendiz.proyectos.show', 2) }}" class="btn btn-sena">Ver Detalles</a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="btn btn-outline-sena">Subir Docs</a>
                    </div>
                </div>

                <!-- Proyecto 3 -->
                <div class="project-card">
                    <h4>Proyecto de Seguridad Informática</h4>
                    <span class="badge bg-warning text-dark mb-2">Pendiente</span>
                    <p>Análisis de vulnerabilidades y propuesta de soluciones</p>

                    <div class="mb-2">Progreso</div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" style="width: 0%"></div>
                    </div>

                    <div class="mb-3">Documentos: 0/3</div>

                    <div>
                        <a href="{{ route('aprendiz.proyectos.show', 3) }}" class="btn btn-sena">Ver Detalles</a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="btn btn-outline-sena">Subir Docs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
