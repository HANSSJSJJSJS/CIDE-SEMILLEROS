<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/aprendiz.css') }}" rel="stylesheet">
</head>
<body class="bg-light main-bg">
    <header class="header-bar shadow-sm d-flex justify-content-between align-items-center px-4 py-3 mb-3">
        <h1 class="m-0 text-success fw-bold">Detalle del Proyecto</h1>
        <div class="d-flex align-items-center gap-3">
            <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm px-3 rounded-3">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <div class="container px-4">
        <div class="row">
            <div class="col-12">
                <div class="content-wrapper shadow-sm bg-white rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $proyecto->nombre_proyecto }}</h2>
                            <div class="text-muted small">ID: {{ $proyecto->id_proyecto }}</div>
                        </div>
                        <span class="badge {{ $proyecto->estado === 'activo' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ strtoupper($proyecto->estado ?? 'SIN ESTADO') }}
                        </span>
                    </div>

                    <p class="text-muted">{{ $proyecto->descripcion }}</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="fw-semibold text-secondary mb-2">Fechas</div>
                                    <div class="small text-muted">Inicio: {{ $proyecto->fecha_inicio ? date('d/m/Y', strtotime($proyecto->fecha_inicio)) : '—' }}</div>
                                    <div class="small text-muted">Fin: {{ $proyecto->fecha_fin ? date('d/m/Y', strtotime($proyecto->fecha_fin)) : '—' }}</div>
                                    <div class="small text-muted">Creado: {{ $proyecto->creado_en ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="fw-semibold text-secondary mb-2">Semillero</div>
                                    <div class="mb-1"><strong>Nombre:</strong> {{ $proyecto->semillero->nombre ?? 'No asignado' }}</div>
                                    @if(isset($lider))
                                        <div class="mb-1"><strong>Líder:</strong> {{ $lider->nombre_completo ?? ($lider->user->name ?? '—') }}</div>
                                        <div class="small text-muted"><strong>Correo:</strong> {{ $lider->user->email ?? $lider->correo_institucional ?? '—' }}</div>
                                    @else
                                        <div class="small text-muted">Líder no asignado</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="fw-semibold text-secondary mb-2">Tipo de Proyecto</div>
                                    <div class="small">{{ $proyecto->tipoProyecto->nombre ?? 'Sin tipo' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="evidencias" class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="m-0">Compañeros asignados</h5>
                        </div>
                        <div class="card-body p-0">
                            @if($companeros->isEmpty())
                                <div class="p-4 text-muted">No hay otros aprendices asignados a este proyecto.</div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($companeros as $ap)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ $ap->nombre_completo ?? trim(($ap->nombres ?? '') . ' ' . ($ap->apellidos ?? '')) ?: 'Aprendiz' }}</div>
                                                <div class="small text-muted">Documento: {{ $ap->documento ?? '—' }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="m-0">Evidencias del proyecto</h5>
                            <span class="badge bg-light text-primary">{{ $evidencias->count() }} registro(s)</span>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ url()->current() }}#evidencias" class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label for="fecha" class="form-label">Fecha de carga</label>
                                    <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control">
                                </div>
                                <div class="col-md-5">
                                    <label for="nombre" class="form-label">Nombre del compañero</label>
                                    <input type="text" id="nombre" name="nombre" value="{{ $nombre }}" class="form-control" placeholder="Ej: Juan Pérez">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Limpiar</a>
                                </div>
                            </form>
                            @if(!empty($nombreError))
                                <div class="alert alert-warning py-2 px-3 mb-3">
                                    {{ $nombreError }}
                                </div>
                            @endif

                            @if($evidencias->isEmpty())
                                <div class="p-4 text-muted">Aún no hay evidencias cargadas para este proyecto.</div>
                            @else
                                <div class="table-responsive mb-0">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Autor</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evidencias as $ev)
                                                @php
                                                    // Buscar un archivo del mismo proyecto cuyo nombre_original coincida con el nombre de la evidencia
                                                    $file = $archivos->first(function($a) use ($ev) {
                                                        return isset($ev->nombre) && isset($a->nombre_original) && $a->nombre_original === $ev->nombre;
                                                    });
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold">{{ $ev->nombre ?? 'Evidencia' }}</td>
                                                    <td class="small">{{ optional($ev->autor)->nombre_completo ?? trim(((optional($ev->autor)->nombres) ?? '') . ' ' . ((optional($ev->autor)->apellidos) ?? '')) ?: '—' }}</td>
                                                    <td>
                                                        <span class="badge {{ ($ev->estado ?? '') === 'aprobado' ? 'bg-success' : (($ev->estado ?? '') === 'rechazado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                            {{ strtoupper($ev->estado ?? 'PENDIENTE') }}
                                                        </span>
                                                    </td>
                                                    <td class="small text-muted">{{ optional($ev->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($file && !empty($file->ruta))
                                                            <a href="{{ Storage::url($file->ruta) }}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-secondary" disabled>Sin archivo</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="btn btn-outline-secondary">Volver</a>
                        <a href="{{ route('aprendiz.archivos.create', ['proyecto' => $proyecto->id_proyecto]) }}" class="btn btn-success">Subir Documentos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

 </body>
 </html>
