{{-- resources/views/admin/recursos/index.blade.php --}} 
@extends('layouts.admin')

@push('styles')
<style>
  /* CABECERA: buscador + filtro + botón */
  .rec-head {
    display:flex;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:16px;
  }

  .rec-search {
    max-width:480px;
    flex:1 1 260px;
  }

  .rec-filter {
    max-width:220px;
    flex:0 0 auto;
  }

  .rec-upload-btn {
    flex:0 0 auto;
  }

  /* En móvil: todo en columna y ancho completo */
  @media (max-width: 768px) {
    .rec-head {
      flex-direction:column;
      align-items:stretch;
    }

    .rec-head > * {
      width:100%;
    }

    .rec-upload-btn {
      width:100%;
    }
  }

  .rec-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(320px,1fr));
    gap:18px;
  }

  .rec-card {
    background:#ffffff;
    border:2px solid #e0e4f0;
    border-radius:26px;
    padding:18px 20px 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
  }

  .rec-card h5 {
    margin:8px 0 10px 0;
    font-weight:700;
    font-size:1.05rem;
    display:flex;
    align-items:center;
    gap:6px;
  }

  .rec-card p {
    margin:0 0 12px 0;
    color:#444;
    font-size:0.95rem;
    min-height:32px;
  }

  .rec-actions {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .rec-section {
    margin-top: 24px;
  }

  .rec-section h4 {
    margin-bottom: 10px;
    font-weight: 700;
  }

  .badge-cat {
    font-size: .70rem;
    padding: .20rem .55rem;
    border-radius:999px;
    background:#f0f4ff;
    text-transform:lowercase;
    font-weight:600;
    color:#333;
  }

  .rec-empty {
    padding: 20px;
    border-radius: 20px;
    background: #f6f6f9;
    text-align:center;
    border:1px dashed #c8cce0;
  }

  /* Botones estilo captura */
  .btn-ver {
    border-radius:999px;
    border:2px solid #37aa4c;
    color:#37aa4c;
    background:#fff;
    font-weight:600;
    padding:4px 18px;
  }
  .btn-ver:hover {
    background:#e9f8ec;
    color:#2b8c3d;
  }

  .btn-descargar {
    border-radius:999px;
    background:#003c64;
    color:#fff;
    border:2px solid #003c64;
    font-weight:600;
    padding:4px 18px;
  }
  .btn-descargar:hover {
    background:#002b48;
    border-color:#002b48;
    color:#fff;
  }

  .btn-eliminar {
    border-radius:999px;
    border:2px solid #ff4a4a;
    color:#ff4a4a;
    background:#fff;
    font-weight:600;
    padding:4px 18px;
  }
  .btn-eliminar:hover {
    background:#ffecec;
    color:#e23232;
  }
</style>
@endpush

@section('module-title','Centro de Recursos')
@section('module-subtitle','Plantillas, manuales y otros documentos')

@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- CABECERA: ahora todo queda junto, sin ms-auto --}}
  <div class="rec-head">
    <div class="input-group rec-search">
      <span class="input-group-text">🔍</span>
      <input id="recSearch" type="search" class="form-control"
             placeholder="Buscar recurso (nombre o descripción)">
    </div>

    <select id="recFilter" class="form-select rec-filter">
      <option value="">Todas las categorías</option>
      <option value="plantillas">Plantillas</option>
      <option value="manuales">Manuales</option>
      <option value="otros">Otros</option>
    </select>

    <button class="btn btn-success rec-upload-btn"
            data-bs-toggle="modal"
            data-bs-target="#recUploadModal">
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
              <div class="col-md-4">
                <label class="form-label">Nombre del archivo *</label>
                <input type="text" name="nombre_archivo" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Categoría *</label>
                <select name="categoria" class="form-select" required>
                  <option value="plantillas">Plantillas</option>
                  <option value="manuales">Manuales</option>
                  <option value="otros">Otros</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Dirigido a *</label>
                <select name="dirigido_a" class="form-select" required>
                  <option value="todos">Aprendices y líderes</option>
                  <option value="aprendices">Solo aprendices</option>
                  <option value="lideres">Solo líderes</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Descripción (opcional)</label>
                <textarea name="descripcion" class="form-control" rows="2"
                          placeholder="Breve descripción del recurso"></textarea>
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

      const deleteBtnHtml = r.can_delete
        ? `<button class="btn btn-sm btn-eliminar btn-outline-danger" data-id="${r.id}">Eliminar</button>`
        : '';

      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <span class="badge-cat">${r.categoria}</span>
          <small class="text-muted">${prettySize(r.size)}</small>
        </div>
        <h5>${iconFromMime(r.mime)} ${escapeHtml(r.titulo)}</h5>
        <p>${escapeHtml(r.descripcion || '')}</p>
        <div class="rec-actions">
          <a class="btn btn-sm btn-ver btn-outline-success" href="${r.url}" target="_blank" rel="noopener">Ver</a>
          <a class="btn btn-sm btn-descargar btn-primary" href="${r.download}">Descargar</a>
          ${deleteBtnHtml}
        </div>
      `;
      grid.appendChild(card);

      if (deleteBtnHtml) {
        const deleteBtn = card.querySelector('button.btn-outline-danger');
        deleteBtn.addEventListener('click', async (e) => {
          const id = e.currentTarget.getAttribute('data-id');
          if (!confirm('¿Eliminar este recurso?')) return;
          await fetch(ENDPOINTS.destroy(id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
          });
          await loadResources();
        });
      }
    });

    section.appendChild(grid);
    recContainer.appendChild(section);
  }

  let searchDebounce;
  recSearch.addEventListener('input', () => {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(loadResources, 300);
  });
  recFilter.addEventListener('change', loadResources);

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
          alert(Object.values(j.errors || { error: [j.message || 'Error al subir'] }).flat().join('\n'));
        } catch {
          alert('Error al subir');
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
      alert('Error de red');
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

  loadResources();
})();
</script>
@endpush
