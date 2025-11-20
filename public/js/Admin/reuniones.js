(() => {
  // === Config desde Blade ===
  const cfgEl = document.getElementById('calendar-config');
  const CFG = cfgEl ? JSON.parse(cfgEl.textContent) : { routes:{}, csrf:null, feriados:[] };
  const ROUTES   = CFG.routes || {};        // { obtener, baseEventos, semilleros, lideres }
  const CSRF     = CFG.csrf || null;
  const HOLIDAYS = CFG.feriados || [];

  // === Estado ===
  let currentDate     = new Date();
  let currentView     = 'month';
  let events          = [];
  let selectedDate    = null;
  let editingEventId  = null;
  let draggedEvent    = null;

  // === Red helper ===
  async function fetchJSON(url, opts = {}) {
    const headers = {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      ...(opts.body ? { 'Content-Type': 'application/json' } : {}),
      ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
      ...(opts.headers || {}),
    };
    const res = await fetch(url, { ...opts, headers });
    if (!res.ok) {
      const txt = await res.text().catch(() => '');
      throw new Error(`HTTP ${res.status} - ${txt}`);
    }
    return res.status === 204 ? null : res.json();
  }
  const escapeHtml = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');

  // === Reglas de horario ===
  const WORK_HOURS = Array.from({ length: 9 }, (_, i) => i + 8); // 08..16

  // === DOM ===
  const prevBtn         = document.getElementById('prevBtn');
  const nextBtn         = document.getElementById('nextBtn');
  const todayBtn        = document.getElementById('todayBtn');
  const currentPeriodEl = document.getElementById('currentPeriod');
  const daysGrid        = document.getElementById('daysGrid');
  const eventModal      = document.getElementById('event-modal');
  const detailDrawer    = document.getElementById('event-detail-drawer');
  const detailOverlay   = document.getElementById('event-detail-overlay');
  const eventForm       = document.getElementById('event-form');
  const closeModal      = document.getElementById('close-modal');
  const drawerCloseBtn  = document.getElementById('drawer-close-btn');
  const cancelBtn       = document.getElementById('cancel-event');
  const drawerCloseCta  = document.getElementById('drawer-close-cta');
  const deleteBtn       = document.getElementById('deleteBtn');
  const modalTitle      = document.getElementById('modal-title');

  const monthView       = document.getElementById('monthView');
  const weekView        = document.getElementById('weekView');
  const dayView         = document.getElementById('dayView');
  const viewBtns        = document.querySelectorAll('.view-btn');

  // === INIT ===
  init();
  async function init() {
    await loadSemilleros();                       // llena el select de semilleros
    const selSem = document.getElementById('event-proyecto');
    selSem?.addEventListener('change', (e) => {
      const id = e.target.value || '';
      loadLideres(id);                            // trae líderes del semillero con correo
    });

    await loadEventsForCurrentPeriod();
    renderCalendar();
  }

  // === Semilleros & Líderes ===
  async function loadSemilleros() {
    const sel = document.getElementById('event-proyecto');
    if (!sel) return;
    if (!ROUTES.semilleros) { sel.innerHTML = '<option value="">(Falta ruta semilleros)</option>'; return; }

    sel.innerHTML = '<option value="">Cargando semilleros…</option>';
    try {
      const { data } = await fetchJSON(ROUTES.semilleros);
      const items = Array.isArray(data) ? data : [];
      sel.innerHTML = '<option value="">Sin semillero seleccionado</option>' +
        items.map(s => `<option value="${s.id}">${escapeHtml(s.nombre)}</option>`).join('');
    } catch (e) {
      console.error('Semilleros:', e);
      sel.innerHTML = '<option value="">Error cargando</option>';
    }
  }

  async function loadLideres(semilleroId) {
    const sel = document.getElementById('event-participants');
    if (!sel) return;
    if (!semilleroId) { sel.innerHTML = ''; return; }
    if (!ROUTES.lideres) { console.error('Falta ROUTES.lideres'); sel.innerHTML = ''; return; }

    sel.innerHTML = '<option>(Cargando líderes…)</option>';
    try {
      const url = new URL(ROUTES.lideres, window.location.origin);
      url.searchParams.set('semillero_id', semilleroId);

      const { data } = await fetchJSON(url.toString());
      const items = Array.isArray(data) ? data : [];

      // Mostrar: "Nombre Apellido — correo@dominio"
      sel.innerHTML = items.map(l => {
        const label = `${escapeHtml(l.nombre || '')} — ${escapeHtml(l.email || '')}`;
        return `<option value="${l.id}" data-email="${escapeHtml(l.email || '')}">${label}</option>`;
      }).join('');
    } catch (e) {
      console.error('Líderes:', e);
      sel.innerHTML = '';
    }
  }

  // === Listeners ===
  prevBtn?.addEventListener('click', async () => { navigatePeriod(-1); await loadEventsForCurrentPeriod(); renderCalendar(); });
  nextBtn?.addEventListener('click', async () => { navigatePeriod( 1); await loadEventsForCurrentPeriod(); renderCalendar(); });
  todayBtn?.addEventListener('click', async () => { goToToday();          await loadEventsForCurrentPeriod(); renderCalendar(); });
  closeModal?.addEventListener('click', closeEventModal);
  drawerCloseBtn?.addEventListener('click', closeDetailDrawer);
  drawerCloseCta?.addEventListener('click', closeDetailDrawer);
  cancelBtn?.addEventListener('click', closeEventModal);

  // Respeta modo solo lectura para LIDER_INTERMEDIARIO
  const READ_ONLY = !!window.READ_ONLY_INTER;
  if (!READ_ONLY) {
    deleteBtn?.addEventListener('click', deleteEvent);
    eventForm?.addEventListener('submit', saveEvent);
  } else {
    // Oculta controles de edición/creación
    deleteBtn && (deleteBtn.style.display = 'none');
    eventForm && (eventForm.querySelectorAll('input,select,textarea,button[type="submit"]').forEach(el => el.disabled = true));
  }
  viewBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
      viewBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentView = btn.dataset.view;
      await loadEventsForCurrentPeriod();
      renderCalendar();
    });
  });
  eventModal?.addEventListener('click', e => { if (e.target === eventModal) closeEventModal(); });
  detailOverlay?.addEventListener('click', closeDetailDrawer);

  // === Carga eventos ===
  async function loadEventsForCurrentPeriod() {
    try {
      const mes  = currentDate.getMonth() + 1;
      const anio = currentDate.getFullYear();
      const url  = new URL(ROUTES.obtener, window.location.origin);
      url.searchParams.set('mes', mes);
      url.searchParams.set('anio', anio);
      const data = await fetchJSON(url.toString());
      const list = data.eventos || [];
      events = list.map(ev => {
        const d   = new Date(ev.fecha_hora);
        const yyyy= d.toISOString().split('T')[0];
        const hh  = String(d.getHours()).padStart(2,'0');
        const mm  = String(d.getMinutes()).padStart(2,'0');
        return {
          id: ev.id,
          date: yyyy,
          time: `${hh}:${mm}`,
          title: ev.titulo,
          duration: ev.duracion || 60,
          type: ev.tipo || 'general',
          location: ev.ubicacion || '',
          description: ev.descripcion || '',
          researchLine: ev.linea_investigacion || '',
          link: ev.link_virtual || ''
        };
      });
    } catch (e) {
      console.error('Error cargando eventos', e);
      events = [];
    }
  }

  // === Render ===
  function renderCalendar() {
    if (currentView === 'month') renderMonthView();
    else if (currentView === 'week') renderWeekView();
    else renderDayView();
    updatePeriodDisplay();
  }

  function renderMonthView() {
    monthView.style.display = 'block';
    weekView.classList.remove('active');
    dayView.classList.remove('active');

    const year  = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDay     = new Date(year, month, 1);
    const lastDay      = new Date(year, month + 1, 0);
    const prevLastDay  = new Date(year, month, 0);
    const firstDayIdx  = firstDay.getDay();
    const lastDayIdx   = lastDay.getDay();
    const nextDays     = 7 - lastDayIdx - 1;

    daysGrid.innerHTML = '';

    for (let i = firstDayIdx; i > 0; i--) {
      const day  = prevLastDay.getDate() - i + 1;
      const date = new Date(year, month - 1, day);
      daysGrid.appendChild(createDayCell(date, true));
    }
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, month, day);
      daysGrid.appendChild(createDayCell(date, false));
    }
    for (let day = 1; day <= nextDays; day++) {
      const date = new Date(year, month + 1, day);
      daysGrid.appendChild(createDayCell(date, true));
    }
  }

  function createDayCell(date, otherMonth) {
    const cell = document.createElement('div');
    cell.className = 'day-cell';
    if (otherMonth) cell.classList.add('other-month');
    if (isToday(date)) cell.classList.add('today');

    const dateStr   = formatDate(date);
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = date.getDate();
    cell.appendChild(dayNumber);

    // eventos del día
    events.filter(e => e.date === dateStr)
      .forEach(event => cell.appendChild(createEventElement(event)));

    // reglas de bloqueo
    const disabled = isWeekend(date) || HOLIDAYS.includes(dateStr) || isPastDay(date);
    if (disabled || READ_ONLY) {
      cell.classList.add('disabled');
    } else {
      cell.addEventListener('click', (e) => {
        if (e.target === cell || e.target === dayNumber) openEventModal(date);
      });
      cell.addEventListener('dragover', handleDragOver);
      cell.addEventListener('drop',    (e) => handleDrop(e, date));
      cell.addEventListener('dragleave', handleDragLeave);
    }
    return cell;
  }

  function renderWeekView() {
    monthView.style.display = 'none';
    weekView.classList.add('active');
    dayView.classList.remove('active');

    const weekStart = getWeekStart(currentDate);
    const weekDays  = Array.from({ length: 7 }, (_, i) => {
      const d = new Date(weekStart);
      d.setDate(weekStart.getDate() + i);
      return d;
    });

    const weekHeader = document.getElementById('weekHeader');
    weekHeader.innerHTML = '<div class="week-time-label">Hora</div>';
    weekDays.forEach(date => {
      const header = document.createElement('div');
      header.className = 'week-day-header';
      if (isToday(date)) header.classList.add('today');
      header.innerHTML = `<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`;
      weekHeader.appendChild(header);
    });

    const weekGrid = document.getElementById('weekGrid');
    weekGrid.innerHTML = '';

    const timesCol = document.createElement('div');
    timesCol.className = 'week-times';
    WORK_HOURS.forEach(h => {
      const tl = document.createElement('div');
      tl.className = 'time-slot-label';
      tl.textContent = formatHour(h);
      timesCol.appendChild(tl);
    });
    weekGrid.appendChild(timesCol);

    weekDays.forEach(date => {
      const dayCol  = document.createElement('div');
      dayCol.className = 'week-day-column';
      const dateStr = formatDate(date);

      const disabledDay = isWeekend(date) || HOLIDAYS.includes(dateStr) || isPastDay(date);
      WORK_HOURS.forEach(hour => {
        const slot = document.createElement('div');
        slot.className = 'week-time-slot';
        slot.dataset.date = dateStr;
        slot.dataset.hour = hour;

        const hm      = hour * 100;
        const isLunch = hm >= 1200 && hm <= 1355;
        const allowed = !disabledDay && hm >= 800 && hm <= 1650 && !isLunch;

if (!allowed || READ_ONLY) {
slot.classList.add('disabled');
} else {
slot.addEventListener('click', () => {
const eventDate = new Date(date);
eventDate.setHours(hour, 0, 0, 0);
openEventModal(eventDate);
});
slot.addEventListener('dragover', handleDragOver);
slot.addEventListener('drop', (e) => {
const eventDate = new Date(date);
eventDate.setHours(hour, 0, 0, 0);
handleDrop(e, eventDate);
});
slot.addEventListener('dragleave', handleDragLeave);
}
});
      });

      events.filter(e => e.date === dateStr)
        .forEach(event => dayCol.appendChild(createWeekEventElement(event)));

      weekGrid.appendChild(dayCol);
    });
  }

  function renderDayView() {
    monthView.style.display = 'none';
    weekView.classList.remove('active');
    dayView.classList.add('active');

    const dateStr    = formatDate(currentDate);
    const isDisabled = isWeekend(currentDate) || HOLIDAYS.includes(dateStr) || isPastDay(currentDate);

    const dayHeader = document.getElementById('dayHeader');
    dayHeader.innerHTML = `<div></div><div class="day-header-info"><div class="day-header-name">${getDayName(currentDate)}</div><div class="day-header-date">${currentDate.getDate()}</div></div>`;

    const dayGrid = document.getElementById('dayGrid');
    dayGrid.innerHTML = '';

    const timesCol = document.createElement('div');
    timesCol.className = 'day-times';
    WORK_HOURS.forEach(h => {
      const tl = document.createElement('div');
      tl.className = 'day-time-label';
      tl.textContent = formatHour(h);
      timesCol.appendChild(tl);
    });
    dayGrid.appendChild(timesCol);

    const slotsCol = document.createElement('div');
    slotsCol.className = 'day-slots';

    WORK_HOURS.forEach(hour => {
      const slot = document.createElement('div');
      slot.className = 'day-time-slot';
      slot.dataset.date = dateStr;
      slot.dataset.hour = hour;

      const hm      = hour * 100;
      const isLunch = hm >= 1200 && hm <= 1355;
      const allowed = !isDisabled && hm >= 800 && hm <= 1650 && !isLunch;

      if (!allowed) {
        slot.classList.add('disabled');
      } else {
        slot.addEventListener('click', () => {
          const eventDate = new Date(currentDate);
          eventDate.setHours(hour, 0, 0, 0);
          openEventModal(eventDate);
        });
        slot.addEventListener('dragover', handleDragOver);
        slot.addEventListener('drop', (e) => {
          const eventDate = new Date(currentDate);
          eventDate.setHours(hour, 0, 0, 0);
          handleDrop(e, eventDate);
        });
        slot.addEventListener('dragleave', handleDragLeave);
      }
      slotsCol.appendChild(slot);
    });

    events.filter(e => e.date === dateStr)
      .forEach(event => slotsCol.appendChild(createDayEventElement(event)));

    dayGrid.appendChild(slotsCol);
  }

  // === Nodos de evento ===
  function createEventElement(event) {
    const el = document.createElement('div');
    el.className = 'event';
    el.draggable = true;
    el.dataset.id = event.id;
    el.innerHTML = `<div class="event-time">${event.time}</div><div class="event-title">${event.title}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
    el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    return el;
  }

  function createWeekEventElement(event) {
    const el = document.createElement('div');
    el.className = 'week-event';
    el.draggable = true;
    el.dataset.id = event.id;
    const [h] = event.time.split(':').map(Number);
    const top = (h - 8) * 60 + 4;
    const height = Math.min(event.duration || 60, 60) - 8;
    el.style.top = `${top}px`;
    el.style.height = `${height}px`;
    el.innerHTML = `<div style="font-weight:600;">${event.time}</div><div style="font-size:10px;">${event.title}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
    el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    return el;
  }

  function createDayEventElement(event) {
    const el = document.createElement('div');
    el.className = 'day-event';
    el.draggable = true;
    el.dataset.id = event.id;
    const [h] = event.time.split(':').map(Number);
    const top = (h - 8) * 80 + 4;
    const height = Math.min((event.duration || 60) / 60 * 80, 80) - 8;
    el.style.top = `${top}px`;
    el.style.height = `${height}px`;
    el.innerHTML = `<div style="font-weight:600;font-size:13px;">${event.time}</div><div style="font-size:12px;margin-top:2px;">${event.title}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
    el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    return el;
  }

  // === Drag & Drop / Modal ===
  function handleDragOver(e) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; e.currentTarget.classList.add('drag-over'); }
  function handleDragLeave(e) { e.currentTarget.classList.remove('drag-over'); }

  async function handleDrop(e, newDate) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (!draggedEvent) return;

    const idx = events.findIndex(ev => ev.id === draggedEvent.id);
    if (idx === -1) return;

    const newDateStr = formatDate(newDate);
    let newTime = events[idx].time;

    if (e.currentTarget.dataset.hour) {
      const hour = parseInt(e.currentTarget.dataset.hour);
      newTime = `${String(hour).padStart(2, '0')}:00`;
    }

    const candidate = new Date(`${newDateStr}T${newTime}:00`);
    if (!isAllowedDateTime(candidate) || isPastDateTime(candidate)) {
      alert('No puedes mover el evento a una fecha/horario no permitido o pasado.');
      return;
    }
    await updateEventOnServer(events[idx].id, newDateStr, newTime);
    await loadEventsForCurrentPeriod();
    renderCalendar();
  }

  function openEventModal(date) {
    selectedDate = date;
    editingEventId = null;
    if (modalTitle) modalTitle.textContent = 'Nueva Reunión';
    if (deleteBtn)  deleteBtn.style.display = 'none';
    if (READ_ONLY) { alert('Solo lectura: no puedes crear reuniones.'); return; }

    eventForm?.reset();

    // Fecha mínima = hoy
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');

    if (dateInput) {
      const todayStr = formatDate(new Date());
      dateInput.setAttribute('min', todayStr);
      const d = isPastDay(date) ? new Date() : date;
      dateInput.value = formatDate(d);
    }

    if (timeInput) {
      // Hora por defecto = ahora (redondeada a 5 min)
      const now = new Date();
      const pad = n => String(n).padStart(2, '0');
      const rounded = new Date(now);
      rounded.setSeconds(0, 0);
      const m = rounded.getMinutes();
      const r = Math.round(m / 5) * 5;
      rounded.setMinutes(r >= 60 ? 55 : r);

      timeInput.value = `${pad(rounded.getHours())}:${pad(rounded.getMinutes())}`;
    }

    eventModal?.classList.add('active');
  }

  function closeEventModal() {
    eventModal?.classList.remove('active');
    eventForm?.reset();
    selectedDate = null;
    editingEventId = null;
  }

  async function saveEvent(e) {
    e.preventDefault();
    if (!selectedDate) return;
    const payload = buildEventPayload();

    const when = new Date(payload.fecha_hora);
    if (isPastDateTime(when)) { alert('No puedes crear reuniones en fecha/horario pasado.'); return; }
    if (!isAllowedDateTime(when)) { alert('Horario no permitido.'); return; }

    try {
      if (editingEventId) {
        await fetchJSON(`${ROUTES.baseEventos}/${editingEventId}`, { method: 'PUT', body: JSON.stringify(payload) });
      } else {
        await fetchJSON(ROUTES.baseEventos, { method: 'POST', body: JSON.stringify(payload) });
      }
      closeEventModal();
      await loadEventsForCurrentPeriod();
      renderCalendar();
    } catch (err) {
      console.error('Error guardando evento', err);
      alert('No se pudo guardar el evento.');
    }
  }

  function buildEventPayload() {
    const dateInput  = document.getElementById('event-date');
    const timeInput  = document.getElementById('event-time');
    const titleInput = document.getElementById('event-title');
    const durationInput = document.getElementById('event-duration');
    const descInput  = document.getElementById('event-description');
    const projSelect = document.getElementById('event-proyecto');
    const partsSelect= document.getElementById('event-participants');

    const dateStr = dateInput ? dateInput.value : formatDate(selectedDate);
    const timeStr = timeInput ? timeInput.value : '09:00';
    const fechaHora = `${dateStr}T${timeStr}:00`;

    const participantes = partsSelect
      ? Array.from(partsSelect.selectedOptions).map(o => parseInt(o.value))
      : [];

    const idProyecto = projSelect && projSelect.value ? parseInt(projSelect.value) : null;

    return {
      titulo: titleInput ? titleInput.value : '',
      tipo: 'presencial',
      linea_investigacion: '',
      descripcion: descInput ? (descInput.value || null) : null,
      fecha_hora: fechaHora,
      duracion: durationInput ? (parseInt(durationInput.value) || 60) : 60,
      ubicacion: 'presencial',
      link_virtual: null,
      recordatorio: 'none',
      id_proyecto: idProyecto,
      participantes
    };
  }

  function showEventDetails(event) {
    editingEventId = event.id;
    document.getElementById('detail-titulo').textContent = event.title || '--';
    document.getElementById('detail-tipo').textContent   = event.type || '--';
    document.getElementById('detail-fecha').textContent  = formatDateLong(new Date(event.date));
    document.getElementById('detail-hora').textContent   = event.time || '--';
    document.getElementById('detail-duracion').textContent = `${event.duration || 60} minutos`;
    document.getElementById('detail-ubicacion').textContent = event.location || '--';
    document.getElementById('detail-descripcion').textContent = event.description || '--';

    const linkField  = document.getElementById('detail-link-field');
    const linkA      = document.getElementById('detail-link');
    const genBtn     = document.getElementById('detail-generate-link');
    const delBtn     = document.getElementById('detail-delete-link');
    const editorBox  = document.getElementById('detail-link-editor');
    const editorInput= document.getElementById('detail-link-input');
    const saveBtn    = document.getElementById('detail-save-link');
    const cancelBtnE = document.getElementById('detail-cancel-edit');

    if (linkField) linkField.style.display = 'block';
    const hasUrl = !!(event.link && event.link.startsWith('http'));

    if (hasUrl) {
      if (linkA) { linkA.href = event.link; linkA.style.display = 'inline-block'; }
      genBtn && (genBtn.style.display = 'inline-block');
      delBtn && (delBtn.style.display = 'inline-block');
      editorBox && (editorBox.style.display = 'none');
    } else {
      linkA && (linkA.style.display = 'none');
      genBtn && (genBtn.style.display = 'inline-block');
      delBtn && (delBtn.style.display = 'none');
      editorBox && (editorBox.style.display = 'block');
      editorInput && (editorInput.value = '');
    }

    if (genBtn) genBtn.onclick = async () => {
      try {
        const data = await fetchJSON(`${ROUTES.baseEventos}/${event.id}/generar-enlace`, {
          method: 'POST',
          body: JSON.stringify({ plataforma: 'teams' }),
        });
        const url = data?.link || data?.enlace || data?.url || null;
        if (url) {
          if (linkA) { linkA.href = url; linkA.style.display = 'inline-block'; }
          delBtn && (delBtn.style.display = 'inline-block');
          editorBox && (editorBox.style.display = 'none');
        }
      } catch (err) {
        console.error(err); alert('No se pudo generar el enlace.');
      }
    };

    if (saveBtn) saveBtn.onclick = async () => {
      const url = (editorInput?.value || '').trim();
      if (!url) { alert('Ingresa un enlace válido'); return; }
      try {
        await fetchJSON(`${ROUTES.baseEventos}/${event.id}`, {
          method: 'PUT',
          body: JSON.stringify({ link_virtual: url }),
        });
        if (linkA) { linkA.href = url; linkA.style.display = 'inline-block'; }
        delBtn && (delBtn.style.display = 'inline-block');
        editorBox && (editorBox.style.display = 'none');
      } catch (err) {
        console.error(err); alert('No se pudo guardar el enlace.');
      }
    };

    if (cancelBtnE) cancelBtnE.onclick = () => { editorBox && (editorBox.style.display = 'none'); };

    if (delBtn) delBtn.onclick = async () => {
      if (!confirm('¿Eliminar el enlace de la reunión?')) return;
      try {
        await fetchJSON(`${ROUTES.baseEventos}/${event.id}`, {
          method: 'PUT',
          body: JSON.stringify({ link_virtual: null }),
        });
        linkA && (linkA.style.display = 'none');
        delBtn.style.display = 'none';
        editorBox && (editorBox.style.display = 'block');
        editorInput && (editorInput.value = '');
      } catch (err) {
        console.error(err); alert('No se pudo eliminar el enlace.');
      }
    };

    openDetailDrawer();
  }

  function openDetailDrawer()  {
    if (detailOverlay) detailOverlay.style.display = 'block';
    if (detailDrawer) {
      detailDrawer.style.display = 'block';
      detailDrawer.setAttribute('aria-hidden', 'false');
    }
  }
  function closeDetailDrawer() {
    if (detailDrawer) {
      // Si algún elemento dentro del drawer tiene el foco, quitarlo antes de ocultar
      const active = document.activeElement;
      if (active && detailDrawer.contains(active)) {
        try { active.blur(); } catch (_) {}
      }
      detailDrawer.setAttribute('aria-hidden', 'true');
      detailDrawer.style.display = 'none';
    }
    if (detailOverlay) detailOverlay.style.display = 'none';
  }

  function editEvent(event) {
    selectedDate   = new Date(event.date + 'T' + event.time);
    editingEventId = event.id;
    modalTitle && (modalTitle.textContent = 'Editar Reunión');
    deleteBtn && (deleteBtn.style.display = 'block');

    const dateInput  = document.getElementById('event-date');
    const timeInput  = document.getElementById('event-time');
    const titleInput = document.getElementById('event-title');
    const durationInput = document.getElementById('event-duration');
    const descInput  = document.getElementById('event-description');

    if (dateInput)  { dateInput.setAttribute('min', formatDate(new Date())); dateInput.value = event.date; }
    if (timeInput)  timeInput.value  = event.time;
    if (titleInput) titleInput.value = event.title;
    if (durationInput) durationInput.value = event.duration;
    if (descInput)  descInput.value  = event.description || '';

    eventModal?.classList.add('active');
  }

  async function deleteEvent() {
    if (!editingEventId) return;
    if (READ_ONLY) { alert('Solo lectura: no puedes eliminar reuniones.'); return; }
    if (!confirm('¿Estás seguro de que quieres eliminar esta reunión?')) return;
    try {
      await fetchJSON(`${ROUTES.baseEventos}/${editingEventId}`, { method: 'DELETE' });
      closeEventModal();
      closeDetailDrawer();
      await loadEventsForCurrentPeriod();
      renderCalendar();
    } catch (err) {
      console.error('Error eliminando evento', err);
      alert('No se pudo eliminar.');
    }
  }

  async function updateEventOnServer(id, newDateStr, newTime) {
    const fecha_hora = `${newDateStr}T${newTime}:00`;
    try {
      await fetchJSON(`${ROUTES.baseEventos}/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ fecha_hora }),
      });
    } catch (err) {
      console.error('Error al actualizar (drop)', err);
      alert('No se pudo mover el evento.');
    }
  }

  // === Utils de vista/fecha ===
  function navigatePeriod(direction) {
    if (currentView === 'month') currentDate.setMonth(currentDate.getMonth() + direction);
    else if (currentView === 'week') currentDate.setDate(currentDate.getDate() + (direction * 7));
    else currentDate.setDate(currentDate.getDate() + direction);
  }
  function goToToday() { currentDate = new Date(); }

  function updatePeriodDisplay() {
    const months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    if (currentView === 'month') {
      currentPeriodEl.textContent = `${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    } else if (currentView === 'week') {
      const ws = getWeekStart(currentDate);
      const we = new Date(ws); we.setDate(ws.getDate() + 6);
      currentPeriodEl.textContent = `${ws.getDate()} ${months[ws.getMonth()]} - ${we.getDate()} ${months[we.getMonth()]} ${we.getFullYear()}`;
    } else {
      currentPeriodEl.textContent = `${currentDate.getDate()} de ${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
  }

  function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay(); // domingo inicio
    const diff = d.getDate() - day;
    return new Date(d.setDate(diff));
  }

  function isWeekend(date) { const dow = date.getDay(); return dow === 0 || dow === 6; }
  function formatDate(date) { return date.toISOString().split('T')[0]; }
  function formatDateLong(date) {
    const days  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const months= ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    return `${days[date.getDay()]}, ${date.getDate()} de ${months[date.getMonth()]} ${date.getFullYear()}`;
  }
  function getDayName(date) { const days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; return days[date.getDay()]; }
  function formatHour(hour) {
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : hour;
    return `${displayHour}:00 ${period}`;
  }
  function isToday(date) {
    const t = new Date();
    return date.getDate() === t.getDate() && date.getMonth() === t.getMonth() && date.getFullYear() === t.getFullYear();
  }

  // Bloqueos por pasado
  function isPastDay(date) {
    const today = new Date(); today.setHours(0,0,0,0);
    const d = new Date(date); d.setHours(0,0,0,0);
    return d < today; // días pasados
  }
  function isPastDateTime(dt) {
    const now = new Date();
    return dt.getTime() < now.getTime();
  }

  function isAllowedDateTime(dt) {
    // horario y almuerzo + feriados y fines de semana ya controlados en la UI,
    // pero revalidamos acá:
    const d = dt.getDay();
    if (d === 0 || d === 6) return false;
    const dateStr = formatDate(dt);
    if (HOLIDAYS.includes(dateStr)) return false;
    const hm = dt.getHours() * 100 + dt.getMinutes();
    if (hm < 800 || hm > 1650) return false;
    if (hm >= 1200 && hm <= 1355) return false;
    return true;
  }
})();
