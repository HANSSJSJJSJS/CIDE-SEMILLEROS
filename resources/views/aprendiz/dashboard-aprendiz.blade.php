<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite('resources/css/aprendiz/style.css')
</head>
<body style="background: url('{{ asset('images/fondo.jpg') }}') no-repeat center center fixed; background-size: cover;">

    <!-- Top bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Panel del Aprendiz SENA</h1>
            <div class="d-flex align-items-center gap-3">
                <div class="text-white small text-end">
                    <div style="font-size:0.78rem; opacity:0.9;">Aprendiz</div>
                </div>
                <div class="user-avatar" title="{{ Auth::user()->name }}">
                    @php
                        $name = trim(Auth::user()->name ?? '');
                        $parts = preg_split('/\s+/', $name);
                        $initials = strtoupper(
                            ($parts[0][0] ?? '') .
                            ($parts[count($parts)-1][0] ?? '')
                        );
                    @endphp
                    {{ $initials }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-3">
                <div class="sidebar">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/sena-logo.png') }}" alt="SENA" class="top-left-logo me-3">
                        <div>
                            <div style="font-weight:700;">Sistema de Gestión</div>
                            <div style="color:var(--muted);font-size:0.9rem;">Semillero</div>
                        </div>
                    </div>

                    <div class="list-group">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action active" aria-current="true">
                            <i class="fa fa-home me-2"></i> Inicio
                        </a>
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-folder-open me-2"></i> Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-file-upload me-2"></i> Subir Documentos
                        </a>
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-user me-2"></i> Mi Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-lg-9">
                <h2 class="fw-bold mb-1">Bienvenido(a), {{ Auth::user()->name }}</h2>
                <p class="text-muted mb-4">Gestiona tus proyectos y documentos desde tu panel de aprendiz</p>

                <!-- Metric cards -->
                <div class="row metrics-grid mb-4 gx-4">
                    <div class="col-md-3">
                        <div class="card-metric text-center">
                            <div class="metric-icon icon-users mx-auto">
                                <i class="fa fa-people-group"></i>
                            </div>
                            <div id="proyectosCount" class="metric-value">{{ $proyectosCount ?? 0 }}</div>
                            <div class="metric-title">Proyectos Asignados</div>
                            <div class="metric-aux mt-2">
                                <a href="{{ route('aprendiz.proyectos.index') }}" class="text-decoration-none" style="color: #1d986f">Ver tus Proyectos</a>
                            </div>
                       </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card-metric text-center">
                            <div class="metric-icon icon-aprendices mx-auto">
                                <i class="fa fa-id-card"></i>
                            </div>
                            <div id="documentosPendientes" class="metric-value">{{ $documentosPendientes ?? 0 }}</div>
                            <div class="metric-title">Documentos Pendientes</div>
                            <div class="metric-aux mt-2 text-warning">
                                <a href="{{ route('aprendiz.archivos.index') }}" class="text-decoration-none" style="color: #0051d3">Subir Documentos</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card-metric text-center">
                            <div class="metric-icon icon-docs mx-auto">
                                <i class="fa fa-file-lines"></i>
                            </div>
                            <div id="documentosCompletados" class="metric-value">{{ $documentosCompletados ?? 0 }}</div>
                            <div class="metric-title">Documentos Completados</div>
                            <div class="metric-aux mt-2 text-muted">
                                <a href="{{ route('aprendiz.proyectos.index') }}" class="text-decoration-none" style="color: #6c757d">Historial</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card-metric text-center">
                            <div class="metric-icon icon-progress mx-auto">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <div id="progresoPromedio" class="metric-value">{{ $progresoPromedio ?? 0 }}%</div>
                            <div class="metric-title">Progreso Promedio</div>
                            <div class="metric-aux mt-2">Últimos 30 Días</div>
                        </div>
                    </div>
                </div>

                <!-- Steps -->
                <div class="steps-section">
                    <h5 class="mb-3">Próximos Pasos</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3">
                                <div style="background:#e9f7ef;color:var(--sena-green);width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700">1</div>
                                <div>
                                    <div class="fw-bold">Revisa tus Proyectos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Consulta los proyectos asignados y sus requisitos</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3">
                                <div style="background:#fff7e6;color:#f08c00;width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700">2</div>
                                <div>
                                    <div class="fw-bold">Prepara Documentos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Reúne los documentos PDF requeridos para cada proyecto</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3">
                                <div style="background:#e6f2ff;color:#1a73e8;width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700">3</div>
                                <div>
                                    <div class="fw-bold">Sube Documentos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Carga los archivos en la sección de subida de documentos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /steps -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Actualización dinámica de contadores -->
    <script>
        function animateCounter(element, start, end, duration = 1000) {
            const range = end - start;
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const value = Math.floor(start + range * progress);
                element.textContent = value + (element.id === 'progresoPromedio' ? '%' : '');
                if (progress < 1) window.requestAnimationFrame(step);
            }

            window.requestAnimationFrame(step);
        }

        async function actualizarContadores() {
            try {
                const response = await fetch("{{ route('aprendiz.dashboard.stats') }}");
                const data = await response.json();

                const counters = {
                    proyectosCount: data.proyectosCount,
                    documentosPendientes: data.documentosPendientes,
                    documentosCompletados: data.documentosCompletados,
                    progresoPromedio: data.progresoPromedio
                };

                for (const key in counters) {
                    const el = document.getElementById(key);
                    if (el) {
                        const current = parseInt(el.textContent) || 0;
                        const next = counters[key];
                        animateCounter(el, current, next);
                    }
                }
            } catch (e) {
                console.error('Error al actualizar los contadores:', e);
            }
        }

        // Actualiza cada 10 segundos
        setInterval(actualizarContadores, 10000);
    </script>

</body>
</html>
