@extends('layouts.aprendiz')

@push('styles')
@php($v = time())
<link rel="stylesheet" href="{{ asset('css/common/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario-views.css') }}?v={{ $v }}">
@endpush

@section('content')
<div class="container" style="padding:8px 0; max-width:1100px;">
    <div class="header">
        <div class="header-top">
            <h1>📅 Mi Calendario (Aprendiz)</h1>
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

<!-- Drawer lateral de Detalle de Reunión (solo lectura) -->
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
</aside>

<!-- Toasts de Calendario -->
<div id="cal-toast-container" class="cal-toast-container" aria-live="polite" aria-atomic="true"></div>

<script>
// Datos: el controlador debe pasar $reuniones (colección) con al menos: titulo, fecha_inicio, fecha_fin, tipo, ubicacion, descripcion, link_virtual, duracion
const REUNIONES = @json(($reuniones ?? collect())->values());
const PARTICIPANTS_MAP = @json(isset($participantesMapa) ? $participantesMapa : []);

// Estado
let currentDate = new Date();
let currentView = 'month';
let events = [];
let draggedEvent = null; // solo para animación; no hay reordenamiento en aprendiz

const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const todayBtn = document.getElementById('todayBtn');
const currentPeriodEl = document.getElementById('currentPeriod');
const daysGrid = document.getElementById('daysGrid');
const detailDrawer = document.getElementById('event-detail-drawer');
const detailOverlay = document.getElementById('event-detail-overlay');
const drawerCloseBtn = document.getElementById('drawer-close-btn');
const drawerCloseCta = document.getElementById('drawer-close-cta');

const monthView = document.getElementById('monthView');
const weekView = document.getElementById('weekView');
const dayView = document.getElementById('dayView');
const viewBtns = document.querySelectorAll('.view-btn');

// Horario permitido: 08:00 a 16:50 (coincide con líder para visualización)
const workHours = Array.from({ length: 9 }, (_, i) => i + 8); // 8..16
let HOLIDAYS = @json(config('app.feriados', []));

async function ensureHolidaysForYear(year){
  try{
    if (ensureHolidaysForYear._year === year) return;
    const url = new URL(`/api/holidays/${year}`, window.location.origin);
    url.searchParams.set('country','CO');
    const res = await fetch(url.toString(), { headers: { 'Accept':'application/json' } });
    if (res.ok){
      const data = await res.json();
      if (Array.isArray(data?.dates) && data.dates.length){ HOLIDAYS = data.dates; ensureHolidaysForYear._year = year; }
    }
  }catch(_){ /* usa fallback local */ }
}

initCalendar();
async function initCalendar(){ await ensureHolidaysForYear(currentDate.getFullYear()); mapReunionesToEvents(); renderCalendar(); }

prevBtn.addEventListener('click', async () => { navigatePeriod(-1); await ensureHolidaysForYear(currentDate.getFullYear()); renderCalendar(); });
nextBtn.addEventListener('click', async () => { navigatePeriod(1); await ensureHolidaysForYear(currentDate.getFullYear()); renderCalendar(); });
todayBtn.addEventListener('click', async () => { goToToday(); await ensureHolidaysForYear(currentDate.getFullYear()); renderCalendar(); });
drawerCloseBtn.addEventListener('click', closeDetailDrawer);
drawerCloseCta.addEventListener('click', closeDetailDrawer);

viewBtns.forEach(btn => { btn.addEventListener('click', async () => { viewBtns.forEach(b => b.classList.remove('active')); btn.classList.add('active'); currentView = btn.dataset.view; renderCalendar(); }); });

enumMap = { planificacion:'Planificación', seguimiento:'Seguimiento', revision:'Revisión', capacitacion:'Capacitación', general:'General' };

