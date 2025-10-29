<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendario de Reuniones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
    @vite('resources/css/aprendiz/style.css')
    <link rel="stylesheet" href="{{ asset('css/aprendiz-calendario.css') }}">

</head>
<body>
    <!-- Top bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Panel del aprendiz SENA</h1>
            <div class="d-flex align-items-center gap-3">
                <div class="text-white small text-end">
                    <div style="font-size:0.78rem; opacity:0.9;">Aprendiz</div>
                </div>
                <div class="user-avatar" title="{{ Auth::user()->name }}">
                    @php
                        $name = trim(Auth::user()->name ?? '');
                        $parts = preg_split('/\s+/', $name);
                        $initials = strtoupper(($parts[0][0] ?? '') . ($parts[count($parts)-1][0] ?? ''));
                    @endphp
                    {{ $initials }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-3">
                <div class="sidebar">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/sena-logo.png') }}" alt="SENA" class="top-left-logo me-3">
                        <div>
                            <div style="font-weight:700;">Sistema de Gestión</div>
                            <div style="color:var(--muted);font-size:0.9rem;">Semillero</div>
                        </div>
                    </div>
                    <div class="list-group">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-home me-2"></i> Inicio
                        </a>
                        <a href="{{ route('aprendiz.proyectos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-folder-open me-2"></i> Mis Proyectos
                        </a>
                        <a href="{{ route('aprendiz.archivos.index') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-file-upload me-2"></i> Subir Documentos
                        </a>
                        <a href="{{ route('aprendiz.perfil.show') }}" class="list-group-item list-group-item-action">
                            <i class="fa fa-user me-2"></i> Mi Perfil
                        </a>
                        <a href="{{ route('aprendiz.calendario.index') }}" class="list-group-item list-group-item-action active" aria-current="true">
                            <i class="fa fa-calendar-days me-2"></i> Calendario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-lg-9">
                <h2 class="fw-bold mb-3">Calendario de Reuniones</h2>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label mb-1">Proyecto</label>
                                <select id="filtroProyecto" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Tipo</label>
                                <select id="filtroTipo" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end gap-2">
                                <button id="btnResetFiltros" class="btn btn-outline-secondary btn-sm">Limpiar</button>
                                <button id="btnAgenda" class="btn btn-primary btn-sm" type="button">Ver Agenda</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    @php
        $events = ($reuniones ?? collect())
            ->map(function($r){
                // color por tipo
                $tipo = $r->tipo ?? null;
                $colorMap = [
                    'planificacion' => '#2563eb',
                    'seguimiento'   => '#16a34a',
                    'revision'      => '#9333ea',
                    'capacitacion'  => '#fb923c',
                    'general'       => '#0ea5e9',
                ];
                $bg = $colorMap[strtolower((string)$tipo)] ?? '#0ea5e9';

                return [
                    'id'    => $r->id ?? null,
                    'title' => ($r->titulo ?? 'Reunión') . (isset($r->proyecto) ? ' · ' . ($r->proyecto->nombre_proyecto ?? $r->proyecto->nombre ?? '') : ''),
                    'start' => (string)($r->fecha_inicio ?? $r->inicio ?? ''),
                    'end'   => (string)($r->fecha_fin ?? $r->fin ?? ''),
                    'backgroundColor' => $bg,
                    'borderColor' => $bg,
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'lider'      => (is_object($r->lider ?? null)
                                            ? (($r->lider->user->name ?? $r->lider->nombre_completo ?? '') )
                                            : (string)($r->lider ?? ($r->lider_nombre ?? ''))),
                        'proyecto'   => isset($r->proyecto) ? ($r->proyecto->nombre_proyecto ?? $r->proyecto->nombre ?? '') : '',
                        'descripcion'=> $r->descripcion ?? '',
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
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            locale: 'es',
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Agenda' },
            height: 'auto',
            // Horario laboral como en Líder
            businessHours: [
                { daysOfWeek: [1,2,3,4,5], startTime: '08:00', endTime: '17:00' }
            ],
            slotMinTime: '08:00:00',
            slotMaxTime: '17:00:00',
            allDaySlot: false,
            nowIndicator: true,
            events: reuniones,
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
        } catch(err){
            console.error('Error inicializando FullCalendar:', err);
            const warn = document.createElement('div');
            warn.className = 'alert alert-warning mt-2';
            warn.textContent = 'No se pudo cargar el calendario. Reintenta recargando la página.';
            calendarEl.replaceWith(warn);
            return;
        }

        // Poblar filtros
        const selProyecto = document.getElementById('filtroProyecto');
        const selTipo = document.getElementById('filtroTipo');
        const proyectos = Array.from(new Set((reuniones||[]).map(e=>e.extendedProps?.proyecto).filter(Boolean))).sort();
        const tipos = Array.from(new Set((reuniones||[]).map(e=>e.extendedProps?.tipo).filter(Boolean))).sort();
        proyectos.forEach(p=>{ const o=document.createElement('option'); o.value=p; o.textContent=p; selProyecto.appendChild(o); });
        tipos.forEach(t=>{ const o=document.createElement('option'); o.value=t; o.textContent=t.charAt(0).toUpperCase()+t.slice(1); selTipo.appendChild(o); });

        function aplicarFiltros(){
            const p = selProyecto.value || '';
            const t = (selTipo.value || '').toLowerCase();
            const filtrados = (reuniones||[]).filter(ev=>{
                const okP = !p || (ev.extendedProps?.proyecto === p);
                const okT = !t || (String(ev.extendedProps?.tipo||'').toLowerCase() === t);
                return okP && okT;
            });
            calendar.removeAllEvents();
            filtrados.forEach(ev=> calendar.addEvent(ev));
        }
        selProyecto.addEventListener('change', aplicarFiltros);
        selTipo.addEventListener('change', aplicarFiltros);
        document.getElementById('btnResetFiltros').addEventListener('click', ()=>{
            selProyecto.value=''; selTipo.value=''; aplicarFiltros();
        });
        document.getElementById('btnAgenda').addEventListener('click', ()=>{
            calendar.changeView('listWeek');
        });
    });
</script>
</body>
</html>
