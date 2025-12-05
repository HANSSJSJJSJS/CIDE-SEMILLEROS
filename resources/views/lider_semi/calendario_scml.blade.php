@extends('layouts.lider_semi')

@push('styles')
@php($v = time())
<link rel="stylesheet" href="{{ asset('css/common/calendario.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendario-views.css') }}?v={{ $v }}">
<link rel="stylesheet" href="{{ asset('css/common/calendar-month.css') }}?v={{ $v }}">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container calendar-scml">
    <div class="cal-hero">
        <h2>Calendario de Actividades</h2>
        <p>Programa y gestiona reuniones y eventos con tus semilleros</p>
    </div>
    <div class="header">
        <div class="header-top">
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

<!-- Modal para agregar/editar eventos - Wizard paso a paso -->
<div class="modal-calendario" id="event-modal">
    <div class="modal-content-calendario wizard-modal">
        <div class="modal-header-calendario">
            <div class="modal-header-icon">
                <div class="form-icon-modal">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div>
                    <h3 class="modal-title-calendario" id="modal-title">Nueva Reunión de Semillero</h3>
                    <p class="modal-subtitle">Completa los siguientes pasos para programar tu reunión</p>
                </div>
            </div>
            <button class="close-modal-calendario" id="close-modal">&times;</button>
        </div>

        <!-- Indicador de pasos -->
        <div class="wizard-steps">
            <div class="wizard-step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Información Básica</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Configuración</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Detalles</div>
            </div>
        </div>

        <form id="event-form">
            @csrf
            <input type="hidden" id="event-id">
            <input type="hidden" id="event-date">
            <input type="hidden" id="event-time">

            <!-- PASO 1: Información Básica -->
            <div class="wizard-content" id="step-1" style="display: block;">
                <div class="step-header">
                    <h4><i class="fas fa-info-circle"></i> Información Básica de la Reunión</h4>
                    <p class="text-muted">Completa los datos principales de tu reunión</p>
                </div>

                <div class="form-group-calendario">
                    <label for="event-title">Título de la reunión <span class="required">*</span></label>
                    <input type="text" id="event-title" name="titulo" class="form-control-calendario" placeholder="Ej: Reunión Semillero IA" required>
                </div>

                <div class="form-group-calendario">
                    <label for="event-type">Tipo de reunión <span class="required">*</span></label>
                    <select id="event-type" name="tipo" class="form-control-calendario" required>
                        <option value="">Seleccione el tipo</option>
                        <option value="planificacion">Planificación de proyecto</option>
                        <option value="seguimiento">Seguimiento proyecto</option>
                        <option value="revision">Revisión de avances</option>
                        <option value="entrega">Entrega de proyecto</option>
                        <option value="capacitacion">Capacitación técnica</option>
                        <option value="general">Reunión general</option>
                    </select>
                </div>

                <div class="form-grid-2">
                    <div class="form-group-calendario">
                        <label for="event-duration">Duración <span class="required">*</span></label>
                        <select id="event-duration" name="duracion" class="form-control-calendario" required>
                            <option value="30">30 minutos</option>
                            <option value="60" selected>1 hora</option>
                            <option value="90">1 hora 30 minutos</option>
                            <option value="120">2 horas</option>
                            <option value="180">3 horas</option>
                            <option value="240">4 horas</option>
                        </select>
                    </div>

                    <div class="form-group-calendario">
                        <label>Hora seleccionada</label>
                        <div class="selected-time-display" id="selected-time-display">
                            <i class="fas fa-clock"></i>
                            <span id="display-time">--:-- --</span>
                        </div>
                    </div>
                </div>

                <div class="form-group-calendario">
                    <label for="event-location">Ubicación <span class="required">*</span></label>
                    <select id="event-location" name="ubicacion" class="form-control-calendario" required>
                        <option value="">Seleccione ubicación</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Reunión Virtual</option>
                        <option value="otra">Otra ubicación</option>
                    </select>
                </div>
            </div>

            <!-- PASO 2: Configuración -->
            <div class="wizard-content" id="step-2" style="display: none;">
                <div class="step-header">
                    <h4><i class="fas fa-cog"></i> Configuración de la Reunión</h4>
                    <p class="text-muted">Selecciona el proyecto y los participantes</p>
                </div>

                <div class="form-group-calendario">
                    <label for="event-proyecto">Proyecto Asociado</label>
                    <select id="event-proyecto" class="form-control-calendario">
                        <option value="">Sin proyecto asignado</option>
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id_proyecto }}" data-aprendices='@json($proyecto->aprendices->pluck("id_aprendiz"))'>
                                {{ $proyecto->nombre_proyecto }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">Al seleccionar un proyecto, se mostrarán sus aprendices asignados</small>
                </div>

                <div class="form-section-calendario">
                    <div class="participants-header">
                        <h4 class="section-title-modal">Participantes <span class="required">*</span></h4>
                        <button type="button" class="btn-select-all" id="select-all-participants">
                            <i class="fas fa-users"></i> Seleccionar Todos
                        </button>
                    </div>

                    <div class="participants-filter">
                        <input type="text" id="search-participants" class="form-control-calendario" placeholder="Buscar aprendiz...">
                    </div>

                    <div class="participants-container-modal" id="participants-list">
                        @foreach($aprendices as $aprendiz)
                        @php($fullName = trim(($aprendiz->nombres ?? '').' '.($aprendiz->apellidos ?? '')))
                        <div class="participant-item-modal" data-aprendiz-id="{{ $aprendiz->id_aprendiz }}" data-aprendiz-name="{{ strtolower($fullName ?: ($aprendiz->nombre_completo ?? '')) }}" data-doc-type="{{ strtolower($aprendiz->tipo_documento ?? '') }}" data-doc="{{ strtolower($aprendiz->documento ?? '') }}">
                            <input type="checkbox" id="participant-{{ $aprendiz->id_aprendiz }}"
                                   class="participant-checkbox-modal"
                                   value="{{ $aprendiz->id_aprendiz }}">
                            <label for="participant-{{ $aprendiz->id_aprendiz }}" class="participant-info-modal">
                                <div class="participant-name-modal">{{ $fullName ?: ($aprendiz->nombre_completo ?? 'Aprendiz') }}</div>
                                <div class="participant-role-modal">Aprendiz - Semillero</div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="participants-count">
                        <i class="fas fa-user-check"></i>
                        <span id="selected-count">0</span> participante(s) seleccionado(s)
                    </div>
                </div>
            </div>

            <!-- PASO 3: Detalles -->
            <div class="wizard-content" id="step-3" style="display: none;">
                <div class="step-header">
                    <h4><i class="fas fa-file-alt"></i> Detalles de la Reunión</h4>
                    <p class="text-muted">Agrega información adicional y recordatorios</p>
                </div>

                <div class="form-group-calendario">
                    <label for="event-description">Descripción de la reunión</label>
                    <textarea id="event-description" class="form-control-calendario" rows="5" placeholder="Describe los temas a tratar, objetivos de la reunión, materiales necesarios, etc."></textarea>
                    <small class="form-help">Esta información será visible para todos los participantes</small>
                </div>

                <div class="form-section-calendario">
                    <h4 class="section-title-modal">Recordatorios</h4>
                    <p class="text-muted small">Selecciona cuándo deseas recibir un recordatorio</p>
                    <div class="notification-options-modal">
                        <div class="notification-option-modal selected" data-value="none">
                            <input type="radio" name="reminder" id="reminder-none" value="none" checked>
                            <label for="reminder-none">
                                <i class="far fa-bell-slash"></i>
                                <span>Sin recordatorio</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="15">
                            <input type="radio" name="reminder" id="reminder-15min" value="15">
                            <label for="reminder-15min">
                                <i class="far fa-clock"></i>
                                <span>15 minutos antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="30">
                            <input type="radio" name="reminder" id="reminder-30min" value="30">
                            <label for="reminder-30min">
                                <i class="far fa-clock"></i>
                                <span>30 minutos antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="60">
                            <input type="radio" name="reminder" id="reminder-1h" value="60">
                            <label for="reminder-1h">
                                <i class="far fa-clock"></i>
                                <span>1 hora antes</span>
                            </label>
                        </div>
                        <div class="notification-option-modal" data-value="1440">
                            <input type="radio" name="reminder" id="reminder-1d" value="1440">
                            <label for="reminder-1d">
                                <i class="far fa-calendar"></i>
                                <span>1 día antes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Resumen de la reunión -->
                <div class="meeting-summary">
                    <h4><i class="fas fa-clipboard-check"></i> Resumen de la Reunión</h4>
                    <div class="summary-item">
                        <strong>Título:</strong> <span id="summary-title">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Tipo:</strong> <span id="summary-type">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Fecha y Hora:</strong> <span id="summary-datetime">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Duración:</strong> <span id="summary-duration">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Ubicación:</strong> <span id="summary-location">--</span>
                    </div>
                    <div class="summary-item">
                        <strong>Participantes:</strong> <span id="summary-participants">--</span>
                    </div>
                </div>
            </div>

            <!-- Botones de navegación del wizard -->
            <div class="wizard-actions">
                <button type="button" class="btn-calendario btn-secondary-calendario" id="btn-prev-step" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn-calendario btn-secondary-calendario" id="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-calendario btn-primary-calendario" id="btn-next-step">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-calendario btn-success-calendario" id="btn-submit" style="display: none;">
                    <i class="fas fa-check"></i> Programar Reunión
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
        <section class="drawer-section" id="detail-asistencia-section" style="display:none;">
            <h4 class="drawer-section-title">Participantes</h4>
            <p class="text-muted" style="font-size:12px;">Marca si el participante asistió o no a la reunión virtual.</p>
            <div id="detail-asistencia-list" class="attendance-list"></div>
            <div id="detail-asistencia-summary" class="attendance-summary" style="margin-top:16px; display:none;"></div>
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
// Estado
let currentDate = new Date();
let currentView = 'month';
let events = [];
let selectedDate = null;
let editingEventId = null;
let draggedEvent = null;

// Helper: formatea 'YYYY-MM-DD' en largo, evitando new Date('YYYY-MM-DD') (UTC)
if (typeof formatDateLongLocalFromYMD !== 'function') {
  function formatDateLongLocalFromYMD(ymd){
    if(!ymd) return '--';
    const [y,m,d] = String(ymd).split('-').map(Number);
    const dt = new Date(y, (m||1)-1, d||1, 12, 0, 0, 0); // mediodía local
    return dt.toLocaleDateString('es-CO', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
  }
}

// Rutas backend
const ROUTES = {
  obtener: "{{ route('lider_semi.eventos.obtener', [], false) }}",
  baseEventos: "{{ route('lider_semi.eventos.crear', [], false) }}",
  asistenciaTemplate: "{{ route('lider_semi.eventos.participantes.asistencia', ['evento' => '__EID__', 'aprendiz' => '__AID__'], false) }}"
};
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const CURRENT_USER_ID = {{ Auth::id() }};
// Mapa id->nombre de aprendices para mostrar participantes en el detalle
const PARTICIPANTS_MAP = @json(isset($aprendices) ? $aprendices->pluck('nombre_completo','id_aprendiz') : []);

// Horario permitido: 08:00 a 16:50 (sin almuerzo 12:00-13:55)
const workHours = Array.from({ length: 9 }, (_, i) => i + 8); // 8..16
let HOLIDAYS = @json(config('app.feriados', []));

const HOLIDAYS_LOADED = new Set();
async function fetchHolidaysYear(year){
  try{
    const url = new URL(`/api/holidays/${year}`, window.location.origin);
    url.searchParams.set('country','CO');
    const res = await fetch(url.toString(), { headers: { 'Accept':'application/json' } });
    if (!res.ok) return [];
    const data = await res.json();
    return Array.isArray(data?.dates) ? data.dates : [];
  }catch(_){ return []; }
}
async function ensureHolidaysForYear(year){
  const targets = [year-1, year, year+1];
  const missing = targets.filter(y => !HOLIDAYS_LOADED.has(y));
  if (missing.length === 0) return;
  const union = new Set(HOLIDAYS);
  const results = await Promise.all(missing.map(y => fetchHolidaysYear(y)));
  results.forEach((dates, idx) => {
    const y = missing[idx];
    if (Array.isArray(dates) && dates.length){ dates.forEach(d=>union.add(d)); HOLIDAYS_LOADED.add(y); }
  });
  HOLIDAYS = Array.from(union);
}

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
const wizardCancelBtn = document.getElementById('btn-cancel');
const wizardNextBtn = document.getElementById('btn-next-step');
const wizardPrevBtn = document.getElementById('btn-prev-step');
const wizardSubmitBtn = document.getElementById('btn-submit');
const drawerCloseCta = document.getElementById('drawer-close-cta');
const deleteBtn = document.getElementById('deleteBtn');
const selectedDateDisplay = document.getElementById('selectedDateDisplay');
const modalTitle = document.getElementById('modal-title');

const monthView = document.getElementById('monthView');
const weekView = document.getElementById('weekView');
const dayView = document.getElementById('dayView');
const viewBtns = document.querySelectorAll('.view-btn');

initCalendar();
async function initCalendar(){ await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); }

prevBtn.addEventListener('click', async () => { navigatePeriod(-1); await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
nextBtn.addEventListener('click', async () => { navigatePeriod(1); await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
todayBtn.addEventListener('click', async () => { goToToday(); await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
if (closeModal) closeModal.addEventListener('click', closeEventModal);
drawerCloseBtn.addEventListener('click', closeDetailDrawer);
drawerCloseCta.addEventListener('click', closeDetailDrawer);
if (cancelBtn) cancelBtn.addEventListener('click', closeEventModal);
if (wizardCancelBtn) wizardCancelBtn.addEventListener('click', closeEventModal);
if (wizardNextBtn) wizardNextBtn.addEventListener('click', ()=> goToStep(currentWizardStep+1));
if (wizardPrevBtn) wizardPrevBtn.addEventListener('click', ()=> goToStep(currentWizardStep-1));
if (deleteBtn) deleteBtn.addEventListener('click', deleteEvent);
if (eventForm) eventForm.addEventListener('submit', saveEvent);

viewBtns.forEach(btn => { btn.addEventListener('click', async () => { viewBtns.forEach(b => b.classList.remove('active')); btn.classList.add('active'); currentView = btn.dataset.view; await loadEventsForCurrentPeriod(); renderCalendar(); }); });

if (eventModal) eventModal.addEventListener('click', (e) => { if (e.target === eventModal) closeEventModal(); });
detailOverlay.addEventListener('click', closeDetailDrawer);

// Wizard state
let currentWizardStep = 1;
const wizardSteps = document.querySelectorAll('.wizard-step');
const wizardContents = [
  document.getElementById('step-1'),
  document.getElementById('step-2'),
  document.getElementById('step-3')
];

// Marcar cuáles campos son realmente requeridos y alternar según el paso visible
document.querySelectorAll('.wizard-content [required]').forEach(el=>{ el.dataset.wasRequired = 'true'; });

function goToStep(step){
  if(step<1||step>3) return;
  currentWizardStep = step;
  wizardSteps.forEach(s=> s.classList.toggle('active', parseInt(s.dataset.step,10)===step));
  wizardContents.forEach((c,i)=> c.style.display = (i===step-1)?'block':'none');
  // Alternar required: solo en el paso visible
  wizardContents.forEach((c,i)=>{
    const makeRequired = (i===step-1);
    c.querySelectorAll('[data-was-required="true"]').forEach(el=>{ try{ el.required = makeRequired; }catch(_){} });
  });
  if (wizardPrevBtn) wizardPrevBtn.style.display = step>1? 'inline-flex':'none';
  if (wizardNextBtn) wizardNextBtn.style.display = step<3? 'inline-flex':'none';
  if (wizardSubmitBtn) wizardSubmitBtn.style.display = step===3? 'inline-flex':'none';
  if(step===3) updateSummary();
}

// Participants filtering and select all
const participantsList = document.getElementById('participants-list');
const participantsSearch = document.getElementById('search-participants');
const selectAllBtn = document.getElementById('select-all-participants');
let currentProjectIds = null; // Set<number> | null
function applyParticipantsFilters(){
  const q = (participantsSearch?.value || '').trim().toLowerCase();
  participantsList.querySelectorAll('.participant-item-modal').forEach(item=>{
    const id = parseInt(item.getAttribute('data-aprendiz-id'));
    const name = (item.dataset.aprendizName || '');
    const byProject = !currentProjectIds || currentProjectIds.has(id);
    const bySearch = !q || name.includes(q);
    item.style.display = (byProject && bySearch) ? '' : 'none';
  });
}
if (participantsSearch){
  participantsSearch.addEventListener('input', applyParticipantsFilters);
}
if (selectAllBtn){
  selectAllBtn.addEventListener('click', ()=>{
    const boxes = participantsList.querySelectorAll('.participant-checkbox-modal');
    const allChecked = Array.from(boxes).every(b=>b.checked);
    boxes.forEach(b=>{
      const parent = b.closest('.participant-item-modal');
      if (parent && parent.style.display !== 'none'){
        b.checked = !allChecked;
      }
    });
    updateSelectedCount();
  });
}
participantsList?.addEventListener('change', (e)=>{
  if(e.target.classList.contains('participant-checkbox-modal')) updateSelectedCount();
});
function updateSelectedCount(){
  const count = participantsList ? participantsList.querySelectorAll('.participant-checkbox-modal:checked').length : 0;
  const countEl = document.getElementById('selected-count');
  if (countEl) countEl.textContent = String(count);
}

// Proyecto -> limitar aprendices visibles y marcar asignados
const proyectoSelect = document.getElementById('event-proyecto');
if (proyectoSelect){
  proyectoSelect.addEventListener('change', ()=>{
    const opt = proyectoSelect.selectedOptions[0];
    try{
      const ids = JSON.parse(opt?.getAttribute('data-aprendices')||'[]');
      currentProjectIds = Array.isArray(ids) && ids.length ? new Set(ids.map(n=>parseInt(n))) : null;
      const boxes = participantsList.querySelectorAll('.participant-checkbox-modal');
      boxes.forEach(b=> b.checked = currentProjectIds ? currentProjectIds.has(parseInt(b.value)) : false);
      applyParticipantsFilters();
      updateSelectedCount();
    }catch(_){ /* ignore */ }
  });
}

// Recordatorios radio styled
document.querySelectorAll('.notification-option-modal').forEach(opt=>{
  opt.addEventListener('click', ()=>{
    document.querySelectorAll('.notification-option-modal').forEach(o=> o.classList.remove('selected'));
    opt.classList.add('selected');
    const val = opt.getAttribute('data-value');
    const radio = opt.querySelector('input[type="radio"]');
    if(radio){ radio.checked = true; radio.value = val; }
  });
});

// Ubicación: mostrar/ocultar link virtual
const locationSelect = document.getElementById('event-location');
const virtualGroup = document.getElementById('virtual-link-group');
if (locationSelect){
  locationSelect.addEventListener('change', ()=>{
    const isVirtual = locationSelect.value === 'virtual';
    if (virtualGroup) virtualGroup.style.display = isVirtual ? 'block' : 'none';
  });
}

async function loadEventsForCurrentPeriod(){
  try{
    const mes = currentDate.getMonth() + 1; const anio = currentDate.getFullYear();
    const url = new URL(ROUTES.obtener, window.location.origin);
    url.searchParams.set('mes', mes); url.searchParams.set('anio', anio);
    const res = await fetch(url.toString(), { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } });
    const data = await res.json();
    const list = data.eventos || [];
    events = list.map(ev => {
      const raw = String(ev.fecha_hora||'');
      let datePart = '', timePart = '';
      if (raw.includes('T')){
        const parts = raw.split('T');
        datePart = parts[0];
        timePart = (parts[1]||'').slice(0,5);
      } else if (raw.includes(' ')){
        const parts = raw.split(' ');
        datePart = parts[0];
        timePart = (parts[1]||'').slice(0,5);
      } else {
        // fallback: usar Date pero sin toISOString
        const d = new Date(raw);
        datePart = formatDateLocal(d);
        timePart = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
      }
      // Participantes: aceptar varios formatos desde backend (incluyendo asistencia)
      let participantsIds = [];
      let participantsNames = [];
      let participantsDetails = [];
      try{
        const p = ev.participantes ?? ev.aprendices ?? ev.aprendices_asignados ?? [];
        if (Array.isArray(p)){
          if (p.length && typeof p[0] === 'number') {
            participantsIds = p;
          } else if (p.length && typeof p[0] === 'string') {
            participantsIds = p.map(x=>parseInt(x)).filter(n=>!Number.isNaN(n));
          } else if (p.length && typeof p[0] === 'object') {
            participantsIds = p.map(o=> parseInt(o.id_aprendiz ?? o.id ?? o.aprendiz_id ?? o.user_id)).filter(n=>!Number.isNaN(n));
            participantsNames = p.map(o=> String(o.nombre_completo ?? o.nombre ?? o.name ?? '').trim()).filter(Boolean);
            participantsDetails = p.map(o=> ({
              id: parseInt(o.id_aprendiz ?? o.id ?? o.aprendiz_id ?? o.user_id),
              nombre: String(o.nombre_completo ?? o.nombre ?? o.name ?? '').trim() || `Aprendiz #${o.id_aprendiz ?? o.id ?? ''}`,
              asistencia: String(o.asistencia ?? 'pendiente')
            })).filter(x=>!Number.isNaN(x.id));
          }
        } else if (typeof p === 'string' && p.length){
          try{ const arr = JSON.parse(p); if(Array.isArray(arr)) participantsIds = arr.map(x=>parseInt(x)).filter(n=>!Number.isNaN(n)); }
          catch{ participantsIds = p.split(',').map(x=>parseInt(x)).filter(n=>!Number.isNaN(n)); }
        }
      }catch(_){ /* noop */ }
      if (!participantsNames.length && participantsIds.length){
        participantsNames = participantsIds.map(id => PARTICIPANTS_MAP?.[id] || `Aprendiz #${id}`);
      }
      // incluir identificador de creador y estado para manejar permisos/estilos
      const creatorId = ev.leader_id ?? ev.id_lider ?? ev.id_usuario ?? null;
      const estado = ev.estado ?? 'programada';
      return {
        id: ev.id,
        date: datePart,
        time: timePart||'09:00',
        title: ev.titulo,
        duration: ev.duracion||60,
        type: ev.tipo||'general',
        location: ev.ubicacion||'',
        description: ev.descripcion||'',
        researchLine: ev.linea_investigacion || '',
        link: ev.link_virtual || '',
        participantsIds,
        participantsNames,
        participantsDetails,
        creatorId,
        estado
      };
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
  const isPastDay = isPastDate(date);
  if (isHoliday) { cell.classList.add('festivo'); }
  if (isWeekend || isHoliday || isPastDay) {
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
  weekDays.forEach(date=>{ const header=document.createElement('div'); header.className='week-day-header'; if(isToday(date)) header.classList.add('today'); if (HOLIDAYS.includes(formatDate(date))) header.classList.add('festivo'); header.innerHTML=`<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`; weekHeader.appendChild(header); });
  const weekGrid=document.getElementById('weekGrid'); weekGrid.innerHTML=''; const timesCol=document.createElement('div'); timesCol.className='week-times';
  workHours.forEach(hour=>{ const tl=document.createElement('div'); tl.className='time-slot-label'; tl.textContent=formatHour(hour); timesCol.appendChild(tl); }); weekGrid.appendChild(timesCol);
  weekDays.forEach(date=>{ const dayCol=document.createElement('div'); dayCol.className='week-day-column'; const dateStr=formatDate(date); if (HOLIDAYS.includes(dateStr)) dayCol.classList.add('festivo');
    const isWeekend = date.getDay()===0 || date.getDay()===6; const isHoliday = HOLIDAYS.includes(dateStr);
    workHours.forEach(hour=>{ const slot=document.createElement('div'); slot.className='week-time-slot'; slot.dataset.date=dateStr; slot.dataset.hour=hour;
      const hm = hour*100; const isLunch = hm>=1200 && hm<=1355; const slotDt = new Date(`${dateStr}T${String(hour).padStart(2,'0')}:00`); const allowed = !isWeekend && !isHoliday && hm>=800 && hm<=1650 && !isLunch && !isInPast(slotDt);
      const occupied = isHourOccupied(dateStr, hour);
      if (!allowed || occupied) { slot.classList.add('disabled'); if (occupied) slot.classList.add('occupied'); }
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
    const hm = hour*100; const isLunch = hm>=1200 && hm<=1355; const slotDt = new Date(`${dateStr}T${String(hour).padStart(2,'0')}:00`); const allowed = !isWeekend && !isHoliday && hm>=800 && hm<=1650 && !isLunch && !isInPast(slotDt);
    const occupied = isHourOccupied(dateStr, hour);
    if (!allowed || occupied) { slot.classList.add('disabled'); if (occupied) slot.classList.add('occupied'); }
    else {
      slot.addEventListener('click',()=>{ const eventDate=new Date(currentDate); eventDate.setHours(hour,0,0,0); openEventModal(eventDate); });
      slot.addEventListener('dragover', handleDragOver); slot.addEventListener('drop',(e)=>{ const eventDate=new Date(currentDate); eventDate.setHours(hour,0,0,0); handleDrop(e,eventDate); }); slot.addEventListener('dragleave', handleDragLeave);
    }
    slotsCol.appendChild(slot); });
  events.filter(e=>e.date===dateStr).forEach(event=>{ slotsCol.appendChild(createDayEventElement(event)); }); dayGrid.appendChild(slotsCol);
}

function createEventElement(event){
  const el=document.createElement('div');
  el.className='event';
  el.draggable=true;
  el.dataset.id=event.id;
  el.innerHTML=`<div class="event-time">${event.time}</div><div class="event-title">${event.title}</div>`;
  // Estilo opaco para eventos cancelados
  if (String(event.estado||'').toLowerCase()==='cancelado'){
    el.style.opacity = '0.45';
  }
  el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); });
  el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; });
  el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; });
  return el;
}
function createWeekEventElement(event){
  const el=document.createElement('div');
  el.className='week-event';
  el.draggable=true;
  el.dataset.id=event.id;
  const [h]=event.time.split(':').map(Number);
  const top=(h-8)*60+4;
  const height=Math.max(20, ((event.duration||60)/60*60) -8);
  el.style.top=`${top}px`;
  el.style.height=`${height}px`;
  el.innerHTML=`<div style="font-weight:600;">${event.time}</div><div style="font-size:10px;">${event.title}</div>`;
  if (String(event.estado||'').toLowerCase()==='cancelado'){
    el.style.opacity = '0.45';
  }
  el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); });
  el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; });
  el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; });
  return el;
}
function createDayEventElement(event){
  const el=document.createElement('div');
  el.className='day-event';
  el.draggable=true;
  el.dataset.id=event.id;
  const [h]=event.time.split(':').map(Number);
  const top=(h-8)*80+4;
  const height=Math.max(24, ((event.duration||60)/60*80) -8);
  el.style.top=`${top}px`;
  el.style.height=`${height}px`;
  el.innerHTML=`<div style="font-weight:600;font-size:13px;">${event.time}</div><div style="font-size:12px;margin-top:2px;">${event.title}</div>`;
  if (String(event.estado||'').toLowerCase()==='cancelado'){
    el.style.opacity = '0.45';
  }
  el.addEventListener('click',(e)=>{ e.stopPropagation(); showEventDetails(event); });
  el.addEventListener('dragstart',(e)=>{ draggedEvent=event; el.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; });
  el.addEventListener('dragend',()=>{ el.classList.remove('dragging'); draggedEvent=null; });
  return el;
}

