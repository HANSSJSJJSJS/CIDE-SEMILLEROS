<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel del Aprendiz SENA - Mi Perfil</title>

    <!-- Bootstrap & Icons primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- CSS personalizado desde public -->
    <link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
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

            <!-- Contenido Principal -->
            <div class="col-md-9">
                <div class="profile-section">
                    <h2 class="mb-3">Mi Perfil</h2>
                    <p class="text-muted mb-4">Información personal y académica</p>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Programa de Formación</label>
                            <input type="text" class="form-control" value="Semillero SENA" disabled>
                        </div>

                        @if(isset($aprendiz->estado))
                            @if($aprendiz->estado === 'Activo')
                                <div class="alert alert-success text-center fw-bold">
                 Estado: HABILITADO EN EL SÍSTEMA
            </div>
        @else
            <div class="alert alert-danger text-center fw-bold">
                Estado: INHABILITADO EN EL SÍSTEMA
            </div>
        @endif
    @else
        <div class="alert alert-secondary text-center fw-bold">
            Estado NO DISPONIBLE EN EL SÍSTEMA
        </div>
    @endif

                    </form>

                    <button type="button" class="btn btn-primary edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        Editar Perfil
                    </button>

                    <!-- Modal de edición de perfil -->
                    <div class="modal fade modal-sena" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editProfileForm" method="POST" action="{{ route('aprendiz.perfil.update') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Programa de Formación</label>
                                            <input type="text" class="form-control" name="programa_formacion" value="Semillero SENA" disabled>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-sena">Actualizar Perfil</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const hasErrors = {!! json_encode($errors->any() ?? false) !!};

        if (hasErrors) {
            const modalEl = document.getElementById('editProfileModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
        }
    });
    </script>
</body>
</html>
