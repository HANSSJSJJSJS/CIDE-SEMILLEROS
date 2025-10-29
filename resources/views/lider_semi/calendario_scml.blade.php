@extends('layouts.lider_semi')

@push('styles')
@php($v = time())
<link rel="stylesheet" href="{{ asset('css/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/calendario-views.css') }}?v={{ $v }}">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="header">
        <div class="header-top">
            <h1>📅 Calendario SCML</h1>
            <div class="view-switcher">
                <button class="view-btn active" data-view="month">Mes</button>
                <button class="view-btn" data-view="week">Semana</button>
                <button class="view-btn" data-view="day">Día</button>
            </div>
        </div>
        <div class="header-controls">
            <div class="nav-controls">
                <button class="nav-btn" id="prevBtn">← Anterior</button>
                <button class="today-btn" id="todayBtn">Hoy</button>
                <button class="nav-btn" id="nextBtn">Siguiente →</button>
            </div>
            <div class="current-period" id="currentPeriod"></div>
        </div>
    </div>

    <!-- Vista de Mes -->
    <div class="calendar-month" id="monthView">
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

    <!-- Vista de Semana -->
    <div class="calendar-week" id="weekView">
        <div class="week-header" id="weekHeader"></div>
        <div class="week-grid" id="weekGrid"></div>
    </div>

    <!-- Vista de Día -->
    <div class="calendar-day" id="dayView">
        <div class="day-header" id="dayHeader"></div>
        <div class="day-grid" id="dayGrid"></div>
    </div>
</div>

<!-- Modal para agregar/editar eventos (nuevo) -->
<div class="modal-calendario" id="event-modal">
    <div class="modal-content-calendario">
        <div class="modal-header-calendario">
            <h3 class="modal-title-calendario" id="modal-title">Nueva Reunión</h3>
            <button class="close-modal-calendario" id="close-modal">&times;</button>
        </div>
        <form id="event-form">
            @csrf
            <input type="hidden" id="event-id">
            
            <div class="form-group-calendario">
                <label for="event-title">Título de la reunión *</label>
                <input type="text" id="event-title" class="form-control-calendario" placeholder="Ej: Reunión Semillero IA" required>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-proyecto">Proyecto (Opcional)</label>
                <select id="event-proyecto" class="form-control-calendario">
                    <option value="">Sin proyecto asignado</option>
                    @isset($proyectos)
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id_proyecto }}">{{ $proyecto->nombre_proyecto }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-date">Fecha *</label>
                <input type="date" id="event-date" class="form-control-calendario" required>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-time">Hora *</label>
                <input type="time" id="event-time" class="form-control-calendario" required>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-duration">Duración (minutos) *</label>
                <input type="number" id="event-duration" class="form-control-calendario" value="60" min="15" step="15" required>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-description">Descripción</label>
                <textarea id="event-description" class="form-control-calendario" rows="3" placeholder="Detalles de la reunión..."></textarea>
            </div>
            
            <div class="form-group-calendario">
                <label for="event-participants">Participantes</label>
                <select id="event-participants" class="form-control-calendario" multiple size="5">
                    @isset($aprendices)
                        @foreach($aprendices as $aprendiz)
                            <option value="{{ $aprendiz->id_aprendiz }}">{{ $aprendiz->nombre_completo }}</option>
                        @endforeach
                    @endisset
                </select>
                <small class="text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples participantes</small>
            </div>
            
            <div class="form-actions-calendario">
                <button type="button" class="btn-calendario btn-secondary-calendario" id="cancel-event">Cancelar</button>
                <button type="submit" class="btn-calendario btn-primary-calendario">
                    <i class="fas fa-save"></i> Guardar Reunión
                </button>
            </div>
        </form>
    </div>
    </div>

