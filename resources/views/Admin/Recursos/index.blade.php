{{-- resources/views/admin/recursos/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
<style>
  .rec-head { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:16px; }
  .rec-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap:14px; }
  .rec-card { background: rgba(255,255,255,0.85); border:1px solid rgba(255,255,255,0.22); border-radius:14px; padding:14px; backdrop-filter: blur(4px); }
  .rec-card h5 { margin:0 0 6px 0; font-weight:600; }
  .rec-card p { margin:0 0 8px 0; color:#444; font-size: 0.95rem; min-height: 40px; }
  .rec-actions { display:flex; gap:8px; flex-wrap:wrap; }
  .rec-section { margin-top: 24px; }
  .rec-section h4 { margin-bottom: 10px; font-weight: 700; }
  .badge-cat { font-size: .75rem; padding: .25rem .5rem; border-radius: 999px; background: #eaf4ff; }
  .rec-empty { padding: 20px; border-radius: 12px; background: #f6f6f9; text-align:center; }
</style>
@endpush

@section('module-title','Centro de Recursos')
@section('module-subtitle','Plantillas, manuales y otros documentos')

@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="rec-head">
    <div class="input-group" style="max-width:480px;">
      <span class="input-group-text">🔍</span>
      <input id="recSearch" type="search" class="form-control" placeholder="Buscar recurso (nombre o descripción)">
    </div>

    <select id="recFilter" class="form-select" style="max-width:220px;">
      <option value="">Todas las categorías</option>
      <option value="plantillas">Plantillas</option>
      <option value="manuales">Manuales</option>
      <option value="otros">Otros</option>
    </select>

    <button class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#recUploadModal">
      <i class="bi bi-upload"></i> Subir recurso
    </button>
  </div>

  <div id="recContainer"></div>

  {{-- Modal subir --}}
 <div class="modal fade"
     id="recUploadModal"
     data-bs-backdrop="false"
     data-bs-keyboard="true"
     tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="recForm" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Subir recurso</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre del archivo *</label>
                <input type="text" name="nombre_archivo" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Categoría *</label>
                <select name="categoria" class="form-select" required>
                  <option value="plantillas">Plantillas</option>
                  <option value="manuales">Manuales</option>
                  <option value="otros">Otros</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Descripción (opcional)</label>
                <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve descripción del recurso"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Archivo *</label>
                <input type="file" name="archivo" class="form-control" required>
                <small class="text-muted">Máx 20MB. Se admite cualquier tipo de archivo.</small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-save"></i> Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(() => {
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  const ENDPOINTS = {
    listar:  "{{ route('admin.recursos.listar', [], false) }}",
    store:   "{{ route('admin.recursos.store',  [], false) }}",
    destroy: (id) => "{{ url('admin/recursos') }}/" + id
  };

  const recContainer = document.getElementById('recContainer');
  const recSearch    = document.getElementById('recSearch');
  const recFilter    = document.getElementById('recFilter');
  const recForm      = document.getElementById('recForm');
  const modalEl      = document.getElementById('recUploadModal');

  // ====== FUNCIONES UTILITARIAS ======
  function iconFromMime(mime) {
    if (!mime) return '📎';
    if (mime.includes('pdf')) return '📘';
    if (mime.includes('word') || mime.includes('officedocument.word')) return '📄';
    if (mime.includes('excel') || mime.includes('spreadsheet')) return '📊';
    if (mime.includes('image')) return '🖼️';
    return '📎';
  }

  function prettySize(bytes) {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    const kb = bytes / 1024;
    if (kb < 1024) return kb.toFixed(1) + ' KB';
    const mb = kb / 1024;
    return mb.toFixed(1) + ' MB';
  }

  function escapeHtml(s) {
    return String(s ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  const cleanupBackdrop = () => {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  };

  // ====== CARGAR RECURSOS ======
  async function loadResources() {
    const q = recSearch.value.trim();
    const categoria = recFilter.value;
    const url = new URL(ENDPOINTS.listar, window.location.origin);
    if (q) url.searchParams.set('q', q);
    if (categoria) url.searchParams.set('categoria', categoria);

    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const json = await res.json();
    renderGrouped(json.data || []);
  }

  function renderGrouped(items) {
    const cats = { plantillas: [], manuales: [], otros: [] };
    items.forEach(it => {
      if (!cats[it.categoria]) cats[it.categoria] = [];
      cats[it.categoria].push(it);
    });

    recContainer.innerHTML = '';
    renderSection('Plantillas del sistema', cats.plantillas);
    renderSection('Manuales y guías', cats.manuales);
    renderSection('Otros documentos', cats.otros);
  }

  function renderSection(title, arr) {
    const section = document.createElement('section');
    section.className = 'rec-section';
    section.innerHTML = `<h4>${title}</h4>`;

    if (!arr || arr.length === 0) {
      section.innerHTML += `<div class="rec-empty">No hay recursos en esta sección.</div>`;
      recContainer.appendChild(section);
      return;
    }

    const grid = document.createElement('div');
    grid.className = 'rec-grid';

    arr.forEach(r => {
      const card = document.createElement('div');
      card.className = 'rec-card';
      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <span class="badge-cat">${r.categoria}</span>
          <small class="text-muted">${prettySize(r.size)}</small>
        </div>
        <h5>${iconFromMime(r.mime)} ${escapeHtml(r.titulo)}</h5>
        <p>${escapeHtml(r.descripcion || '')}</p>
        <div class="rec-actions">
          <a class="btn btn-sm btn-outline-primary" href="${r.url}" target="_blank" rel="noopener">Ver</a>
          <a class="btn btn-sm btn-primary" href="${r.download}">Descargar</a>
          <button class="btn btn-sm btn-outline-danger" data-id="${r.id}">Eliminar</button>
        </div>
      `;
      grid.appendChild(card);

      // eliminar
      card.querySelector('button.btn-outline-danger').addEventListener('click', async (e) => {
        const id = e.currentTarget.getAttribute('data-id');
        if (!confirm('¿Eliminar este recurso?')) return;
        await fetch(ENDPOINTS.destroy(id), {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        await loadResources();
      });
    });

    section.appendChild(grid);
    recContainer.appendChild(section);
  }

  // ====== BUSCAR / FILTRAR ======
  let searchDebounce;
  recSearch.addEventListener('input', () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(loadResources, 300);
  });
  recFilter.addEventListener('change', loadResources);

  // ====== SUBIDA DE ARCHIVOS ======
  const btnSubmit = recForm.querySelector('button[type="submit"]');
  const btnText = btnSubmit.innerHTML;

  function setLoading(on) {
    btnSubmit.disabled = on;
    btnSubmit.innerHTML = on
      ? '<span class="spinner-border spinner-border-sm me-2"></span> Subiendo...'
      : btnText;
  }

  recForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Construir FormData antes de deshabilitar inputs
    const fd = new FormData(recForm);
    setLoading(true);

    try {
      const res = await fetch(ENDPOINTS.store, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: fd
      });

      if (!res.ok) {
        const txt = await res.text();
        try {
          const j = JSON.parse(txt);
          console.error('UPLOAD 422', j);
          alert(Object.values(j.errors || { error: [j.message || 'Error al subir'] }).flat().join('\n'));
        } catch {
          console.error('UPLOAD ERROR', res.status, txt);
          alert('Error al subir el recurso');
        }
        return;
      }

      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();

      modalEl.addEventListener('hidden.bs.modal', () => {
        recForm.reset();
        setLoading(false);
        cleanupBackdrop();
        loadResources();
      }, { once: true });

    } catch (err) {
      console.error('UPLOAD EXCEPTION', err);
      alert('Ocurrió un error de red.');
    } finally {
      if (!document.body.classList.contains('modal-open')) {
        setLoading(false);
        cleanupBackdrop();
      }
    }
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    setLoading(false);
    cleanupBackdrop();
  });

  // ====== INICIO ======
  loadResources();
})();
</script>
@endpush
