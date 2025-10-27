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
    <style>
        :root{ --header-h: 68px; --gap: 16px; }
        body.main-bg{background:linear-gradient(180deg,#f7fafc 0,#eef7f1 100%)}
        .header-bar{background:#ffffff;border-radius:16px;border:1px solid #e9ecef}
        .header-bar .brand-text{color:#198754}
        /* Sidebar estático para evitar superposición sobre el contenido */
        .sidebar{background:#ffffff;border:1px solid #e9ecef;border-radius:16px; position:static; top:auto}
        .sidebar .nav-link{color:#0f172a;border-radius:12px;padding:.65rem 1rem}
        .sidebar .nav-link:hover{background:#f1f5f9}
        .sidebar .nav-link.active{background:#0d6efd;color:#fff}
        /* El main arranca a la misma altura visual que el sidebar */
        .content-wrapper{border:1px solid #e9ecef;border-radius:16px;background:#fff; margin-top: 0; position:relative; z-index:1}
        .project-card{border:1px solid #e9ecef;border-radius:16px;transition:.2s;position:relative;background:#fff;display:flex;flex-direction:column;height:100%}
        .project-card:hover{box-shadow:0 8px 24px rgba(16,24,40,.08);transform:translateY(-1px)}
        .project-head{background:linear-gradient(135deg,#15803d,#16a34a); color:#fff; border-top-left-radius:16px;border-top-right-radius:16px;padding:14px 16px}
        .project-body{padding:16px}
        .project-footer{padding:16px; padding-top:0}
        .badge.status{border-radius:999px;padding:.4rem .75rem;font-weight:700;letter-spacing:.3px}
        .meta-title{font-size:.8rem;color:#64748b;font-weight:700;text-transform:uppercase}
        .btn-pill{border-radius:999px}
        .container-fluid{max-width:1200px; margin:0 auto}
        main.main-col{padding-top: calc(var(--gap));}
    </style>
</head>

<body class="bg-light main-bg">
    <!-- ENCABEZADO -->
<header class="header-bar shadow-sm d-flex justify-content-between align-items-center px-4 py-3" style="height: var(--header-h); margin-bottom: var(--gap);">
    <h1 class="m-0 text-success fw-bold">Bienvenido a <span class="brand-text">CIDE SEMILLERO</span></h1>
    <div class="user-info d-flex align-items-center gap-3">
        <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-dark btn-sm px-3 btn-pill">Cerrar sesión</button>
        </form>
    </div>
</header>

<div class="container-fluid px-4">
    <div class="row align-items-start gx-4 gy-4">
        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 sidebar p-3 shadow-sm">
            <div class="text-center mb-4 mt-2">
                <div class="user-avatar mx-auto mb-2 rounded-circle bg-success" style="width:56px;height:56px"></div>
                <p class="fw-semibold mb-0 text-dark">Nombre Usuario</p>
            </div>
            <ul class="nav flex-column gap-1">
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">📊 Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.proyectos.index') }}" class="nav-link active">📁 Mis Proyectos</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.archivos.index') }}" class="nav-link">📤 Subir Documentos</a></li>
                <li class="nav-item"><a href="{{ route('aprendiz.perfil.show') }}" class="nav-link">👤 Mi Perfil</a></li>
            </ul>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="col-md-9 col-lg-10 p-3 d-flex flex-column main-col">
            <div class="content-wrapper shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold m-0">Mis Proyectos</h2>
                        <p class="text-muted mb-0">Proyectos asignados en el semillero SENA</p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($proyectos as $proyecto)
                        <div class="col-12 col-sm-6 col-lg-3 d-flex">
                            <div class="project-card shadow-sm w-100 mb-2">
                                <div class="project-head d-flex justify-content-between align-items-start">
                                    <h5 class="fw-bold m-0 text-white">{{ $proyecto->nombre_proyecto }}</h5>
                                    <span class="badge status {{ $proyecto->estado === 'activo' ? 'bg-success' : 'bg-warning text-dark' }} bg-opacity-100">
                                        {{ strtoupper($proyecto->estado) }}
                                    </span>
                                </div>
                                <div class="project-body">
                                    <div class="meta-title">Fechas del Proyecto</div>
                                    <div class="text-muted small mb-2">Inicio: {{ date('d/m/Y', strtotime($proyecto->fecha_inicio)) }} | Fin: {{ date('d/m/Y', strtotime($proyecto->fecha_fin)) }}</div>
                                    <p class="text-muted mb-3">{{ $proyecto->descripcion }}</p>
                                    <p class="mb-3 small">
                                        <strong>Semillero:</strong> {{ $proyecto->semillero->nombre ?? 'No asignado' }} <br>
                                        <strong>Tipo:</strong> {{ $proyecto->tipoProyecto->nombre ?? 'Sin tipo' }}
                                    </p>
                                </div>
                                <div class="project-footer">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('aprendiz.proyectos.show', $proyecto->id_proyecto) }}" class="btn btn-success btn-pill px-3 fw-semibold">Ver Detalles</a>
                                        <a href="{{ route('aprendiz.archivos.create', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-outline-success btn-pill px-3 fw-semibold">Subir Docs</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
