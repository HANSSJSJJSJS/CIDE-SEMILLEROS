@extends('layouts.admin-lider')

@section('content')
<div class="container-fluid mt-4 px-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="fw-bold" style="color:#2d572c;">Gestión de Semilleros</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSemillero">
      <i class="bi bi-plus-circle me-1"></i> Nuevo Semillero
    </button>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Buscar</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="semillero, línea, líder">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Línea de investigación</label>
          <input type="text" name="linea" value="{{ request('linea') }}" class="form-control" placeholder="Ej: IA, IoT…">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Líder</label>
          <input type="text" name="lider" value="{{ request('lider') }}" class="form-control" placeholder="Nombre del líder">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button class="btn btn-outline-secondary w-100">Filtrar</button>
        </div>
      </div>
    </div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead style="background-color:#2d572c;color:#fff;">
          <tr>
            <th class="py-3 px-4">SEMILLERO</th>
            <th class="py-3">LÍNEA DE INVESTIGACIÓN</th>
            <th class="py-3">LÍDER SEMILLERO</th>
            <th class="py-3">CORREO ELECTRÓNICO</th>
            <th class="py-3">TELÉFONO</th>
            <th class="py-3 text-end pe-4">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($semilleros as $s)
            <tr>
              <td class="py-3 px-4">
                <div class="fw-semibold">{{ $s->semillero ?: '—' }}</div>
                <small class="text-muted">ID: {{ $s->id }}</small>
              </td>
              <td class="py-3">{{ $s->linea_investigacion ?: '—' }}</td>
              <td class="py-3">{{ $s->lider ?: '—' }}</td>
              <td class="py-3">{{ $s->correo ?: '—' }}</td>
              <td class="py-3">{{ $s->telefono ?: '—' }}</td>
              <td class="py-3 text-end pe-4">
                <a href="{{ route('admin.semilleros.edit',$s->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:4px 12px;">
                  <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('admin.semilleros.destroy',$s->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar este semillero?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" style="border-radius:20px;padding:4px 12px;">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-diagram-3 fs-1 opacity-50 d-block mb-2"></i>
                No hay semilleros que coincidan con tu búsqueda.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($semilleros instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-3">
      {{ $semilleros->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>

{{-- Modal crear --}}
<div class="modal fade" id="modalSemillero" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.semilleros.store') }}">
      @csrf
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Registrar Semillero</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nombre del semillero</label>
          <input name="nombre" class="form-control" value="{{ old('nombre') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Línea de investigación</label>
          <input name="linea_investigacion" class="form-control" value="{{ old('linea_investigacion') }}" required>
        </div>
        <div class="mb-1">
          <label class="form-label fw-semibold">Líder (opcional)</label>
          <select name="id_lider_usuario" class="form-select">
            <option value="">— Sin asignar —</option>
            @foreach($lideres as $l)
              <option value="{{ $l->id_usuario }}" @selected(old('id_lider_usuario')==$l->id_usuario)>
                {{ $l->nombre }} ({{ $l->correo_institucional }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success">Guardar</button>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const modalEl = document.getElementById('modalSemillero');
  const form = modalEl?.querySelector('form');
  const tbody = document.querySelector('table.table tbody');
  const BASE = '{{ url('admin/semilleros') }}';
  if(!form || !tbody) return;

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const url = this.action;
    const fd = new FormData(this);
    const headers = {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'};
    const meta = document.querySelector('meta[name="csrf-token"]');
    if(meta) headers['X-CSRF-TOKEN'] = meta.content;

    try{
      const res = await fetch(url, {method:'POST', headers, body: fd});
      if(!res.ok){ showToast('No se pudo crear el semillero','danger'); return; }
      const data = await res.json().catch(()=>null);
      if(!data || data.ok!==true){ showToast('Error al crear','danger'); return; }

      const it = data.item || {};
      // Agregar fila al inicio
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="py-3 px-4">
          <div class="fw-semibold">${escapeHtml(it.semillero||'')}</div>
          <small class="text-muted">ID: ${escapeHtml(String(it.id||''))}</small>
        </td>
        <td class="py-3">${escapeHtml(it.linea_investigacion||'')}</td>
        <td class="py-3">${escapeHtml(it.lider||'')}</td>
        <td class="py-3">${escapeHtml(it.correo||'')}</td>
        <td class="py-3">${escapeHtml(it.telefono||'')}</td>
        <td class="py-3 text-end pe-4">
          <a href="${BASE}/${it.id}/edit" class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:4px 12px;"><i class="bi bi-pencil"></i> Editar</a>
          <form action="${BASE}/${it.id}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este semillero?');">
            <input type="hidden" name="_token" value="${meta?meta.content:''}">
            <input type="hidden" name="_method" value="DELETE">
            <button class="btn btn-sm btn-outline-danger" style="border-radius:20px;padding:4px 12px;"><i class="bi bi-trash"></i> Eliminar</button>
          </form>
        </td>`;
      tbody.prepend(tr);

      // Cerrar modal, limpiar y toast
      bootstrap.Modal.getInstance(modalEl)?.hide();
      document.querySelectorAll('.modal-backdrop').forEach(b=>b.remove());
      form.reset();
      showToast('Semillero creado','success');
    }catch(err){
      showToast('Error de red','danger');
    }
  });

  function escapeHtml(s){ return String(s).replace(/[&<>"']/g, (c)=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[c])); }
  function showToast(msg, variant='success'){
    const wrap = document.getElementById('toastWrap') || (()=>{ const d=document.createElement('div'); d.id='toastWrap'; d.style.position='fixed'; d.style.top='20px'; d.style.right='20px'; d.style.zIndex='1080'; document.body.appendChild(d); return d; })();
    const el = document.createElement('div');
    el.className = `toast align-items-center text-bg-${variant} border-0 show`;
    el.role = 'alert';
    el.style.minWidth = '260px';
    el.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    wrap.appendChild(el);
    setTimeout(()=> el.remove(), 3000);
  }
});
</script>
@endpush
@endsection
