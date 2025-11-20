@extends('layouts.aprendiz')

@section('title', 'Mis Documentos')
@section('module-title','Mis Documentos')
@section('module-subtitle','Gestión y subida de archivos')

@section('content')
<div class="container-fluid py-4 documentos-page">
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm upload-card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> Subir Nuevo Documento</h5>
        </div>
        <div class="card-body">
          <form id="formUploadDoc" action="{{ route('aprendiz.documentos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
              <label for="id_proyecto" class="form-label">Proyecto *</label>
              <select name="id_proyecto" id="id_proyecto" class="form-select @error('id_proyecto') is-invalid @enderror" required>
                <option value="">Selecciona un proyecto</option>
                @foreach($proyectos as $proyecto)
                  <option value="{{ $proyecto->id_proyecto }}" {{ (string)request('proyecto', (string)($proyectoSeleccionado ?? '')) === (string)$proyecto->id_proyecto ? 'selected' : '' }}>
                    {{ $proyecto->nombre_proyecto }}
                  </option>
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
                  <small>O haz clic para seleccionar archivo</small>
                </div>
                <input type="file" name="archivo" id="archivo" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/*,text/plain,text/csv,application/zip,application/x-rar-compressed" class="dropzone-input @error('archivo') is-invalid @enderror" required>
              </div>
              <small class="text-muted">Tipos permitidos: PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, Imágenes, TXT, CSV, ZIP/RAR. Tamaño máximo: 10MB</small>
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
          <p><strong>Proyectos disponibles:</strong> {{ $proyectos->count() }}</p>
          <p class="text-muted mb-1">Tipos de archivo permitidos:</p>
          <ul class="small mb-0">
            <li>Documentos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</li>
            <li>Imágenes: JPG, PNG, GIF</li>
            <li>Comprimidos: ZIP, RAR</li>
            <li>Otros: TXT, CSV</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