<!-- Drawer lateral de Detalle de Reunión -->
<div id="event-detail-overlay" class="drawer-overlay" style="display:none;"></div>
<aside id="event-detail-drawer" class="drawer" aria-hidden="true" style="display:none;">
    <div class="drawer-header">
        <div class="drawer-avatar" id="detail-avatar">RM</div>
        <h3 class="drawer-title" id="detail-title">Detalle de la reunión</h3>
        <button type="button" class="drawer-close" id="drawer-close-btn">&times;</button>
    </div>
    <div class="drawer-body">
        <section class="drawer-section">
            <h4 class="drawer-section-title">Información General</h4>
            <div class="drawer-grid">
                <div class="drawer-field">
                    <div class="drawer-label">Título</div>
                    <div class="drawer-value" id="detail-titulo">--</div>
                </div>
                <div class="drawer-field">
                    <div class="drawer-label">Tipo</div>
                    <div class="drawer-value" id="detail-tipo">--</div>
                </div>
                <div class="drawer-field">
                    <div class="drawer-label">Fecha</div>
                    <div class="drawer-value" id="detail-fecha">--</div>
                </div>
                <div class="drawer-field">
                    <div class="drawer-label">Hora</div>
                    <div class="drawer-value" id="detail-hora">--</div>
                </div>
                <div class="drawer-field">
                    <div class="drawer-label">Duración</div>
                    <div class="drawer-value" id="detail-duracion">--</div>
                </div>
                <div class="drawer-field">
                    <div class="drawer-label">Ubicación</div>
                    <div class="drawer-value" id="detail-ubicacion">--</div>
                </div>
                <div class="drawer-field" id="detail-link-field" style="display:none;">
                    <div class="drawer-label">Enlace</div>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <a href="#" id="detail-link" class="drawer-link" target="_blank" rel="noopener" style="display:none;">Abrir reunión</a>
                        <button type="button" id="detail-outlook-link" class="btn-calendario btn-secondary-calendario btn-small" style="display:none;">Abrir en Outlook</button>
                        <button type="button" id="detail-delete-link" title="Eliminar enlace" style="display:none; background:none;border:none;cursor:pointer;font-size:18px;">🗑️</button>
                    </div>
                    <div id="detail-link-editor" style="display:none; margin-top:8px; width:100%;">
                        <input type="url" id="detail-link-input" placeholder="Pega un enlace de reunión (https://...)" style="width:100%; padding:8px; border:1px solid #e1e4e8; border-radius:6px;" />
                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <button type="button" id="detail-save-link" class="btn-calendario btn-primary-calendario btn-small">Guardar enlace</button>
                            <button type="button" id="detail-cancel-edit" class="btn-calendario btn-secondary-calendario btn-small">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="drawer-section">
            <h4 class="drawer-section-title">Participantes</h4>
            <div id="detail-participantes" class="participants-chips">--</div>
        </section>
        <section class="drawer-section">
            <h4 class="drawer-section-title">Descripción</h4>
            <div id="detail-descripcion" class="drawer-description">--</div>
        </section>
        <div class="drawer-actions">
            <button type="button" class="btn-calendario btn-secondary-calendario" id="drawer-close-cta">Cerrar</button>
        </div>
    </div>


<script>
// Estado
let currentDate = new Date();
let currentView = 'month';
let events = [];
let selectedDate = null;
let editingEventId = null;
let draggedEvent = null;

// Rutas backend
const ROUTES = {
  obtener: "{{ route('lider_semi.eventos.obtener', [], false) }}",
  baseEventos: "{{ route('lider_semi.eventos.crear', [], false) }}"
};
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Horario permitido: 08:00 a 16:50 (sin almuerzo 12:00-13:55)
const workHours = Array.from({ length: 9 }, (_, i) => i + 8); // 8..16
const HOLIDAYS = @json(config('app.feriados', []));

