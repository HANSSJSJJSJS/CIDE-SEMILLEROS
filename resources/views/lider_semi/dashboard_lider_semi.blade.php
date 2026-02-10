@extends('layouts.lider_semi')

@section('content')
<style>
  .dash-wrap{background:transparent;padding:1rem 0}
  .dash-title{font-weight:800;color:#0f172a}
  .metric-card{border:1px solid rgba(226,232,240,.55);border-radius:16px;padding:18px;background:rgba(255,255,255,.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);box-shadow:0 6px 20px rgba(15,23,42,.10);position:relative;transition:transform .12s ease,box-shadow .12s ease}
  .metric-card.metric-link{cursor:pointer}
  .metric-card.metric-link:hover{transform:translateY(-2px);box-shadow:0 10px 25px rgba(15,23,42,.12)}
  .metric-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:8px}
  .metric-kpi{font-size:28px;font-weight:800;color:#0f172a;margin:0}
  .metric-sub{color:#64748b;margin:0}
  .metric-pill{display:inline-block;margin-top:6px;font-size:12px;padding:4px 10px;border-radius:999px}
  .pill-green{background:#e6f6ed;color:#22c55e}
  .pill-yellow{background:#fff7e6;color:#f59e0b}
  .list-card{border:1px solid rgba(226,232,240,.55);border-radius:16px;background:rgba(255,255,255,.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);box-shadow:0 6px 20px rgba(15,23,42,.10)}
  .item{display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;background:rgba(255,255,255,.35);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(226,232,240,.50)}
  .item+.item{margin-top:10px}
  .avatar{width:36px;height:36px;border-radius:999px;background:#e2f7ee;color:#0f766e;font-weight:800;display:flex;align-items:center;justify-content:center}
  .status-pill{font-size:12px;padding:4px 10px;border-radius:999px;background:#ecfdf5;color:#16a34a}
  .bar{height:6px;background:#e2e8f0;border-radius:999px;overflow:hidden}
  .bar>span{display:block;height:100%;background:#22c55e}
  .actions-card{border:1px solid rgba(226,232,240,.55);border-radius:16px;background:rgba(255,255,255,.55);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);box-shadow:0 6px 20px rgba(15,23,42,.10)}
  .btn-block{display:block;width:100%;border-radius:12px;padding:12px 16px;font-weight:700}
  .btn-green{background:#22c55e;color:#fff}
  .btn-green:hover{filter:brightness(1.05)}
  .btn-dark{background:#0f172a;color:#fff}
  .btn-outline-blue{background:rgba(255,255,255,.35);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:2px solid #2563eb;color:#2563eb}
  .btn-outline-blue:hover{background:rgba(239,246,255,.55)}
</style>

<div class="container-fluid dash-wrap">
  <h3 class="dash-title mb-1">¡Bienvenido, {{ strtolower(Auth::user()->name) }}! 👋</h3>
  <p class="text-muted mb-4">Aquí está el resumen de tus semilleros y actividades recientes.</p>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <a href="{{ route('lider_semi.semilleros') }}" class="text-decoration-none text-reset">
      <div class="metric-card metric-link">
        <div class="metric-icon" style="background:#e6f6ed;color:#16a34a"><i class="bi bi-people-fill"></i></div>
        <p class="metric-kpi">{{ $proyectosActivos ?? 0 }}</p>
        <p class="metric-sub">Proyectos Activos</p>
        <span class="metric-pill pill-green">+2 este mes</span>
      </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('lider_semi.aprendices') }}" class="text-decoration-none text-reset">
      <div class="metric-card metric-link">
        <div class="metric-icon" style="background:#e0f2fe;color:#2563eb"><i class="bi bi-person-badge"></i></div>
        <p class="metric-kpi">{{ $totalAprendices }}</p>
        <p class="metric-sub">Aprendices Totales</p>
        <span class="metric-pill" style="background:#e0f2fe;color:#2563eb">+8 este mes</span>
      </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('lider_semi.documentos') }}" class="text-decoration-none text-reset">
      <div class="metric-card metric-link">
        <div class="metric-icon" style="background:#fff7e6;color:#f59e0b"><i class="bi bi-file-earmark-check"></i></div>
        <p class="metric-kpi">{{ $documentosPendientes ?? 0 }}</p>
        <p class="metric-sub">Documentos Pendientes</p>
        <span class="metric-pill pill-yellow">{{ $documentosPendientes ?? 0 }} pendientes</span>
      </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="{{ route('lider_semi.documentos') }}" class="text-decoration-none text-reset">
      <div class="metric-card metric-link">
        <div class="metric-icon" style="background:#e6f6ed;color:#22c55e"><i class="bi bi-graph-up-arrow"></i></div>
        <p class="metric-kpi">{{ $documentosRevisados ?? 0 }}</p>
        <p class="metric-sub">Documentos Revisados</p>
        <span class="metric-pill pill-green">+12%</span>
      </div>
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="list-card p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="fw-bold mb-0">Actividad de Aprendices</h5>
          <a href="{{ route('lider_semi.documentos') }}" class="small text-primary">Ver todos →</a>
        </div>
        @php
          $items = $actividadReciente ?? [];
        @endphp
        @forelse($items as $act)
          @php
            $titulo = $act['titulo'] ?? 'Actividad';
            preg_match('/^([A-Za-zÁÉÍÓÚÜÑ][^\s]*)\s+([A-Za-zÁÉÍÓÚÜÑ])?/u', $titulo, $m);
            $ini = strtoupper((($m[1] ?? 'A')[0] ?? 'A') . ($m[2] ?? ''));
            $status = $act['tipo'] === 'documento' ? 'Pendiente' : 'Activo';
            $statusClass = $status === 'Pendiente' ? 'pill-yellow' : 'pill-green';
            $pct = $status === 'Pendiente' ? 35 : 78;
          @endphp
          <div class="item">
            <div class="avatar">{{ $ini }}</div>
            <div class="flex-fill">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">{{ $titulo }}</div>
                <span class="status-pill {{ $statusClass }}">{{ $status }}</span>
              </div>
              <div class="text-muted small">{{ $act['descripcion'] ?? '' }}</div>
              <div class="d-flex align-items-center gap-2 mt-2">
                <div class="bar flex-fill"><span style="width: {{ $pct }}%"></span></div>
                <small class="text-muted">{{ $act['tiempo'] ?? '' }}</small>
              </div>
            </div>
          </div>
        @empty
          <div class="text-muted p-3">No hay actividad reciente.</div>
        @endforelse
      </div>
    </div>

    <div class="col-lg-4">
      <div class="actions-card p-4">
        <h5 class="fw-bold mb-3">Acciones Rápidas</h5>
        <div class="d-grid gap-3">
          <a href="{{ route('lider_semi.documentos') }}" class="btn-block btn-green"><i class="bi bi-plus-circle me-2"></i> Crear Evidencia</a>
          <a href="{{ route('lider_semi.semilleros') }}" class="btn-block btn-dark"><i class="bi bi-person-plus me-2"></i> Asignar Aprendiz</a>
          <a href="{{ route('lider_semi.calendario') }}" class="btn-block btn-outline-blue"><i class="bi bi-calendar-plus me-2"></i> Programar Reunión</a>
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
