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
                    <form id="formUploadDoc" action="{{ route('aprendiz.documentos.store') }}" method="POST" enctype="multipart/form-data" data-uploadassigned-url-template="{{ route('aprendiz.documentos.uploadAssigned', ['id' => ':ID']) }}">
                        @csrf

                        @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
                        <div class="mb-3">
                            <label for="evidencia_asignada" class="form-label">Evidencia asignada (opcional)</label>
                            <select id="evidencia_asignada" class="form-select">
                                <option value="">— Selecciona la evidencia que subira —</option>
                                @foreach($pendientesAsignadas->sortBy('fecha_limite') as $pend)
                                    @php
                                        $t = strtolower(trim((string)($pend->tipo_documento ?? $pend->tipo_archivo ?? '')));
                                        $acceptForThis = 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/*,text/plain,text/csv,application/zip,application/x-rar-compressed';
                                        $labelForThis = strtoupper($t ?: 'ARCHIVO');
                                        if (in_array($t, ['pdf'], true)) { $acceptForThis = '.pdf,application/pdf'; $labelForThis = 'Archivo PDF (.pdf)'; }
                                        elseif (in_array($t, ['doc','docx','word','documento'], true)) { $acceptForThis = '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'; $labelForThis = 'Documento Word (.doc, .docx)'; }
                                        elseif (in_array($t, ['ppt','pptx','presentacion','presentación'], true)) { $acceptForThis = '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation'; $labelForThis = 'Presentación PowerPoint (.ppt, .pptx)'; }
                                        elseif (in_array($t, ['img','imagen','image'], true)) { $acceptForThis = 'image/*'; $labelForThis = 'Imagen (JPG, PNG, GIF)'; }
                                        elseif (in_array($t, ['video','mp4','avi','mov','mkv'], true)) { $acceptForThis = 'video/*'; $labelForThis = 'Video (MP4, AVI, MOV, MKV)'; }
                                        elseif (in_array($t, ['enlace','link','url'], true)) { $labelForThis = 'Enlace (URL)'; }
                                    @endphp
                                    <option value="{{ $pend->id_documento }}"
                                            data-proyecto="{{ $pend->id_proyecto }}"
                                            data-accept="{{ $acceptForThis }}"
                                            data-tipo="{{ $t }}"
                                            data-label="{{ $labelForThis }}"
                                            data-fecha-limite="{{ !empty($pend->fecha_limite) ? \Carbon\Carbon::parse($pend->fecha_limite)->format('Y-m-d') : '' }}">
                                        {{ $pend->nombre_proyecto ?? 'Proyecto' }} — {{ $pend->documento ?? 'Evidencia' }} {{ !empty($pend->fecha_limite) ? '(' . \Carbon\Carbon::parse($pend->fecha_limite)->format('Y-m-d') . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Si eliges una evidencia, el archivo se subirá directamente a esa asignación.</small>
                        </div>
                        @endif

                        <div id="deadlineAlert" class="alert alert-warning" style="display:none;" role="alert"></div>

                        <div class="mb-3">
                            <label for="id_proyecto" class="form-label">Proyecto *</label>
                            <select name="id_proyecto" id="id_proyecto" class="form-select @error('id_proyecto') is-invalid @enderror" required>
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
                                        <span id="dz-title-generic" @if($tipoAsignado) style="display:none" @endif>Arrastra archivo aquí</span>
                                        <span id="dz-title-assigned" @if(!$tipoAsignado) style="display:none" @endif>
                                            Arrastra aquí tu <span id="dz-title-assigned-label">{{ strtolower($labelTipoAsignado ?? '') }}</span>
                                        </span>
                                    </div>
                                    <small>
                                        <span id="dz-note-generic" @if($tipoAsignado) style="display:none" @endif>O haz clic para seleccionar archivo</span>
                                        <span id="dz-note-assigned" @if(!$tipoAsignado) style="display:none" @endif>
                                            Tipo asignado por tu líder: <strong id="dz-assigned-label">{{ $labelTipoAsignado }}</strong>.
                                        </span>
                                    </small>
                                </div>
                                <input type="file"
                                       name="archivo"
                                       id="archivo"
                                       accept="{{ $acceptTipos }}"
                                       class="dropzone-input @error('archivo') is-invalid @enderror"
                                       required>
                            </div>
                            <small class="text-muted">
                                <span id="dz-expected-generic" @if($tipoAsignado) style="display:none" @endif>
                                    Tipos permitidos: PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX, Imágenes, TXT, CSV, ZIP/RAR. Tamaño máximo: 10MB.
                                </span>
                                <span id="dz-expected-assigned" @if(!$tipoAsignado) style="display:none" @endif>
                                    Para la evidencia asignada se espera un archivo de tipo:
                                    <strong id="dz-expected-label">{{ $labelTipoAsignado }}</strong>. Tamaño máximo: 10MB.
                                </span>
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

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload"></i> Subir Documento
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card evidencias-box shadow-sm">
                <div class="section-head section-head-blue d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <h5 class="mb-0">Información</h5>
                    </div>
                    @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
                        <span class="badge count-badge">{{ $pendientesAsignadas->count() }} pendiente(s)</span>
                    @endif
                </div>
                <div class="card-body">
                    <p><strong>Aprendiz:</strong> {{ $aprendiz->nombre_completo }}</p>
                    <p><strong>Proyectos asignados:</strong> {{ $proyectos->count() }}</p>
                    <p><strong>Documentos subidos:</strong> {{ $documentos->count() }}</p>

                    @if(isset($pendientesAsignadas) && $pendientesAsignadas->isNotEmpty())
                        <hr class="border-success">
                        <h6 class="mb-3 text-success"><i class="fas fa-exclamation-triangle me-1"></i> Evidencias asignadas pendientes</h6>
                        <ul class="small mb-0 list-unstyled">
                            @foreach($pendientesAsignadas->sortBy('fecha_limite') as $pendiente)
                                <li class="mb-2 pend-item" data-evid="{{ $pendiente->id_documento }}" style="cursor:pointer;">
                                    <div class="p-2 rounded border border-success bg-success bg-opacity-10 pend-card" data-card-id="{{ $pendiente->id_documento }}">
                                        <div class="mb-1 small text-muted">ID: {{ $pendiente->id_documento }}</div>
                                        <div><strong>Proyecto:</strong> {{ $pendiente->nombre_proyecto ?? '—' }}</div>
                                        @if(isset($pendiente->numero_evidencia))
                                            <div><strong>Número de Evidencia:</strong> #{{ $pendiente->numero_evidencia }}</div>
                                        @endif
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
                                        <div class="mt-2 d-flex align-items-center" style="gap:8px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-preguntar-evid" data-evid="{{ $pendiente->id_documento }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="apr-tooltip-blue" title="Usa este botón para enviar dudas al líder sobre esta evidencia">
                                                <i class="bi bi-question-circle"></i> Preguntar al líder
                                            </button>
                                            @if(!empty($pendiente->respuesta_lider))
                                                <button type="button" class="btn btn-sm btn-light" onclick="toggleQA_apr({{ $pendiente->id_documento }})" title="Ver respuesta del líder" style="border:1px solid #e0e0e0;">
                                                    <i class="bi bi-chat-dots-fill" style="color:#002f6c"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @if(!empty($pendiente->respuesta_lider))
                                            <div id="qa_apr_{{ $pendiente->id_documento }}" class="mt-2" style="display:none;">
                                                <div class="p-2" style="background:#f3fbf4;border:1px dashed #a5d6a7;border-radius:8px;">
                                                    <div class="small text-success mb-1"><i class="bi bi-reply-fill"></i> Respuesta del líder</div>
                                                    <div class="text-success" style="white-space:pre-wrap;">{{ $pendiente->respuesta_lider }}</div>
                                                    @if(!empty($pendiente->respondido_en))
                                                        <div class="small text-muted mt-1">Respondido: {{ $pendiente->respondido_en }}</div>
                                                    @endif
                                                </div>
                                            </div>
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

    {{-- Lista de documentos subidos --}}
    <div class="row">
        <div class="col-12">
            <div class="card evidencias-box shadow-sm">
                <div class="section-head section-head-blue d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open"></i>
                        <h5 class="mb-0">Mis Documentos Subidos</h5>
                    </div>
                    <span class="badge count-badge">{{ $documentos->count() }} documento(s)</span>
                </div>
                <div class="card-body">
                    @if($documentos->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>No has subido ningún documento aún</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle evid-table" id="docsTable">
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
                                                @if(isset($doc->numero_evidencia))
                                                    <span class="badge bg-primary me-1">#{{ $doc->numero_evidencia }}</span>
                                                @endif
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
                                                    @php $obsId = 'obs_'.$doc->id_documento; @endphp
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2 align-baseline"
                                                            title="Ver observación"
                                                            data-bs-toggle="collapse" data-bs-target="#{{ $obsId }}"
                                                            aria-expanded="false" aria-controls="{{ $obsId }}">
                                                        <i class="bi bi-chat-left-text"></i>
                                                    </button>
                                                    <div id="{{ $obsId }}" class="collapse mt-1">
                                                        <div class="small text-muted p-2 border rounded bg-light" style="max-width: 320px;">
                                                            <strong>Observación:</strong> {{ $doc->descripcion }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="acciones-docs">
                                                    {{-- Ver inline --}}
                                                    <a href="{{ route('aprendiz.documentos.view', $doc->id_documento) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Ver">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
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
                                                            <button type="submit" class="btn btn-sm btn-success w-100" title="Guardar">
                                                                <i class="fas fa-save"></i> Guardar
                                                            </button>
                                                        </form>
                                                    @elseif($estado === 'aprobado')
                                                        <span class="badge bg-success ms-1">Aprobada, no editable</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark ms-1">Aún sin archivo, no editable</span>
                                                    @endif

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
  // Inicializar tooltips Bootstrap para elementos con data-bs-toggle="tooltip"
  try{
    const tooltipTriggerList = Array.prototype.slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) { try{ new bootstrap.Tooltip(el); }catch(_e){} });
  }catch(_e){}
  const form = document.getElementById('formUploadDoc');
  if (!form) return;
  const dz = form.querySelector('.dropzone-box');
  const input = form.querySelector('#archivo');
  const dzText = form.querySelector('.dz-content .fw-bold');
  const proyectoSelect = document.getElementById('id_proyecto');
  const evidenciaSelect = document.getElementById('evidencia_asignada');
  const urlTemplate = form.getAttribute('data-uploadassigned-url-template');
  const dzTitleGeneric = document.getElementById('dz-title-generic');
  const dzTitleAssigned = document.getElementById('dz-title-assigned');
  const dzTitleAssignedLabel = document.getElementById('dz-title-assigned-label');
  const dzNoteGeneric = document.getElementById('dz-note-generic');
  const dzNoteAssigned = document.getElementById('dz-note-assigned');
  const dzAssignedLabel = document.getElementById('dz-assigned-label');
  const dzExpectedGeneric = document.getElementById('dz-expected-generic');
  const dzExpectedAssigned = document.getElementById('dz-expected-assigned');
  const dzExpectedLabel = document.getElementById('dz-expected-label');
  const deadlineAlert = document.getElementById('deadlineAlert');
  const submitBtn = form.querySelector('button[type="submit"]');
  const todayStr = (new Date()).toISOString().slice(0,10);
  if (dz && input){
    dz.addEventListener('click', function(e){
      if (e.target !== input) { input.click(); }
    });
    input.addEventListener('change', function(){
      if (input.files && input.files.length > 0 && dzText){ dzText.textContent = input.files[0].name; }
    });
  }

  // Permitir seleccionar haciendo clic en las tarjetas del listado de pendientes
  document.querySelectorAll('li.pend-item').forEach(function(li){
    li.addEventListener('click', function(){
      const id = this.getAttribute('data-evid');
      if (!id || !evidenciaSelect) return;
      evidenciaSelect.value = id;
      evidenciaSelect.dispatchEvent(new Event('change'));
      // Desplazar al formulario de subida y enfocar input de archivo
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setTimeout(()=>{ if (input) input.focus(); }, 300);
    });
  });

  // Botón explícito Seleccionar
  document.querySelectorAll('.seleccionar-evidencia').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.stopPropagation();
      const id = this.getAttribute('data-evid');
      if (!id || !evidenciaSelect) return;
      evidenciaSelect.value = id;
      evidenciaSelect.dispatchEvent(new Event('change'));
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setTimeout(()=>{ if (input) input.focus(); }, 300);
    });
  });

  // Cambiar modo del formulario según evidencia seleccionada
  if (evidenciaSelect) {
    evidenciaSelect.addEventListener('change', function(){
      const id = this.value;
      if (id) {
        const opt = this.selectedOptions[0];
        const proj = opt.getAttribute('data-proyecto');
        const accept = opt.getAttribute('data-accept');
        const label = opt.getAttribute('data-label') || '';
        const fechaLimite = opt.getAttribute('data-fecha-limite') || '';
        const expired = !!(fechaLimite && fechaLimite < todayStr);
        if (deadlineAlert) {
          if (expired) {
            deadlineAlert.style.display = '';
            deadlineAlert.innerHTML = `<i class="fas fa-exclamation-triangle"></i> La fecha límite (<strong>${fechaLimite}</strong>) ya venció. No puedes subir esta evidencia.`;
          } else {
            deadlineAlert.style.display = 'none';
            deadlineAlert.textContent = '';
          }
        }
        if (submitBtn) {
          submitBtn.disabled = expired;
        }
        if (proyectoSelect && proj) {
          proyectoSelect.value = proj;
          proyectoSelect.disabled = true;
        }
        if (input && accept) {
          input.setAttribute('accept', accept);
        }
        if (urlTemplate) {
          form.setAttribute('action', urlTemplate.replace(':ID', id));
          form.dataset.mode = 'assigned';
        }
        // Mostrar mensajes de tipo asignado
        if (dzTitleGeneric) dzTitleGeneric.style.display = 'none';
        if (dzNoteGeneric) dzNoteGeneric.style.display = 'none';
        if (dzExpectedGeneric) dzExpectedGeneric.style.display = 'none';
        if (dzTitleAssigned) dzTitleAssigned.style.display = '';
        if (dzNoteAssigned) dzNoteAssigned.style.display = '';
        if (dzExpectedAssigned) dzExpectedAssigned.style.display = '';
        if (dzTitleAssignedLabel) dzTitleAssignedLabel.textContent = (label || '').toLowerCase();
        if (dzAssignedLabel) dzAssignedLabel.textContent = label;
        if (dzExpectedLabel) dzExpectedLabel.textContent = label;
        // Marcar visualmente la tarjeta seleccionada
        document.querySelectorAll('.pend-card').forEach(el => { el.style.boxShadow = ''; el.style.borderWidth='1px'; });
        const card = document.querySelector(`.pend-card[data-card-id="${id}"]`);
        if (card) { card.style.boxShadow = '0 0 0 2px #198754 inset'; card.style.borderWidth='2px'; }
      } else {
        // Volver a modo general
        if (proyectoSelect) { proyectoSelect.disabled = false; }
        if (deadlineAlert) { deadlineAlert.style.display = 'none'; deadlineAlert.textContent = ''; }
        if (submitBtn) { submitBtn.disabled = false; }
        if (input) {
          input.setAttribute('accept', '{{ $acceptTipos }}');
        }
        form.setAttribute('action', "{{ route('aprendiz.documentos.store') }}");
        delete form.dataset.mode;
        // Volver a mensajes genéricos
        if (dzTitleGeneric) dzTitleGeneric.style.display = '';
        if (dzNoteGeneric) dzNoteGeneric.style.display = '';
        if (dzExpectedGeneric) dzExpectedGeneric.style.display = '';
        if (dzTitleAssigned) dzTitleAssigned.style.display = 'none';
        if (dzNoteAssigned) dzNoteAssigned.style.display = 'none';
        if (dzExpectedAssigned) dzExpectedAssigned.style.display = 'none';
        if (dzTitleAssignedLabel) dzTitleAssignedLabel.textContent = '';
        document.querySelectorAll('.pend-card').forEach(el => { el.style.boxShadow = ''; el.style.borderWidth='1px'; });
      }
    });
  }
  form.addEventListener('submit', async function(e){
    // Si es subida a evidencia asignada, dejar submit normal (no AJAX)
    if (form.dataset.mode === 'assigned') {
      return true;
    }
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

  // Modal elegante para "Preguntar al líder"
  (function(){
    let modalEl, modal, txtArea, btnOk, btnCancel, counterEl, currentEvidId = null;

    function ensureModal(){
      if (modalEl) return;
      modalEl = document.createElement('div');
      modalEl.className = 'modal fade admin-modal';
      modalEl.id = 'askLeaderModal';
      modalEl.tabIndex = -1;
      modalEl.setAttribute('aria-hidden','true');
      modalEl.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-question-circle me-2"></i> Preguntar al líder</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <label class="form-label fw-semibold mb-2">Escribe tu pregunta para el líder sobre esta evidencia:</label>
              <textarea class="form-control" id="askLeaderText" rows="4" maxlength="1000" placeholder="Escribe tu pregunta..." style="border-radius:12px"></textarea>
              <div class="d-flex justify-content-between align-items-center mt-2 small">
                <span class="text-muted">Mínimo 3 caracteres</span>
                <span id="askCounter" class="text-muted">0 / 1000</span>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success" id="askLeaderSubmit">Aceptar</button>
            </div>
          </div>
        </div>`;
      document.body.appendChild(modalEl);
      modal = new bootstrap.Modal(modalEl);
      txtArea = modalEl.querySelector('#askLeaderText');
      btnOk = modalEl.querySelector('#askLeaderSubmit');
      btnCancel = modalEl.querySelector('[data-bs-dismiss="modal"]');
      counterEl = modalEl.querySelector('#askCounter');

      txtArea.addEventListener('input', ()=>{
        const v = txtArea.value || '';
        counterEl.textContent = `${v.length} / 1000`;
        if (v.trim().length >= 3){ btnOk.removeAttribute('disabled'); }
        else { btnOk.setAttribute('disabled','disabled'); }
      });

      modalEl.addEventListener('shown.bs.modal', ()=>{ txtArea.focus(); txtArea.select(); });
      modalEl.addEventListener('hidden.bs.modal', ()=>{ currentEvidId = null; txtArea.value=''; counterEl.textContent = '0 / 1000'; btnOk.setAttribute('disabled','disabled'); });

      btnOk.addEventListener('click', async ()=>{
        const pregunta = (txtArea.value||'').trim();
        if (!currentEvidId || pregunta.length < 3) return;
        btnOk.disabled = true; btnOk.dataset._label = btnOk.innerHTML; btnOk.innerHTML = 'Enviando...';
        try{
          const resp = await fetch(`/aprendiz/documentos/${currentEvidId}/preguntar`,{
            method:'POST',
            headers:{
              'Content-Type':'application/json',
              'X-Requested-With':'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pregunta })
          });
          const data = await resp.json().catch(()=>({}));
          if (resp.ok && data?.ok){
            modal.hide();
            // Marcar visualmente en la tarjeta correspondiente
            const trigger = document.querySelector(`.btn-preguntar-evid[data-evid="${currentEvidId}"]`);
            if (trigger){
              const parent = trigger.closest('.pend-card');
              if (parent && !parent.querySelector('.badge.bg-secondary')){
                const span = document.createElement('span');
                span.className = 'badge bg-secondary ms-2';
                span.textContent = 'Pregunta enviada';
                trigger.parentElement.appendChild(span);
              }
            }
          } else {
            alert(data?.message || 'No se pudo enviar la pregunta.');
          }
        }catch(err){
          console.error(err);
          alert('Error de conexión. Intenta nuevamente.');
        } finally {
          btnOk.disabled = false; if (btnOk.dataset._label) btnOk.innerHTML = btnOk.dataset._label;
        }
      });
    }

    function openAskModal(evidId){
      ensureModal();
      currentEvidId = evidId;
      modal.show();
    }

    // Reemplazar prompt por modal
    document.querySelectorAll('.btn-preguntar-evid').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        const evidId = this.getAttribute('data-evid');
        if (!evidId) return;
        openAskModal(evidId);
      });
    });
  })();
  // Toggle panel de pregunta/respuesta (aprendiz)
  window.toggleQA_apr = function(id){
    const panel = document.getElementById('qa_apr_' + id);
    if (!panel) return;
    const show = panel.style.display !== 'block';
    panel.style.display = show ? 'block' : 'none';
    if (show) {
      // Marcar como leída la respuesta del líder si existe
      fetch(`/aprendiz/documentos/${id}/respuesta-leida`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      }).then(()=>{
        // refrescar resumen de notificaciones si la función existe en el layout
        if (typeof fetchSummary === 'function') { fetchSummary(); }
      }).catch(()=>{});
    }
  };
});
</script>
@endpush