// Indicador flotante de hora durante drag
let dragTimeIndicatorEl=null;
function showDragIndicator(text, clientX, clientY){
  if(!dragTimeIndicatorEl){
    dragTimeIndicatorEl=document.createElement('div');
    dragTimeIndicatorEl.id='drag-time-indicator';
    dragTimeIndicatorEl.style.position='fixed';
    dragTimeIndicatorEl.style.zIndex='9999';
    dragTimeIndicatorEl.style.pointerEvents='none';
    dragTimeIndicatorEl.style.background='rgba(0,0,0,0.75)';
    dragTimeIndicatorEl.style.color='#fff';
    dragTimeIndicatorEl.style.padding='4px 8px';
    dragTimeIndicatorEl.style.borderRadius='6px';
    dragTimeIndicatorEl.style.fontSize='12px';
    document.body.appendChild(dragTimeIndicatorEl);
  }
  dragTimeIndicatorEl.textContent=text;
  dragTimeIndicatorEl.style.left=`${clientX+12}px`;
  dragTimeIndicatorEl.style.top=`${clientY+12}px`;
  dragTimeIndicatorEl.style.display='block';
}
function hideDragIndicator(){ if(dragTimeIndicatorEl){ dragTimeIndicatorEl.style.display='none'; } }

function handleDragOver(e){ e.preventDefault(); e.dataTransfer.dropEffect='move'; e.currentTarget.classList.add('drag-over');
  // Mostrar hora del slot sobre el que se arrastra
  let hour = e.currentTarget.dataset && e.currentTarget.dataset.hour ? parseInt(e.currentTarget.dataset.hour,10) : null;
  if(Number.isInteger(hour)){
    const label = formatHour(hour);
    showDragIndicator(label, e.clientX, e.clientY);
    // Guardamos para uso en drop
    e.currentTarget.dataset.hoverHour = String(hour);
  }
}
function handleDragLeave(e){ e.currentTarget.classList.remove('drag-over'); hideDragIndicator(); if(e.currentTarget.dataset) delete e.currentTarget.dataset.hoverHour; }
async function handleDrop(e,newDate){ e.preventDefault(); e.currentTarget.classList.remove('drag-over'); hideDragIndicator(); if(!draggedEvent) return; const idx=events.findIndex(ev=>ev.id===draggedEvent.id); if(idx===-1) return; const newDateStr=formatDate(newDate); let newTime=events[idx].time; const hAttr = (e.currentTarget.dataset && (e.currentTarget.dataset.hoverHour||e.currentTarget.dataset.hour)) || null; if(hAttr){ const hour=parseInt(hAttr,10); newTime=`${String(hour).padStart(2,'0')}:00`; }
  const candidate = new Date(`${newDateStr}T${newTime}:00`); if(!isAllowedDateTime(candidate)) { showToast('No puedes mover el evento a un horario no permitido o en el pasado.','warning'); return; } if(hasClientConflict(candidate, events[idx].duration, draggedEvent.id)) { showToast('Ese horario ya está ocupado.','warning'); return; }
  if(crossesLunch(candidate, events[idx].duration)) { showToast('No puedes mover la reunión cruzando el horario de almuerzo (12:00 a 13:55).','warning'); return; }
  await updateEventOnServer(events[idx].id,newDateStr,newTime); await loadEventsForCurrentPeriod(); renderCalendar(); }