function mapReunionesToEvents(){
  try{
    events = (REUNIONES||[]).map(r=>{
      // intentamos varios nombres de campos para robustez
      const titulo = r.titulo || r.nombre || 'Reunión';
      const tipo = r.tipo || 'general';
      const ubicacion = r.ubicacion || '';
      const descripcion = r.descripcion || '';
      const link = r.link_virtual || '';
      const dur = parseInt(r.duracion||0,10) || guessDuration(r);
      // fecha/hora: usar fecha_inicio/fecha_fin o fecha_hora + duracion
      let startISO = '';
      if (r.fecha_inicio) {
        startISO = String(r.fecha_inicio);
      } else if (r.fecha_hora) {
        startISO = String(r.fecha_hora);
      }
      // normalizar a yyyy-mm-ddTHH:MM:SS
      const start = new Date(startISO);
      const datePart = isNaN(start.getTime()) ? '' : formatDateLocal(start);
      const timePart = isNaN(start.getTime()) ? '09:00' : `${String(start.getHours()).padStart(2,'0')}:${String(start.getMinutes()).padStart(2,'0')}`;
      // participantes (nombres si vienen, si no mapear por id)
      let participantsIds = [];
      let participantsNames = [];
      const p = r.participantes || r.aprendices || [];
      if (Array.isArray(p)){
        if (p.length && typeof p[0] === 'string') {
          participantsNames = p;
        } else if (p.length && typeof p[0] === 'number') {
          participantsIds = p; participantsNames = p.map(id=> PARTICIPANTS_MAP?.[id] || `Aprendiz #${id}`);
        } else if (p.length && typeof p[0] === 'object') {
          participantsIds = p.map(o=> parseInt(o.id_aprendiz ?? o.id ?? o.aprendiz_id ?? o.user_id)).filter(n=>!Number.isNaN(n));
          participantsNames = p.map(o=> String(o.nombre_completo ?? o.nombre ?? o.name ?? '').trim()).filter(Boolean);
        }
      }
      return { id: r.id_evento || r.id || null, date: datePart, time: timePart, title: titulo, duration: dur||60, type: tipo, location: ubicacion, description: descripcion, link, participantsIds, participantsNames };
    });
  }catch(e){ console.error('mapReunionesToEvents error', e); events = []; }
}

function guessDuration(r){
  try{
    if (r.fecha_inicio && r.fecha_fin){
      const a = new Date(r.fecha_inicio).getTime();
      const b = new Date(r.fecha_fin).getTime();
      if (!Number.isNaN(a) && !Number.isNaN(b) && b>a) return Math.round((b-a)/60000);
    }
  }catch(_){ }
  return 60;
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
  const isPastDay = isPastDate(date);
  if (isHoliday) { cell.classList.add('festivo'); }
  if (isWeekend || isHoliday || isPastDay) {
    cell.classList.add('disabled');
  } else {
    // Sin creación para aprendiz: clic solo abre detalle si hay evento
  }
  return cell;
}

function renderWeekView(){
  monthView.style.display='none'; weekView.classList.add('active'); dayView.classList.remove('active');
  const weekStart=getWeekStart(currentDate); const weekDays=Array.from({length:7},(_,i)=>{ const d=new Date(weekStart); d.setDate(weekStart.getDate()+i); return d; });
  const weekHeader=document.getElementById('weekHeader'); weekHeader.innerHTML='<div class="week-time-label">Hora</div>';
  weekDays.forEach(date=>{ const header=document.createElement('div'); header.className='week-day-header'; if(isToday(date)) header.classList.add('today'); if (HOLIDAYS.includes(formatDate(date))) header.classList.add('festivo'); header.innerHTML=`<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`; weekHeader.appendChild(header); });
  const weekGrid=document.getElementById('weekGrid'); weekGrid.innerHTML=''; const timesCol=document.createElement('div'); timesCol.className='week-times';
  workHours.forEach(hour=>{ const tl=document.createElement('div'); tl.className='time-slot-label'; tl.textContent=formatHour(hour); timesCol.appendChild(tl); }); weekGrid.appendChild(timesCol);
  weekDays.forEach(date=>{ const dayCol=document.createElement('div'); dayCol.className='week-day-column'; const dateStr=formatDate(date); if (HOLIDAYS.includes(dateStr)) dayCol.classList.add('festivo');
    workHours.forEach(hour=>{ const slot=document.createElement('div'); slot.className='week-time-slot'; slot.dataset.date=dateStr; slot.dataset.hour=hour;
      const hm = hour*100; const isLunch = hm>=1200 && hm<=1355; const slotDt = new Date(`${dateStr}T${String(hour).padStart(2,'0')}:00`); const allowed = hm>=800 && hm<=1650 && !isLunch; // sin validación de pasado
      const occupied = isHourOccupied(dateStr, hour);
      if (!allowed || occupied) { slot.classList.add('disabled'); if (occupied) slot.classList.add('occupied'); }
      dayCol.appendChild(slot); });
    events.filter(e=>e.date===dateStr).forEach(event=>{ dayCol.appendChild(createWeekEventElement(event)); }); weekGrid.appendChild(dayCol);
  });
}

