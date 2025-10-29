<div class="modal fade" id="modalNuevoSemillero" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Semillero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.semilleros.store') }}" method="POST" id="form-semillero">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nombre del semillero</label>
              <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
              @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Línea de investigación</label>
              <input type="text" name="linea_investigacion" class="form-control" value="{{ old('linea_investigacion') }}" required>
              @error('linea_investigacion') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Líder de semillero (opcional)</label>
              <select name="id_lider_semi" id="select-lider-disponible" class="form-select">
                <option value="">— Sin asignar —</option>
                {{-- Se llenará por AJAX al abrir --}}
              </select>
              @error('id_lider_semi') <div class="text-danger small">{{ $message }}</div> @enderror
              <div class="form-text">Solo aparecen líderes sin semillero asignado.</div>
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

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Reabrir modal si hubo errores de validación
  @if ($errors->any())
    if (window.bootstrap?.Modal) {
      new bootstrap.Modal(document.getElementById('modalNuevoSemillero')).show();
    }
  @endif

  // Cargar líderes disponibles cuando se abre el modal
  const modalEl = document.getElementById('modalNuevoSemillero');
  if (!modalEl) return;

  modalEl.addEventListener('shown.bs.modal', async () => {
    const select = document.getElementById('select-lider-disponible');
    if (!select || select.dataset.loaded === '1') return;

    try {
      const url = "{{ route('admin.semilleros.lideres-disponibles') }}";
      const resp = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if (!resp.ok) throw new Error('Error cargando líderes');
      const data = await resp.json();

      // Limpia todo excepto la primera opción
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
      // deja el select con "Sin asignar"
    }
  });
});
</script>
@endpush
