@extends('layouts.aprendiz')

@section('title','Calendario')
@section('module-title','')
@section('module-subtitle','')

@push('styles')
@php $v = time(); @endphp
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario-views.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendar-month.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/lider_semi/calendario-lider.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/aprendiz/aprendiz-calendario.css') }}?v={{ $v }}">

<style>
  /* Fijar la fecha (currentPeriod) a la derecha y evitar que salte de línea */
  .cal-lider .header .header-controls{ display:flex; align-items:center; gap:12px; flex-wrap: nowrap; width:100%; position: relative; padding-right: 240px; }
  .cal-lider .header .header-controls .view-switcher{ flex:0 0 auto; min-width:0; }
  .cal-lider .header .header-controls .nav-controls{ flex:0 0 auto; min-width:0; }
  .cal-lider .header .header-controls .current-period{
    position: absolute; right: 0; top: 50%; transform: translateY(-50%);
    white-space: nowrap; font-weight: 800; color:#0b3357; text-align: right; min-width: 220px;
  }
  /* Mantener controles visibles (opcional sticky si hay scroll) */
  .cal-lider .header{ position: static; top: auto; z-index: auto; background: transparent; }
  /* Badge verde en los días del mes con reuniones asignadas */
  .cal-lider #calendar .fc-daygrid-day-frame{ position: relative; }
  .cal-lider #calendar .scml-day-badge{
    position:absolute; left:6px; top:6px; z-index:2; min-width:26px; height:26px;
    padding: 2px 6px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;
    background: linear-gradient(135deg, #1bb152, #178a3f);
    color:#0b3357; font-weight:800; box-shadow: 0 6px 14px rgba(0,0,0,.12), inset 0 1px 0 rgba(255,255,255,.25);
    font-size:.85rem;
  }
</style>

@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const monthView = document.getElementById('monthView');
  const weekView = document.getElementById('weekView');
  const dayView = document.getElementById('dayView');
  const buttons = document.querySelectorAll('.view-switcher .view-btn');

  // Genera grilla simple del mes actual (solo diseño)
  function renderMonthStatic(){
    const grid = document.getElementById('daysGrid');
    if(!grid) return;
    grid.innerHTML = '';
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const first = new Date(year, month, 1);
    const last = new Date(year, month+1, 0);
    const prevLast = new Date(year, month, 0);
    const startWeekIndex = first.getDay(); // 0=Dom
    // Días previos para completar la primera fila
    for(let i=startWeekIndex; i>0; i--){
      const d = prevLast.getDate()-i+1;
      const cell = document.createElement('div');
      cell.className = 'day-cell other-month';
      cell.innerHTML = `<div class="day-number">${d}</div>`;
      grid.appendChild(cell);
    }
    // Días del mes
    for(let d=1; d<=last.getDate(); d++){
      const date = new Date(year, month, d);
      const cell = document.createElement('div');
      cell.className = 'day-cell';
      if (date.toDateString() === today.toDateString()) cell.classList.add('today');
      cell.innerHTML = `<div class="day-number">${d}</div>`;
      grid.appendChild(cell);
    }
    // Días del siguiente mes para completar 6 filas (42 celdas)
    const total = grid.children.length;
    for(let d=1; total + d <= 42; d++){
      const cell = document.createElement('div');
      cell.className = 'day-cell other-month';
      cell.innerHTML = `<div class="day-number">${d}</div>`;
      grid.appendChild(cell);
    }
  }

  // Genera slots del día (08-17) incluyendo marca de almuerzo
  function renderDayStatic(){
    const slotsCol = document.querySelector('#dayGrid .day-slots');
    if(!slotsCol) return;
    slotsCol.innerHTML = '';
    const hours = [8,9,10,11,12,13,14,15,16,17];
    hours.forEach(h=>{
      const slot = document.createElement('div');
      slot.className = 'day-time-slot';
      if (h===12 || h===13) slot.classList.add('lunch');
      slotsCol.appendChild(slot)
    });
  }

  // Mantener ocultas las vistas estáticas; Mes lo maneja FullCalendar
  function show(view){ /* no-op */ }

  buttons.forEach(btn => {
    btn.addEventListener('click', function(e){
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }, true); // capture primero
  });

  // Inicialmente: no mostrar monthView estático (evita duplicado)
  try { if (monthView) monthView.style.display = 'none'; } catch {}
});
</script>
@endpush

