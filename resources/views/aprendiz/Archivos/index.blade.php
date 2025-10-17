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

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .file-item .check {
            color: var(--sena-green);
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
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action">
                            📁 Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action active">
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
                <div class="upload-section">
                    <h2 class="mb-3">Subir Documentos</h2>
                    <p class="text-muted mb-4">Carga los documentos PDF requeridos para tus proyectos</p>

                    <form action="{{ route('aprendiz.archivos.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Selecciona un Proyecto</label>
                            <select class="form-select" name="proyecto_id" required>
                                <option value="">Proyecto de Seguridad Informática</option>
                                <option value="">Proyecto de Desarrollo Web</option>
                                <option value="">Proyecto de Análisis de Datos</option>
                                <option value="">Proyecto de Redes y Telecomunicaciones</option>
                                <option value="">Proyecto de Inteligencia Artificial</option>
                            </select>
                        </div>

                        <div class="dropzone mb-4">
                            <img src="{{ asset('images/file-icon.png') }}" alt="File Icon" width="48" class="mb-3">
                            <h5>Arrastra archivos aquí</h5>
                            <p class="text-muted">o haz clic para seleccionar archivos PDF</p>
                            <input type="file" name="documentos[]" multiple accept=".pdf" class="d-none" id="fileInput">
                        </div>

                        <div class="mb-4">
                            <h6>Documentos Cargados</h6>
                            <div class="file-item">
                                <div>
                                    <span class="check">✓</span>
                                    <span>Propuesta_Proyecto.pdf</span>
                                </div>
                                <small class="text-muted">2.50 MB • 2025-10-15</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sena">Guardar Documentos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropzone = document.querySelector('.dropzone');
        const fileInput = document.getElementById('fileInput');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#39B54A';
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = '#ccc';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            fileInput.files = e.dataTransfer.files;
        });
    </script>
</body>
</html>