const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const todayBtn = document.getElementById('todayBtn');
const currentPeriodEl = document.getElementById('currentPeriod');
const daysGrid = document.getElementById('daysGrid');
const eventModal = document.getElementById('event-modal');
const detailDrawer = document.getElementById('event-detail-drawer');
const detailOverlay = document.getElementById('event-detail-overlay');
const eventForm = document.getElementById('event-form');
const closeModal = document.getElementById('close-modal');
const drawerCloseBtn = document.getElementById('drawer-close-btn');
const cancelBtn = document.getElementById('cancel-event');
const drawerCloseCta = document.getElementById('drawer-close-cta');
const deleteBtn = document.getElementById('deleteBtn');
const selectedDateDisplay = document.getElementById('selectedDateDisplay');
const modalTitle = document.getElementById('modal-title');

const monthView = document.getElementById('monthView');
const weekView = document.getElementById('weekView');
const dayView = document.getElementById('dayView');
const viewBtns = document.querySelectorAll('.view-btn');

initCalendar();
async function initCalendar(){ await loadEventsForCurrentPeriod(); renderCalendar(); }

prevBtn.addEventListener('click', async () => { navigatePeriod(-1); await loadEventsForCurrentPeriod(); renderCalendar(); });
nextBtn.addEventListener('click', async () => { navigatePeriod(1); await loadEventsForCurrentPeriod(); renderCalendar(); });
todayBtn.addEventListener('click', async () => { goToToday(); await loadEventsForCurrentPeriod(); renderCalendar(); });
if (closeModal) closeModal.addEventListener('click', closeEventModal);
drawerCloseBtn.addEventListener('click', closeDetailDrawer);
drawerCloseCta.addEventListener('click', closeDetailDrawer);
if (cancelBtn) cancelBtn.addEventListener('click', closeEventModal);
if (deleteBtn) deleteBtn.addEventListener('click', deleteEvent);
if (eventForm) eventForm.addEventListener('submit', saveEvent);

viewBtns.forEach(btn => { btn.addEventListener('click', async () => { viewBtns.forEach(b => b.classList.remove('active')); btn.classList.add('active'); currentView = btn.dataset.view; await loadEventsForCurrentPeriod(); renderCalendar(); }); });

if (eventModal) eventModal.addEventListener('click', (e) => { if (e.target === eventModal) closeEventModal(); });
detailOverlay.addEventListener('click', closeDetailDrawer);

