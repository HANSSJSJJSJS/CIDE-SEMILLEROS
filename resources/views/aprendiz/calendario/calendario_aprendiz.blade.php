@extends('layouts.aprendiz')

@section('title','Calendario')
@section('module-title','')
@section('module-subtitle','')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/aprendiz/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/aprendiz-calendario.css') }}">
@php $v = time(); @endphp
<link rel="stylesheet" href="{{ asset('css/common/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario-views.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendar-month.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/lider_semi/calendario-lider.css') }}?v={{ $v }}">
<style>
  /* Asegurar que el modal Bootstrap quede por encima de cualquier overlay del diseño SCML */
  .modal { z-index: 2000 !important; }
  .modal-backdrop { z-index: 1990 !important; }
  /* Evitar que algún overlay accidental bloquee los clics dentro del modal */
  .modal .modal-dialog { pointer-events: auto; }
  /* Asegurar clic en botones de cierre aunque haya overlays con pointer-events */
  .modal .btn-close,
  .modal [data-bs-dismiss="modal"] { pointer-events: auto; }
  /* Backdrop más claro */
  .modal-backdrop.show { opacity: .36; }

  /* Modal SCML look */
  #eventoModal .modal-dialog { max-width: 640px; }
  #eventoModal .modal-content{
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    background: rgba(255,255,255,.92);
    box-shadow: inset 0 4px 0 #19ad4c, 0 20px 40px rgba(0,0,0,.18);
  }
  @supports ((backdrop-filter: blur(8px)) or (-webkit-backdrop-filter: blur(8px))) {
    #eventoModal .modal-content{ -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); }
  }
  #eventoModal .modal-header{ border-bottom: 1px solid rgba(0,0,0,.08); padding: 16px 20px; }
  #eventoModal .modal-title{ font-weight: 800; color: #0b3357; letter-spacing: .2px; }
  #eventoModal .modal-body{ padding: 18px 20px; }
  #eventoModal .modal-footer{ border-top: 1px solid rgba(0,0,0,.08); padding: 14px 20px; }

  /* Campos del cuerpo en formato limpio */
  #eventoModal .modal-body > div { margin-bottom: .75rem; }
  #eventoModal .modal-body strong{ display:block; color:#0b3357; margin-bottom:.15rem; }
  #eventoModal #eventoProyecto,
  #eventoModal #eventoLider,
  #eventoModal #eventoFecha,
  #eventoModal #eventoDesc { color:#0b3357; }

  /* chips de participantes */
  #eventoModal #eventoParticipantes .badge{ background:#f3f4f6; color:#0b3357; border-color: rgba(0,0,0,.08); }

  /* Enlace como botón pildora */
  #eventoModal #eventoEnlace{
    display:inline-block; background:#19ad4c; color:#fff !important; font-weight:800;
    padding:.35rem .7rem; border-radius:10px; border:1px solid rgba(0,0,0,.06);
  }
  #eventoModal #eventoEnlace:hover{ filter:brightness(.98); }

  /* Código como etiqueta */
  #eventoModal #eventoCodigo{
    display:inline-block; background:#e7f0ff; color:#0b3357; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    padding:.25rem .5rem; border-radius:8px; border:1px solid rgba(0,0,0,.06);
  }

  /* Badges dentro del modal */
  #eventoModal .badge.rounded-pill{ padding: .45rem .7rem; font-weight: 800; letter-spacing: .3px; }
  #eventoModal #badgeTipo{ background: #2563eb !important; }
  #eventoModal #badgeUbic{ background: #6b7280 !important; }

  /* Tipos de texto */
  #eventoModal .modal-body strong{ color:#0b3357; }
  #eventoModal .modal-body a{ color:#0b3357; font-weight:700; text-decoration: underline; }
  #eventoModal .modal-body a:hover{ opacity:.85; }

  /* Botón cerrar estilo suave */
  #eventoModal .modal-footer .btn{
    background: #f3f4f6; color:#0b3357; border:1px solid rgba(0,0,0,.08);
    font-weight: 700; border-radius: 12px; padding: .6rem 1.1rem;
  }
  #eventoModal .modal-footer .btn:hover{ background: #e5e7eb; }
  #eventoModal .modal-footer .btn:active{ background: #d1d5db; }
  /* Solo diseño: FullCalendar en vista mes similar a la captura */
  .cal-lider #calendar .fc-scrollgrid,
  .cal-lider #calendar .fc-theme-standard .fc-scrollgrid {
    border: 0;
  }
  .cal-lider #calendar .fc-col-header {
    border: 0;
  }
  .cal-lider #calendar .fc-col-header .fc-col-header-cell {
    background: #0b3357; /* azul encabezado */
    color: #fff;
    border: 0;
    font-weight: 700;
    padding: 8px 6px;
  }
  .cal-lider #calendar .fc-col-header .fc-day {
    border: 0;
  }
  .cal-lider #calendar .fc-col-header .fc-col-header-cell:first-child {
    border-top-left-radius: 12px;
  }
  .cal-lider #calendar .fc-col-header .fc-col-header-cell:last-child {
    border-top-right-radius: 12px;
  }
  .cal-lider #calendar .fc-daygrid-day,
  .cal-lider #calendar .fc-daygrid-day-frame {
    border: 0;
  }
  .cal-lider #calendar .fc-daygrid-day {
    background: rgba(255,255,255,.82);
  }
  .cal-lider #calendar .fc-daygrid-day-number {
    color: #0b3357;
    font-weight: 600;
  }
  .cal-lider #calendar .fc-daygrid-day.fc-day-today {
    background: rgba(5, 150, 105, .09); /* verde muy claro */
  }
  .cal-lider #calendar .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    display: inline-block;
    background: #16a34a; /* verde */
    color: #fff;
    padding: 2px 6px;
    border-radius: 8px;
    box-shadow: 0 1px 0 rgba(0,0,0,.08);
  }
  /* Evento estilo píldora como la referencia */
  .cal-lider #calendar .scml-pill {
    border: 1px solid rgba(0,0,0,.04);
    border-radius: 18px;
    padding: 8px 12px;
    background: linear-gradient(180deg, #19ad4c 0%, #0b8d2f 100%);
    color: #0b3357; /* título en azul oscuro */
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 2px 0 rgba(0,0,0,.06);
    text-decoration: none;
    width: 100%;
    display: block;
  }
  .cal-lider #calendar .scml-pill .fc-event-main {
    display:flex; align-items:center; gap:8px;
    flex-wrap: wrap;
  }
  .cal-lider #calendar .scml-pill .fc-event-time {
    background: rgba(255,255,255,.45);
    color: #fff;
    border-radius: 10px;
    padding: 2px 10px;
    font-weight: 800;
    line-height: 1.1;
    font-size: clamp(0.65rem, 1vw + 0.25rem, 0.85rem);
    flex: 0 0 auto;
  }
  .cal-lider #calendar .scml-pill .fc-event-title {
    font-weight: 700;
    font-size: clamp(0.8rem, 1.2vw + 0.2rem, 1rem);
    color: #0b3357;
    white-space: normal;
    overflow: visible;
    text-overflow: initial;
    flex: 1 1 120px;
    word-break: break-word;
  }
  .cal-lider #calendar .scml-pill:hover { box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 3px 0 rgba(0,0,0,.08); }

  /* línea secundaria (líder) responsive y legible */
  .cal-lider #calendar .scml-pill .scml-meta{
    color: #ffffff;
    font-weight: 600;
    font-size: clamp(0.72rem, 1vw + 0.18rem, 0.9rem);
    opacity: 0.95;
  }

  /* Ajuste móvil: compactar padding y radio */
  @media (max-width: 576px){
    .cal-lider #calendar .scml-pill{ padding: 6px 10px; border-radius: 16px; }
  }
  /* Suavizar rejilla */
  .cal-lider #calendar .fc-theme-standard td,
  .cal-lider #calendar .fc-theme-standard th {
    border: 0;
  }
  .cal-lider #calendar .fc-daygrid {
    border-radius: 12px;
    overflow: hidden;
  }
  /* Contenedor card más aire, sin afectar lógica */
  .cal-lider .card.shadow-sm {
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,.06);
  }
  .cal-lider .card.shadow-sm .card-body { padding: 16px; }
  /* Fines de semana ligeramente desaturados */
  .cal-lider #calendar .fc-day-sat .fc-daygrid-day-frame,
  .cal-lider #calendar .fc-day-sun .fc-daygrid-day-frame { filter: saturate(.95); }
  /* Hover sutil de celda */
  .cal-lider #calendar .fc-daygrid-day:hover { background: rgba(255,255,255,.92); }
  /* Soporte blur donde exista */
  @supports ((backdrop-filter: blur(2px)) or (-webkit-backdrop-filter: blur(2px))) {
    .cal-lider #calendar .fc-daygrid-day { -webkit-backdrop-filter: blur(2px); backdrop-filter: blur(2px); }
  }
  /* Separación entre el encabezado y el calendario */
  .cal-lider .header{ margin-bottom: 28px; }
  @media (min-width: 768px){ .cal-lider .header{ margin-bottom: 40px; } }
  @media (min-width: 992px){ .cal-lider .header{ margin-bottom: 56px; } }

  /* =====================
     Semana (timeGridWeek)
     ===================== */
  /* Barra de encabezado de días (azul y redondeada) */
  .cal-lider #calendar .fc-timegrid .fc-col-header { border: 0; overflow: visible; }
  .cal-lider #calendar .fc-timegrid .fc-col-header .fc-col-header-cell {
    background: #0b3357 !important; color:#fff !important; border:0; padding: 10px 8px; font-weight:800;
    overflow: visible; position: relative;
  }
  .cal-lider #calendar .fc-timegrid .fc-col-header .fc-col-header-cell:first-child { border-top-left-radius: 16px; }
  .cal-lider #calendar .fc-timegrid .fc-col-header .fc-col-header-cell:last-child { border-top-right-radius: 16px; }
  .cal-lider #calendar .fc-timegrid .fc-col-header .fc-col-header-cell a.fc-col-header-cell-cushion{ color:#fff !important; text-decoration:none; font-weight:800; text-transform: uppercase; letter-spacing:.03em; }

  /* Columna de horas (izquierda) y líneas suaves */
  .cal-lider #calendar .fc-timegrid .fc-timegrid-axis { background: rgba(255,255,255,.9); color:#587089; font-weight:700; border:0; }
  .cal-lider #calendar .fc-timegrid .fc-timegrid-slot { border-color: rgba(0,0,0,.06); }

  /* Hoy: pastilla verde en el encabezado y fondo sutil en la columna */
  .cal-lider #calendar .fc-timegrid .fc-col-header .fc-col-header-cell.fc-day-today a.fc-col-header-cell-cushion{
    display:inline-block; padding:10px 14px; border-radius:16px;
    background: linear-gradient(180deg, #19ad4c 0%, #0b8d2f 100%);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25);
  }
  .cal-lider #calendar .fc-timegrid-col.fc-day-today .fc-timegrid-col-frame { background: rgba(5,150,105,.08) !important; }

  /* Píldoras verdes en semana (reutiliza .scml-pill) */
  .cal-lider #calendar .fc-timegrid-event.scml-pill { border-radius: 14px; }

  /* Franja de almuerzo 12:00-14:00 con etiqueta */
  .cal-lider #calendar .fc-timegrid-slots tr[data-time="12:00:00"],
  .cal-lider #calendar .fc-timegrid-slots tr[data-time="13:00:00"]{ background: rgba(5, 150, 105, .05); }
  .cal-lider #calendar .fc-timegrid-slots .fc-timegrid-slot-lane{ position: relative; }
  .cal-lider #calendar .fc-timegrid-slots tr[data-time="12:00:00"] .fc-timegrid-slot-lane::before,
  .cal-lider #calendar .fc-timegrid-slots tr[data-time="13:00:00"] .fc-timegrid-slot-lane::before{
    content: 'ALMUERZO'; position: absolute; left: 50%; transform: translateX(-50%);
    color: rgba(0,0,0,.25); font-weight: 800; letter-spacing: .15rem; font-size:.85rem;
  }
