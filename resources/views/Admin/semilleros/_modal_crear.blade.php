<div class="modal fade"
     id="modalNuevoSemillero"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="true"
     data-bs-keyboard="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <div>
          <h5 class="modal-title fw-semibold mb-0">Nuevo Semillero</h5>
          <small class="text-muted">Registra un nuevo semillero y asigna (opcional) un líder</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="{{ route('admin.semilleros.store') }}" method="POST" id="form-semillero">
        @csrf
        <div class="modal-body pt-3">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nombre del semillero <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
              <div class="form-text">Usa un nombre claro y fácil de identificar.</div>
              @error('nombre') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Línea de investigación <span class="text-danger">*</span></label>
              <input type="text" name="linea_investigacion" class="form-control" value="{{ old('linea_investigacion') }}" required>
              <div class="form-text">Ej: Seguridad Alimentaria, TIC, Energías renovables…</div>
              @error('linea_investigacion') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Líder de semillero <span class="text-muted">(opcional)</span></label>
              <select name="id_lider_semi" id="select-lider-disponible" class="form-select">
                <option value="">— Sin asignar —</option>
                {{-- Se llenará por AJAX al abrir --}}
              </select>
              <div class="form-text">Solo aparecen líderes sin semillero asignado.</div>
              @error('id_lider_semi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger mt-3">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-check2-circle me-1"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('styles')
<style>
  /* Backdrop específico para el modal de semilleros */
  .modal-backdrop.modal-blur {
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    background-color: rgba(15, 23, 42, 0.75); /* más oscuro para que se note */
    opacity: 1 !important; /* sobrescribe la opacidad por defecto de Bootstrap */
  }
</style>
@endpush

@push('scripts')
@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.bootstrap?.Modal) {
    new bootstrap.Modal(document.getElementById('modalNuevoSemillero'), {
      backdrop: 'static',
      keyboard: false
    }).show();
  }
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('modalNuevoSemillero');
  if (!modalEl) return;

  const cleanupBackdrop = () => {
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  };
  modalEl.addEventListener('hidden.bs.modal', cleanupBackdrop);

  modalEl.addEventListener('shown.bs.modal', async () => {
    // Aplicar blur al backdrop solo para este modal
    document.querySelectorAll('.modal-backdrop').forEach(b => b.classList.add('modal-blur'));
    const select = document.getElementById('select-lider-disponible');
    if (!select || select.dataset.loaded === '1') return;

    try {
      const url = "{{ route('admin.semilleros.lideres-disponibles') }}";
      const resp = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if (!resp.ok) throw new Error('Error cargando líderes');
      const data = await resp.json();

      [...select.querySelectorAll('option')].forEach((o, i) => { if (i>0) o.remove(); });
      data.forEach(item => {
        const op = document.createElement('option');
        op.value = item.id_lider_semi;
        op.textContent = `${item.nombre}${item.correo ? ' ('+item.correo+')' : ''}`;
        select.appendChild(op);
      });

      select.dataset.loaded = '1';
    } catch (e) {
      console.error(e);
    }
  });
});
</script>
@endpush