async function loadEventsForCurrentPeriod(){
  try{
    const mes = currentDate.getMonth() + 1; const anio = currentDate.getFullYear();
    const url = new URL(ROUTES.obtener, window.location.origin);
    url.searchParams.set('mes', mes); url.searchParams.set('anio', anio);
    const res = await fetch(url.toString(), { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
    const data = await res.json();
    const list = data.eventos || [];
    events = list.map(ev => {
      const d = new Date(ev.fecha_hora);
      const yyyy = d.toISOString().split('T')[0];
      const hh = String(d.getHours()).padStart(2,'0');
      const mm = String(d.getMinutes()).padStart(2,'0');
      return { id: ev.id, date: yyyy, time: `${hh}:${mm}`, title: ev.titulo, duration: ev.duracion||60, type: ev.tipo||'general', location: ev.ubicacion||'', description: ev.descripcion||'', researchLine: ev.linea_investigacion || '', link: ev.link_virtual || '' };
    });
  }catch(e){ console.error('Error cargando eventos', e); events = []; }
}

function renderCalendar(){ if(currentView==='month') renderMonthView(); else if(currentView==='week') renderWeekView(); else renderDayView(); updatePeriodDisplay(); }

function renderMonthView(){
  monthView.style.display='block'; weekView.classList.remove('active'); dayView.classList.remove('active');
  const year = currentDate.getFullYear(); const month = currentDate.getMonth();
  const firstDay = new Date(year, month, 1); const lastDay = new Date(year, month+1, 0); const prevLastDay = new Date(year, month, 0);
  const firstDayIndex = firstDay.getDay(); const lastDayIndex = lastDay.getDay(); const nextDays = 7 - lastDayIndex - 1;
  daysGrid.innerHTML='';
  for(let i=firstDayIndex;i>0;i--){ const day = prevLastDay.getDate()-i+1; const date=new Date(year,month-1,day); daysGrid.appendChild(createDayCell(date,true)); }
  for(let day=1; day<=lastDay.getDate(); day++){ const date=new Date(year,month,day); daysGrid.appendChild(createDayCell(date,false)); }
  for(let day=1; day<=nextDays; day++){ const date=new Date(year,month+1,day); daysGrid.appendChild(createDayCell(date,true)); }
}

function createDayCell(date, otherMonth){
  const cell=document.createElement('div'); cell.className='day-cell'; if(otherMonth) cell.classList.add('other-month'); if(isToday(date)) cell.classList.add('today');
  const dateStr = formatDate(date); cell.dataset.date=dateStr; const dayNumber=document.createElement('div'); dayNumber.className='day-number'; dayNumber.textContent=date.getDate(); cell.appendChild(dayNumber);
  events.filter(e=>e.date===dateStr).forEach(event=>{ cell.appendChild(createEventElement(event)); });
  const isWeekend = date.getDay()===0 || date.getDay()===6;
  const isHoliday = HOLIDAYS.includes(formatDate(date));
  if (isWeekend || isHoliday) {
    cell.classList.add('disabled');
  } else {
    cell.addEventListener('click',(e)=>{ if(e.target===cell||e.target===dayNumber){ openEventModal(date); } });
    cell.addEventListener('dragover', handleDragOver);
    cell.addEventListener('drop', (e)=>handleDrop(e,date));
    cell.addEventListener('dragleave', handleDragLeave);
  }
  return cell;
}

function renderWeekView(){
  monthView.style.display='none'; weekView.classList.add('active'); dayView.classList.remove('active');
  const weekStart=getWeekStart(currentDate); const weekDays=Array.from({length:7},(_,i)=>{ const d=new Date(weekStart); d.setDate(weekStart.getDate()+i); return d; });
  const weekHeader=document.getElementById('weekHeader'); weekHeader.innerHTML='<div class="week-time-label">Hora</div>';
  weekDays.forEach(date=>{ const header=document.createElement('div'); header.className='week-day-header'; if(isToday(date)) header.classList.add('today'); header.innerHTML=`<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`; weekHeader.appendChild(header); });
  const weekGrid=document.getElementById('weekGrid'); weekGrid.innerHTML=''; const timesCol=document.createElement('div'); timesCol.className='week-times';
  workHours.forEach(hour=>{ const tl=document.createElement('div'); tl.className='time-slot-label'; tl.textContent=formatHour(hour); timesCol.appendChild(tl); }); weekGrid.appendChild(timesCol);
  weekDays.forEach(date=>{ const dayCol=document.createElement('div'); dayCol.className='week-day-column'; const dateStr=formatDate(date);
    const isWeekend = date.getDay()===0 || date.getDay()===6; const isHoliday = HOLIDAYS.includes(dateStr);
    workHours.forEach(hour=>{ const slot=document.createElement('div'); slot.className='week-time-slot'; slot.dataset.date=dateStr; slot.dataset.hour=hour;
      const hm = hour*100; const isLunch = hm>=1200 && hm<=1355; const allowed = !isWeekend && !isHoliday && hm>=800 && hm<=1650 && !isLunch;
      if (!allowed) { slot.classList.add('disabled'); }
      else {
        slot.addEventListener('click',()=>{ const eventDate=new Date(date); eventDate.setHours(hour,0,0,0); openEventModal(eventDate); });
        slot.addEventListener('dragover', handleDragOver);
        slot.addEventListener('drop',(e)=>{ const eventDate=new Date(date); eventDate.setHours(hour,0,0,0); handleDrop(e,eventDate); });
        slot.addEventListener('dragleave', handleDragLeave);
      }
      dayCol.appendChild(slot); });
    events.filter(e=>e.date===dateStr).forEach(event=>{ dayCol.appendChild(createWeekEventElement(event)); }); weekGrid.appendChild(dayCol);
  });
}

function renderDayView(){
  monthView.style.display='none'; weekView.classList.remove('active'); dayView.classList.add('active'); const dateStr=formatDate(currentDate);
  const dayHeader=document.getElementById('dayHeader'); dayHeader.innerHTML=`<div></div><div class="day-header-info"><div class="day-header-name">${getDayName(currentDate)}</div><div class="day-header-date">${currentDate.getDate()}</div></div>`;
  const dayGrid=document.getElementById('dayGrid'); dayGrid.innerHTML=''; const timesCol=document.createElement('div'); timesCol.className='day-times';
  workHours.forEach(hour=>{ const tl=document.createElement('div'); tl.className='day-time-label'; tl.textContent=formatHour(hour); timesCol.appendChild(tl); }); dayGrid.appendChild(timesCol);
  const slotsCol=document.createElement('div'); slotsCol.className='day-slots';  const isWeekend = currentDate.getDay()===0 || currentDate.getDay()===6; const isHoliday = HOLIDAYS.includes(dateStr);
  workHours.forEach(hour=>{ const slot=document.createElement('div'); slot.className='day-time-slot'; slot.dataset.date=dateStr; slot.dataset.hour=hour;
    const hm = hour*100; const isLunch = hm>=1200 && hm<=1355; const allowed = !isWeekend && !isHoliday && hm>=800 && hm<=1650 && !isLunch;
    if (!allowed) { slot.classList.add('disabled'); }
    else {
      slot.addEventListener('click',()=>{ const eventDate=new Date(currentDate); eventDate.setHours(hour,0,0,0); openEventModal(eventDate); });
      slot.addEventListener('dragover', handleDragOver); slot.addEventListener('drop',(e)=>{ const eventDate=new Date(currentDate); eventDate.setHours(hour,0,0,0); handleDrop(e,eventDate); }); slot.addEventListener('dragleave', handleDragLeave);
    }
    slotsCol.appendChild(slot); });
  events.filter(e=>e.date===dateStr).forEach(event=>{ slotsCol.appendChild(createDayEventElement(event)); }); dayGrid.appendChild(slotsCol);
}

function createEventElement(event){ const el=document.createElement('div'); el.className='event'; el.draggable=true; el.dataset.id=event.id; el.innerHTML=`<div class="event-time">${event.time}</div><div class="event-title">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; }); el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; }); return el; }
function createWeekEventElement(event){ const el=document.createElement('div'); el.className='week-event'; el.draggable=true; el.dataset.id=event.id; const [h]=event.time.split(':').map(Number); const top=(h-8)*60+4; const height=Math.min(event.duration||60,60)-8; el.style.top=`${top}px`; el.style.height=`${height}px`; el.innerHTML=`<div style="font-weight:600;">${event.time}</div><div style="font-size:10px;">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; }); el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; }); return el; }
function createDayEventElement(event){ const el=document.createElement('div'); el.className='day-event'; el.draggable=true; el.dataset.id=event.id; const [h]=event.time.split(':').map(Number); const top=(h-8)*80+4; const height=Math.min((event.duration||60)/60*80,80)-8; el.style.top=`${top}px`; el.style.height=`${height}px`; el.innerHTML=`<div style="font-weight:600;font-size:13px;">${event.time}</div><div style="font-size:12px;margin-top:2px;">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; }); el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; }); return el; }

function handleDragOver(e){ e.preventDefault(); e.dataTransfer.dropEffect='move'; e.currentTarget.classList.add('drag-over'); }
function handleDragLeave(e){ e.currentTarget.classList.remove('drag-over'); }
async function handleDrop(e,newDate){ e.preventDefault(); e.currentTarget.classList.remove('drag-over'); if(!draggedEvent) return; const idx=events.findIndex(ev=>ev.id===draggedEvent.id); if(idx===-1) return; const newDateStr=formatDate(newDate); let newTime=events[idx].time; if(e.currentTarget.dataset.hour){ const hour=parseInt(e.currentTarget.dataset.hour); newTime=`${String(hour).padStart(2,'0')}:00`; }
  const candidate = new Date(`${newDateStr}T${newTime}:00`); if(!isAllowedDateTime(candidate)) { alert('No puedes mover el evento a un horario no permitido.'); return; }
  await updateEventOnServer(events[idx].id,newDateStr,newTime); await loadEventsForCurrentPeriod(); renderCalendar(); }

function openEventModal(date){
  // Permitimos abrir el modal siempre (especialmente desde vista mensual, donde la hora es 00:00)
  // La validación de horario/día se hace al guardar y en los slots/drag de semana/día.
  selectedDate=date; editingEventId=null; modalTitle.textContent='Nueva Reunión'; if (deleteBtn) deleteBtn.style.display='none';
  if (eventForm) eventForm.reset();
  const dateInput = document.getElementById('event-date');
  const timeInput = document.getElementById('event-time');
  if (dateInput) dateInput.value = formatDate(date);
  if (timeInput) timeInput.value = date.getHours()? `${String(date.getHours()).padStart(2,'0')}:00` : '09:00';
  if (eventModal) eventModal.classList.add('active');
}
function closeEventModal(){ if (eventModal) eventModal.classList.remove('active'); if (eventForm) eventForm.reset(); selectedDate=null; editingEventId=null; }

async function saveEvent(e){
  e.preventDefault(); if(!selectedDate) return; const payload=buildEventPayload();
  if(!isAllowedDateTime(new Date(payload.fecha_hora))) { alert('Horario no permitido.'); return; }
  try{
    let res;
    if(editingEventId){
      res = await fetch(`${ROUTES.baseEventos}/${editingEventId}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload)});
    } else {
      res = await fetch(ROUTES.baseEventos,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload)});
    }
    if(!res.ok){ const txt = await res.text(); console.error('Error respuesta servidor', res.status, txt); alert('No se pudo guardar el evento. Revisa la consola para detalles.'); return; }
    closeEventModal(); await loadEventsForCurrentPeriod(); renderCalendar();
  }catch(err){ console.error('Error guardando evento',err); alert('Error guardando evento (red).'); }
}