</style>
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
                    const title = document.createElement('div');
                    title.className = 'fc-event-title fc-sticky';
                    const t = String(arg.event.title || 'Reunión');
                    // Título corto: truncamos para que no se vea grande; el detalle completo queda en el modal al hacer click
                    const MAX = 18;
                    title.textContent = t.length > MAX ? (t.slice(0, MAX) + '…') : t;
                    // estilos para forzar una sola línea con elipsis si fuera necesario
                    title.style.whiteSpace = 'nowrap';
                    title.style.overflow = 'hidden';
                    title.style.textOverflow = 'ellipsis';
                    wrap.appendChild(title);
                    // No agregamos línea secundaria (líder/participantes) para mantener la píldora compacta
                    return { domNodes: [wrap] };
                }catch(e){ return {}; }
            },
            // Bloque de almuerzo sombreado (12:00 - 14:00)
            eventSources: [
                function(fetchInfo, successCallback) {
                    const start = new Date(fetchInfo.start.valueOf());
                    const end = new Date(fetchInfo.end.valueOf());
                    const events = [];
                    for (let d = new Date(start); d < end; d.setDate(d.getDate()+1)) {
                        const dow = d.getDay(); // 0=Dom..6=Sáb
                        if (dow >= 1 && dow <= 5) { // L-V
                            const y = d.getFullYear();
                            const m = (d.getMonth()+1).toString().padStart(2,'0');
                            const day = d.getDate().toString().padStart(2,'0');
                            events.push({
                                start: `${y}-${m}-${day}T12:00:00`,
                                end: `${y}-${m}-${day}T14:00:00`,
                                display: 'background',
                                backgroundColor: '#fde2e2',
                                borderColor: 'transparent'
                            });
                        }
                    }
                    successCallback(events);
                }
            ],
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

        calendar.on('datesSet', (info) => {
            updatePeriod(info.start);
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
