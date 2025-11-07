@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4 px-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold" style="color:#2d572c;">Gestión de Semilleros</h3>
  </div>

  {{-- Botón abrir modal --}}
  <button type="button" class="btn btn-primary mb-3"
          data-bs-toggle="modal" data-bs-target="#modalNuevoSemillero">
    <i class="fa fa-plus me-1"></i> Nuevo semillero
  </button>

  @include('Admin.semilleros._modal_crear')

  {{-- Filtro simple --}}
  <div class="row g-2">
    <div class="col-md-10">
      <input name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por semillero, línea o líder">
    </div>
    <div class="col-md-2">
      <button class="btn btn-success w-100"><i class="bi bi-search"></i> Buscar</button>
    </div>
  </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead style="background-color:#2d572c;color:#fff;">
        <tr>
          <th>Semillero</th>
          <th>Línea de investigación</th>
          <th>Líder</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @forelse($semilleros as $s)
        <tr>
          <td>{{ $s->nombre }}</td>
          <td>{{ $s->linea_investigacion }}</td>
          <td>{{ $s->lider_nombre ? $s->lider_nombre.' ('.$s->lider_correo.')' : '—' }}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#modalEditarSemillero"
                    data-id="{{ $s->id_semillero }}">
              <i class="bi bi-pencil"></i> Editar
            </button>
            <form action="{{ route('admin.semilleros.destroy',$s->id_semillero) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('¿Eliminar este semillero?');">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i> Eliminar
              </button>

                      <a class="btn btn-outline-info btn-sm"
            href="{{ route('admin.semilleros.proyectos.index', $s->id_semillero) }}">
            <i class="bi bi-folder2-open"></i> Ver proyectos
          </a>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-center text-muted py-4">Sin registros</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $semilleros->links('pagination::bootstrap-5') }}
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="modalEditarSemillero" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Editar Semillero</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditarSemillero" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            {{-- Editables --}}
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" id="editNombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Línea de investigación</label>
              <input type="text" name="linea_investigacion" id="editLinea" class="form-control" required>
            </div>

            {{-- Solo lectura del líder actual --}}
            <div class="col-md-6">
              <label class="form-label">Líder actual</label>
              <input type="text" class="form-control" id="liderNombreRO" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo del líder</label>
              <input type="text" class="form-control" id="liderCorreoRO" readonly>
            </div>

            {{-- Buscador de líder disponible --}}
            <div class="col-12">
              <label class="form-label">Asignar nuevo líder (solo disponibles)</label>
              <input type="text" id="buscarLider" class="form-control" placeholder="Escribe nombre o correo...">
              <div id="resultadosLider" class="list-group mt-2" style="max-height:220px;overflow:auto;"></div>
              <input type="hidden" name="id_lider_semi" id="editIdLider">
              <small class="text-muted">Selecciona un líder de la lista para reemplazar al actual.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalEditarSemillero');
  const form  = document.getElementById('formEditarSemillero');

  const editNombre = document.getElementById('editNombre');
  const editLinea  = document.getElementById('editLinea');
  const liderRO    = document.getElementById('liderNombreRO');
  const correoRO   = document.getElementById('liderCorreoRO');
  const idLiderInp = document.getElementById('editIdLider');

  const buscarInp  = document.getElementById('buscarLider');
  const listaRes   = document.getElementById('resultadosLider');

  // Cargar datos en el modal
  modal.addEventListener('show.bs.modal', async (e) => {
    // 🔹 Cierra el de "Nuevo" si estuviera abierto (evita glitches)
    const mNuevo = document.getElementById('modalNuevoSemillero');
    const instNuevo = mNuevo ? bootstrap.Modal.getInstance(mNuevo) : null;
    instNuevo?.hide();

    const id = e.relatedTarget?.dataset.id;
    if (!id) return;

    // Reset buscador
    buscarInp.value = '';
    listaRes.innerHTML = '';

    const res  = await fetch(`{{ url('admin/semilleros') }}/${id}/edit`);
    const data = await res.json();

    form.action   = `{{ url('admin/semilleros') }}/${id}`;
    editNombre.value = data.nombre || '';
    editLinea.value  = data.linea_investigacion || '';
    liderRO.value    = data.lider_nombre || '—';
    correoRO.value   = data.lider_correo || '—';
    idLiderInp.value = data.id_lider_semi || '';
  });

  // Buscador con debounce
  let t;
  buscarInp.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(async () => {
      const q = buscarInp.value.trim();
      if (q.length < 2) { listaRes.innerHTML = ''; return; }

      const includeCurrent = idLiderInp.value ? `&include_current=${encodeURIComponent(idLiderInp.value)}` : '';
      const url = `{{ route('admin.semilleros.lideres-disponibles') }}?q=${encodeURIComponent(q)}${includeCurrent}`;

      const res = await fetch(url);
      const items = await res.json();

      listaRes.innerHTML = '';
      if (!Array.isArray(items)) return;

      items.forEach(r => {
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'list-group-item list-group-item-action';
        a.innerHTML = `<div class="fw-semibold">${r.nombre}</div><small class="text-muted">${r.correo || '—'}</small>`;
        a.addEventListener('click', (ev) => {
          ev.preventDefault();
          // Reemplaza relación de líder (solo en el form; se guarda al enviar)
          idLiderInp.value = r.id_lider_semi;
          liderRO.value    = r.nombre;
          correoRO.value   = r.correo || '—';
          listaRes.innerHTML = '';
          buscarInp.value = '';
        });
        listaRes.appendChild(a);
      });
    }, 250);
  });


  const cleanupBackdrop = () => {
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  };
  modal.addEventListener('hidden.bs.modal', cleanupBackdrop);
  const modalNuevo = document.getElementById('modalNuevoSemillero');
  modalNuevo?.addEventListener('hidden.bs.modal', cleanupBackdrop);
});
</script>
@endpush
@endsection