function renderDayView(){
  monthView.style.display='none'; weekView.classList.remove('active'); dayView.classList.add('active'); const dateStr=formatDate(currentDate);
  const dayHeader=document.getElementById('dayHeader'); dayHeader.innerHTML=`<div></div><div class="day-header-info"><div class="day-header-name">${getDayName(currentDate)}</div><div class="day-header-date">${currentDate.getDate()}</div></div>`;
  const dayGrid=document.getElementById('dayGrid'); dayGrid.innerHTML=''; const timesCol=document.createElement('div'); timesCol.className='day-times';
  workHours.forEach(hour=>{ const tl=document.createElement('div'); tl.className='day-time-label'; tl.textContent=formatHour(hour); timesCol.appendChild(tl); }); dayGrid.appendChild(timesCol);
  const slotsCol=document.createElement('div'); slotsCol.className='day-slots';
  workHours.forEach(hour=>{ const slot=document.createElement('div'); slot.className='day-time-slot'; slot.dataset.date=dateStr; slot.dataset.hour=hour; slotsCol.appendChild(slot); });
  events.filter(e=>e.date===dateStr).forEach(event=>{ slotsCol.appendChild(createDayEventElement(event)); }); dayGrid.appendChild(slotsCol);
}

function createEventElement(event){ const el=document.createElement('div'); el.className='event'; el.draggable=false; el.dataset.id=event.id; el.innerHTML=`<div class="event-time">${event.time}</div><div class="event-title">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); return el; }
function createWeekEventElement(event){ const el=document.createElement('div'); el.className='week-event'; el.draggable=false; el.dataset.id=event.id; const [h]=event.time.split(':').map(Number); const top=(h-8)*60+4; const height=Math.max(20, ((event.duration||60)/60*60) -8); el.style.top=`${top}px`; el.style.height=`${height}px`; el.innerHTML=`<div style="font-weight:600;">${event.time}</div><div style="font-size:10px;">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); return el; }
function createDayEventElement(event){ const el=document.createElement('div'); el.className='day-event'; el.draggable=false; el.dataset.id=event.id; const [h]=event.time.split(':').map(Number); const top=(h-8)*80+4; const height=Math.max(24, ((event.duration||60)/60*80) -8); el.style.top=`${top}px`; el.style.height=`${height}px`; el.innerHTML=`<div style="font-weight:600;font-size:13px;">${event.time}</div><div style="font-size:12px;margin-top:2px;">${event.title}</div>`; el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); }); return el; }

