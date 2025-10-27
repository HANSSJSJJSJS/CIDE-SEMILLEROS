<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Mis Proyectos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="{{ asset('css/aprendiz.css') }}" rel="stylesheet">
</head>

<body class="bg-light main-bg">
    <!-- ENCABEZADO -->
<header class="header-bar shadow-sm d-flex justify-content-between align-items-center px-4 py-3 mb-3">
    <h1 class="m-0 text-success fw-bold">Bienvenido a <span class="brand-text">CIDE SEMILLERO</span></h1>
    <div class="user-info d-flex align-items-center gap-3">
        <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-dark btn-sm px-3 rounded-3">Cerrar sesión</button>
        </form>
    </div>
</header>

<div class="container-fluid px-4">
    <div class="row align-items-start">
        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <div class="text-center mb-4 mt-2">
                <div class="user-avatar mx-auto mb-2"></div>
                <p class="fw-semibold mb-0 text-dark">Nombre Usuario</p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.proyectos.index') }}" class="nav-link active">📁 Mis Proyectos</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.archivos.index') }}" class="nav-link">📤 Subir Documentos</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.perfil.show') }}" class="nav-link">👤 Mi Perfil</a></li>
            </ul>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="col-md-9 col-lg-10 p-3 d-flex flex-column">
            <div class="content-wrapper shadow-sm bg-white rounded-4 p-4">
                <h2 class="fw-bold mb-2">Mis Proyectos</h2>
                <p class="text-muted mb-4">Proyectos asignados en el semillero SENA</p>

                @foreach ($proyectos as $proyecto)
                    <div class="project-card shadow-sm p-4 mb-4 rounded-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="fw-bold m-0">{{ $proyecto->nombre_proyecto }}</h4>
                            <span class="badge
                                {{ $proyecto->estado === 'activo' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ strtoupper($proyecto->estado) }}
                            </span>
                        </div>

                        <p class="text-muted small mb-2">{{ $proyecto->descripcion }}</p>

                        <div class="small fw-semibold text-secondary">Fechas del Proyecto</div>
                        <p class="text-muted small">
                            Inicio: {{ date('d/m/Y', strtotime($proyecto->fecha_inicio)) }} |
                            Fin: {{ date('d/m/Y', strtotime($proyecto->fecha_fin)) }}
                        </p>

                        <p class="mb-3 small">
                            <strong>Semillero:</strong> {{ $proyecto->semillero->nombre ?? 'No asignado' }} <br>
                            <strong>Tipo:</strong> {{ $proyecto->tipoProyecto->nombre ?? 'Sin tipo' }}
                        </p>

                        <div class="d-flex gap-2">
                            <a href="{{ route('aprendiz.proyectos.show', $proyecto->id_proyecto) }}" class="btn btn-success rounded-3 px-3 fw-semibold">
                                Ver Detalles
                            </a>
                            <a href="{{ route('aprendiz.archivos.create', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-outline-success rounded-3 px-3 fw-semibold">
                                Subir Docs                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
</div>
</body>
</html>