@section('content')
                <div class="cal-lider">

                <div class="header">
                    <div class="header-top">
                        <div class="header-title">
                            <div class="title-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-week" viewBox="0 0 16 16">
                                    <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"></path>
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path>
                                </svg>
                            </div>
                            <div class="title-text">
                                <h1 class="title">Calendario de Reuniones</h1>
                                <div class="subtitle">Consulta tus eventos y reuniones</div>
                            </div>
                        </div>
                    </div>
                    <div class="header-controls">
                        <div class="view-switcher">
                            <button class="view-btn active" data-view="month">Mes</button>
                            <button class="view-btn" data-view="week">Semana</button>
                            <button class="view-btn" data-view="day">Día</button>
                        </div>
                        <div class="nav-controls">
                            <button class="nav-btn" id="prevBtn">‹ Anterior</button>
                            <button class="today-btn" id="todayBtn">Hoy</button>
                            <button class="nav-btn" id="nextBtn">Siguiente ›</button>
                        </div>
                        <div class="current-period" id="currentPeriod">&nbsp;</div>
                    </div>
                </div>

                <!-- Aviso de reuniones próximas -->
                <div id="prox-notice-cal" class="alert alert-info py-2 px-3 d-none" role="alert" style="font-size:.95rem">
                    Tienes reuniones virtuales próximas.
                </div>



                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Vista de Mes (estática) -->
                        <div class="calendar-month" id="monthView" style="display:none;">
                            <div class="weekdays">
                                <div class="weekday">Dom</div>
                                <div class="weekday">Lun</div>
                                <div class="weekday">Mar</div>
                                <div class="weekday">Mié</div>
                                <div class="weekday">Jue</div>
                                <div class="weekday">Vie</div>
                                <div class="weekday">Sáb</div>
                            </div>
                            <div class="days-grid" id="daysGrid"></div>
                        </div>

                        <div class="calendar-week" id="weekView" style="display:none;">
                            <div class="week-header" id="weekHeader">
                                <div class="week-time-label">Hora</div>
                                <div class="week-day-header"><div class="week-day-name">Dom</div><div class="week-day-number">14</div></div>
                                <div class="week-day-header"><div class="week-day-name">Lun</div><div class="week-day-number">15</div></div>
                                <div class="week-day-header"><div class="week-day-name">Mar</div><div class="week-day-number">16</div></div>
                                <div class="week-day-header today active"><div class="week-day-pill"><div class="wd-name">Mié</div><div class="wd-day">17</div></div></div>
                                <div class="week-day-header"><div class="week-day-name">Jue</div><div class="week-day-number">18</div></div>
                                <div class="week-day-header"><div class="week-day-name">Vie</div><div class="week-day-number">19</div></div>
                                <div class="week-day-header"><div class="week-day-name">Sáb</div><div class="week-day-number">20</div></div>

                            </div>
                            <div class="week-grid" id="weekGrid">
                                <div class="week-times">
                                    <div class="time-slot-label">08:00</div>
                                    <div class="time-slot-label">09:00</div>
                                    <div class="time-slot-label">10:00</div>
                                    <div class="time-slot-label">11:00</div>
                                    <div class="time-slot-label lunch">12:00</div>
                                    <div class="time-slot-label lunch">13:00</div>
                                    <div class="time-slot-label">14:00</div>
                                    <div class="time-slot-label">15:00</div>
                                    <div class="time-slot-label">16:00</div>
                                    <div class="time-slot-label">17:00</div>
                                </div>
                                <div class="week-day-column"></div>
                                <div class="week-day-column"></div>
                                <div class="week-day-column"></div>
                                <div class="week-day-column active"></div>
                                <div class="week-day-column"></div>
                                <div class="week-day-column"></div>
                                <div class="week-day-column"></div>

                            </div>
                        </div>

                        <!-- Vista de Día (estática) -->
                        <div class="calendar-day" id="dayView" style="display:none;">
                            <div class="day-header" id="dayHeader">
                                <div class="day-header-card">
                                    <div class="dh-week">Mié</div>
                                    <div class="dh-day">17</div>
                                </div>
                            </div>
                            <div class="day-grid" id="dayGrid">
                                <div class="day-times">
                                    <div class="day-time-label">08:00</div>
                                    <div class="day-time-label">09:00</div>
                                    <div class="day-time-label">10:00</div>
                                    <div class="day-time-label">11:00</div>
                                    <div class="day-time-label lunch">12:00</div>
                                    <div class="day-time-label lunch">13:00</div>
                                    <div class="day-time-label">14:00</div>
                                    <div class="day-time-label">15:00</div>
                                    <div class="day-time-label">16:00</div>
                                    <div class="day-time-label">17:00</div>
                                </div>
                                <div class="day-slots"></div>
                            </div>
                        </div>
                        <div id="apr-week-header" class="apr-week-header d-none"></div>
                        <div id='calendar'></div>
                    </div>
                </div>
                </div>
@endsection

