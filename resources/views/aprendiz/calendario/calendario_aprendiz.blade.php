@extends('layouts.aprendiz')

@section('title','Calendario')
@section('module-title','')
@section('module-subtitle','')

@push('styles')
@php $v = time(); @endphp
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/aprendiz/aprendiz-calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario-views.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendar-month.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/lider_semi/calendario-lider.css') }}?v={{ $v }}">

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
                                <div class="subtitle">Consulta tus Eventos y reuniones</div>
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
                        <div class="calendar-week" id="weekView">
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
                        <div id="apr-week-header" class="apr-week-header d-none"></div>
                        <div id='calendar' class="d-none"></div>
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
        let calendar;
        try{
        const _cal = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            locale: 'es',
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Agenda', prev: 'Anterior', next: 'Siguiente' },
            height: 'auto',
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
            allDaySlot: false,
            nowIndicator: true,
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

                // Enlace y código
                const enlace = info.event.extendedProps.link || '';
                const codigo = info.event.extendedProps.codigo || '';
                const wrapLink = document.getElementById('eventoEnlaceWrap');
                const aLink = document.getElementById('eventoEnlace');
                const wrapCod = document.getElementById('eventoCodigoWrap');
                const spanCod = document.getElementById('eventoCodigo');
                if (enlace) {
                    wrapLink.style.display='block';
                    aLink.href = enlace;
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
                    wrapLink.style.display='none';
                    aLink.removeAttribute('href');
                    aLink.textContent = '';
                }
                if (codigo) { wrapCod.style.display='block'; spanCod.textContent = codigo; } else { wrapCod.style.display='none'; spanCod.textContent='—'; }

                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            },
        });
        calendar = _cal;
        calendar.render();

        // Fallbacks robustos para que el botón "Cerrar" funcione siempre
        (function(){
            const modalEl = document.getElementById('eventoModal');
            if (!modalEl || !window.bootstrap) return;
            const ensureModal = () => bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: true, keyboard: true });

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
                const start = new Date(info.start.valueOf());
                const days = [];
                for (let i=0;i<7;i++){ const d = new Date(start); d.setDate(start.getDate()+i); days.push(d); }
                const dayNames = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
                const today = new Date(); today.setHours(0,0,0,0);
                let html = '<div class="wk-hora">Hora</div>';
                days.forEach(d=>{
                    const isToday = d.toDateString() === today.toDateString();
                    if (isToday){
                        html += `<div class="wk-day today"><div class="wk-pill"><div class="wk-name">${dayNames[d.getDay()]}</div><div class="wk-num">${d.getDate()}</div></div></div>`;
                    } else {
                        html += `<div class="wk-day"><div class="wk-name">${dayNames[d.getDay()]}</div><div class="wk-num">${d.getDate()}</div></div>`;
                    }
                });
                hdr.innerHTML = html;
            }catch{}
        }

        calendar.on('datesSet', (info) => {
            updatePeriod(info.start);
            renderAprWeekHeader(info);
        });

        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                viewButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const view = btn.getAttribute('data-view');
                const map = { month: 'dayGridMonth', week: 'timeGridWeek', day: 'timeGridDay' };
                calendar.changeView(map[view] || 'dayGridMonth');
                updatePeriod(calendar.getDate());
            });
        });

        prevBtn?.addEventListener('click', () => { calendar.prev(); updatePeriod(calendar.getDate()); });
        nextBtn?.addEventListener('click', () => { calendar.next(); updatePeriod(calendar.getDate()); });
        todayBtn?.addEventListener('click', () => { calendar.today(); updatePeriod(calendar.getDate()); });
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
</script>
@endpush
