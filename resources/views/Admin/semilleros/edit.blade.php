{{-- Botón Editar en cada fila --}}
<button class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal" data-bs-target="#modalEditarSemillero"
        data-id="{{ $s->id_semillero }}">
  <i class="bi bi-pencil"></i> Editar
</button>

{{-- MODAL EDITAR (único en la página) --}}
<div class="modal fade"
     id="modalEditarSemillero"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="false"
     data-bs-keyboard="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Editar Semillero</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formEditarSemillero" method="POST">
        @csrf @method('PUT')

        <div class="modal-body">
          <div class="row g-3">
            {{-- Editables --}}
            <div class="col-md-6">
              <label class="form-label">Nombre del semillero</label>
              <input type="text" name="nombre" id="editNombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Línea de investigación</label>
              <input type="text" name="linea_investigacion" id="editLinea" class="form-control" required>
            </div>

            {{-- Solo lectura del líder actual --}}
            <div class="col-md-6">
              <label class="form-label">Líder actual</label>
              <input type="text" class="form-control" id="liderNombreRO" value="—" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo del líder</label>
              <input type="text" class="form-control" id="liderCorreoRO" value="—" readonly>
            </div>

            {{-- Buscador de líder disponible (reemplaza líder) --}}
            <div class="col-12">
              <label class="form-label">Buscar y asignar nuevo líder (solo líderes sin semillero)</label>
              <input type="text" id="buscarLider" class="form-control" placeholder="Escribe nombre o correo...">
              <div id="resultadosLider" class="list-group mt-2" style="max-height:220px;overflow:auto;"></div>
              <input type="hidden" name="id_lider_semi" id="editIdLider">
              <small class="text-muted">Al seleccionar uno, se asignará como nuevo líder del semillero.</small>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('modalEditarSemillero');
  const form    = document.getElementById('formEditarSemillero');

  const nombre  = document.getElementById('editNombre');
  const linea   = document.getElementById('editLinea');
  const lidNom  = document.getElementById('liderNombreRO');
  const lidCor  = document.getElementById('liderCorreoRO');
  const lidId   = document.getElementById('editIdLider');
  const buscar  = document.getElementById('buscarLider');
  const lista   = document.getElementById('resultadosLider');

  // Helper: elimina cualquier backdrop residual
  const killBackdrops = () => {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open','noscroll');
    document.body.style.removeProperty('padding-right');
  };

  // Fuerza instancia SIEMPRE sin backdrop
  const ensureNoBackdrop = () => {
    // crea o reutiliza instancia, obligando opciones
    const inst = bootstrap.Modal.getOrCreateInstance(modalEl, {
      backdrop: false,
      keyboard: true,
      focus: true
    });
    // por si otro modal ensució el DOM
    killBackdrops();
    return inst;
  };

  // Delegación: antes de abrir (click en botón editar), limpia overlays y fuerza opciones
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('[data-bs-target="#modalEditarSemillero"]');
    if (!btn) return;
    // apaga overlay del sidebar (móvil)
    document.getElementById('sidebarOverlay')?.classList.remove('show');
    document.body.classList.remove('noscroll');
    // mata cualquier backdrop previo
    killBackdrops();
    // asegura instancia sin backdrop
    ensureNoBackdrop();
  });

  // Cargar datos del semillero al abrir
  modalEl.addEventListener('show.bs.modal', async (e) => {
    ensureNoBackdrop(); // asegura backdrop:false incluso si Bootstrap re-instancia
    killBackdrops();    // limpia si algo alcanzó a inyectar
    const id = e.relatedTarget?.dataset.id;
    if (!id) return;

    try {
      const res  = await fetch(`{{ url('admin/semilleros') }}/${id}/edit`, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('No se pudo cargar el semillero');
      const data = await res.json();

      form.action  = `{{ url('admin/semilleros') }}/${id}`;
      nombre.value = data.nombre ?? '';
      linea.value  = data.linea_investigacion ?? '';
      lidNom.value = data.lider_nombre ?? '—';
      lidCor.value = data.lider_correo ?? '—';
      lidId.value  = data.id_lider_semi ?? '';
    } catch (err) {
      console.error(err);
    }
  });

  // Durante y después de abrir/cerrar: limpieza agresiva de backdrops
  modalEl.addEventListener('shown.bs.modal',  killBackdrops);
  modalEl.addEventListener('hide.bs.modal',   killBackdrops);
  modalEl.addEventListener('hidden.bs.modal', () => {
    killBackdrops();
    // limpia buscador
    buscar.value = '';
    lista.innerHTML = '';
  });

  // Buscador con debounce
  let t;
  buscar.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(async () => {
      const q = buscar.value.trim();
      if (q.length < 2) { lista.innerHTML = ''; return; }

      const includeCurrent = lidId.value ? `&include_current=${encodeURIComponent(lidId.value)}` : '';
      const url = `{{ route('admin.semilleros.lideres-disponibles') }}?q=${encodeURIComponent(q)}${includeCurrent}`;

      try {
        const res  = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Error cargando líderes');
        const rows = await res.json();

        lista.innerHTML = '';
        (rows || []).forEach(r => {
          const a = document.createElement('a');
          a.href = '#';
          a.className = 'list-group-item list-group-item-action';
          a.innerHTML = `<div class="fw-semibold">${r.nombre}</div><small class="text-muted">${r.correo ?? '—'}</small>`;
          a.addEventListener('click', (ev) => {
            ev.preventDefault();
            lidId.value  = r.id_lider_semi;
            lidNom.value = r.nombre;
            lidCor.value = r.correo ?? '—';
            lista.innerHTML = '';
            buscar.value = '';
          });
          lista.appendChild(a);
        });
      } catch (err) {
        console.error(err);
      }
    }, 250);
  });
});
</script>
@endpush