function openEventModal(date){
  // Antes de abrir, validar si el día tiene todavía horas disponibles entre 8am y 4pm (sin almuerzo)
  const dateOnly = formatDate(date);
  if (isDayFullyBooked(dateOnly)){
    showToast('Este día ya está completamente lleno de reuniones entre 8:00 a 4:00.','warning');
    return;
  }

  const firstHour = getFirstAvailableHour(dateOnly);
  // Si no hay primera hora disponible, no permitir nuevos agendamientos
  if (firstHour === null){
    showToast('Este día no tiene más horarios disponibles para agendar.','warning');
    return;
  }

  const chosenHour = date.getHours() ? date.getHours() : firstHour;
  const normalized = new Date(date); normalized.setHours(chosenHour,0,0,0);
  selectedDate=normalized; editingEventId=null; modalTitle.textContent='Nueva Reunión'; if (deleteBtn) deleteBtn.style.display='none';
  if (eventForm) eventForm.reset();
  const dateInput = document.getElementById('event-date');
  const timeInput = document.getElementById('event-time');
  if (dateInput) dateInput.value = formatDate(normalized);
  if (timeInput) timeInput.value = `${String(chosenHour).padStart(2,'0')}:00`;
  const displayTime = document.getElementById('display-time');
  if (displayTime){
    const h = chosenHour; const period = h>=12? 'PM':'AM'; const hh = h>12? h-12 : h; displayTime.textContent = `${String(hh).padStart(2,'0')}:00 ${period}`;
  }
  enforceDurationOptions();
  currentWizardStep = 1; goToStep(1);
  if (eventModal) eventModal.classList.add('active');
}
function closeEventModal(){ if (eventModal) eventModal.classList.remove('active'); if (eventForm) eventForm.reset(); selectedDate=null; editingEventId=null; }

