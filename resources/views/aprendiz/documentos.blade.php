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

    {{-- Aviso general para subir evidencias --}}
    <div class="alert alert-info" role="alert">
        @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
            <i class="fas fa-info-circle"></i>
            Tienes evidencias asignadas pendientes de cargar. Por favor sube los archivos correspondientes en este módulo.
        @else
            <i class="fas fa-info-circle"></i>
            Recuerda subir aquí las evidencias de tus proyectos cuando te sean asignadas por tu líder.
        @endif
    </div>

    {{-- Formulario para subir documentos --}}
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
                            <select name="id_proyecto" id="id_proyecto" class="form-select @error('id_proyecto') is-invalid @enderror" required @if(empty($pendientesAsignadas) || $pendientesAsignadas->isEmpty()) disabled @endif>
                                <option value="">Selecciona un proyecto</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id_proyecto }}" {{ (string)request('proyecto') === (string)$proyecto->id_proyecto ? 'selected' : '' }}>{{ $proyecto->nombre_proyecto }}</option>
                                @endforeach
                            </select>
                            @error('id_proyecto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $tipoAsignado = null;
                            $labelTipoAsignado = null;
                            $acceptTipos = 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/*,text/plain,text/csv,application/zip,application/x-rar-compressed';

                            if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty()) {
                                $p = $pendientesAsignadas->first();
                                $t = strtolower(trim((string)($p->tipo_documento ?? $p->tipo_archivo ?? '')));
                                if ($t !== '') {
                                    if (in_array($t, ['pdf'], true)) {
                                        $tipoAsignado = 'pdf';
                                        $labelTipoAsignado = 'Archivo PDF (.pdf)';
                                        $acceptTipos = '.pdf,application/pdf';
                                    } elseif (in_array($t, ['doc','docx','word','documento'], true)) {
                                        $tipoAsignado = 'word';
                                        $labelTipoAsignado = 'Documento Word (.doc, .docx)';
                                        $acceptTipos = '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                                    } elseif (in_array($t, ['ppt','pptx','presentacion','presentación'], true)) {
                                        $tipoAsignado = 'presentacion';
                                        $labelTipoAsignado = 'Presentación PowerPoint (.ppt, .pptx)';
                                        $acceptTipos = '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation';
                                    } elseif (in_array($t, ['img','imagen','image'], true)) {
                                        $tipoAsignado = 'imagen';
                                        $labelTipoAsignado = 'Imagen (JPG, PNG, GIF)';
                                        $acceptTipos = 'image/*';
                                    } elseif (in_array($t, ['video','mp4','avi','mov','mkv'], true)) {
                                        $tipoAsignado = 'video';
                                        $labelTipoAsignado = 'Video (MP4, AVI, MOV, MKV)';
                                        $acceptTipos = 'video/*';
                                    } elseif (in_array($t, ['link','enlace','url'], true)) {
                                        $tipoAsignado = 'enlace';
                                        $labelTipoAsignado = 'Enlace (URL)';
                                    } else {
                                        $tipoAsignado = 'otro';
                                        $labelTipoAsignado = strtoupper($t);
                                    }
                                }
                            }
                        @endphp

                        <div class="mb-3">
                            <label for="archivo" class="form-label">Archivo *</label>
                            <div class="dropzone-box">
                                <div class="dz-content">
                                    <i class="bi bi-file-earmark-arrow-up"></i>
                                    <div class="fw-bold">
                                        @if($tipoAsignado && $tipoAsignado !== 'enlace')
                                            Arrastra aquí tu {{ strtolower($labelTipoAsignado) }}
                                        @else
                                            Arrastra archivo aquí
                                        @endif
                                    </div>
                                    <small>
                                        @if($tipoAsignado === 'enlace')
                                            Para la evidencia asignada se requiere un <strong>enlace (URL)</strong>. Este campo es solo para archivos físicos.
                                        @elseif($tipoAsignado)
                                            Tipo asignado por tu líder: <strong>{{ $labelTipoAsignado }}</strong>.
                                        @else
                                            O haz clic para seleccionar archivo
                                        @endif
                                    </small>
                                </div>
                                <input type="file"
                                       name="archivo"
                                       id="archivo"
                                       accept="{{ $acceptTipos }}"
                                       class="dropzone-input @error('archivo') is-invalid @enderror"
                                       @if(empty($pendientesAsignadas) || $pendientesAsignadas->isEmpty()) disabled @else required @endif>
                            </div>
                            <small class="text-muted">
                                @if($tipoAsignado && $tipoAsignado !== 'enlace')
                                    Para la evidencia asignada se espera un archivo de tipo:
                                    <strong>{{ $labelTipoAsignado }}</strong>. Tamaño máximo: 10MB.
                                @else
                                    Tipos permitidos: PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, Imágenes, TXT, CSV, ZIP/RAR. Tamaño máximo: 10MB.
                                @endif
                            </small>
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

                        <button type="submit" class="btn btn-success w-100" @if(empty($pendientesAsignadas) || $pendientesAsignadas->isEmpty()) disabled @endif>
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

                    @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
                        <hr>
                        <h6>Información de las evidencias asignadas pendientes:</h6>
                        <ul class="small mb-0">
                            @foreach($pendientesAsignadas->sortBy('fecha_limite') as $pendiente)
                                <li class="mb-2">
                                    <div><strong>Proyecto:</strong> {{ $pendiente->nombre_proyecto ?? '—' }}</div>
                                    <div><strong>Título:</strong> {{ $pendiente->documento ?? '—' }}</div>
                                    <div><strong>Descripción:</strong> {{ $pendiente->descripcion ?? '—' }}</div>
                                    <div>
                                        <strong>Fecha límite:</strong>
                                        @if(!empty($pendiente->fecha_limite))
                                            {{ \Carbon\Carbon::parse($pendiente->fecha_limite)->format('Y-m-d') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <hr>
                        <h6>No hay evidencias pendientes por entregar.</h6>
                        <p class="small mb-0">
                            Tu líder aún no te ha asignado nuevas evidencias para este módulo. Cuando se asignen,
                            aparecerán aquí con su información de proyecto, título, descripción y fecha límite.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de Evidencias Asignadas Pendientes (subida por evidencia) --}}
    @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> Evidencias Asignadas Pendientes</h5>
                        <span class="badge bg-primary">{{ $pendientesAsignadas->count() }} pendiente(s)</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($pendientesAsignadas->sortBy('fecha_limite') as $pendiente)
                                @php
                                    $tBase = strtolower(trim((string)($pendiente->tipo_documento ?? $pendiente->tipo_archivo ?? '')));
                                    if (in_array($tBase, ['doc','docx','word','documento'], true)) {
                                        $tBase = 'word';
                                    } elseif (in_array($tBase, ['ppt','pptx','presentacion','presentación'], true)) {
                                        $tBase = 'presentacion';
                                    } elseif (in_array($tBase, ['img','imagen','image'], true)) {
                                        $tBase = 'imagen';
                                    } elseif (in_array($tBase, ['link','enlace','url'], true)) {
                                        $tBase = 'enlace';
                                    }

                                    $acceptByTipo = [
                                        'pdf'          => '.pdf,application/pdf',
                                        'word'         => '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'presentacion' => '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        'imagen'       => 'image/*',
                                        'video'        => 'video/*',
                                    ];
                                    $acceptForThis = $acceptByTipo[$tBase] ?? 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/*,text/plain,text/csv,application/zip,application/x-rar-compressed';
                                @endphp
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-4 border-primary">
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-2">
                                                <div class="fw-semibold">{{ $pendiente->documento ?? 'Evidencia' }}</div>
                                                <div class="small text-muted">Proyecto: {{ $pendiente->nombre_proyecto ?? '—' }}</div>
                                                @if(!empty($pendiente->descripcion))
                                                    <div class="small text-muted">{{ $pendiente->descripcion }}</div>
                                                @endif
                                                <div class="small text-muted">
                                                    <strong>Fecha límite:</strong>
                                                    @if(!empty($pendiente->fecha_limite))
                                                        {{ \Carbon\Carbon::parse($pendiente->fecha_limite)->format('Y-m-d') }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                                <div class="small mt-1">
                                                    <strong>Tipo requerido:</strong>
                                                    @if($tBase === 'enlace')
                                                        Enlace (URL)
                                                    @elseif($tBase === 'pdf')
                                                        Archivo PDF (.pdf)
                                                    @elseif($tBase === 'word')
                                                        Documento Word (.doc, .docx)
                                                    @elseif($tBase === 'presentacion')
                                                        Presentación (.ppt, .pptx)
                                                    @elseif($tBase === 'imagen')
                                                        Imagen (JPG, PNG, GIF)
                                                    @elseif($tBase === 'video')
                                                        Video (MP4, AVI, MOV, MKV)
                                                    @else
                                                        Otro tipo de archivo
                                                    @endif
                                                </div>
                                            </div>

                                            <form class="mt-2 pending-upload-form" method="POST" action="{{ route('aprendiz.documentos.uploadAssigned', $pendiente->id_documento) }}" enctype="multipart/form-data">
                                                @csrf
                                                @if($tBase === 'enlace')
                                                    <div class="mb-2">
                                                        <label class="form-label small mb-1">Enlace de la evidencia (URL)</label>
                                                        <input type="url" name="link_url" class="form-control form-control-sm" placeholder="https://..." required>
                                                    </div>
                                                @else
                                                    <div class="mb-2">
                                                        <label class="form-label small mb-1">Archivo de evidencia</label>
                                                        <input type="file" name="archivo" class="form-control form-control-sm" accept="{{ $acceptForThis }}" required>
                                                    </div>
                                                @endif
                                                <button type="submit" class="btn btn-sm btn-primary mt-auto">
                                                    <i class="fas fa-upload"></i> Subir a esta evidencia
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                            <table class="table table-hover" id="docsTable">
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Documento</th>
                                        <th>Tipo</th>
                                        <th>Tamaño</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="docsTbody">
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
                                                @php
                                                    $estado = strtolower((string)($doc->estado ?? 'pendiente'));
                                                    $label = $estado === 'aprobado' ? 'Aprobada' : ($estado === 'rechazado' ? 'Rechazada' : 'Pendiente');
                                                    $badgeClass = $estado === 'aprobado' ? 'bg-success' : ($estado === 'rechazado' ? 'bg-danger' : 'bg-warning text-dark');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                                @if($estado === 'rechazado' && !empty($doc->descripcion))
                                                    <div class="small text-muted mt-1" style="max-width:240px;">
                                                        <strong>Observación:</strong> {{ $doc->descripcion }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="acciones-docs">
                                                    {{-- Descargar --}}
                                                    <a href="{{ route('aprendiz.documentos.download', $doc->id_documento) }}" class="btn btn-sm btn-success me-1" title="Descargar">
                                                        <i class="fas fa-download"></i>
                                                    </a>

                                                    @if(!empty($doc->ruta_archivo) && $estado !== 'aprobado')
                                                        {{-- Editar / reemplazar archivo --}}
                                                        <form action="{{ route('aprendiz.documentos.update', $doc->id_documento) }}"
                                                              method="POST"
                                                              enctype="multipart/form-data"
                                                              class="d-inline-block align-middle mt-1"
                                                              style="max-width: 230px;">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="file"
                                                                   name="archivo"
                                                                   class="form-control form-control-sm mb-1"
                                                                   style="width: 180px;"
                                                                   title="Selecciona un nuevo archivo para reemplazar">
                                                            <input type="text"
                                                                   name="descripcion"
                                                                   class="form-control form-control-sm mb-1"
                                                                   placeholder="Nuevo título (opcional)"
                                                                   value="{{ $doc->documento }}">
                                                            <button type="submit" class="btn btn-sm btn-secondary w-100" title="Editar entrega">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($estado === 'aprobado')
                                                        <span class="badge bg-success ms-1">Aprobada, no editable</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark ms-1">Aún sin archivo, no editable</span>
                                                    @endif

                                                    {{-- Eliminar --}}
                                                    <form action="{{ route('aprendiz.documentos.destroy', $doc->id_documento) }}"
                                                          method="POST"
                                                          class="d-inline ms-1"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('formUploadDoc');
  if (!form) return;
  const dz = form.querySelector('.dropzone-box');
  const input = form.querySelector('#archivo');
  const dzText = form.querySelector('.dz-content .fw-bold');
  if (dz && input){
    dz.addEventListener('click', function(e){
      if (e.target !== input) { input.click(); }
    });
    input.addEventListener('change', function(){
      if (input.files && input.files.length > 0 && dzText){ dzText.textContent = input.files[0].name; }
    });
  }
  form.addEventListener('submit', async function(e){
    try{
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');
      if (btn){ btn.disabled = true; btn.dataset._label = btn.innerHTML; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...'; }
      const fd = new FormData(form);
      const resp = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: fd
      });
      if(!resp.ok){
        let msg = 'Error HTTP '+resp.status;
        try { const err = await resp.json();
          if (err?.message) msg = err.message;
          if (err?.errors){
            const firstKey = Object.keys(err.errors)[0];
            if (firstKey && Array.isArray(err.errors[firstKey]) && err.errors[firstKey][0]){
              msg = err.errors[firstKey][0];
            }
          }
        } catch(_e){}
        throw new Error(msg);
      }
      const data = await resp.json();
      if (!data?.ok){ throw new Error('Respuesta inválida'); }

      // Si el backend indica que hay que recargar (por ejemplo, se completó una evidencia pendiente), recargar
      if (data.reload){
        window.location.reload();
        return;
      }

      const tBody = document.getElementById('docsTbody');
      if (tBody){
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${data.documento.proyecto ?? ''}</td>
          <td><i class="fas fa-file-alt text-primary"></i> ${data.documento.documento ?? ''}</td>
          <td><span class="badge bg-secondary">${String(data.documento.tipo||'').toUpperCase()}</span></td>
          <td>${data.documento.tamanio_kb ?? 0} KB</td>
          <td>${data.documento.fecha ?? ''}</td>
          <td>
            <a href="${data.documento.download_url}" class="btn btn-sm btn-success" title="Descargar"><i class="fas fa-download"></i></a>
            <form action="${data.documento.delete_url}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="_method" value="DELETE">
              <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
            </form>
          </td>`;
        tBody.prepend(tr);
        // Reset form
        form.reset();
        if (dzText){ dzText.textContent = 'Arrastra archivo aquí'; }
        // Aviso
        const ok = document.createElement('div');
        ok.className = 'alert alert-success mt-3';
        ok.textContent = 'Documento subido correctamente';
        form.parentElement.insertBefore(ok, form);
        setTimeout(()=> ok.remove(), 2500);
      } else {
        // Fallback si no existe tabla
        window.location.reload();
      }
    } catch(err){
      console.error(err);
      alert((err && err.message) ? err.message : 'No se pudo subir el documento. Inténtalo nuevamente.');
    } finally {
      const btn = form.querySelector('button[type="submit"]');
      if (btn){ btn.disabled = false; if (btn.dataset._label) btn.innerHTML = btn.dataset._label; }
    }
  });

  // Envío AJAX para formularios de evidencias pendientes
  const pendingForms = document.querySelectorAll('form.pending-upload-form'); 
  pendingForms.forEach((pf) => {
    pf.addEventListener('submit', async function(ev){
      ev.preventDefault();
      const btn = pf.querySelector('button[type="submit"], button');
      try {
        if (btn){ btn.disabled = true; btn.dataset._label = btn.innerHTML; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...'; }
        const fd = new FormData(pf);
        const resp = await fetch(pf.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          body: fd
        });
        if (!resp.ok){
          let msg = 'Error HTTP '+resp.status;
          try { const err = await resp.json(); if (err?.message) msg = err.message; } catch(_e){}
          throw new Error(msg);
        }
        const data = await resp.json();
        if (!data?.ok){ throw new Error('Respuesta inválida'); }
        // Recargar para reflejar que la evidencia ya no está pendiente y aparece en la lista inferior
        window.location.reload();
      } catch(err){
        console.error(err);
        alert((err && err.message) ? err.message : 'No se pudo subir la evidencia.');
      } finally {
        if (btn){ btn.disabled = false; if (btn.dataset._label) btn.innerHTML = btn.dataset._label; }
      }
    });
  });
});
</script>
@endpush
