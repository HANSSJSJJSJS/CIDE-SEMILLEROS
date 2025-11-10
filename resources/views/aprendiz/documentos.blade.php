@extends('layouts.aprendiz')

@section('title', 'Mis Documentos')
@section('module-title','Mis Documentos')
@section('module-subtitle','Gestión y subida de archivos')

@section('content')
<div class="container-fluid py-4 documentos-page">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-file-upload"></i> Gestión de Documentos
            </h2>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Formulario para subir documentos --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm upload-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> Subir Nuevo Documento</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('aprendiz.documentos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="id_proyecto" class="form-label">Proyecto *</label>
                            <select name="id_proyecto" id="id_proyecto" class="form-select @error('id_proyecto') is-invalid @enderror" required>
                                <option value="">Selecciona un proyecto</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id_proyecto }}">{{ $proyecto->nombre_proyecto }}</option>
                                @endforeach
                            </select>
                            @error('id_proyecto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="archivo" class="form-label">Archivo *</label>
                            <div class="dropzone-box">
                                <div class="dz-content">
                                    <i class="bi bi-file-earmark-arrow-up"></i>
                                    <div class="fw-bold">Arrastra archivo aquí</div>
                                    <small>O haz clic para seleccionar PDF</small>
                                </div>
                                <input type="file" name="archivo" id="archivo" accept="application/pdf" class="dropzone-input @error('archivo') is-invalid @enderror" required>
                            </div>
                            <small class="text-muted">Solo PDF. Tamaño máximo: 10MB</small>
                            @error('archivo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Ej: Informe final, Presentación, etc.">
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload"></i> Subir Documento
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información</h5>
                </div>
                <div class="card-body">
                    <p><strong>Aprendiz:</strong> {{ $aprendiz->nombre_completo }}</p>
                    <p><strong>Proyectos asignados:</strong> {{ $proyectos->count() }}</p>
                    <p><strong>Documentos subidos:</strong> {{ $documentos->count() }}</p>
                    <hr>
                    <h6>Tipos de archivo permitidos:</h6>
                    <ul class="small">
                        <li>Documentos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</li>
                        <li>Imágenes: JPG, PNG, GIF</li>
                        <li>Comprimidos: ZIP, RAR</li>
                        <li>Otros: TXT, CSV</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de documentos subidos --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder-open"></i> Mis Documentos Subidos</h5>
                </div>
                <div class="card-body">
                    @if($documentos->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>No has subido ningún documento aún</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Documento</th>
                                        <th>Tipo</th>
                                        <th>Tamaño</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documentos as $doc)
                                        <tr>
                                            <td>{{ $doc->nombre_proyecto }}</td>
                                            <td>
                                                <i class="fas fa-file-alt text-primary"></i>
                                                {{ $doc->documento }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ strtoupper(pathinfo($doc->ruta_archivo, PATHINFO_EXTENSION)) }}</span>
                                            </td>
                                            <td>{{ number_format($doc->tamanio / 1024, 2) }} KB</td>
                                            <td>{{ \Carbon\Carbon::parse($doc->fecha_subida)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('aprendiz.documentos.download', $doc->id_documento) }}" class="btn btn-sm btn-success" title="Descargar">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('aprendiz.documentos.destroy', $doc->id_documento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
