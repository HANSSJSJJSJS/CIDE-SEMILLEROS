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

@forelse ($proyectos as $proyecto)
    <div class="project-card mb-3">
        <h4>{{ $proyecto->nombre_proyecto }}</h4>

        <span class="badge bg-{{ $proyecto->estado === 'activo' ? 'success' : 'warning' }} mb-2">
            {{ ucfirst($proyecto->estado ?? 'pendiente') }}
        </span>

        <p>{{ $proyecto->descripcion ?? 'Sin descripción disponible.' }}</p>

        <div class="mb-2">Fechas del Proyecto</div>
        <p class="text-muted">
            Inicio: {{ $proyecto->fecha_inicio ? \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') : 'No definida' }} -
            Fin: {{ $proyecto->fecha_fin ? \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') : 'No definida' }}
        </p>

        <div class="mb-3">
            Semillero: {{ $proyecto->semillero->nombre ?? 'No asignado' }} <br>
            Tipo: {{ $proyecto->tipoProyecto->nombre ?? 'No especificado' }}
        </div>

        <div>
            <a href="{{ route('aprendiz.proyectos.show', $proyecto->id_proyecto) }}" class="btn btn-sena">Ver Detalles</a>
            <a href="{{ route('aprendiz.archivos.create', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-outline-sena">Subir Docs</a>
        </div>
    </div>
@empty
    <div class="alert alert-warning">
        No tienes proyectos asignados actualmente.
    </div>
@endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
