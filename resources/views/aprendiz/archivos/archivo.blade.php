@extends('layouts.aprendiz')

@section('title','Mis Documentos')
@section('module-title','Mis Documentos')
@section('module-subtitle','Consulta y filtra tus archivos subidos')

@section('content')
<div class="container-fluid py-3 documentos-page">
  <div class="glass-box p-4">
    <form id="uploadForm" method="POST" action="{{ route('aprendiz.documentos.store') }}" enctype="multipart/form-data">
      @csrf
      <!-- Selección de proyecto -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Selecciona un proyecto</label>
        <select name="id_proyecto" id="projectSelect" class="form-select" required>
          <option value="">Selecciona un proyecto</option>
          @foreach(($proyectos ?? collect()) as $p)
            <option value="{{ $p->id_proyecto }}">{{ $p->nombre_proyecto ?? ('Proyecto #'.$p->id_proyecto) }}</option>
          @endforeach
        </select>
      </div>

      <!-- Dropzone verde -->
      <div class="dropzone-box my-3">
        <div class="dz-content">
          <i class="bi bi-file-earmark-arrow-up"></i>
          <div class="fw-bold fs-5">Arrastra archivo aquí</div>
          <small>O haz clic para seleccionar PDF</small>
        </div>
        <input id="fileInput" type="file" name="archivo" accept="application/pdf,.pdf" class="dropzone-input" required>
      </div>

      <!-- Lista de documentos cargados (maqueta) -->
      <div class="mt-3" id="filesList">
        <div class="fw-bold mb-2">Documentos Cargados</div>
        <div class="list-group" id="filesGroup"></div>
      </div>

      <!-- Botón guardar -->
      <div class="mt-3">
        <button id="saveBtn" type="submit" class="btn btn-success w-100">Guardar Documentos</button>
      </div>
    </form>
  </div>
  
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const input = document.getElementById('fileInput');
    const list  = document.getElementById('filesGroup');
    const save  = document.getElementById('saveBtn');
    function fmtSize(bytes){
      if(!bytes && bytes !== 0) return '';
      const u=['B','KB','MB','GB']; let i=0; let v=bytes; while(v>=1024 && i<u.length-1){ v/=1024; i++; }
      return v.toFixed(2)+' '+u[i];
    }
    input?.addEventListener('change', (e)=>{
      const f = input.files && input.files[0];
      list.innerHTML = '';
      if(!f){ save.disabled = true; return; }
      const isPdf = f.type === 'application/pdf' || /\.pdf$/i.test(f.name);
      const tooBig = f.size > 10*1024*1024; // 10MB
      if(!isPdf){
        list.innerHTML = '<div class="list-group-item text-danger">Solo se permiten archivos PDF</div>';
        input.value = '';
        save.disabled = true;
        return;
      }
      if(tooBig){
        list.innerHTML = '<div class="list-group-item text-danger">El archivo supera 10MB</div>';
        input.value = '';
        save.disabled = true;
        return;
      }
      const now = new Date();
      const li = document.createElement('div');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = '<div>'+
        '<div class="fw-semibold"><i class="bi bi-check2-circle text-success me-2"></i>' + (f.name || 'archivo.pdf') + '</div>'+
        '<small class="text-muted">' + fmtSize(f.size) + ' – ' + now.toISOString().slice(0,10) + '</small>'+
      '</div>'+
      '<button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Quitar">×</button>';
      li.querySelector('button')?.addEventListener('click', ()=>{ input.value=''; list.innerHTML=''; save.disabled=true; });
      list.appendChild(li);
      // Habilitar guardar si hay archivo válido; el 'required' del select validará proyecto al enviar
      save.disabled = false;
    });
  })();
</script>
@endpush
