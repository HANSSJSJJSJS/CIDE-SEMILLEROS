@extends('layouts.lider_semi')

@section('content')
<div class="container-fluid">
  <h3 class="fw-bold mb-4">¡Bienvenido, {{ Auth::user()->name }}! 👋</h3>
  <p class="text-muted">Aquí está el resumen de tus semilleros y actividades recientes.</p>

  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-people-fill fs-3 text-success"></i>
        <h3 class="fw-bold mt-2">5</h3>
        <p class="text-muted">Semilleros Activos</p>
        <small class="text-success">+2 este mes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-person-badge fs-3 text-primary"></i>
        <h3 class="fw-bold mt-2">47</h3>
        <p class="text-muted">Aprendices Totales</p>
        <small class="text-primary">+8 este mes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-file-earmark-check fs-3 text-warning"></i>
        <h3 class="fw-bold mt-2">24</h3>
        <p class="text-muted">Documentos Revisados</p>
        <small class="text-danger">3 pendientes</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-metric text-center">
        <i class="bi bi-graph-up-arrow fs-3 text-purple"></i>
        <h3 class="fw-bold mt-2">87%</h3>
        <p class="text-muted">Progreso Promedio</p>
        <small class="text-success">+12%</small>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Actividad de Aprendices</h5>
        <div class="text-muted text-center" style="height:200px;">(Gráfico o tabla aquí)</div>
      </div>
      <div class="card p-4 mt-4">
        <h5 class="fw-bold mb-3">Actividad Reciente</h5>
        <div class="list-group">
          <div class="list-group-item border-0">
            <i class="bi bi-person-plus text-success me-2"></i>
            <strong>María González</strong> se unió al Semillero de IA
            <div class="text-muted small">Hace 5 minutos</div>
          </div>
          <div class="list-group-item border-0">
            <i class="bi bi-file-earmark-arrow-up text-primary me-2"></i>
            <strong>Carlos Pérez</strong> subió un documento <em>(Proyecto Final - Desarrollo Web.pdf)</em>
            <div class="text-muted small">Hace 1 hora</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card p-4">
        <h5 class="fw-bold mb-3">Acciones Rápidas</h5>
        <div class="d-grid gap-3">
          <button class="btn btn-success"><i class="bi bi-plus-circle me-2"></i> Crear Semillero</button>
          <button class="btn btn-dark"><i class="bi bi-person-plus me-2"></i> Agregar Aprendiz</button>
          <button class="btn btn-outline-success"><i class="bi bi-upload me-2"></i> Subir Documento</button>
          <button class="btn btn-outline-primary"><i class="bi bi-calendar-plus me-2"></i> Programar Reunión</button>
          <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalProyectosLider">
            <i class="bi bi-kanban me-2"></i> Ver Proyectos
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal: Proyectos del Líder -->
<div class="modal fade" id="modalProyectosLider" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="proj-modal-title">Proyectos que gestionas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Vista: listado -->
        <div id="proyectos-list" class="list-group small"></div>

        <!-- Vista: detalle -->
        <div id="proyecto-detalle" class="d-none">
          <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="btn-proj-back">
            <i class="bi bi-arrow-left"></i> Volver al listado
          </button>
          <div class="mb-2">
            <div class="fw-bold" id="proj-name"></div>
            <div class="text-muted small" id="proj-meta"></div>
          </div>
          <ul class="nav nav-tabs" id="projTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-resumen" data-bs-toggle="tab" data-bs-target="#pane-resumen" type="button" role="tab">Resumen</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-semilleros" data-bs-toggle="tab" data-bs-target="#pane-semilleros" type="button" role="tab">Semilleros</button>
            </li>
          </ul>
          <div class="tab-content border-start border-end border-bottom p-3">
            <div class="tab-pane fade show active" id="pane-resumen" role="tabpanel">
              <div id="proj-kpis" class="row g-3"></div>
            </div>
            <div class="tab-pane fade" id="pane-semilleros" role="tabpanel">
              <div class="d-flex gap-2 align-items-end mb-3 position-relative">
                <div class="flex-grow-1">
                  <label class="form-label small mb-1">Agregar aprendiz por nombre/correo (o ID)</label>
                  <input type="text" autocomplete="off" class="form-control form-control-sm" id="input-user-id" placeholder="Busca por nombre/correo o pega el ID">
                  <div id="cand-list" class="list-group position-absolute w-100 d-none" style="z-index:1056; max-height:220px; overflow:auto;"></div>
                </div>
                <button class="btn btn-sm btn-success" id="btn-add-part">Agregar</button>
              </div>
              <div id="lista-participantes" class="list-group small"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
  </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  const modalEl = document.getElementById('modalProyectosLider');
  if(!modalEl) return;
  const titleEl = document.getElementById('proj-modal-title');
  modalEl.addEventListener('show.bs.modal', async ()=>{
    const cont = document.getElementById('proyectos-list');
    const detalle = document.getElementById('proyecto-detalle');
    if(!cont) return;
    // reset vistas
    cont.classList.remove('d-none');
    detalle.classList.add('d-none');
    titleEl.textContent = 'Proyectos que gestionas';
    cont.innerHTML = '<div class="text-muted">Cargando proyectos...</div>';
    try{
      const r = await fetch("{{ route('lider_semi.proyectos.listado') }}", {credentials:'same-origin'});
      const data = await r.json();
      const items = data?.proyectos || [];
      if(items.length===0){ cont.innerHTML = '<div class="text-muted">No tienes proyectos asociados.</div>'; return; }
      cont.innerHTML = '';
      items.forEach(p=>{
        const li = document.createElement('a');
        li.className = 'list-group-item list-group-item-action';
        const estado = `<span class="badge bg-light text-dark ms-2">${p.estado||''}</span>`;
        const kpis = `<span class="ms-2 text-muted">Aprendices: ${p.aprendices||0} · Aprobados: ${p.docs_aprobados||0} · Pendientes: ${p.docs_pendientes||0}</span>`;
        li.innerHTML = `<div class="fw-bold">${p.nombre||'Proyecto'} ${estado}</div>
                        <div class="small text-muted">${p.fecha_inicio||''} — ${p.fecha_fin||''} ${kpis}</div>`;
        li.href = '#';
        li.addEventListener('click', (ev)=>{ ev.preventDefault(); abrirDetalleProyecto(p.id); });
        cont.appendChild(li);
      });
    }catch(e){
      cont.innerHTML = `<div class="text-danger">No se pudieron cargar los proyectos.</div>`;
      console.error(e);
    }
  });

  async function abrirDetalleProyecto(id){
    const cont = document.getElementById('proyectos-list');
    const detalle = document.getElementById('proyecto-detalle');
    const nameEl = document.getElementById('proj-name');
    const metaEl = document.getElementById('proj-meta');
    const kpisEl = document.getElementById('proj-kpis');
    const partsEl = document.getElementById('lista-participantes');
    const addBtn = document.getElementById('btn-add-part');
    const inputUser = document.getElementById('input-user-id');
    const candList = document.getElementById('cand-list');

    titleEl.textContent = 'Detalle de proyecto';
    cont.classList.add('d-none');
    detalle.classList.remove('d-none');
    nameEl.textContent = 'Cargando...';
    metaEl.textContent = '';
    kpisEl.innerHTML = '<div class="text-muted">Cargando KPIs...</div>';
    partsEl.innerHTML = '';
    inputUser.value = '';
    detalle.dataset.proyectoId = id;

    try{
      const [r1,r2] = await Promise.all([
        fetch(`{{ url('/lider_semillero/proyectos') }}/${id}`, {credentials:'same-origin'}),
        fetch(`{{ url('/lider_semillero/proyectos') }}/${id}/participantes`, {credentials:'same-origin'})
      ]);
      const d1 = await r1.json();
      const d2 = await r2.json();
      const p = d1?.proyecto || {};
      nameEl.textContent = p.nombre || 'Proyecto';
      metaEl.textContent = `${p.fecha_inicio||''} — ${p.fecha_fin||''} · Estado: ${p.estado||''}`;
      const k = d1?.kpi || {};
      kpisEl.innerHTML = `
        <div class="col-6 col-md-4"><div class="border rounded p-2 text-center"><div class="fw-bold">${k.aprendices||0}</div><div class="text-muted small">Aprendices</div></div></div>
        <div class="col-6 col-md-4"><div class="border rounded p-2 text-center"><div class="fw-bold">${k.docs_aprobados||0}</div><div class="text-muted small">Docs Aprobados</div></div></div>
        <div class="col-6 col-md-4"><div class="border rounded p-2 text-center"><div class="fw-bold">${k.docs_pendientes||0}</div><div class="text-muted small">Docs Pendientes</div></div></div>`;

      renderParticipantes(d2?.participantes || [], partsEl, id);

      addBtn.onclick = async ()=>{
        const uid = parseInt(inputUser.dataset.uid||inputUser.value,10);
        if(!uid){ inputUser.focus(); return; }
        addBtn.disabled = true;
        try{
          const r = await fetch(`{{ url('/lider_semillero/proyectos') }}/${id}/participantes`, {
            method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({user_id: uid})
          });
          const dj = await r.json();
          if(dj?.ok){
            await recargarParticipantes(id, partsEl);
            inputUser.value='';
          }
        }finally{
          addBtn.disabled = false;
        }
      };
      // Autocomplete
      const debounce = (fn, ms=250)=>{ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); } };
      const queryCands = debounce(async ()=>{
        const q = inputUser.value.trim();
        if(q.length < 2){ candList.classList.add('d-none'); candList.innerHTML=''; inputUser.dataset.uid=''; return; }
        candList.innerHTML = '<div class="list-group-item text-muted">Buscando...</div>';
        candList.classList.remove('d-none');
        try{
          const r = await fetch(`{{ url('/lider_semillero/proyectos') }}/${id}/candidatos?q=`+encodeURIComponent(q), {credentials:'same-origin'});
          const d = await r.json();
          const arr = d?.candidatos||[];
          if(arr.length===0){ candList.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>'; return; }
          candList.innerHTML = '';
          arr.forEach(c=>{
            const a = document.createElement('a');
            a.href='#'; a.className='list-group-item list-group-item-action';
            a.innerHTML = `<div class="fw-bold">${c.name||'Aprendiz'}</div><div class="small text-muted">${c.email||''} ${c.ficha? '· Ficha '+c.ficha: ''}</div>`;
            a.addEventListener('click', (ev)=>{ ev.preventDefault(); inputUser.value = `${c.name} (${c.email})`; inputUser.dataset.uid = c.id; candList.classList.add('d-none'); });
            candList.appendChild(a);
          });
        }catch(e){ candList.innerHTML = '<div class="list-group-item text-danger">Error al buscar</div>'; }
      }, 250);
      inputUser.addEventListener('input', ()=>{ inputUser.dataset.uid=''; queryCands(); });
      inputUser.addEventListener('focus', ()=>{ if(candList.innerHTML) candList.classList.remove('d-none'); });
      inputUser.addEventListener('blur', ()=>{ setTimeout(()=>candList.classList.add('d-none'), 150); });
    }catch(e){
      console.error(e);
    }
  }

  async function recargarParticipantes(id, partsEl){
    const r = await fetch(`{{ url('/lider_semillero/proyectos') }}/${id}/participantes`, {credentials:'same-origin'});
    const d = await r.json();
    renderParticipantes(d?.participantes||[], partsEl, id);
  }

  function renderParticipantes(list, partsEl, projectId){
    partsEl.innerHTML = '';
    if(!list.length){ partsEl.innerHTML = '<div class="text-muted">Sin aprendices asignados.</div>'; return; }
    list.forEach(u=>{
      const item = document.createElement('div');
      item.className = 'list-group-item d-flex justify-content-between align-items-center';
      item.innerHTML = `<div><div class="fw-bold">${u.name||'Aprendiz'}</div><div class="text-muted small">${u.email||''} ${u.ficha? '· Ficha '+u.ficha: ''}</div></div>`;
      const btn = document.createElement('button');
      btn.className = 'btn btn-sm btn-outline-danger';
      btn.textContent = 'Quitar';
      btn.addEventListener('click', async ()=>{
        btn.disabled = true;
        try{
          await fetch(`{{ url('/lider_semillero/proyectos') }}/${projectId}/participantes/${u.id}`, {method:'DELETE', credentials:'same-origin', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
          await recargarParticipantes(projectId, partsEl);
        }finally{ btn.disabled = false; }
      });
      const wrap = document.createElement('div');
      wrap.appendChild(btn);
      item.appendChild(wrap);
      partsEl.appendChild(item);
    });
  }

  document.getElementById('btn-proj-back')?.addEventListener('click', ()=>{
    document.getElementById('proyectos-list')?.classList.remove('d-none');
    document.getElementById('proyecto-detalle')?.classList.add('d-none');
    titleEl.textContent = 'Proyectos que gestionas';
  });
});
</script>
@endpush
@endsection