<!-- Modal Detalle -->
<div class="modal fade" id="eventoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content"> 
      <div class="modal-header">
        <h5 class="modal-title" id="eventoTitulo">Detalle de la reunión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge rounded-pill text-bg-primary" id="badgeTipo" style="display:none"></span>
            <span class="badge rounded-pill text-bg-secondary" id="badgeUbic" style="display:none"></span>
        </div>
        <div class="mb-2"><strong>Proyecto:</strong> <span id="eventoProyecto">—</span></div>
        <div class="mb-2"><strong>Líder:</strong> <span id="eventoLider">—</span></div>
        <div class="mb-2"><strong>Fecha y hora:</strong> <span id="eventoFecha">—</span></div>
        <div class="mb-3"><strong>Participantes:</strong>
            <div id="eventoParticipantes" class="mt-1" style="display:flex;flex-wrap:wrap;gap:8px;">
                <span class="text-muted">—</span>
            </div>
        </div>
        <div class="mb-2" id="eventoEnlaceWrap" style="display:none;">
            <strong>Enlace:</strong>
            <a id="eventoEnlace" href="#" target="_blank" rel="noopener">Abrir reunión</a>
        </div>
        <div class="mb-2" id="eventoCodigoWrap" style="display:none;">
            <strong>Código:</strong> <span id="eventoCodigo">—</span>
        </div>
        <div class="mb-2"><strong>Descripción:</strong> <div id="eventoDesc" class="text-muted">—</div></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    @php
        $events = ($reuniones ?? collect())
            ->map(function($r){
                $tipo = $r->tipo ?? null;
                $colorMap = [
                    'planificacion' => '#2563eb',
                    'seguimiento'   => '#16a34a',
                    'revision'      => '#9333ea',
                    'capacitacion'  => '#fb923c',
                    'general'       => '#0ea5e9',
                ];
                $bg = $colorMap[strtolower((string)$tipo)] ?? '#0ea5e9';

                // Normalizar inicio y fin: si no hay fin, calcular con duracion (minutos)
                $start = (string)($r->fecha_inicio ?? $r->inicio ?? '');
                $end   = (string)($r->fecha_fin ?? $r->fin ?? '');
                if ($start && !$end) {
                    try {
                        $d = new \DateTime($start);
                        $mins = (int)($r->duracion ?? 60);
                        if ($mins <= 0) { $mins = 60; }
                        $d->modify("+{$mins} minutes");
                        $end = $d->format('Y-m-d\TH:i:s');
                    } catch (\Throwable $e) { $end = $start; }
                }

                return [
                    'id'    => $r->id ?? null,
                    'title' => ($r->titulo ?? 'Reunión') . (isset($r->proyecto) ? ' · ' . ($r->proyecto->nombre_proyecto ?? $r->proyecto->nombre ?? '') : ''),
                    'start' => $start,
                    'end'   => $end,
                    // Colores se forzarán por diseño en eventDidMount/CSS para unificar estilo
                    'extendedProps' => [
                        'lider'      => (is_object($r->lider ?? null)
                                            ? (($r->lider->user->name ?? $r->lider->nombre_completo ?? '') )
                                            : (string)($r->lider ?? ($r->lider_nombre ?? ''))),
                        'proyecto'   => isset($r->proyecto) ? ($r->proyecto->nombre_proyecto ?? $r->proyecto->nombre ?? '') : '',
                        'descripcion'=> (isset($r->descripcion) && $r->descripcion !== null && $r->descripcion !== '')
                            ? $r->descripcion
                            : (isset($r->proyecto) ? ($r->proyecto->descripcion ?? '') : ''),
                        'tipo'       => $tipo,
                        'ubicacion'  => $r->ubicacion ?? null,
                        'link'       => $r->link_virtual ?? null,
                        'codigo'     => $r->codigo_reunion ?? null,
                        'participantes' => is_array($r->participantes ?? null) ? $r->participantes : [],
                    ],
                ];
            })
            ->values();
    @endphp
    const reuniones = @json($events);

    document.addEventListener('DOMContentLoaded', function() {

        const calendarEl = document.getElementById('calendar');
        const monthView = document.getElementById('monthView');
        const weekView = document.getElementById('weekView');
        const dayView = document.getElementById('dayView');
        const calRoot = document.querySelector('.cal-lider');
        let calendar;
        try{
        let currentViewMode = 'month';
        let currentDate = new Date();

        function syncStickyTop(){
            try{
                if (!calRoot) return;
                const header = calRoot.querySelector('.header');
                if (!header) return;
                const h = Math.ceil(header.getBoundingClientRect().height || 0);
                if (h > 0) calRoot.style.setProperty('--apr-cal-sticky-top', `${h}px`);
            }catch{}
        }

        const pad2 = (n)=> String(n).padStart(2,'0');
        const ymdLocal = (d)=>{
            const x = new Date(d);
            const y = x.getFullYear();
            const m = pad2(x.getMonth()+1);
            const dd = pad2(x.getDate());
            return `${y}-${m}-${dd}`;
        };
        const isSameDay = (a,b)=>{
            try{
                return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
            }catch{ return false; }
        };
        const getWeekStart = (date)=>{
            const d = new Date(date);
            const day = d.getDay();
            d.setDate(d.getDate() - day);
            d.setHours(0,0,0,0);
            return d;
        };
        const getDayName = (date)=> ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'][date.getDay()];
        const toEventLite = (ev)=>{
            try{
                const start = ev?.start ? new Date(ev.start) : null;
                const end = ev?.end ? new Date(ev.end) : null;
                if (!start) return null;
                const date = ymdLocal(start);
                const time = `${pad2(start.getHours())}:${pad2(start.getMinutes())}`;
                let duration = 60;
                if (end && isFinite(end.getTime())) {
                    duration = Math.max(15, Math.round((end.getTime()-start.getTime())/60000));
                }
                return {
                    id: ev?.id ?? null,
                    title: ev?.title ?? 'Reunión',
                    date,
                    time,
                    duration,
                    start,
                    end,
                    extendedProps: ev?.extendedProps || {}
                };
            }catch{ return null; }
        };

        function showEventModalFromLite(lite){
            try{
                if (!lite) return;
                const modalEl = document.getElementById('eventoModal');
                document.getElementById('eventoTitulo').textContent = lite.title || 'Reunión';
                document.getElementById('eventoProyecto').textContent = lite.extendedProps?.proyecto || '—';
                document.getElementById('eventoLider').textContent = lite.extendedProps?.lider || '—';
                const fmt = (d)=> d ? d.toLocaleString('es-CO', { dateStyle:'medium', timeStyle:'short' }) : '';
                document.getElementById('eventoFecha').textContent = `${fmt(lite.start)}${lite.end?' - '+fmt(lite.end):''}`;
                document.getElementById('eventoDesc').textContent = lite.extendedProps?.descripcion || '—';

                const tipo = lite.extendedProps?.tipo || '';
                const ubic = lite.extendedProps?.ubicacion || '';
                const badgeTipo = document.getElementById('badgeTipo');
                const badgeUbic = document.getElementById('badgeUbic');
                if (tipo) { badgeTipo.style.display='inline-block'; badgeTipo.textContent = tipo.charAt(0).toUpperCase()+tipo.slice(1); } else { badgeTipo.style.display='none'; }
                if (ubic) { badgeUbic.style.display='inline-block'; badgeUbic.textContent = ubic.charAt(0).toUpperCase()+ubic.slice(1); } else { badgeUbic.style.display='none'; }

                const cont = document.getElementById('eventoParticipantes');
                cont.innerHTML = '';
                const part = lite.extendedProps?.participantes || [];
                if (part.length === 0) {
                    cont.innerHTML = '<span class="text-muted">—</span>';
                } else {
                    part.forEach(n=>{
                        const chip = document.createElement('span');
                        chip.className = 'badge rounded-pill text-bg-light border';
                        chip.textContent = n;
                        cont.appendChild(chip);
                    });
                }

                let enlace = lite.extendedProps?.link || '';
                const ubicRaw = lite.extendedProps?.ubicacion || '';
                const descRaw = lite.extendedProps?.descripcion || '';
                const urlRegex = /(https?:\/\/[^\s]+)/i;
                if (!enlace) {
                    const m1 = (ubicRaw||'').match(urlRegex);
                    if (m1) enlace = m1[0];
                }
                if (!enlace) {
                    const m2 = (descRaw||'').match(urlRegex);
                    if (m2) enlace = m2[0];
                }
                const codigo = lite.extendedProps?.codigo || '';
                const wrapLink = document.getElementById('eventoEnlaceWrap');
                const aLink = document.getElementById('eventoEnlace');
                const wrapCod = document.getElementById('eventoCodigoWrap');
                const spanCod = document.getElementById('eventoCodigo');
                if (enlace) {
                    wrapLink.style.display='block';
                    aLink.href = enlace;
                    aLink.target = '_blank';
                    aLink.rel = 'noopener noreferrer';
                    let label = '';
                    try {
                        const u = new URL(enlace);
                        const host = u.hostname.toLowerCase();
                        if (host.includes('teams.microsoft')) label = 'Microsoft Teams';
                        else if (host.includes('meet.google')) label = 'Google Meet';
                        else if (host.includes('zoom.us')) label = 'Zoom';
                        else label = host;
                    } catch(e) {
                        label = lite.title || 'Reunión';
                    }
                    aLink.textContent = label || lite.title || 'Reunión';
                } else {
                    wrapLink.style.display='block';
                    aLink.removeAttribute('href');
                    aLink.removeAttribute('target');
                    aLink.textContent = 'Sin enlace disponible';
                }
                if (codigo) { wrapCod.style.display='block'; spanCod.textContent = codigo; } else { wrapCod.style.display='none'; spanCod.textContent='—'; }

                const modal = new bootstrap.Modal(modalEl, { backdrop: false, keyboard: true });
                modal.show();
            }catch{}
        }

        function renderWeekView(){
            const weekHeader = document.getElementById('weekHeader');
            const weekGrid = document.getElementById('weekGrid');
            if (!weekHeader || !weekGrid) return;
            const weekStart = getWeekStart(currentDate);
            const weekDays = Array.from({length:7},(_,i)=>{ const d=new Date(weekStart); d.setDate(weekStart.getDate()+i); return d; });

            weekHeader.innerHTML = '<div class="week-time-label">Hora</div>';
            const activeDateStr = ymdLocal(currentDate);
            weekDays.forEach(date=>{
                const header=document.createElement('div');
                header.className='week-day-header';
                const dateStr = ymdLocal(date);
                if (isSameDay(date, new Date())) header.classList.add('today');
                if (dateStr === activeDateStr) header.classList.add('active');
                header.innerHTML = (dateStr === activeDateStr)
                  ? `<div class="week-day-pill"><div class="wd-name">${getDayName(date)}</div><div class="wd-day">${date.getDate()}</div></div>`
                  : `<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`;
                weekHeader.appendChild(header);
            });

            const workHours = [8,9,10,11,12,13,14,15,16];
            weekGrid.innerHTML = '';
            const timesCol=document.createElement('div');
            timesCol.className='week-times';
            workHours.forEach(hour=>{
                const tl=document.createElement('div');
                tl.className='time-slot-label';
                if (hour===12 || hour===13) tl.classList.add('lunch');
                tl.textContent = `${pad2(hour)}:00`;
                timesCol.appendChild(tl);
            });
            weekGrid.appendChild(timesCol);

            const lites = (reuniones||[]).map(toEventLite).filter(Boolean);
            weekDays.forEach(date=>{
                const dayCol=document.createElement('div');
                dayCol.className='week-day-column';
                const dateStr=ymdLocal(date);
                if (dateStr === activeDateStr) dayCol.classList.add('active');
                if (isSameDay(date, new Date())) dayCol.classList.add('today');
                workHours.forEach(hour=>{
                    const slot=document.createElement('div');
                    slot.className='week-time-slot';
                    if (hour===12 || hour===13) slot.classList.add('lunch');
                    dayCol.appendChild(slot);
                });

                lites.filter(e=>e.date===dateStr).forEach(ev=>{
                    const el=document.createElement('div');
                    el.className='week-event';
                    const h = ev.start.getHours();
                    const top = (h-8)*60 + 4;
                    const height = Math.max(20, ((ev.duration||60)/60*60) - 8);
                    el.style.top = `${top}px`;
                    el.style.height = `${height}px`;
                    el.innerHTML = `<div style="font-weight:600;">${ev.time}</div><div style="font-size:10px;">${ev.title}</div>`;
                    el.addEventListener('click', (e)=>{ e.stopPropagation(); showEventModalFromLite(ev); });
                    dayCol.appendChild(el);
                });
                weekGrid.appendChild(dayCol);
            });
        }

        function renderDayView(){
            const dayHeader = document.getElementById('dayHeader');
            const dayGrid = document.getElementById('dayGrid');
            if (!dayHeader || !dayGrid) return;
            dayHeader.innerHTML = `<div class="day-header-card"><div class="dh-week">${getDayName(currentDate)}</div><div class="dh-day">${currentDate.getDate()}</div></div>`;
            const dateStr = ymdLocal(currentDate);
            const workHours = [8,9,10,11,12,13,14,15,16];

            dayGrid.innerHTML = '';
            const timesCol=document.createElement('div');
            timesCol.className='day-times';
            workHours.forEach(hour=>{
                const tl=document.createElement('div');
                tl.className='day-time-label';
                if (hour===12 || hour===13) tl.classList.add('lunch');
                tl.textContent = `${pad2(hour)}:00`;
                timesCol.appendChild(tl);
            });
            dayGrid.appendChild(timesCol);

            const slotsCol=document.createElement('div');
            slotsCol.className='day-slots';
            workHours.forEach(hour=>{
                const slot=document.createElement('div');
                slot.className='day-time-slot';
                if (hour===12 || hour===13) slot.classList.add('lunch');
                slotsCol.appendChild(slot);
            });

            const lites = (reuniones||[]).map(toEventLite).filter(Boolean);
            lites.filter(e=>e.date===dateStr).forEach(ev=>{
                const el=document.createElement('div');
                el.className='day-event';
                const h = ev.start.getHours();
                const top = (h-8)*80 + 4;
                const height = Math.max(24, ((ev.duration||60)/60*80) - 8);
                el.style.top = `${top}px`;
                el.style.height = `${height}px`;
                el.innerHTML = `<div style="font-weight:600;font-size:13px;">${ev.time}</div><div style="font-size:12px;margin-top:2px;">${ev.title}</div>`;
                el.addEventListener('click', (e)=>{ e.stopPropagation(); showEventModalFromLite(ev); });
                slotsCol.appendChild(el);
            });
            dayGrid.appendChild(slotsCol);
        }

        function setViewMode(mode){
            currentViewMode = mode;
            const calWrap = document.getElementById('calendar');
            if (mode === 'month') {
                monthView && (monthView.style.display = 'none');
                weekView && (weekView.style.display = 'none');
                dayView && (dayView.style.display = 'none');
                if (calWrap) calWrap.style.display = 'block';
                document.getElementById('apr-week-header')?.classList.add('d-none');
                calendar.changeView('dayGridMonth');
                updatePeriod(calendar.getDate());
                syncStickyTop();
            } else if (mode === 'week') {
                if (calWrap) calWrap.style.display = 'none';
                monthView && (monthView.style.display = 'none');
                weekView && (weekView.style.display = 'block');
                dayView && (dayView.style.display = 'none');
                document.getElementById('apr-week-header')?.classList.add('d-none');
                currentDate = calendar.getDate();
                updatePeriod(currentDate);
                renderWeekView();
                syncStickyTop();
            } else {
                if (calWrap) calWrap.style.display = 'none';
                monthView && (monthView.style.display = 'none');
                weekView && (weekView.style.display = 'none');
                dayView && (dayView.style.display = 'block');
                document.getElementById('apr-week-header')?.classList.add('d-none');
                currentDate = calendar.getDate();
                updatePeriod(currentDate);
                renderDayView();
                syncStickyTop();
            }
        }

        const _cal = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            locale: 'es',
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Agenda', prev: 'Anterior', next: 'Siguiente' },
            height: 'auto',
            expandRows: true,
            editable: false,
            selectable: false,
            eventStartEditable: false,
            eventDurationEditable: false,
            dayMaxEvents: true,
            dayMaxEventRows: true,
            eventOverlap: true,
            eventDisplay: 'block',
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
            eventDidMount: function(info){
                try{
                    info.el.classList.add('scml-pill');
                }catch{}
            },
            // Horario laboral como en Líder
            businessHours: [
                { daysOfWeek: [1,2,3,4,5], startTime: '08:00', endTime: '17:00' }
            ],
            slotMinTime: '08:00:00',
            slotMaxTime: '17:00:00',
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00',
            allDaySlot: false,
            nowIndicator: true,
            slotLaneDidMount: function(arg){
                try{
                    const h = arg?.date?.getHours?.();
                    if (h === 12 || h === 13) {
                        arg.el.classList.add('lunch');
                        if (!arg.el.querySelector('.scml-lunch-label')) {
                            const lab = document.createElement('div');
                            lab.className = 'scml-lunch-label';
                            lab.textContent = 'ALMUERZO';
                            arg.el.appendChild(lab);
                        }
                    }
                }catch{}
            },
            slotLabelDidMount: function(arg){
                try{
                    const h = arg?.date?.getHours?.();
                    if (h === 12 || h === 13) {
                        arg.el.classList.add('lunch');
                    }
                }catch{}
            },
            events: reuniones,
            eventContent: function(arg){
                try{
                    const wrap = document.createElement('div');
                    wrap.style.display = 'flex';
                    wrap.style.alignItems = 'center';
                    wrap.style.gap = '8px';

                    // Chip de hora (HH:MM o rango)
                    const hh = (d)=> d ? d.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit', hour12:false}) : '';
                    const start = arg.event.start;
                    const end = arg.event.end;
                    const timeChip = document.createElement('span');
                    timeChip.className = 'fc-event-time';
                    timeChip.textContent = end ? `${hh(start)}–${hh(end)}` : hh(start);
                    // estilos del chip (reforzados por CSS global, aquí por si hay override)
                    timeChip.style.background = 'rgba(255,255,255,.45)';
                    timeChip.style.color = '#fff';
                    timeChip.style.borderRadius = '10px';
                    timeChip.style.padding = '2px 8px';
                    timeChip.style.fontWeight = '800';
                    timeChip.style.fontSize = '.8rem';

                    // Título truncado
                    const title = document.createElement('div');
                    title.className = 'fc-event-title fc-sticky';
                    const t = String(arg.event.title || 'Reunión');
                    const MAX = 18;
                    title.textContent = t.length > MAX ? (t.slice(0, MAX) + '…') : t;
                    title.style.whiteSpace = 'nowrap';
                    title.style.overflow = 'hidden';
                    title.style.textOverflow = 'ellipsis';
                    title.style.fontWeight = '700';
                    title.style.color = '#0b3357';

                    wrap.appendChild(timeChip);
                    wrap.appendChild(title);
                    return { domNodes: [wrap] };
                }catch(e){ return {}; }
            },
            // Sombreado de almuerzo se maneja por CSS en timeGridWeek (12:00 y 13:00)
            eventClick: function(info) {
                const modalEl = document.getElementById('eventoModal');
                document.getElementById('eventoTitulo').textContent = info.event.title || 'Reunión';
                document.getElementById('eventoProyecto').textContent = info.event.extendedProps.proyecto || '—';
                document.getElementById('eventoLider').textContent = info.event.extendedProps.lider || '—';

                const inicio = info.event.start;
                const fin = info.event.end;
                const fmt = (d)=> d ? d.toLocaleString('es-CO', { dateStyle:'medium', timeStyle:'short' }) : '';
                document.getElementById('eventoFecha').textContent = `${fmt(inicio)}${fin?' - '+fmt(fin):''}`;
                document.getElementById('eventoDesc').textContent = info.event.extendedProps.descripcion || '—';

                // Badges
                const tipo = info.event.extendedProps.tipo || '';
                const ubic = info.event.extendedProps.ubicacion || '';
                const badgeTipo = document.getElementById('badgeTipo');
                const badgeUbic = document.getElementById('badgeUbic');
                if (tipo) { badgeTipo.style.display='inline-block'; badgeTipo.textContent = tipo.charAt(0).toUpperCase()+tipo.slice(1); } else { badgeTipo.style.display='none'; }
                if (ubic) { badgeUbic.style.display='inline-block'; badgeUbic.textContent = ubic.charAt(0).toUpperCase()+ubic.slice(1); } else { badgeUbic.style.display='none'; }

                // Participantes chips
                const cont = document.getElementById('eventoParticipantes');
                cont.innerHTML = '';
                const part = info.event.extendedProps.participantes || [];
                if (part.length === 0) {
                    cont.innerHTML = '<span class="text-muted">—</span>';
                } else {
                    part.forEach(n=>{
                        const chip = document.createElement('span');
                        chip.className = 'badge rounded-pill text-bg-light border';
                        chip.textContent = n;
                        cont.appendChild(chip);
                    });
                }

                // Enlace y código (detectar incluso si está en 'ubicacion' o dentro de la descripción)
                let enlace = info.event.extendedProps.link || '';
                const ubicRaw = info.event.extendedProps.ubicacion || '';
                const descRaw = info.event.extendedProps.descripcion || '';
                const urlRegex = /(https?:\/\/[^\s]+)/i;
                if (!enlace) {
                    const m1 = (ubicRaw||'').match(urlRegex);
                    if (m1) enlace = m1[0];
                }
                if (!enlace) {
                    const m2 = (descRaw||'').match(urlRegex);
                    if (m2) enlace = m2[0];
                }
                const codigo = info.event.extendedProps.codigo || '';
                const wrapLink = document.getElementById('eventoEnlaceWrap');
                const aLink = document.getElementById('eventoEnlace');
                const wrapCod = document.getElementById('eventoCodigoWrap');
                const spanCod = document.getElementById('eventoCodigo');
                if (enlace) {
                    wrapLink.style.display='block';
                    aLink.href = enlace;
                    aLink.target = '_blank';
                    aLink.rel = 'noopener noreferrer';
                    // Derivar etiqueta del enlace
                    let label = '';
                    try {
                        const u = new URL(enlace);
                        const host = u.hostname.toLowerCase();
                        if (host.includes('teams.microsoft')) label = 'Microsoft Teams';
                        else if (host.includes('meet.google')) label = 'Google Meet';
                        else if (host.includes('zoom.us')) label = 'Zoom';
                        else label = host;
                    } catch(e) {
                        label = info.event.title || 'Reunión';
                    }
                    aLink.textContent = label || info.event.title || 'Reunión';
                } else {
                    // Mostrar igualmente la sección con estado claro si no hay enlace
                    wrapLink.style.display='block';
                    aLink.removeAttribute('href');
                    aLink.removeAttribute('target');
                    aLink.textContent = 'Sin enlace disponible';
                }
                if (codigo) { wrapCod.style.display='block'; spanCod.textContent = codigo; } else { wrapCod.style.display='none'; spanCod.textContent='—'; }

                const modal = new bootstrap.Modal(modalEl, { backdrop: false, keyboard: true });
                modal.show();
            },
        });
        calendar = _cal;
        calendar.render();

        // Render inicial del periodo y del header de semana (por si datesSet no corre de inmediato)
        try { updatePeriod(calendar.getDate()); } catch {}
        try { renderAprWeekHeader({ view: calendar.view, start: calendar.view?.currentStart }); } catch {}

        // Fallbacks robustos para que el botón "Cerrar" funcione siempre
        (function(){
            const modalEl = document.getElementById('eventoModal');
            if (!modalEl || !window.bootstrap) return;
            const ensureModal = () => bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: false, keyboard: true });

            // Click en botones con data-bs-dismiss
            modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    try { ensureModal().hide(); } catch {}
                });
            });

            // Cerrar con ESC aunque falle el manejo interno
            modalEl.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { try { ensureModal().hide(); } catch {} }
            });

            // Cerrar haciendo click en el backdrop del propio modal
            modalEl.addEventListener('click', (e) => {
                if (e.target === modalEl) { try { ensureModal().hide(); } catch {} }
            });

            // Al cerrar, limpiar cualquier backdrop/estado colgado
            modalEl.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });
        })();

        // Enlazar botones del header SCML
        const viewButtons = document.querySelectorAll('.view-switcher .view-btn');
        const currentPeriodEl = document.getElementById('currentPeriod');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const todayBtn = document.getElementById('todayBtn');

        function updatePeriod(date) {
            try {
                const fmt = new Intl.DateTimeFormat('es-CO', { month: 'long', year: 'numeric' });
                currentPeriodEl.textContent = fmt.format(date).replace(/^./, c => c.toUpperCase());
            } catch (e) {
                currentPeriodEl.textContent = date.toLocaleDateString('es-CO', { month: 'long', year: 'numeric' });
            }
        }

        function renderAprWeekHeader(info){
            try{
                const hdr = document.getElementById('apr-week-header');
                if (!hdr) return;
                const isWeek = info.view?.type === 'timeGridWeek';
                hdr.classList.toggle('d-none', !isWeek);
                if (!isWeek) { hdr.innerHTML = ''; return; }
                const baseStart = info?.start || info?.view?.currentStart;
                if (!baseStart) { hdr.innerHTML = ''; return; }
                const start = new Date(baseStart.valueOf());
                const days = [];
                for (let i=0;i<7;i++){ const d = new Date(start); d.setDate(start.getDate()+i); days.push(d); }
                const dayNames = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
                const today = new Date(); today.setHours(0,0,0,0);
                let html = '<div class="week-header" id="weekHeader">';
                html += '<div class="week-time-label">Hora</div>';
                days.forEach(d=>{
                    const isToday = d.toDateString() === today.toDateString();
                    if (isToday){
                        html += `<div class="week-day-header today active"><div class="week-day-pill"><div class="wd-name">${dayNames[d.getDay()]}</div><div class="wd-day">${d.getDate()}</div></div></div>`;
                    } else {
                        html += `<div class="week-day-header"><div class="week-day-name">${dayNames[d.getDay()]}</div><div class="week-day-number">${d.getDate()}</div></div>`;
                    }
                });
                html += '</div>';
                hdr.innerHTML = html;
            }catch(e){
                try { console.error('renderAprWeekHeader error:', e, info); } catch {}
            }
        }

        calendar.on('datesSet', (info) => {
            try{
                const isTimeGrid = info?.view?.type === 'timeGridWeek' || info?.view?.type === 'timeGridDay';
                calendar.setOption('contentHeight', isTimeGrid ? 1400 : 'auto');
            }catch{}
            updatePeriod(info.start);
            renderAprWeekHeader(info);
        });

        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                viewButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const view = btn.getAttribute('data-view');
                try {
                    setViewMode(view || 'month');
                } catch {}
            });
        });

        prevBtn?.addEventListener('click', () => {
            if (currentViewMode === 'month') {
                calendar.prev();
                updatePeriod(calendar.getDate());
            } else if (currentViewMode === 'week') {
                currentDate.setDate(currentDate.getDate() - 7);
                updatePeriod(currentDate);
                renderWeekView();
            } else {
                currentDate.setDate(currentDate.getDate() - 1);
                updatePeriod(currentDate);
                renderDayView();
            }
        });
        nextBtn?.addEventListener('click', () => {
            if (currentViewMode === 'month') {
                calendar.next();
                updatePeriod(calendar.getDate());
            } else if (currentViewMode === 'week') {
                currentDate.setDate(currentDate.getDate() + 7);
                updatePeriod(currentDate);
                renderWeekView();
            } else {
                currentDate.setDate(currentDate.getDate() + 1);
                updatePeriod(currentDate);
                renderDayView();
            }
        });
        todayBtn?.addEventListener('click', () => {
            if (currentViewMode === 'month') {
                calendar.today();
                updatePeriod(calendar.getDate());
            } else {
                currentDate = new Date();
                updatePeriod(currentDate);
                if (currentViewMode === 'week') renderWeekView();
                else renderDayView();
            }
        });

        // Estado inicial
        try { setViewMode('month'); } catch {}
        try { syncStickyTop(); } catch {}
        try { window.addEventListener('resize', syncStickyTop, { passive: true }); } catch {}
        } catch(err){
            console.error('Error inicializando FullCalendar:', err);
            const warn = document.createElement('div');
            warn.className = 'alert alert-warning mt-2';
            warn.textContent = 'No se pudo cargar el calendario. Reintenta recargando la página.';
            calendarEl.replaceWith(warn);
            return;
        }

        // Notificación silenciosa de reuniones virtuales próximas (<30 min; urgente <=5 min)
        function actualizarNotificacionSilenciosaCal(){
            const el = document.getElementById('prox-notice-cal');
            if (!el) return;
            const now = Date.now();
            let show = false;
            let urgent5 = false;
            (reuniones||[]).forEach(ev=>{
                try{
                    const hasLink = !!(ev?.extendedProps?.link);
                    if (!hasLink) return;
                    const t = ev?.start ? new Date(ev.start).getTime() : NaN;
                    if (!isFinite(t)) return;
                    const diff = (t - now) / 60000; // minutos hasta inicio
                    if (diff >= 0 && diff <= 30) {
                        show = true;
                        if (diff <= 5) urgent5 = true;
                    }
                }catch{}
            });
            if (show){
                el.textContent = urgent5
                    ? 'Una reunión virtual comienza en menos de 5 minutos.'
                    : 'Tienes reuniones virtuales que comienzan en menos de 30 minutos.';
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        }

        actualizarNotificacionSilenciosaCal();
        setInterval(actualizarNotificacionSilenciosaCal, 60000);
    });
    // Cierres robustos del modal por si algún handler falla
    document.addEventListener('click', function(ev){
        try{
            const modalEl = document.getElementById('eventoModal');
            if (!modalEl || !window.bootstrap) return;
            const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
            // Click en cualquier botón con data-bs-dismiss dentro del modal
            if (ev.target.closest('#eventoModal [data-bs-dismiss="modal"]')) {
                ev.preventDefault();
                inst.hide();
            }
            // Click en backdrop del modal
            if (modalEl.classList.contains('show') && ev.target === modalEl){
                inst.hide();
            }
            // Click explícito sobre el backdrop de Bootstrap
            if (document.querySelector('.modal-backdrop') && ev.target.classList && ev.target.classList.contains('modal-backdrop')){
                inst.hide();
            }
        }catch{}
    }, true);

    // Cerrar con tecla Escape de forma garantizada
    document.addEventListener('keydown', function(ev){
        try{
            if (ev.key !== 'Escape') return;
            const modalEl = document.getElementById('eventoModal');
            if (!modalEl || !window.bootstrap) return;
            const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
            if (modalEl.classList.contains('show')) inst.hide();
        }catch{}
    });
</script>
@endpush