function showEventDetails(event){
  document.getElementById('detail-titulo').textContent = event.title || '--';
  document.getElementById('detail-tipo').textContent = (enumMap[event.type]||event.type||'--');
  document.getElementById('detail-fecha').textContent = formatDateLong(new Date(event.date));
  document.getElementById('detail-hora').textContent = event.time || '--';
  document.getElementById('detail-duracion').textContent = formatDurationMinutes(event.duration||60);
  document.getElementById('detail-ubicacion').textContent = event.location || '--';
  document.getElementById('detail-descripcion').textContent = event.description || '--';
  const linkField = document.getElementById('detail-link-field');
  const linkA = document.getElementById('detail-link');
  const isVirtualMeeting = String(event.location||'').toLowerCase()==='virtual';
  if (linkField) linkField.style.display = isVirtualMeeting ? 'block' : 'none';
  if (isVirtualMeeting){
    const hasUrl = !!(event.link && String(event.link).startsWith('http'));
    if (hasUrl) { if (linkA){ linkA.href = event.link; linkA.style.display='inline-block'; } }
    else { if (linkA){ linkA.removeAttribute('href'); linkA.style.display='none'; } }
  }
  // Participantes: chips con nombres
  const chips = document.getElementById('detail-participantes');
  if (chips){
    const names = (event.participantsNames && event.participantsNames.length)
      ? event.participantsNames
      : (event.participantsIds||[]).map(id => PARTICIPANTS_MAP?.[id] || `Aprendiz #${id}`);
    chips.innerHTML = names.length ? names.map(n=>`<span class="chip">${n}</span>`).join(' ') : '--';
  }
  openDetailDrawer();
}

function openDetailDrawer(){ detailOverlay.style.display='block'; detailDrawer.style.display='block'; }
function closeDetailDrawer(){ detailOverlay.style.display='none'; detailDrawer.style.display='none'; }

function navigatePeriod(direction){ if(currentView==='month'){ currentDate.setMonth(currentDate.getMonth()+direction); } else if(currentView==='week'){ currentDate.setDate(currentDate.getDate()+(direction*7)); } else { currentDate.setDate(currentDate.getDate()+direction); } }
function goToToday(){ currentDate = new Date(); }
function updatePeriodDisplay(){ const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; if(currentView==='month'){ currentPeriodEl.textContent=`${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } else if(currentView==='week'){ const ws=getWeekStart(currentDate); const we=new Date(ws); we.setDate(ws.getDate()+6); currentPeriodEl.textContent=`${ws.getDate()} ${months[ws.getMonth()]} - ${we.getDate()} ${months[we.getMonth()]} ${we.getFullYear()}`; } else { currentPeriodEl.textContent=`${currentDate.getDate()} de ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } }
function getWeekStart(date){ const d=new Date(date); const day=d.getDay(); const diff=d.getDate()-day; return new Date(d.setDate(diff)); }
function formatDateLocal(date){ const y = date.getFullYear(); const m = String(date.getMonth()+1).padStart(2,'0'); const d = String(date.getDate()).padStart(2,'0'); return `${y}-${m}-${d}`; }
function formatDate(date){ return formatDateLocal(date); }
function formatDateLong(date){ const days=['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado']; const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; return `${days[date.getDay()]}, ${date.getDate()} de ${months[date.getMonth()]} ${date.getFullYear()}`; }
function getDayName(date){ const days=['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; return days[date.getDay()]; }
function formatHour(hour){ const period = hour>=12? 'PM':'AM'; const displayHour = hour>12? hour-12: hour; return `${displayHour}:00 ${period}`; }
function isToday(date){ const t=new Date(); return date.getDate()===t.getDate() && date.getMonth()===t.getMonth() && date.getFullYear()===t.getFullYear(); }
function isPastDate(date){ const today = new Date(); const d = new Date(date.getFullYear(), date.getMonth(), date.getDate()); const t = new Date(today.getFullYear(), today.getMonth(), today.getDate()); return d.getTime() < t.getTime(); }
function isHourOccupied(dateStr, hour){ return events.some(ev=> ev.date===dateStr && parseInt((ev.time||'09:00').split(':')[0])===hour); }
function formatDurationMinutes(min){ const m = parseInt(min||0,10); const h = Math.floor(m/60); const rest = m % 60; if(h<=0) return `${m} min`; if(rest===0) return h===1? '1 hora' : `${h} horas`; return `${h} h ${rest} min`; }
</script>
@endsection