function buildEventPayload(){
  const dateInput = document.getElementById('event-date');
  const timeInput = document.getElementById('event-time');
  const titleInput = document.getElementById('event-title');
  const durationInput = document.getElementById('event-duration');
  const descInput = document.getElementById('event-description');
  const projSelect = document.getElementById('event-proyecto');
  const partsSelect = document.getElementById('event-participants');
  const dateStr = dateInput ? dateInput.value : formatDate(selectedDate);
  const timeStr = timeInput ? timeInput.value : '09:00';
  const fechaHora = `${dateStr}T${timeStr}:00`;
  const participantes = partsSelect ? Array.from(partsSelect.selectedOptions).map(o=>parseInt(o.value)) : [];
  const idProyecto = projSelect && projSelect.value ? parseInt(projSelect.value) : null;
  return {
    titulo: titleInput ? titleInput.value : '',
    tipo: 'presencial',
    linea_investigacion: '',
    descripcion: descInput ? (descInput.value||null) : null,
    fecha_hora: fechaHora,
    duracion: durationInput ? (parseInt(durationInput.value)||60) : 60,
    ubicacion: 'presencial',
    link_virtual: null,
    recordatorio: 'none',
    id_proyecto: idProyecto,
    participantes
  };
}

function showEventDetails(event){
  editingEventId = event.id;
  document.getElementById('detail-titulo').textContent = event.title || '--';
  document.getElementById('detail-tipo').textContent = event.type || '--';
  document.getElementById('detail-fecha').textContent = formatDateLong(new Date(event.date));
  document.getElementById('detail-hora').textContent = event.time || '--';
  document.getElementById('detail-duracion').textContent = `${event.duration||60} minutos`;
  document.getElementById('detail-ubicacion').textContent = event.location || '--';
  document.getElementById('detail-descripcion').textContent = event.description || '--';
  const linkField = document.getElementById('detail-link-field');
  const linkA = document.getElementById('detail-link');
  const genBtn = document.getElementById('detail-generate-link');
  const delBtn = document.getElementById('detail-delete-link');
  const editorBox = document.getElementById('detail-link-editor');
  const editorInput = document.getElementById('detail-link-input');
  const saveBtn = document.getElementById('detail-save-link');
  const cancelBtnEdit = document.getElementById('detail-cancel-edit');
  if (linkField) linkField.style.display = 'block';
  const hasUrl = !!(event.link && event.link.startsWith('http'));
  if (hasUrl) {
    if (linkA) { linkA.href = event.link; linkA.style.display = 'inline-block'; }
    if (genBtn) genBtn.style.display = 'inline-block';
    if (delBtn) delBtn.style.display = 'inline-block';
    if (editorBox) editorBox.style.display = 'none';
  } else {
    if (linkA) linkA.style.display = 'none';
    if (genBtn) genBtn.style.display = 'inline-block';
    if (delBtn) delBtn.style.display = 'none';
    if (editorBox) editorBox.style.display = 'block';
    if (editorInput) editorInput.value = '';
  }
  if (genBtn) genBtn.onclick = async () => {
    try {
      const res = await fetch(`${ROUTES.baseEventos}/${event.id}/generar-enlace`, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'}, body: JSON.stringify({ plataforma: 'teams' }) });
      if(!res.ok){ const t = await res.text(); console.error('No se pudo generar enlace', t); alert('No se pudo generar el enlace.'); return; }
      const data = await res.json();
      const url = data.link || data.enlace || data.url || null;
      if(url){ if (linkA) { linkA.href = url; linkA.style.display='inline-block'; } if (delBtn) delBtn.style.display='inline-block'; if (editorBox) editorBox.style.display='none'; }
    }catch(err){ console.error(err); alert('Error generando enlace'); }
  };
  if (saveBtn) saveBtn.onclick = async () => {
    const url = editorInput.value.trim();
    if(!url){ alert('Ingresa un enlace válido'); return; }
    try{
      const res = await fetch(`${ROUTES.baseEventos}/${event.id}`, { method:'PUT', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body: JSON.stringify({ link_virtual: url }) });
      if(!res.ok){ const t = await res.text(); console.error('No se pudo guardar enlace', t); alert('No se pudo guardar el enlace.'); return; }
      if (linkA) { linkA.href = url; linkA.style.display='inline-block'; }
      if (delBtn) delBtn.style.display='inline-block';
      if (editorBox) editorBox.style.display='none';
    }catch(err){ console.error(err); alert('Error guardando enlace'); }
  };
  if (cancelBtnEdit) cancelBtnEdit.onclick = () => { if (editorBox) editorBox.style.display='none'; };
  if (delBtn) delBtn.onclick = async () => {
    if(!confirm('¿Eliminar el enlace de la reunión?')) return;
    try{
      const res = await fetch(`${ROUTES.baseEventos}/${event.id}`, { method:'PUT', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body: JSON.stringify({ link_virtual: null }) });
      if(!res.ok){ const t = await res.text(); console.error('No se pudo eliminar enlace', t); alert('No se pudo eliminar el enlace.'); return; }
      if (linkA) linkA.style.display='none';
      if (delBtn) delBtn.style.display='none';
      if (editorBox) editorBox.style.display='block';
      if (editorInput) editorInput.value='';
    }catch(err){ console.error(err); alert('Error eliminando enlace'); }
  };
  openDetailDrawer();
}

function openDetailDrawer(){ detailOverlay.style.display='block'; detailDrawer.style.display='block'; }
function closeDetailDrawer(){ detailOverlay.style.display='none'; detailDrawer.style.display='none'; }

function editEvent(event){
  selectedDate=new Date(event.date+'T'+event.time); editingEventId=event.id; modalTitle.textContent='Editar Reunión'; if (deleteBtn) deleteBtn.style.display='block';
  const dateInput = document.getElementById('event-date');
  const timeInput = document.getElementById('event-time');
  const titleInput = document.getElementById('event-title');
  const durationInput = document.getElementById('event-duration');
  const descInput = document.getElementById('event-description');
  if (dateInput) dateInput.value = event.date;
  if (timeInput) timeInput.value = event.time;
  if (titleInput) titleInput.value = event.title;
  if (durationInput) durationInput.value = event.duration;
  if (descInput) descInput.value = event.description||'';
  if (eventModal) eventModal.classList.add('active');
}

function editFromDetail(){ const event=events.find(e=>e.id===editingEventId); if(event){ closeDetailDrawer(); editEvent(event); } }

async function deleteEvent(){
  if(!editingEventId) return; if(!confirm('¿Estás seguro de que quieres eliminar esta reunión?')) return;
  try{
    const res = await fetch(`${ROUTES.baseEventos}/${editingEventId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    if(!res.ok){ const txt = await res.text(); console.error('Error eliminando', res.status, txt); alert('No se pudo eliminar.'); return; }
    closeEventModal(); closeDetailDrawer(); await loadEventsForCurrentPeriod(); renderCalendar();
  }catch(err){ console.error('Error eliminando evento',err); alert('Error eliminando (red).'); }
}

async function updateEventOnServer(id,newDateStr,newTime){
  const fechaHora=`${newDateStr}T${newTime}:00`;
  try{
    const res = await fetch(`${ROUTES.baseEventos}/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({fecha_hora:fechaHora})});
    if(!res.ok){ const txt = await res.text(); console.error('Error al actualizar (drop)', res.status, txt); alert('No se pudo mover el evento.'); }
  }catch(err){ console.error('Error actualizando evento',err); }
}

