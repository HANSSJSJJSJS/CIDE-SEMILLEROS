<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Subir Documentos</title>

    <!-- Bootstrap y FontAwesome primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- CSS personalizado desde public -->
    <link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
</head>
<body class="bg-light">
    <!-- Top bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Panel del aprendiz SENA</h1>
            <div class="d-flex align-items-center gap-3">
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

                <div class="text-white small text-end">
                    <div>{{ Auth::user()->name }}</div>
                    <div style="font-size:0.78rem; opacity:0.9;">Aprendiz</div>
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
                        <img src="{{ asset('images/logo-sena.png') }}" alt="SENA" style="height:48px; object-fit:contain;" class="me-3">
                        <div>
                            <div style="font-weight:700;">Sistema de Gestión</div>
                            <div style="color:var(--muted);font-size:0.9rem;">Semillero</div>
                        </div>
                    </div>

                    <div class="list-group">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-home me-2"></i> Inicio
                        </a>
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-folder-open me-2"></i> Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action active">
                            <i class="fa fa-file-upload me-2"></i> Subir Documentos
                        </a>
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-user me-2"></i> Mi Perfil
                        </a>
                        <a href="{{ route('aprendiz.calendario.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-calendar me-2"></i> Calendario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-lg-9">
                <div class="upload-section">
                    <h2 class="mb-3">Subir Documentos</h2>
                    <p class="text-muted mb-4">Carga los documentos PDF requeridos para tus proyectos</p>

                    <!-- Formulario de Subida Integrado -->
                    <form action="{{ route('aprendiz.archivos.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="mb-4">
                            <label for="proyecto_select" class="form-label">Selecciona un Proyecto</label>
                            <select class="form-select" name="proyecto_id" id="proyecto_select" required>
                                <option value="">Selecciona un proyecto</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id_proyecto }}">{{ $proyecto->nombre_proyecto ?? $proyecto->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proyecto_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

                        <div class="dropzone mb-4" id="dropzoneArea">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-file-earmark-arrow-up text-muted mb-3" viewBox="0 0 16 16">
                              <path d="M12.854 2.854a.5.5 0 0 0-.708-.708L8.5 4.793V1.5a.5.5 0 0 0-1 0v3.293L4.854 2.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0z"/>
                              <path d="M14 14V4.342A1 1 0 0 0 13.78 3.65L11 0h-9A1.5 1.5 0 0 0 1 1.5v13A1.5 1.5 0 0 0 2.5 15h10a1.5 1.5 0 0 0 1.5-1.5M10.5 0v3.5a.5.5 0 0 0 .5.5h3zM3 4a1 1 0 0 1 1-1h5.5v3h3v8a1 1 0 0 1-1 1h-10a1 1 0 0 1-1-1z"/>
                            </svg>
                            <h5 id="dropzoneText">Arrastra archivos aquí</h5>
                            <p class="text-muted">o haz clic para seleccionar archivos PDF</p>
                            <input type="file" name="documentos[]" multiple accept=".pdf" class="d-none" id="fileInput">
                        </div>

                        <div class="mb-4" id="selectedFilesList" style="display:none;">
                            <h6>Archivos a subir:</h6>
                            <ul class="list-group" id="filePreviewUl"></ul>
                        </div>

                        <button type="submit" class="btn btn-sena">Guardar Documentos</button>
                    </form>

                    <h4 class="mt-5 mb-3">Documentos ya Cargados</h4>

                    <form method="GET" action="{{ route('aprendiz.archivos.index') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Proyecto</label>
                            <select name="proyecto" class="form-select">
                                <option value="">Todos</option>
                                @foreach($proyectos as $p)
                                    <option value="{{ $p->id_proyecto }}" {{ (isset($proyecto) && (string)$proyecto === (string)$p->id_proyecto) ? 'selected' : '' }}>
                                        {{ $p->nombre_proyecto ?? $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" value="{{ $fecha }}" class="form-control" />
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-sena" type="submit">Filtrar</button>
                            <a class="btn btn-outline-secondary" href="{{ route('aprendiz.archivos.index') }}">Limpiar</a>
                        </div>
                    </form>

                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            @forelse ($archivos as $archivo)
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item py-3">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center" style="width:36px;height:36px;">PDF</div>
                                                <div>
                                                    <div class="fw-semibold">{{ $archivo->nombre_original ?? $archivo->nombre_archivo }}</div>
                                                    <div class="text-muted small">Proyecto: {{ $archivo->proyecto->nombre_proyecto ?? $archivo->proyecto->nombre ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $fecha = $archivo->subido_en ?? $archivo->created_at ?? null;
                                                @endphp
                                                <div class="small text-muted">Subido el: {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') : '—' }}</div>
                                                <div class="mt-1">
                                                    @if(!empty($archivo->ruta))
                                                        <a href="{{ Storage::url($archivo->ruta) }}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>Sin archivo</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">Aún no has subidos documentos.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $archivos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap y JS del drag/drop -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropzoneArea = document.getElementById('dropzoneArea');
        const fileInput = document.getElementById('fileInput');
        const filePreviewUl = document.getElementById('filePreviewUl');
        const selectedFilesList = document.getElementById('selectedFilesList');
        const dropzoneText = document.getElementById('dropzoneText');

        function updateFilePreview(files) {
            filePreviewUl.innerHTML = '';
            if (files.length > 0) {
                selectedFilesList.style.display = 'block';
                dropzoneText.textContent = `${files.length} documento(s) seleccionado(s)`;

                Array.from(files).forEach(file => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.textContent = file.name;
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-secondary';
                    badge.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    li.appendChild(badge);
                    filePreviewUl.appendChild(li);
                });
            } else {
                selectedFilesList.style.display = 'none';
                dropzoneText.textContent = 'Arrastra archivos aquí';
            }
        };

        dropzoneArea.addEventListener('click', () => fileInput.click());

        dropzoneArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzoneArea.style.borderColor = '#39B54A';
            dropzoneArea.style.backgroundColor = '#e6ffe6';
        });

        dropzoneArea.addEventListener('dragleave', () => {
            dropzoneArea.style.borderColor = '#ccc';
            dropzoneArea.style.backgroundColor = '#f8f9fa';
        });

        dropzoneArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzoneArea.style.borderColor = '#ccc';
            dropzoneArea.style.backgroundColor = '#f8f9fa';
            fileInput.files = e.dataTransfer.files;
            updateFilePreview(fileInput.files);
        });

        fileInput.addEventListener('change', () => updateFilePreview(fileInput.files));
    </script>
</body>
</html>
