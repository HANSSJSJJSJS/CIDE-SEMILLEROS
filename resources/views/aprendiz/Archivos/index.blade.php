<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Subir Documentos</title>

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

        .upload-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 3rem;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
        }

        .dropzone:hover {
            border-color: var(--sena-green);
        }

        .btn-sena {
            background-color: var(--sena-green);
            color: white;
            width: 100%;
        }

        .btn-sena:hover {
            background-color: #2d9d3b;
            color: white;
        }

        /* Estilos para la lista de archivos */
        .file-list {
            max-height: 300px; /* Limita la altura para scroll */
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .file-item:last-child {
            border-bottom: none;
        }

        .file-item .check {
            color: var(--sena-green);
            margin-right: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Barra Superior -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>📋 Panel del Aprendiz SENA</h1>
            <div>
                <!-- Usar Auth::user() con Blade es seguro si ya estás autenticado -->
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
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action">
                            📁 Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action active">
                            📤 Subir Documentos
                        </a>
                        <!-- Asegúrate de que esta ruta existe -->
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action">
                            👤 Mi Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <div class="upload-section">
                    <h2 class="mb-3">Subir Documentos</h2>
                    <p class="text-muted mb-4">Carga los documentos PDF requeridos para tus proyectos (Máx. 10MB por archivo)</p>

                    <!-- Formulario de Subida Integrado -->
                    <form action="{{ route('aprendiz.archivos.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="mb-4">
                            <label for="proyecto_select" class="form-label">Selecciona un Proyecto</label>
                            <select class="form-select" name="proyecto_id" id="proyecto_select" required>
                                <option value="">Selecciona un proyecto</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id_proyecto }}">{{ $proyecto->nombre }}</option>
                                @endforeach
                                <option value="1">Proyecto de Seguridad Informática</option>
                                <option value="2">Proyecto de Desarrollo Web</option>
                                <option value="3">Proyecto de Análisis de Datos</option>
                                <option value="4">Proyecto de Redes y Telecomunicaciones</option>
                                <option value="5">Proyecto de Inteligencia Artificial</option>
                                <option value="6">Proyecto de Ciencia de Datos</option>
                                <option value="7">Proyecto de Desarrollo de Aplicaciones Móviles</option>
                                <option value="8">Proyecto de Ciberseguridad</option>
                                <option value="9">Proyecto de Internet de las Cosas (IoT)</option>
                                <option value="10">Proyecto de Computación en la Nube</option>
                            </select>
                            <!-- Muestra error de validación de Laravel -->
                            @error('proyecto_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mensajes de Sesión (Éxito o Errores Globales) -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="dropzone mb-4" id="dropzoneArea">
                            <!-- Icono simple o texto -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-file-earmark-arrow-up text-muted mb-3" viewBox="0 0 16 16">
                              <path d="M12.854 2.854a.5.5 0 0 0-.708-.708L8.5 4.793V1.5a.5.5 0 0 0-1 0v3.293L4.854 2.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0z"/>
                              <path d="M14 14V4.342A1 1 0 0 0 13.78 3.65L11 0h-9A1.5 1.5 0 0 0 1 1.5v13A1.5 1.5 0 0 0 2.5 15h10a1.5 1.5 0 0 0 1.5-1.5M10.5 0v3.5a.5.5 0 0 0 .5.5h3zM3 4a1 1 0 0 1 1-1h5.5v3h3v8a1 1 0 0 1-1 1h-10a1 1 0 0 1-1-1z"/>
                            </svg>
                            <h5 id="dropzoneText">Arrastra archivos aquí</h5>
                            <p class="text-muted">o haz clic para seleccionar archivos PDF</p>
                            <!-- El input real para los archivos -->
                            <input type="file" name="documentos[]" multiple accept=".pdf" class="d-none" id="fileInput">
                        </div>

                        <!-- Lista previa de archivos seleccionados -->
                        <div class="mb-4" id="selectedFilesList" style="display:none;">
                            <h6>Archivos a subir:</h6>
                            <ul class="list-group" id="filePreviewUl">
                                <!-- Archivos seleccionados se insertarán aquí -->
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-sena">Guardar Documentos</button>
                    </form>

                    <h4 class="mt-5 mb-3">Documentos ya Cargados</h4>

                    <!-- Lista de Documentos Subidos (Recibidos del Controller) -->
                    <div class="file-list">
                        @forelse ($archivos as $archivo)
                            <div class="file-item">
                                <div class="d-flex align-items-center">
                                    <span class="check">✓</span>
                                    <!-- Nombre del archivo + nombre del proyecto -->
                                    <div class="ms-1">
                                        <span class="fw-semibold">{{ $archivo->nombre_archivo }}</span>
                                        <small class="text-muted d-block">Proyecto: {{ $archivo->proyecto->nombre ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-3">Subido el: {{ $archivo->created_at->format('Y-m-d') }}</small>

                                    <!-- Formulario de Eliminación -->
                                    <form action="{{ route('aprendiz.archivos.destroy', $archivo->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este documento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <!-- Icono de Bote de Basura -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4h7.764L13 13V5H4z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted">Aún no has subido documentos.</div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropzoneArea = document.getElementById('dropzoneArea');
        const fileInput = document.getElementById('fileInput');
        const filePreviewUl = document.getElementById('filePreviewUl');
        const selectedFilesList = document.getElementById('selectedFilesList');
        const dropzoneText = document.getElementById('dropzoneText');

        // Función para actualizar la previsualización de archivos
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
        }

        // 1. Clic en Dropzone
        dropzoneArea.addEventListener('click', () => fileInput.click());

        // 2. Eventos de Drag & Drop
        dropzoneArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzoneArea.style.borderColor = '#39B54A';
            dropzoneArea.style.backgroundColor = '#e6ffe6'; // Efecto visual
        });

        dropzoneArea.addEventListener('dragleave', () => {
            dropzoneArea.style.borderColor = '#ccc';
            dropzoneArea.style.backgroundColor = '#f8f9fa';
        });

        dropzoneArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzoneArea.style.borderColor = '#ccc';
            dropzoneArea.style.backgroundColor = '#f8f9fa';

            // Asigna los archivos arrastrados al input
            fileInput.files = e.dataTransfer.files;
            updateFilePreview(fileInput.files);
        });

        // 3. Cuando se seleccionan archivos con el diálogo de selección
        fileInput.addEventListener('change', () => {
            updateFilePreview(fileInput.files);
        });

    </script>
</body>
</html>