function navigatePeriod(direction){ if(currentView==='month'){ currentDate.setMonth(currentDate.getMonth()+direction); } else if(currentView==='week'){ currentDate.setDate(currentDate.getDate()+(direction*7)); } else { currentDate.setDate(currentDate.getDate()+direction); } }
function goToToday(){ currentDate = new Date(); }
function updatePeriodDisplay(){ const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; if(currentView==='month'){ currentPeriodEl.textContent=`${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } else if(currentView==='week'){ const ws=getWeekStart(currentDate); const we=new Date(ws); we.setDate(ws.getDate()+6); currentPeriodEl.textContent=`${ws.getDate()} ${months[ws.getMonth()]} - ${we.getDate()} ${months[we.getMonth()]} ${we.getFullYear()}`; } else { currentPeriodEl.textContent=`${currentDate.getDate()} de ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } }
function getWeekStart(date){ const d=new Date(date); const day=d.getDay(); const diff=d.getDate()-day; return new Date(d.setDate(diff)); }
function formatDate(date){ return date.toISOString().split('T')[0]; }
function formatDateLong(date){ const days=['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado']; const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; return `${days[date.getDay()]}, ${date.getDate()} de ${months[date.getMonth()]} ${date.getFullYear()}`; }
function getDayName(date){ const days=['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; return days[date.getDay()]; }
function formatHour(hour){ const period = hour>=12? 'PM':'AM'; const displayHour = hour>12? hour-12: hour; return `${displayHour}:00 ${period}`; }
function isToday(date){ const t=new Date(); return date.getDate()===t.getDate() && date.getMonth()===t.getMonth() && date.getFullYear()===t.getFullYear(); }
function isAllowedDateTime(dt){ const d=dt.getDay(); if(d===0||d===6) return false; const dateStr=formatDate(dt); if(HOLIDAYS.includes(dateStr)) return false; const hm=dt.getHours()*100 + dt.getMinutes(); if(hm<800 || hm>1650) return false; if(hm>=1200 && hm<=1355) return false; return true; }
</script>
@endsection
