@extends('layouts.aprendiz')

@section('title','Panel del Aprendiz')
@section('module-title','')
@section('module-subtitle','')


@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/aprendiz/dashboard.css') }}">
@endpush

@section('content')


    <div class="dashboard-hero mb-4">
        <div class="p-4 p-md-5">
            <h2 class="fw-bold mb-1">Bienvenido(a), {{ Auth::user()->name }}</h2>
            <p class="text-muted mb-4">Gestiona tus proyectos y documentos desde tu panel de aprendiz</p>

            <div class="row metrics-grid gx-4">
                <div class="col-md-4 mb-3">
                    <div class="card-metric">
                        <div class="metric-title mb-1">Proyectos Asignados</div>
                        <div id="proyectosCount" class="metric-value">{{ $proyectosCount ?? 0 }}</div>
                        <div class="metric-aux mt-2">
                            <a href="{{ route('aprendiz.proyectos.index') }}" class="text-decoration-none">Ver tus Proyectos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card-metric">
                        <div class="metric-title mb-1">Documentos Pendientes</div>
                        <div id="documentosPendientes" class="metric-value">{{ $documentosPendientes ?? 0 }}</div>
                        <div class="metric-aux mt-2">
                            <a href="{{ route('aprendiz.archivos.index') }}" class="text-decoration-none">Subir Documentos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card-metric">
                        <div class="metric-title mb-1">Documentos Completados</div>
                        <div id="documentosCompletados" class="metric-value">{{ $documentosCompletados ?? 0 }}</div>
                        <div class="metric-aux mt-2">Últimos 30 Días</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


                <!-- Steps -->
                <div class="steps-section">
                    <h5 class="mb-3">Próximos Pasos</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="step-chip chip-green">1</div>
                                <div>
                                    <div class="fw-bold">Revisa tus Proyectos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Consulta los proyectos asignados y sus requisitos</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="step-chip chip-amber">2</div>
                                <div>
                                    <div class="fw-bold">Prepara Documentos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Reúne los documentos PDF requeridos para cada proyecto</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="step-chip chip-blue">3</div>
                                <div>
                                    <div class="fw-bold">Sube Documentos</div>
                                    <div class="text-muted" style="font-size:0.95rem;">Carga los archivos en la sección de subida de documentos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /steps -->

                <!-- Próximas reuniones -->
                <div class="mt-4 meetings-section">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">Próximas reuniones</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-realizadas">Mostrar realizadas (0)</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-reset-realizadas">Restablecer realizadas</button>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('aprendiz.calendario.index') }}">
                                Ver calendario
                            </a>
                        </div>
                    </div>
                    <!-- Notificación silenciosa -->
                    <div id="prox-notice" class="alert alert-info py-2 px-3 d-none" role="alert" style="font-size:.95rem">
                        Tienes reuniones que comienzan pronto.
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body" id="proximas-list">
                            <div class="text-muted">Cargando...</div>
                        </div>
                    </div>
                </div>
                <!-- /Próximas reuniones -->

                <!-- Modal: Confirmar restablecer realizadas -->
                <div class="modal fade" id="modal-reset-realizadas" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Restablecer reuniones realizadas</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">
                                    Esto volverá a mostrar todas las reuniones que marcaste como realizadas en este dispositivo.
                                    <br>
                                    <strong id="txt-count-realizadas">Tienes 0 reuniones marcadas.</strong>
                                    <br>¿Deseas continuar?
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success" id="btn-confirm-reset-realizadas">Restablecer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de realizadas (colapsable) -->
                <div class="mt-2 d-none" id="proximas-done-wrap">
                    <div class="card border-0">
                        <div class="card-body" id="proximas-done-list"></div>
                    </div>
                </div>
@endsection