async function saveEvent(e){
  e.preventDefault(); if(!selectedDate) return; const payload=buildEventPayload();
  const titleEl = document.getElementById('event-title');
  const titleVal = titleEl ? String(titleEl.value||'').trim() : '';
  if(!titleVal){
    showToast('El título es obligatorio.','warning');
    try{ goToStep(1); titleEl?.focus(); }catch(_){/* noop */}
    return;
  }
  const startDt = new Date(payload.fecha_hora);
  if(!isAllowedDateTime(startDt)) { showToast('Horario no permitido o en el pasado.','error'); return; }
  if(crossesLunch(startDt, payload.duracion)) { showToast('La reunión no puede cruzar el horario de almuerzo (12:00 a 13:55).','warning'); return; }
  if(hasClientConflict(startDt, payload.duracion, editingEventId)) { showToast('El horario seleccionado ya está ocupado.','warning'); return; }
  try{
    let res;
    if(editingEventId){
      res = await fetch(`${ROUTES.baseEventos}/${editingEventId}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload)});
    } else {
      res = await fetch(ROUTES.baseEventos,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload)});
    }
    if(!res.ok){ const txt = await res.text(); console.error('Error respuesta servidor', res.status, txt); showToast('No se pudo guardar el evento. Revisa la consola para detalles.','error'); return; }
    closeEventModal(); await loadEventsForCurrentPeriod(); renderCalendar(); showToast('Evento guardado correctamente','success');
  }catch(err){ console.error('Error guardando evento',err); showToast('Error guardando evento (red).','error'); }
}

function buildEventPayload(){
  const dateInput = document.getElementById('event-date');
  const timeInput = document.getElementById('event-time');
  const titleInput = document.getElementById('event-title');
  const typeSelect = document.getElementById('event-type');
  const durationInput = document.getElementById('event-duration');
  const locationSelect2 = document.getElementById('event-location');
  const linkInput = document.getElementById('event-virtual-link');
  const descInput = document.getElementById('event-description');
  const projSelect = document.getElementById('event-proyecto');
  const dateStr = dateInput ? dateInput.value : formatDate(selectedDate);
  const timeStr = timeInput ? timeInput.value : '09:00';
  const fechaHora = `${dateStr}T${timeStr}:00`;
  const participantes = participantsList ? Array.from(participantsList.querySelectorAll('.participant-checkbox-modal:checked')).map(b=>parseInt(b.value)) : [];
  const idProyecto = projSelect && projSelect.value ? parseInt(projSelect.value) : null;
  const ubicacion = locationSelect2 ? (locationSelect2.value||'') : '';
  const link_virtual = (ubicacion==='virtual' && linkInput && linkInput.value.trim()) ? linkInput.value.trim() : null;
  const recordatorio = (document.querySelector('input[name="reminder"]:checked')?.value)||'none';
  return {
    titulo: titleInput ? titleInput.value : '',
    tipo: typeSelect ? (typeSelect.value||'general') : 'general',
    linea_investigacion: '',
    descripcion: descInput ? (descInput.value||null) : null,
    fecha_hora: fechaHora,
    duracion: durationInput ? (parseInt(durationInput.value)||60) : 60,
    ubicacion: ubicacion||'presencial',
    link_virtual,
    recordatorio,
    id_proyecto: idProyecto,
    participantes
  };
}

function updateSummary(){
  const title = document.getElementById('event-title')?.value||'--';
  const type = document.getElementById('event-type')?.selectedOptions[0]?.textContent||'--';
  const dateStr = document.getElementById('event-date')?.value||formatDate(selectedDate||new Date());
  const timeStr = document.getElementById('event-time')?.value||'09:00';
  const durationSel = document.getElementById('event-duration');
  const durationText = durationSel ? durationSel.selectedOptions[0].textContent : '--';
  const locationSel = document.getElementById('event-location');
  const locationText = locationSel ? locationSel.selectedOptions[0].textContent : '--';
  const parts = participantsList ? Array.from(participantsList.querySelectorAll('.participant-checkbox-modal:checked')).length : 0;
  const dt = new Date(`${dateStr}T${timeStr}:00`);
  const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  const hours = dt.getHours(); const period = hours>=12? 'PM':'AM'; const hh = hours>12? hours-12: hours; const pretty = `${dt.getDate()} de ${months[dt.getMonth()]} ${dt.getFullYear()}, ${String(hh).padStart(2,'0')}:${String(dt.getMinutes()).padStart(2,'0')} ${period}`;
  const set = (id,val)=>{ const el=document.getElementById(id); if(el) el.textContent = val; };
  set('summary-title', title);
  set('summary-type', type);
  set('summary-datetime', pretty);
  set('summary-duration', durationText);
  set('summary-location', locationText);
  set('summary-participants', `${parts} seleccionado(s)`);
}

function showEventDetails(event){
  editingEventId = event.id;
  document.getElementById('detail-titulo').textContent = event.title || '--';
  document.getElementById('detail-tipo').textContent = event.type || '--';
  // Evitar usar new Date('YYYY-MM-DD') (interpreta UTC). Formatear con helper local.
  document.getElementById('detail-fecha').textContent = formatDateLongLocalFromYMD(event.date);
  document.getElementById('detail-hora').textContent = event.time || '--';
  document.getElementById('detail-duracion').textContent = formatDurationMinutes(event.duration||60);
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
  // Si es presencial (no virtual), ocultar completamente la sección de enlace
  const isVirtualMeeting = String(event.location||'').toLowerCase()==='virtual';
  if (linkField) linkField.style.display = isVirtualMeeting ? 'block' : 'none';
  if (isVirtualMeeting){
    const hasUrl = !!(event.link && String(event.link).startsWith('http'));
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
  }

  // Participantes: chips con nombres
  const chips = document.getElementById('detail-participantes');
  if (chips){
    const names = (event.participantsNames && event.participantsNames.length)
      ? event.participantsNames
      : (event.participantsIds||[]).map(id => PARTICIPANTS_MAP?.[id] || `Aprendiz #${id}`);
    chips.innerHTML = names.length
      ? names.map(n=>`<span class="chip">${n}</span>`).join(' ')
      : '--';
  }
  // Checklist de asistencia (solo reuniones virtuales)
  const asistenciaSection = document.getElementById('detail-asistencia-section');
  const asistenciaList = document.getElementById('detail-asistencia-list');
  const asistenciaSummary = document.getElementById('detail-asistencia-summary');
  if (asistenciaSection && asistenciaList && asistenciaSummary) {
    if (isVirtualMeeting && Array.isArray(event.participantsDetails) && event.participantsDetails.length) {
      asistenciaSection.style.display = 'block';
      asistenciaList.innerHTML = '';
      event.participantsDetails.forEach(p => {
        const row = document.createElement('div');
        row.className = 'attendance-item-card';
        row.innerHTML = `
          <div class="attendance-card-header">
            <div class="attendance-name">${p.nombre}</div>
          </div>
          <div class="attendance-card-body">
            <div class="attendance-label">Asistencia</div>
            <p class="attendance-help">Marca si el participante asistió o no a la reunión virtual.</p>
            <div class="attendance-options">
              <label class="attendance-option">
                <input type="radio" name="asistencia-${event.id}-${p.id}" value="pendiente"> Pendiente
              </label>
              <label class="attendance-option">
                <input type="radio" name="asistencia-${event.id}-${p.id}" value="asistio"> Asistió
              </label>
              <label class="attendance-option">
                <input type="radio" name="asistencia-${event.id}-${p.id}" value="no_asistio"> No asistió
              </label>
            </div>
          </div>`;
        const radios = row.querySelectorAll('input[type="radio"]');
        radios.forEach(r => {
          if (r.value === (p.asistencia || 'pendiente')) {
            r.checked = true;
          }
          r.addEventListener('change', async () => {
            try {
              const url = ROUTES.asistenciaTemplate
                .replace('__EID__', String(event.id))
                .replace('__AID__', String(p.id));
              const res = await fetch(url, {
                method: 'PUT',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': CSRF,
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ asistencia: r.value })
              });
              if (!res.ok) {
                console.error('Error al actualizar asistencia', res.status, await res.text());
                showToast('No se pudo actualizar la asistencia','error');
              } else {
                showToast('Asistencia actualizada','success');
                p.asistencia = r.value;
                updateAttendanceSummary(event, asistenciaSummary);
              }
            } catch (err) {
              console.error('Error al actualizar asistencia', err);
              showToast('Error de red al actualizar asistencia','error');
            }
          });
        });
        asistenciaList.appendChild(row);
      });
      updateAttendanceSummary(event, asistenciaSummary);
      asistenciaSummary.style.display = 'block';
    } else {
      asistenciaSection.style.display = 'none';
      asistenciaList.innerHTML = '';
      asistenciaSummary.innerHTML = '';
      asistenciaSummary.style.display = 'none';
    }
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

function updateAttendanceSummary(event, summaryEl){
  if (!summaryEl) return;
  let pendientes = 0, asistieron = 0, noAsistieron = 0;
  if (Array.isArray(event.participantsDetails)){
    event.participantsDetails.forEach(p => {
      const st = String(p.asistencia || 'pendiente');
      if (st === 'asistio') asistieron++;
      else if (st === 'no_asistio') noAsistieron++;
      else pendientes++;
    });
  }
  summaryEl.innerHTML = `
    <div class="attendance-summary-card">
      <div class="attendance-summary-header">Resumen de Asistencia</div>
      <div class="attendance-summary-subtitle">Estado actual de los participantes de la reunión</div>
      <div class="attendance-summary-grid">
        <div class="attendance-summary-item">
          <div class="attendance-summary-count">${asistieron}</div>
          <div class="attendance-summary-label">Asistieron</div>
        </div>
        <div class="attendance-summary-item">
          <div class="attendance-summary-count">${noAsistieron}</div>
          <div class="attendance-summary-label">No asistieron</div>
        </div>
        <div class="attendance-summary-item">
          <div class="attendance-summary-count">${pendientes}</div>
          <div class="attendance-summary-label">Pendientes</div>
        </div>
      </div>
    </div>
  `;
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
  if(!editingEventId) return; if(!confirm('¿Estás seguro de que quieres cancelar esta reunión?')) return;
  try{
    const res = await fetch(`${ROUTES.baseEventos}/${editingEventId}`,{
      method:'DELETE',
      headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
    });
    let data = null;
    try{ data = await res.json(); }catch(_){ /* puede no venir JSON */ }
    const okFlag = data && (data.success === true || data.ok === true);
    if(!res.ok || okFlag === false){
      const msg = (data && data.message) ? data.message : 'No se pudo cancelar la reunión.';
      console.error('Error cancelando', res.status, data);
      showToast(msg,'error');
      return;
    }
    closeEventModal();
    closeDetailDrawer();
    await loadEventsForCurrentPeriod();
    renderCalendar();
    showToast('Reunión cancelada correctamente','success');
  }catch(err){
    console.error('Error cancelando evento',err);
    showToast('Error de red al cancelar la reunión.','error');
  }
}

async function updateEventOnServer(id,newDateStr,newTime){
  const fechaHora=`${newDateStr}T${newTime}:00`;
  try{
    const res = await fetch(`${ROUTES.baseEventos}/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({fecha_hora:fechaHora})});
    if(!res.ok){ const txt = await res.text(); console.error('Error al actualizar (drop)', res.status, txt); showToast('No se pudo mover el evento.','error'); }
  }catch(err){ console.error('Error actualizando evento',err); }
}

function navigatePeriod(direction){ if(currentView==='month'){ currentDate.setMonth(currentDate.getMonth()+direction); } else if(currentView==='week'){ currentDate.setDate(currentDate.getDate()+(direction*7)); } else { currentDate.setDate(currentDate.getDate()+direction); } }
function goToToday(){ currentDate = new Date(); }
function updatePeriodDisplay(){ const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; if(currentView==='month'){ currentPeriodEl.textContent=`${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } else if(currentView==='week'){ const ws=getWeekStart(currentDate); const we=new Date(ws); we.setDate(ws.getDate()+6); currentPeriodEl.textContent=`${ws.getDate()} ${months[ws.getMonth()]} - ${we.getDate()} ${months[we.getMonth()]} ${we.getFullYear()}`; } else { currentPeriodEl.textContent=`${currentDate.getDate()} de ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`; } }
function getWeekStart(date){ const d=new Date(date); const day=d.getDay(); const diff=d.getDate()-day; return new Date(d.setDate(diff)); }
function formatDateLocal(date){
  // YYYY-MM-DD en zona horaria local
  const y = date.getFullYear();
  const m = String(date.getMonth()+1).padStart(2,'0');
  const d = String(date.getDate()).padStart(2,'0');
  return `${y}-${m}-${d}`;
}
function formatDate(date){ return formatDateLocal(date); }
function formatDateLong(date){ const days=['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado']; const months=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; return `${days[date.getDay()]}, ${date.getDate()} de ${months[date.getMonth()]} ${date.getFullYear()}`; }
function getDayName(date){ const days=['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; return days[date.getDay()]; }
function formatHour(hour){ const period = hour>=12? 'PM':'AM'; const displayHour = hour>12? hour-12: hour; return `${displayHour}:00 ${period}`; }
function isToday(date){ const t=new Date(); return date.getDate()===t.getDate() && date.getMonth()===t.getMonth() && date.getFullYear()===t.getFullYear(); }
function isAllowedDateTime(dt){ const d=dt.getDay(); if(d===0||d===6) return false; const dateStr=formatDate(dt); if(HOLIDAYS.includes(dateStr)) return false; const now=new Date(); if(dt.getTime() < now.getTime()) return false; const hm=dt.getHours()*100 + dt.getMinutes(); if(hm<800 || hm>1650) return false; if(hm>=1200 && hm<=1355) return false; return true; }
function isPastDate(date){ const today = new Date(); const d = new Date(date.getFullYear(), date.getMonth(), date.getDate()); const t = new Date(today.getFullYear(), today.getMonth(), today.getDate()); return d.getTime() < t.getTime(); }
function isInPast(dt){ const now = new Date(); return dt.getTime() < now.getTime(); }

// Helpers de conflicto/ocupación en cliente
function crossesLunch(startDt, durationMinutes){
  const m = parseInt(durationMinutes||0,10); if(!m) return false; const end = new Date(startDt.getTime() + m*60000);
  const ymd = formatDate(startDt);
  const lunchStart = new Date(`${ymd}T12:00:00`);
  const lunchEnd = new Date(`${ymd}T13:55:00`);
  return (startDt < lunchEnd) && (end > lunchStart);
}
function enforceDurationOptions(){
  const dInput = document.getElementById('event-date'); const tInput = document.getElementById('event-time'); const durSel = document.getElementById('event-duration');
  if(!dInput || !tInput || !durSel) return; const ds=dInput.value; const ts=tInput.value; if(!ds || !ts) return; const start = new Date(`${ds}T${ts}:00`);
  Array.from(durSel.options).forEach(opt=>{ const val=parseInt(opt.value||0,10); if(!val) return; opt.disabled = crossesLunch(start, val); });
}
function formatDurationMinutes(min){ const m = parseInt(min||0,10); const h = Math.floor(m/60); const rest = m % 60; if(h<=0) return `${m} min`; if(rest===0) return h===1? '1 hora' : `${h} horas`; return `${h} h ${rest} min`; }
function isHourOccupied(dateStr, hour){
  // Un slot de 1 hora [hour, hour+1) se considera ocupado si
  // cualquier evento existente en ese día se cruza con ese intervalo.
  const slotStart = new Date(`${dateStr}T${String(hour).padStart(2,'0')}:00:00`).getTime();
  const slotEnd = slotStart + 60*60000; // 1 hora

  return events.some(ev => {
    if (ev.date !== dateStr) return false;
    const [h,m] = (ev.time||'09:00').split(':').map(Number);
    const evStart = new Date(`${ev.date}T${String(h).padStart(2,'0')}:${String(m||0).padStart(2,'0')}:00`).getTime();
    const evEnd = evStart + ((ev.duration||60)*60000);
    // Se cruza si hay intersección de intervalos
    return (slotStart < evEnd) && (slotEnd > evStart);
  });
}
function hasClientConflict(startDt, durationMinutes, ignoreId){
  const start = startDt.getTime();
  const end = start + (parseInt(durationMinutes||60)*60000);
  return events.some(ev=>{
    if(ignoreId && ev.id===ignoreId) return false;
    if(formatDate(startDt)!==ev.date) return false;
    const [h,m] = (ev.time||'09:00').split(':').map(Number);
    const evStart = new Date(`${ev.date}T${String(h).padStart(2,'0')}:${String(m||0).padStart(2,'0')}:00`).getTime();
    const evEnd = evStart + ((ev.duration||60)*60000);
    return (start < evEnd) && (end > evStart);
  });
}
function getFirstAvailableHour(dateStr){
  const today = formatDateLocal(new Date());
  const now = new Date();
  const minHour = (dateStr===today) ? Math.max(8, now.getHours()+1) : 8;
  for(const h of workHours){
    if (h < minHour) continue;
    const hm = h*100; const isLunch = hm>=1200 && hm<=1355; const weekendOrHoliday = (()=>{ const d = new Date(`${dateStr}T00:00:00`); return d.getDay()===0||d.getDay()===6||HOLIDAYS.includes(dateStr); })();
    if (weekendOrHoliday) return null;
    const allowed = hm>=800 && hm<=1650 && !isLunch;
    const slotDt = new Date(`${dateStr}T${String(h).padStart(2,'0')}:00`);
    if(allowed && !isHourOccupied(dateStr,h) && !isInPast(slotDt)) return h;
  }
  return null;
}

// Determina si un día ya está completamente lleno entre 8:00 y 16:00 (excluyendo 12:00-13:55)
function isDayFullyBooked(dateStr){
  // Si es fin de semana o festivo, la lógica de bloqueo ya aplica por otros chequeos
  const d = new Date(`${dateStr}T00:00:00`);
  if (d.getDay()===0 || d.getDay()===6 || HOLIDAYS.includes(dateStr)) return false;

  for (const h of workHours){
    const hm = h*100;
    const isLunch = hm>=1200 && hm<=1355;
    const allowed = hm>=800 && hm<=1650 && !isLunch;
    if (!allowed) continue;
    // Si hay al menos una hora permitida sin ocupar, el día no está lleno
    if (!isHourOccupied(dateStr, h)){
      return false;
    }
  }
  // Todas las horas permitidas están ocupadas
  return true;
}

// Toasts accesibles (definición global)
function showToast(message, type='info'){
  try{
    const container = document.getElementById('cal-toast-container');
    if(!container){ console.warn('Toast container no encontrado'); return; }
    const toast = document.createElement('div');
    toast.className = `cal-toast ${type}`;
    toast.setAttribute('role','status');
    toast.innerHTML = `
      <div class="cal-toast-icon">${type==='success'?'✅':type==='error'?'⛔':type==='warning'?'⚠️':'ℹ️'}</div>
      <div class="cal-toast-message">${message}</div>
      <button class="cal-toast-close" aria-label="Cerrar">×</button>
    `;
    const close = ()=>{ toast.classList.add('hide'); setTimeout(()=> toast.remove(), 200); };
    toast.querySelector('.cal-toast-close')?.addEventListener('click', close);
    container.appendChild(toast);
    setTimeout(close, 4000);
  }catch(e){ console.error('showToast error', e); }
}
</script>
@endsection