@push('scripts')
<script>
        function animateCounter(element, start, end, duration = 1000) {
            const range = end - start;
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const value = Math.floor(start + range * progress);
                element.textContent = value + (element.id === 'progresoPromedio' ? '%' : '');
                if (progress < 1) window.requestAnimationFrame(step);
            }

            window.requestAnimationFrame(step);
        }

        function autoMarkOld(){
            const items = window._proxItems||[];
            if (!items.length) return;
            const now = Date.now();
            const DAY = 24*60*60*1000;
            const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
            let set = [];
            try{ set = JSON.parse(doneRaw);}catch{ set = []}
            let changed = false;
            items.forEach(ev=>{
                if (!ev?.fecha_hora) return;
                const t = new Date(ev.fecha_hora).getTime();
                if ((now - t) > DAY && !set.includes(ev.id)){
                    set.push(ev.id);
                    changed = true;
                }
            });
            if (changed){ localStorage.setItem('aprendiz_reuniones_done', JSON.stringify(set)); }
        }

        async function actualizarContadores() {
            try {
                const response = await fetch("{{ route('aprendiz.dashboard.stats') }}");
                const data = await response.json();

                const counters = {
                    proyectosCount: data.proyectosCount,
                    documentosPendientes: data.documentosPendientes,
                    documentosCompletados: data.documentosCompletados,
                    progresoPromedio: data.progresoPromedio
                };

                for (const key in counters) {
                    const el = document.getElementById(key);
                    if (el) {
                        const current = parseInt(el.textContent) || 0;
                        const next = counters[key];
                        animateCounter(el, current, next);
                    }
                }
            } catch (e) {
                console.error('Error al actualizar los contadores:', e);
            }
        }

        // Actualiza cada 10 segundos
        setInterval(actualizarContadores, 10000);

        window._proxItems = [];

        async function cargarProximas(){
            const cont = document.getElementById('proximas-list');
            if (!cont) return;
            try{
                const r = await fetch("{{ route('aprendiz.dashboard.proximas') }}", { credentials: 'same-origin' });
                if (!r.ok){
                    const txt = await r.text();
                    cont.innerHTML = `<div class="text-danger">No se pudieron cargar las reuniones (HTTP ${r.status}).</div>`;
                    console.error('Dashboard proximas HTTP error:', r.status, txt?.slice(0,500));
                    return;
                }
                let data;
                const ct = r.headers.get('content-type')||'';
                if (ct.includes('application/json')){ data = await r.json(); }
                else {
                    const txt = await r.text();
                    console.warn('Dashboard proximas no-JSON response:', txt?.slice(0,500));
                    cont.innerHTML = '<div class="text-danger">No se pudieron cargar las reuniones.</div>';
                    return;
                }
                if (data && data.error){
                    cont.innerHTML = `<div class="text-danger">No se pudieron cargar las reuniones. Intenta recargar la página. Detalle: ${data.error}</div>`;
                    console.error('Dashboard proximas error:', data.error);
                    return;
                }
                const items = (data?.proximas)||[];
                window._proxItems = items;
                // Auto-marcar como realizadas si pasaron 24h
                autoMarkOld();
                // Notificación silenciosa < 30 min (especial <=5 min)
                actualizarNotificacionSilenciosa(items);
                const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
                let done = [];
                try{ done = JSON.parse(doneRaw);}catch{ done = []}
                if(items.length===0){
                    cont.innerHTML = '<div class="text-muted">No tienes reuniones próximas.</div>';
                    return;
                }
                cont.innerHTML = '';
                const visibles = items.filter(ev=> !done.includes(ev.id));
                const aRender = visibles.slice(0,5);
                if(aRender.length===0){
                    cont.innerHTML = '<div class="text-muted">No tienes reuniones próximas (todas marcadas como realizadas).</div>';
                    actualizarDoneUI(done);
                    return;
                }
                aRender.forEach(ev=>{
                    const row = document.createElement('div');
                    row.className = 'd-flex flex-wrap align-items-center justify-content-between border-bottom py-2';
                    const fecha = ev.fecha_hora ? new Date(ev.fecha_hora) : null;
                    const fmt = fecha ? fecha.toLocaleString('es-CO',{dateStyle:'medium', timeStyle:'short'}) : '';
                    const chip = construirChipEstado(fecha);
                    const urgente = esUrgente(fecha) ? 'style="color:#b91c1c"' : '';
                    const tip = construirTooltip(ev);
                    row.innerHTML = `
                        <div class="me-2">
                            <div class="fw-bold" ${tip}>${ev.titulo || 'Reunión'}${ev.proyecto? ' · '+ev.proyecto: ''} ${chip}</div>
                            <div class="text-muted" style="font-size:.9rem;" ${urgente}>${fmt}${ev.duracion? ' · '+ev.duracion+' min':''}</div>
                        </div>
                        <div class="d-flex gap-2">
                            ${ev.link_virtual? `<a class="btn btn-sm btn-success" href="${ev.link_virtual}" target="_blank" rel="noopener">Unirme</a>`: ''}
                            <button class="btn btn-sm btn-outline-secondary" onclick='descargarICS(${JSON.stringify(ev).replace(/"/g,"&quot;")})'>Descargar .ics</button>
                            <button class="btn btn-sm btn-outline-dark" data-ev="${ev.id}">Realizada</button>
                        </div>`;
                    cont.appendChild(row);
                });

                cont.querySelectorAll('button.btn-outline-dark[data-ev]').forEach(b=>{
                    b.addEventListener('click',()=>{
                        const id = JSON.parse(b.getAttribute('data-ev'));
                        const doneRaw2 = localStorage.getItem('aprendiz_reuniones_done')||'[]';
                        let set = [];
                        try{ set = JSON.parse(doneRaw2);}catch{ set = []}
                        if(!set.includes(id)) set.push(id);
                        localStorage.setItem('aprendiz_reuniones_done', JSON.stringify(set));
                        cargarProximas();
                    });
                });
                actualizarDoneUI(done);
                inicializarTooltips();
            }catch(e){
                cont.innerHTML = '<div class="text-danger">No se pudieron cargar las reuniones. Verifica tu conexión o recarga.</div>';
                console.error(e);
            }
        }

        function actualizarNotificacionSilenciosa(items){
            const el = document.getElementById('prox-notice');
            if (!el) return;
            const now = Date.now();
            let show = false;
            let urgent5 = false;
            (items||[]).forEach(ev=>{
                if (!ev?.fecha_hora) return;
                const t = new Date(ev.fecha_hora).getTime();
                const diff = (t - now) / 60000; // minutos hasta inicio
                if (diff >= 0 && diff <= 30) {
                    show = true;
                    if (diff <= 5) urgent5 = true;
                }
            });
            if (show){
                el.textContent = urgent5
                    ? 'Una reunión comienza en menos de 5 minutos.'
                    : 'Tienes reuniones que comienzan en menos de 30 minutos.';
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        }

        function construirChipEstado(dateObj){
            if (!dateObj) return '';
            const now = new Date();
            const sameDay = dateObj.toDateString() === now.toDateString();
            const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate()+1);
            const isTomorrow = dateObj.toDateString() === tomorrow.toDateString();
            const diffDays = Math.floor((dateObj - now)/(24*60*60*1000));
            if (sameDay) return '<span class="badge rounded-pill" style="background:#1d986f1a;color:#1d986f">Hoy</span>';
            if (isTomorrow) return '<span class="badge rounded-pill" style="background:#e6f2ff;color:#1a73e8">Mañana</span>';
            if (diffDays <= 7) return '<span class="badge rounded-pill" style="background:#fff7e6;color:#f08c00">Esta semana</span>';
            return '';
        }

        function esUrgente(dateObj){
            if (!dateObj) return false;
            const diffMin = (dateObj.getTime() - Date.now())/60000;
            return diffMin >= 0 && diffMin <= 60;
        }

        function construirTooltip(ev){
            const lider = ev.lider ? `Líder: ${ev.lider}` : '';
            const parts = Array.isArray(ev.participantes) ? ev.participantes.slice(0,2).join(', ') : '';
            const more = (Array.isArray(ev.participantes) && ev.participantes.length>2) ? ` +${ev.participantes.length-2}` : '';
            const body = [lider, parts? `Participantes: ${parts}${more}`:''].filter(Boolean).join(' • ');
            return body ? `data-bs-toggle="tooltip" data-bs-placement="top" title="${body.replace(/"/g,'&quot;')}"` : '';
        }

        function inicializarTooltips(){
            const triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            triggers.forEach(el=>{ new bootstrap.Tooltip(el); });
        }

        function actualizarDoneUI(doneIds){
            const btnToggle = document.getElementById('btn-toggle-realizadas');
            const wrap = document.getElementById('proximas-done-wrap');
            const list = document.getElementById('proximas-done-list');
            const noshowRaw = localStorage.getItem('aprendiz_reuniones_noasistido')||'[]';
            let noshow = [];
            try{ noshow = JSON.parse(noshowRaw);}catch{ noshow = [] }
            const count = Array.isArray(doneIds) ? doneIds.length : 0;
            if (btnToggle){ btnToggle.textContent = `Mostrar realizadas (${count})`; }
            if (!wrap || !list) return;
            list.innerHTML = '';
            if (count === 0 && noshow.length === 0){ wrap.classList.add('d-none'); return; }
            const map = new Map((window._proxItems||[]).map(it=>[it.id,it]));
            doneIds.forEach(id=>{
                const ev = map.get(id) || { id, titulo:'Reunión' };
                const row = document.createElement('div');
                row.className = 'd-flex flex-wrap align-items-center justify-content-between border-bottom py-2';
                row.innerHTML = `
                    <div class="me-2">
                        <div class="fw-bold">${ev.titulo || 'Reunión'}${ev.proyecto? ' · '+ev.proyecto: ''}</div>
                        <div class="text-muted" style="font-size:.9rem;">Marcada como realizada</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" data-unmark="${id}">Desmarcar</button>
                    </div>`;
                list.appendChild(row);
            });
            // Agregar no asistidos
            noshow.forEach(id=>{
                const ev = map.get(id) || { id, titulo:'Reunión' };
                const row = document.createElement('div');
                row.className = 'd-flex flex-wrap align-items-center justify-content-between border-bottom py-2';
                row.innerHTML = `
                    <div class="me-2">
                        <div class="fw-bold">${ev.titulo || 'Reunión'}${ev.proyecto? ' · '+ev.proyecto: ''} <span class="badge rounded-pill text-bg-secondary">No asistido</span></div>
                        <div class="text-muted" style="font-size:.9rem;">Evento pasado sin marcar asistencia</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success" data-mark-done="${id}">Marcar como realizada</button>
                    </div>`;
                list.appendChild(row);
            });
            // Listeners desmarcar
            list.querySelectorAll('button[data-unmark]').forEach(b=>{
                b.addEventListener('click',()=>{
                    const id = JSON.parse(b.getAttribute('data-unmark'));
                    const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
                    let set = [];
                    try{ set = JSON.parse(doneRaw);}catch{ set = []}
                    set = set.filter(x=> x!==id);
                    localStorage.setItem('aprendiz_reuniones_done', JSON.stringify(set));
                    cargarProximas();
                });
            });
            // Listener marcar como realizada desde no asistido
            list.querySelectorAll('button[data-mark-done]').forEach(b=>{
                b.addEventListener('click',()=>{
                    const id = JSON.parse(b.getAttribute('data-mark-done'));
                    const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
                    const nsRaw = localStorage.getItem('aprendiz_reuniones_noasistido')||'[]';
                    let set = []; let ns=[];
                    try{ set = JSON.parse(doneRaw);}catch{ set = []}
                    try{ ns = JSON.parse(nsRaw);}catch{ ns = []}
                    if(!set.includes(id)) set.push(id);
                    ns = ns.filter(x=> x!==id);
                    localStorage.setItem('aprendiz_reuniones_done', JSON.stringify(set));
                    localStorage.setItem('aprendiz_reuniones_noasistido', JSON.stringify(ns));
                    cargarProximas();
                });
            });
        }

        // Marca como "no asistido" todo evento pasado que no esté realizado
        function autoMarkNoShow(items){
            const now = Date.now();
            const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
            const nsRaw = localStorage.getItem('aprendiz_reuniones_noasistido')||'[]';
            let done = []; let ns=[];
            try{ done = JSON.parse(doneRaw);}catch{ done = []}
            try{ ns = JSON.parse(nsRaw);}catch{ ns = []}
            let changed=false;
            (items||[]).forEach(ev=>{
                if (!ev?.fecha_hora) return;
                const t = new Date(ev.fecha_hora).getTime();
                if (t < now && !done.includes(ev.id) && !ns.includes(ev.id)){
                    ns.push(ev.id); changed=true;
                }
            });
            if (changed){ localStorage.setItem('aprendiz_reuniones_noasistido', JSON.stringify(ns)); }
        }

        function descargarICS(ev){
            const dt = ev.fecha_hora ? new Date(ev.fecha_hora) : null;
            if(!dt){ return; }
            const pad = n=> String(n).padStart(2,'0');
            const y = dt.getFullYear();
            const m = pad(dt.getMonth()+1);
            const d = pad(dt.getDate());
            const hh = pad(dt.getHours());
            const mm = pad(dt.getMinutes());
            const ss = pad(dt.getSeconds());
            // Formato local naive (sin TZ) para evitar desfases visuales
            const dtStart = `${y}${m}${d}T${hh}${mm}${ss}`;
            const durMin = parseInt(ev.duracion||60);
            const end = new Date(dt.getTime()+durMin*60000);
            const y2=end.getFullYear(), m2=pad(end.getMonth()+1), d2=pad(end.getDate()), h2=pad(end.getHours()), mi2=pad(end.getMinutes()), s2=pad(end.getSeconds());
            const dtEnd = `${y2}${m2}${d2}T${h2}${mi2}${s2}`;
            const title = (ev.titulo||'Reunión').replace(/\n/g,' ');
            const desc = `Proyecto: ${ev.proyecto||''}`;
            const loc = ev.ubicacion||'';
            const url = ev.link_virtual||'';
            const ics = `BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//CIDE SENA//Aprendiz//ES\nBEGIN:VEVENT\nUID:${Date.now()}-${Math.random().toString(36).slice(2)}@cide\nDTSTART:${dtStart}\nDTEND:${dtEnd}\nSUMMARY:${title}\nDESCRIPTION:${desc}${url? ' - '+url:''}\nLOCATION:${loc}\nEND:VEVENT\nEND:VCALENDAR`;
            const blob = new Blob([ics],{type:'text/calendar;charset=utf-8'});
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `reunion-${y}${m}${d}-${hh}${mm}.ics`;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        // Cargar al iniciar
        actualizarContadores();
        cargarProximas();
        // Verificación periódica para auto-marcar pasadas de 24h
        setInterval(()=>{ autoMarkOld(); cargarProximas(); }, 30*60*1000); // cada 30 minutos

        // Reset de reuniones marcadas como realizadas (con confirmación)
        const btnReset = document.getElementById('btn-reset-realizadas');
        const modalEl = document.getElementById('modal-reset-realizadas');
        const btnConfirmReset = document.getElementById('btn-confirm-reset-realizadas');
        const txtCount = document.getElementById('txt-count-realizadas');
        const btnToggle = document.getElementById('btn-toggle-realizadas');
        let modalReset;
        if (modalEl){ modalReset = new bootstrap.Modal(modalEl); }
        if (btnReset && modalReset){
            btnReset.addEventListener('click', ()=>{
                const doneRaw = localStorage.getItem('aprendiz_reuniones_done')||'[]';
                let set = [];
                try{ set = JSON.parse(doneRaw);}catch{ set = []}
                if (txtCount){ txtCount.textContent = `Tienes ${set.length} reuniones marcadas.`; }
                modalReset.show();
            });
        }
        if (btnConfirmReset){
            btnConfirmReset.addEventListener('click', ()=>{
                localStorage.removeItem('aprendiz_reuniones_done');
                modalReset?.hide();
                cargarProximas();
            });
        }
        if (btnToggle){
            btnToggle.addEventListener('click', ()=>{
                const wrap = document.getElementById('proximas-done-wrap');
                wrap?.classList.toggle('d-none');
            });
        }
    </script>
@endpush
