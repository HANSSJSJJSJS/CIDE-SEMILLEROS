(() => {
  // === Config desde Blade ===
  const cfgEl = document.getElementById('calendar-config');
  const CFG = cfgEl ? JSON.parse(cfgEl.textContent) : { routes:{}, csrf:null, feriados:[] };
  const ROUTES   = CFG.routes || {};        // { obtener, baseEventos, semilleros, lideres }
  const CSRF     = CFG.csrf || null;
  const CURRENT_USER_ID = CFG.currentUserId ?? null;
  const CURRENT_USER_ROLE = CFG.currentUserRole ?? null;
  let HOLIDAYS = CFG.feriados || [];
  const HOLIDAYS_LOADED = new Set();
  const FIXED_MD_CO = ['01-01','05-01','07-20','08-07','12-08','12-25'];
  const fixedColombia = (year)=> FIXED_MD_CO.map(md => `${String(year).padStart(4,'0')}-${md}`);

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
    const targets = [year - 1, year, year + 1];
    const missing = targets.filter(y => !HOLIDAYS_LOADED.has(y));
    if (missing.length === 0) return;
    const union = new Set(HOLIDAYS);
    const results = await Promise.all(missing.map(y => fetchHolidaysYear(y)));
    results.forEach((dates, idx) => {
      const y = missing[idx];
      if (Array.isArray(dates) && dates.length){
        dates.forEach(d => union.add(d));
      } else {
        // Fallback client-side: fechas fijas mínimas de Colombia
        fixedColombia(y).forEach(d => union.add(d));
      }
      HOLIDAYS_LOADED.add(y);
    });
    HOLIDAYS = Array.from(union);
  }

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

  // === Fecha (banner) ===
  function updateDateBanner(dateStr) {
    if (!dateStr) return;
    const [y,m,d] = dateStr.split('-').map(Number);
    const dt = new Date(y, (m||1)-1, d||1);
    updateDateBannerFromDate(dt);
  }

  function updateDateBannerFromDate(dt) {
    const banner = document.getElementById('date-banner');
    if (!banner || !(dt instanceof Date)) return;
    const dayEl   = document.getElementById('date-banner-day');
    const monthEl = document.getElementById('date-banner-month');
    const longEl  = document.getElementById('date-banner-long');

    const MONTH_ABBR = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
    const MONTH_LONG = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const WEEK_LONG  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];

    const dd = String(dt.getDate());
    const mm = MONTH_ABBR[dt.getMonth()] || '';
    const weekday = WEEK_LONG[dt.getDay()] || '';
    const monthLong = MONTH_LONG[dt.getMonth()] || '';
    const year = dt.getFullYear();
    // Formato exacto del mockup: "Miércoles, 17 de diciembre 2025"
    const long = `${capitalize(weekday)}, ${dd} de ${monthLong} ${year}`;

    if (dayEl) dayEl.textContent = dd;
    if (monthEl) monthEl.textContent = mm;
    if (longEl) longEl.textContent = long;
  }

  function capitalize(s){ return (s||'').charAt(0).toUpperCase() + (s||'').slice(1); }

  // === Participantes (UI mejorada) ===
  function filterParticipants(term) {
    const q = (term || '').toLowerCase();
    // Filtrar lista visual
    const list = document.getElementById('participants-list');
    if (list) {
      list.querySelectorAll('.participant-item').forEach(item => {
        const txt = (item.getAttribute('data-text') || '').toLowerCase();
        item.style.display = q && !txt.includes(q) ? 'none' : 'flex';
      });
    }
  }

  function renderParticipantsList() {
    const list = document.getElementById('participants-list');
    const sel  = document.getElementById('event-participants');
    if (!list || !sel) return;
    list.innerHTML = '';
    Array.from(sel.options).forEach(opt => {
      const item = buildParticipantItem(opt.value, opt.textContent);
      list.appendChild(item);
    });
    updateParticipantsUIFromSelect();
    rebuildChips();
  }

  function buildParticipantItem(id, label) {
    const item = document.createElement('div');
    item.className = 'participant-item';
    item.dataset.id = String(id);
    item.setAttribute('data-text', label || '');
    const initials = getInitials(label || '');
    item.innerHTML = `
      <div class="participant-check"><i class="fas fa-check" style="font-size:12px"></i></div>
      <div class="participant-avatar">${initials}</div>
      <div style="flex:1">
        <div class="participant-name">${label?.split(' — ')[0] || label}</div>
        <div class="participant-sub">${label?.split(' — ')[1] || 'Líder de semillero'}</div>
      </div>
    `;
    item.addEventListener('click', () => toggleParticipantSelection(id));
    return item;
  }

  function toggleParticipantSelection(id) {
    const sel = document.getElementById('event-participants');
    const opt = sel ? Array.from(sel.options).find(o => String(o.value) === String(id)) : null;
    if (!opt) return;
    opt.selected = !opt.selected;
    updateParticipantsUIFromSelect();
    rebuildChips();
  }

  function updateParticipantsUIFromSelect() {
    const sel = document.getElementById('event-participants');
    const list = document.getElementById('participants-list');
    if (!sel || !list) return;
    const selectedIds = new Set(Array.from(sel.selectedOptions).map(o => String(o.value)));
    list.querySelectorAll('.participant-item').forEach(item => {
      const id = item.dataset.id;
      if (selectedIds.has(id)) item.classList.add('selected');
      else item.classList.remove('selected');
    });
  }

  function rebuildChips() {
    const chips = document.getElementById('participants-chips');
    const sel = document.getElementById('event-participants');
    if (!chips || !sel) return;
    chips.innerHTML = '';
    Array.from(sel.selectedOptions).forEach(opt => {
      const chip = document.createElement('div');
      chip.className = 'chip';
      chip.innerHTML = `${(opt.textContent || '').split(' — ')[0]} <button type="button" class="chip-close" aria-label="Quitar">×</button>`;
      chip.querySelector('.chip-close').addEventListener('click', () => {
        opt.selected = false; updateParticipantsUIFromSelect(); rebuildChips();
      });
      chips.appendChild(chip);
    });
  }

  function getInitials(text) {
    const name = (text || '').split(' — ')[0] || '';
    const parts = name.trim().split(/\s+/).slice(0,2);
    return parts.map(p => p.charAt(0)).join('').toUpperCase() || 'LS';
  }
  const escapeHtml = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');

  // === Reglas de horario ===
  const WORK_HOURS = Array.from({ length: 10 }, (_, i) => i + 8); // 08..17 (límite 17:00)

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
    await ensureHolidaysForYear(currentDate.getFullYear());
    await loadAllLeaders();                       // llena participantes con TODOS los líderes
    const selSem = document.getElementById('event-proyecto');
    selSem?.addEventListener('change', async (e) => {
      const id = e.target.value || '';
      await markSemilleroLeaderSelected(id);      // selecciona automáticamente el líder del semillero
    });

    await loadEventsForCurrentPeriod();
    renderCalendar();

    // Buscar participantes
    const search = document.getElementById('participants-search');
    if (search && !search.dataset.bound) {
      search.addEventListener('input', () => filterParticipants(search.value));
      search.dataset.bound = '1';
    }
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

  // Cargar TODOS los líderes (para que el selector muestre la lista completa)
  async function loadAllLeaders() {
    const sel = document.getElementById('event-participants');
    if (!sel) return;
    if (!ROUTES.lideres) { console.error('Falta ROUTES.lideres'); return; }

    sel.innerHTML = '<option>(Cargando líderes…)</option>';
    try {
      const { data } = await fetchJSON(ROUTES.lideres);
      const items = Array.isArray(data) ? data : [];
      sel.innerHTML = items.map(l => {
        const label = `${escapeHtml(l.nombre || '')} — ${escapeHtml(l.email || '')}`;
        return `<option value="${l.id}" data-email="${escapeHtml(l.email || '')}">${label}</option>`;
      }).join('');
      renderParticipantsList();
    } catch (e) {
      console.error('Todos los líderes:', e);
      sel.innerHTML = '';
    }
  }

  // Marca como seleccionado el líder del semillero
  async function markSemilleroLeaderSelected(semilleroId) {
    const sel = document.getElementById('event-participants');
    if (!sel || !semilleroId) return;
    if (!ROUTES.lideres) return;

    try {
      const url = new URL(ROUTES.lideres, window.location.origin);
      url.searchParams.set('semillero_id', semilleroId);
      const { data } = await fetchJSON(url.toString());
      const leader = Array.isArray(data) ? data[0] : null;
      if (leader) {
        const opt = Array.from(sel.options).find(o => String(o.value) === String(leader.id));
        if (opt) opt.selected = true; // no deselecciona otros
        updateParticipantsUIFromSelect();
        rebuildChips();
      }
    } catch (e) {
      console.error('Seleccionar líder de semillero:', e);
    }
  }

  // === Listeners ===
  prevBtn?.addEventListener('click', async () => { navigatePeriod(-1); await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
  nextBtn?.addEventListener('click', async () => { navigatePeriod( 1); await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
  todayBtn?.addEventListener('click', async () => { goToToday();          await ensureHolidaysForYear(currentDate.getFullYear()); await loadEventsForCurrentPeriod(); renderCalendar(); });
  closeModal?.addEventListener('click', closeEventModal);
  drawerCloseBtn?.addEventListener('click', closeDetailDrawer);
  drawerCloseCta?.addEventListener('click', closeDetailDrawer);
  cancelBtn?.addEventListener('click', closeEventModal);
  deleteBtn?.addEventListener('click', deleteEvent);
  eventForm?.addEventListener('submit', saveEvent);
  viewBtns.forEach(btn => {
    btn.addEventListener('click', async () => {
      viewBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentView = btn.dataset.view;
      await ensureHolidaysForYear(currentDate.getFullYear());
      await loadEventsForCurrentPeriod();
      renderCalendar();
    });
  });
  eventModal?.addEventListener('click', e => { if (e.target === eventModal) closeEventModal(); });
  detailOverlay?.addEventListener('click', closeDetailDrawer);

  // Botón "Nueva Reunión" (abre modal en la fecha actual)
  const newBtn = document.getElementById('newMeetingBtn');
  if (newBtn && !newBtn.dataset.bound) {
    newBtn.addEventListener('click', () => {
      openEventModal(new Date());
    });
    newBtn.dataset.bound = '1';
  }

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
        const raw = String(ev.fecha_hora || ev.fecha_inicio || ev.fecha || '').trim();
        const parseBackendDateTime = (value) => {
          const s = String(value || '').trim();
          if (!s) return { datePart: '', timePart: '' };
          const tzAware = /[zZ]$/.test(s) || /[+-]\d{2}:\d{2}$/.test(s);
          if (tzAware) {
            const d = new Date(s);
            if (!Number.isNaN(d.getTime())) {
              return {
                datePart: formatDate(d),
                timePart: `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`
              };
            }
          }
          if (s.includes('T')) {
            const parts = s.split('T');
            const datePart = parts[0];
            const t = (parts[1] || '').replace('Z', '');
            const timePart = t.slice(0, 5);
            return { datePart, timePart };
          }
          if (s.includes(' ')) {
            const parts = s.split(' ');
            const datePart = parts[0];
            const timePart = (parts[1] || '').slice(0, 5);
            return { datePart, timePart };
          }
          const d = new Date(s);
          if (!Number.isNaN(d.getTime())) {
            return {
              datePart: formatDate(d),
              timePart: `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`
            };
          }
          return { datePart: '', timePart: '' };
        };

        const { datePart, timePart } = parseBackendDateTime(raw);

        let participantsIds = [];
        try {
          const p = ev.participantes ?? [];
          if (Array.isArray(p)) {
            participantsIds = p.map(x => parseInt(x)).filter(n => !Number.isNaN(n));
          } else if (typeof p === 'string' && p.length) {
            try {
              const arr = JSON.parse(p);
              if (Array.isArray(arr)) participantsIds = arr.map(x => parseInt(x)).filter(n => !Number.isNaN(n));
            } catch (_) {
              participantsIds = p.split(',').map(x => parseInt(x)).filter(n => !Number.isNaN(n));
            }
          }
        } catch (_) {}

        let participantsDetails = [];
        try {
          const pd = ev.participantes_detalle ?? ev.participantes_detalles ?? [];
          if (Array.isArray(pd)) {
            participantsDetails = pd;
          } else if (pd && typeof pd === 'object' && Array.isArray(pd.data)) {
            participantsDetails = pd.data;
          }
        } catch (_) {}

        return {
          id: ev.id_evento,
          date: datePart,
          time: timePart || '09:00',
          title: ev.titulo,
          duration: ev.duracion || 60,
          type: ev.tipo || 'general',
          location: ev.ubicacion || '',
          description: ev.descripcion || '',
          id_proyecto: ev.id_proyecto || null,
          researchLine: ev.linea_investigacion || '',
          link: ev.link_virtual || '',
          idAdmin: ev.id_admin ?? null,
          createdBy: ev.creado_por ?? null,
          idLiderSemi: ev.id_lider_semi ?? null,
          readOnly: !!(ev.id_lider_semi ?? null) && !(ev.id_admin ?? null),
          participantsIds,
          participantsDetails,
          semilleroLeader: ev.lider_semillero ?? null,
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
    weekView.style.display  = 'none';
    dayView.style.display   = 'none';
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

    // marcar festivo visualmente
    if (HOLIDAYS.includes(dateStr)) cell.classList.add('festivo');

    // reglas de bloqueo
    const disabled = isWeekend(date) || HOLIDAYS.includes(dateStr) || isPastDay(date);
    if (disabled) {
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
    weekView.style.display  = 'block';
    dayView.style.display   = 'none';
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
      const dateStrX = formatDate(date);
      const isActive = dateStrX === formatDate(currentDate);
      if (isToday(date)) header.classList.add('today');
      if (isActive) header.classList.add('active');
      if (HOLIDAYS.includes(dateStrX)) header.classList.add('festivo');
      if (isActive) {
        header.innerHTML = `<div class="week-day-pill"><div class="wd-name">${getDayName(date)}</div><div class="wd-day">${date.getDate()}</div></div>`;
      } else {
        header.innerHTML = `<div class="week-day-name">${getDayName(date)}</div><div class="week-day-number">${date.getDate()}</div>`;
      }
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
      const hmLabel = h * 100;
      if (hmLabel >= 1200 && hmLabel <= 1355) tl.classList.add('lunch');
      timesCol.appendChild(tl);
    });
    weekGrid.appendChild(timesCol);

    weekDays.forEach(date => {
      const dayCol  = document.createElement('div');
      dayCol.className = 'week-day-column';
      const dateStr = formatDate(date);
      if (HOLIDAYS.includes(dateStr)) dayCol.classList.add('festivo');
      if (isToday(date)) dayCol.classList.add('today');
      if (dateStr === formatDate(currentDate)) dayCol.classList.add('active');

      const disabledDay = isWeekend(date) || HOLIDAYS.includes(dateStr) || isPastDay(date);
      WORK_HOURS.forEach(hour => {
        const slot = document.createElement('div');
        slot.className = 'week-time-slot';
        slot.dataset.date = dateStr;
        slot.dataset.hour = hour;

        const hm      = hour * 100;
        const isLunch = hm >= 1200 && hm <= 1355;
        const allowed = !disabledDay && hm >= 800 && hm < 1700 && !isLunch;

        if (isLunch) slot.classList.add('lunch');

        if (!allowed) {
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
        dayCol.appendChild(slot);
      });

      events.filter(e => e.date === dateStr)
        .forEach(event => dayCol.appendChild(createWeekEventElement(event)));

      weekGrid.appendChild(dayCol);
    });
  }

  function renderDayView() {
    monthView.style.display = 'none';
    weekView.style.display  = 'none';
    dayView.style.display   = 'block';
    weekView.classList.remove('active');
    dayView.classList.add('active');

    const dateStr    = formatDate(currentDate);
    const isHoliday  = HOLIDAYS.includes(dateStr);
    const isDisabled = isWeekend(currentDate) || isHoliday || isPastDay(currentDate);

    const dayHeader = document.getElementById('dayHeader');
    const DOW3 = ['DOM','LUN','MAR','MIE','JUE','VIE','SAB'];
    const dow = DOW3[currentDate.getDay()] || '';
    const dd  = currentDate.getDate();
    dayHeader.innerHTML = `
      <div class="day-header-card">
        <div class="dh-week">${dow}</div>
        <div class="dh-day">${dd}</div>
      </div>
    `;
    if (isHoliday) {
      const badge = document.createElement('span');
      badge.className = 'badge-festivo';
      badge.textContent = 'Festivo';
      dayHeader.appendChild(badge);
    }

    const dayGrid = document.getElementById('dayGrid');
    dayGrid.innerHTML = '';

    const timesCol = document.createElement('div');
    timesCol.className = 'day-times';
    if (isToday(currentDate)) timesCol.classList.add('today');
    WORK_HOURS.forEach(h => {
      const tl = document.createElement('div');
      tl.className = 'day-time-label';
      tl.textContent = formatHour(h);
      // Sombrear almuerzo también en la columna de horas
      const hmLabel = h * 100;
      if (hmLabel >= 1200 && hmLabel <= 1355) tl.classList.add('lunch');
      timesCol.appendChild(tl);
    });
    dayGrid.appendChild(timesCol);

    const slotsCol = document.createElement('div');
    slotsCol.className = 'day-slots';
    if (isToday(currentDate)) slotsCol.classList.add('today');

    // Sombreado por festivo (vista Día)
    if (isHoliday) {
      timesCol.classList.add('festivo');
      slotsCol.classList.add('festivo');
    }

    WORK_HOURS.forEach(hour => {
      const slot = document.createElement('div');
      slot.className = 'day-time-slot';
      slot.dataset.date = dateStr;
      slot.dataset.hour = hour;

      const hm      = hour * 100;
      const isLunch = hm >= 1200 && hm <= 1355;
      const allowed = !isDisabled && hm >= 800 && hm < 1700 && !isLunch;

      if (isLunch) slot.classList.add('lunch');

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

    // Indicador de hora actual
    const now = new Date();
    const isTodayLocal = isToday(currentDate);
    if (isTodayLocal) {
      const hmNow = now.getHours() * 100 + now.getMinutes();
      const insideWork = hmNow >= 800 && hmNow < 1700 && !(hmNow >= 1200 && hmNow <= 1355);
      if (insideWork) {
        const topPx = (now.getHours() - 8) * 80 + Math.round((now.getMinutes() / 60) * 80);
        const line = document.createElement('div');
        line.className = 'day-current-time';
        line.style.top = `${topPx}px`;
        slotsCol.appendChild(line);
      }
    }

    events.filter(e => e.date === dateStr)
      .forEach(event => slotsCol.appendChild(createDayEventElement(event)));

    dayGrid.appendChild(slotsCol);

    // Auto-actualizar la línea de hora actual cada minuto cuando sea hoy
    if (isTodayLocal) {
      if (renderDayView._timer) clearInterval(renderDayView._timer);
      renderDayView._timer = setInterval(() => {
        // Solo si seguimos en vista día y en el mismo día
        if (currentView !== 'day') { clearInterval(renderDayView._timer); return; }
        const t = new Date();
        if (!isToday(currentDate)) { clearInterval(renderDayView._timer); return; }
        renderDayView();
      }, 60000);
    }
  }

  // === Nodos de evento ===
  function createEventElement(event) {
    const el = document.createElement('div');
    el.className = 'event';
    el.draggable = !event.readOnly;
    if (event.readOnly) {
      el.classList.add('event-readonly');
    }
    el.dataset.id = event.id;
    el.innerHTML = `<div class="event-time">${event.time}</div><div class="event-title">${event.title}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    if (!event.readOnly) {
      el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
      el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    }
    return el;
  }

  function createWeekEventElement(event) {
    const el = document.createElement('div');
    el.className = 'week-event';
    el.draggable = !event.readOnly;
    if (event.readOnly) {
      el.classList.add('event-readonly');
    }
    el.dataset.id = event.id;
    const parts = String(event.time || '00:00').split(':');
    const h = parseInt(parts[0] || '0', 10);
    const m = parseInt(parts[1] || '0', 10);
    const top = (h - 8) * 60 + (isNaN(m) ? 0 : m) + 2;
    const durationMin = Math.max(15, Number(event.duration) || 60);
    const colHeight = WORK_HOURS.length * 60;
    let height = Math.round((durationMin / 60) * 60) - 4;
    if (top + height > colHeight) {
      height = Math.max(20, colHeight - top - 2);
    }
    el.style.top = `${Math.max(0, top)}px`;
    el.style.height = `${Math.max(20, height)}px`;
    el.innerHTML = `<div class="we-time">${event.time}</div><div class="we-title">${escapeHtml(event.title || '')}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    if (!event.readOnly) {
      el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
      el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    }
    return el;
  }

  function createDayEventElement(event) {
    const el = document.createElement('div');
    el.className = 'day-event';
    el.draggable = !event.readOnly;
    if (event.readOnly) {
      el.classList.add('event-readonly');
    }
    el.dataset.id = event.id;

    const parts = String(event.time || '00:00').split(':');
    const h = parseInt(parts[0] || '0', 10);
    const m = parseInt(parts[1] || '0', 10);
    const top = (h - 8) * 80 + Math.round(((isNaN(m) ? 0 : m) / 60) * 80) + 2;
    const durationMin = Math.max(15, Number(event.duration) || 60);
    const colHeight = WORK_HOURS.length * 80;
    let height = Math.round((durationMin / 60) * 80) - 4;
    if (top + height > colHeight) {
      height = Math.max(20, colHeight - top - 2);
    }
    el.style.top = `${Math.max(0, top)}px`;
    el.style.height = `${Math.max(20, height)}px`;
    el.innerHTML = `<div style="font-weight:600;font-size:13px;">${event.time}</div><div style="font-size:12px;margin-top:2px;">${escapeHtml(event.title || '')}</div>`;
    el.addEventListener('click', (e) => { e.stopPropagation(); showEventDetails(event); });
    if (!event.readOnly) {
      el.addEventListener('dragstart', (e) => { draggedEvent = event; el.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
      el.addEventListener('dragend',   ()  => { el.classList.remove('dragging'); draggedEvent = null; });
    }
    return el;
  }

  function handleDragOver(e) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; e.currentTarget.classList.add('drag-over'); }
  function handleDragLeave(e) { e.currentTarget.classList.remove('drag-over'); }

  async function handleDrop(e, newDate) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (!draggedEvent) return;

    if (draggedEvent.readOnly) {
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('Este evento es solo lectura','info');
      return;
    }

    const idx = events.findIndex(ev => ev.id === draggedEvent.id);
    if (idx === -1) return;

    if (events[idx]?.readOnly) {
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('Este evento es solo lectura','info');
      return;
    }

    const newDateStr = formatDate(newDate);
    let newTime = events[idx].time;

    if (e.currentTarget.dataset.hour) {
      const hour = parseInt(e.currentTarget.dataset.hour);
      newTime = `${String(hour).padStart(2, '0')}:00`;
    }

    const candidate = new Date(`${newDateStr}T${newTime}:00`);
    if (!isAllowedDateTime(candidate) || isPastDateTime(candidate)) {
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('No puedes mover el evento a un horario no permitido o pasado','error'); else alert('No puedes mover el evento a una fecha/horario no permitido o pasado.');
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

    eventForm?.reset();

    // Limpiar selección de participantes y refrescar UI
    {
      const selParts = document.getElementById('event-participants');
      if (selParts) {
        Array.from(selParts.options).forEach(o => { o.selected = false; });
        updateParticipantsUIFromSelect();
        rebuildChips();
      }
    }

    // Controles de fecha/hora
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');

    // Fecha mínima = hoy y banner
    if (dateInput) {
      const todayStr = formatDate(new Date());
      dateInput.setAttribute('min', todayStr);
      const dInput = isPastDay(date) ? new Date() : date;
      dateInput.value = formatDate(dInput);
      if (!dateInput.dataset.bannerBound) {
        dateInput.addEventListener('change', () => updateDateBanner(dateInput.value));
        dateInput.dataset.bannerBound = '1';
      }
    }
    // Actualizar SIEMPRE el banner (aunque no exista #event-date)
    {
      const src = (date instanceof Date) ? date : (selectedDate instanceof Date ? selectedDate : new Date());
      updateDateBannerFromDate(src);
    }

    // Hora: input (si existe) o autoselección
    if (timeInput) {
      timeInput.setAttribute('min', '08:00');
      timeInput.setAttribute('max', '16:59');
      const pad = n => String(n).padStart(2, '0');
      let hh, mm;
      if (date instanceof Date) { hh = date.getHours(); mm = date.getMinutes(); }
      const isInvalidHour = (h) => (typeof h !== 'number') || h < 8 || h > 16 || (h >= 12 && h < 14);
      if (isInvalidHour(hh)) {
        const now = new Date();
        const rounded = new Date(now);
        rounded.setSeconds(0, 0);
        const m = rounded.getMinutes();
        const r = Math.round(m / 5) * 5;
        rounded.setMinutes(r >= 60 ? 55 : r);
        let candH = rounded.getHours();
        if (isInvalidHour(candH)) candH = 10; // fallback a 10:00 AM
        hh = candH; mm = rounded.getMinutes();
      }
      timeInput.value = `${pad(hh)}:${pad(mm)}`;
      updateSelectedTimeDisplay(timeInput.value);
      if (!timeInput.dataset.bound) {
        timeInput.addEventListener('input', () => updateSelectedTimeDisplay(timeInput.value));
        timeInput.dataset.bound = '1';
      }
    } else {
      // Sin input de hora: escoger automáticamente una hora disponible
      let chosen = null;
      if (date instanceof Date && isWorkingDayLocal(date)) {
        const h = date.getHours();
        const m = date.getMinutes();
        const pad = n => String(n).padStart(2,'0');
        const invalid = (x)=> (x<8 || x>16 || (x>=12 && x<14));
        const candidate = `${pad(h)}:${pad(m||0)}`;
        const busy = getBusyTimesForDate(date);
        if (!invalid(h) && m === 0 && !busy.has(candidate)) {
          chosen = candidate;
        }
      }
      if (!chosen) {
        chosen = firstAvailableTime(date instanceof Date ? date : new Date());
      }
      const [H,M] = chosen.split(':').map(Number);
      if (selectedDate instanceof Date) {
        selectedDate.setHours(H, M, 0, 0);
      } else if (date instanceof Date) {
        selectedDate = new Date(date);
        selectedDate.setHours(H, M, 0, 0);
      }
      updateSelectedTimeDisplay(chosen);
    }

    eventModal?.classList.add('active');

    // Refresco de banner (seguridad extra)
    const bDay = document.getElementById('date-banner-day');
    const bMonth = document.getElementById('date-banner-month');
    const bLong = document.getElementById('date-banner-long');
    const baseDate = (selectedDate instanceof Date) ? selectedDate : (date instanceof Date ? date : new Date());
    if (bDay && bMonth && bLong && baseDate instanceof Date) {
      const MONTH_ABBR = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
      const WEEK_LONG  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
      const MONTH_LONG = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
      const dd = String(baseDate.getDate());
      const mm = MONTH_ABBR[baseDate.getMonth()] || '';
      const weekday = WEEK_LONG[baseDate.getDay()] || '';
      const long = `${capitalize(weekday)}, ${dd} de ${MONTH_LONG[baseDate.getMonth()]} ${baseDate.getFullYear()}`;
      bDay.textContent = dd;
      bMonth.textContent = mm;
      bLong.textContent = long;
    }
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
    if (isPastDateTime(when)) { if (typeof mostrarNotificacion==='function') mostrarNotificacion('No puedes crear reuniones en fecha/horario pasado','error'); else alert('No puedes crear reuniones en fecha/horario pasado.'); return; }
    if (!isAllowedDateTime(when)) { if (typeof mostrarNotificacion==='function') mostrarNotificacion('Horario no permitido','error'); else alert('Horario no permitido.'); return; }

    try {
      if (editingEventId) {
        await fetchJSON(`${ROUTES.baseEventos}/${editingEventId}`, { method: 'PUT', body: JSON.stringify(payload) });
        if (payload.ubicacion === 'virtual') {
          try { await fetchJSON(`${ROUTES.baseEventos}/${editingEventId}/generar-enlace`, { method: 'POST', body: JSON.stringify({ plataforma: 'teams' }) }); } catch (_) {}
        }
      } else {
        const resp = await fetchJSON(ROUTES.baseEventos, { method: 'POST', body: JSON.stringify(payload) });
        const newId = resp?.evento?.id_evento || resp?.id_evento || resp?.evento?.id || null;
        if (payload.ubicacion === 'virtual' && newId) {
          try { await fetchJSON(`${ROUTES.baseEventos}/${newId}/generar-enlace`, { method: 'POST', body: JSON.stringify({ plataforma: 'teams' }) }); } catch (_) {}
        }
      }
      closeEventModal();
      await loadEventsForCurrentPeriod();
      renderCalendar();
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('Reunión guardada correctamente','success');
    } catch (err) {
      console.error('Error guardando evento', err);
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo guardar el evento','error'); else alert('No se pudo guardar el evento.');
    }
  }

  function buildEventPayload() {
    const dateInput  = document.getElementById('event-date');
    const timeInput  = document.getElementById('event-time');
    const titleInput = document.getElementById('event-title');
    const durationInput = document.getElementById('event-duration');
    const descInput  = document.getElementById('event-description');
    const projSelect = document.getElementById('event-proyecto');
    const partsSelect = document.getElementById('event-participants');
    const locSelect  = document.getElementById('event-location');

    const dateStr = dateInput ? dateInput.value : formatDate(selectedDate);
    const pad = n => String(n).padStart(2,'0');
    const invalid = (h)=> (h<8 || h>16 || (h>=12 && h<14));
    let timeStr;
    if (timeInput && timeInput.value) {
      timeStr = timeInput.value;
    } else if (selectedDate instanceof Date) {
      let hh = selectedDate.getHours();
      let mm = selectedDate.getMinutes();
      if (invalid(hh)) { hh = 10; mm = 0; }
      timeStr = `${pad(hh)}:${pad(mm)}`;
    } else {
      timeStr = '10:00';
    }
    const fechaHora = `${dateStr}T${timeStr}:00`;

    const participantes = partsSelect
      ? Array.from(partsSelect.selectedOptions).map(o => parseInt(o.value))
      : [];

    const idProyecto = projSelect && projSelect.value ? parseInt(projSelect.value) : null;

    return {
      titulo: titleInput ? titleInput.value : '',
      tipo: 'REUNION',
      descripcion: descInput ? (descInput.value || null) : null,
      fecha_hora: fechaHora,
      duracion: durationInput ? (parseInt(durationInput.value) || 60) : 60,
      ubicacion: locSelect && locSelect.value ? locSelect.value : 'presencial',
      link_virtual: null,
      recordatorio: 'none',
      id_proyecto: idProyecto,
      participantes
    };
  }

  async function showEventDetails(event) {
    editingEventId = event.id;
    document.getElementById('detail-titulo').textContent = event.title || '--';
    document.getElementById('detail-tipo').textContent   = event.type || '--';
    document.getElementById('detail-fecha').textContent  = formatDateLong(new Date(event.date));
    document.getElementById('detail-hora').textContent   = event.time || '--';
    document.getElementById('detail-duracion').textContent = `${event.duration || 60} minutos`;
    document.getElementById('detail-ubicacion').textContent = event.location || '--';

    const creadorEl = document.getElementById('detail-creador');
    if (creadorEl) {
      const ls = event.semilleroLeader;
      const name = (ls && (ls.nombre || ls.name)) ? String(ls.nombre || ls.name) : '';
      const email = (ls && ls.email) ? String(ls.email) : '';
      creadorEl.textContent = name ? (email ? `${name} (${email})` : name) : '--';
    }

    const descEl = document.getElementById('detail-descripcion');
    if (descEl) descEl.textContent = (event.description || '').trim() || '--';

    const asistenciaSection = document.getElementById('detail-asistencia-section');
    const asistenciaList = document.getElementById('detail-asistencia-list');
    const asistenciaSummary = document.getElementById('detail-asistencia-summary');
    if (asistenciaSection && asistenciaList && asistenciaSummary) {
      const normalizeAttendanceStatus = (raw) => {
        const base = String(raw || 'pendiente').toLowerCase();
        if (base === 'asistio' || base === 'asistió') return 'asistio';
        const compact = base.replace(/[\s_-]+/g, '');
        if (compact === 'noasistio') return 'no_asistio';
        if (compact === 'asistio') return 'asistio';
        return 'pendiente';
      };
      const details = Array.isArray(event.participantsDetails) ? event.participantsDetails : [];
      if (!details.length) {
        asistenciaSection.style.display = 'none';
        asistenciaList.innerHTML = '';
        asistenciaSummary.innerHTML = '';
        asistenciaSummary.style.display = 'none';
      } else {
        asistenciaSection.style.display = 'block';
        asistenciaList.innerHTML = '';
        const counts = { pendiente: 0, asistio: 0, no_asistio: 0 };
        details.forEach(p => {
          const st = normalizeAttendanceStatus(p?.asistencia);
          counts[st] = (counts[st] || 0) + 1;
          const label = st === 'asistio' ? 'Asistió' : (st === 'no_asistio' ? 'No asistió' : 'Pendiente');
          const row = document.createElement('div');
          row.className = 'attendance-item';
          row.innerHTML = `<div class="name">${escapeHtml(p?.nombre || 'Participante')}</div><div class="hint">Estado: ${escapeHtml(label)}</div>`;
          asistenciaList.appendChild(row);
        });
        asistenciaSummary.style.display = 'block';
        asistenciaSummary.innerHTML = `<h5>Resumen</h5><div class="row"><div>Pendiente: ${counts.pendiente}</div><div>Asistió: ${counts.asistio}</div><div>No asistió: ${counts.no_asistio}</div></div>`;
      }
    }

    // Participantes (nombres): usar IDs asignados desde backend (evento_asignaciones)
    const namesList = document.getElementById('detail-participants-names');
    if (namesList) {
      namesList.innerHTML = '';
      try {
        const parts = Array.isArray(event.participantsIds) ? event.participantsIds : [];
        const sel = document.getElementById('event-participants');
        const optById = new Map(
          sel ? Array.from(sel.options).map(o => [String(o.value), o]) : []
        );
        if (parts.length) {
          parts.forEach(pid => {
            const opt = optById.get(String(pid));
            const label = opt ? (opt.textContent || '') : `Líder #${pid}`;
            const name = label.split(' — ')[0] || label;
            const email = label.split(' — ')[1] || '';
            const li = document.createElement('li');
            li.innerHTML = `<div style="font-weight:800;color:#0f172a;">${escapeHtml(name)}</div><div class="drawer-label">${escapeHtml(email)}</div>`;
            namesList.appendChild(li);
          });
        } else {
          namesList.innerHTML = '<li>--</li>';
        }
      } catch (e) {
        console.error('Participantes (nombres):', e);
        namesList.innerHTML = '<li>--</li>';
      }
    }

    const linkField  = document.getElementById('detail-link-field');
    const linkA      = document.getElementById('detail-link');
    const genBtn     = document.getElementById('detail-generate-link');
    const delBtn     = document.getElementById('detail-delete-link');
    const editorBox  = document.getElementById('detail-link-editor');
    const editorInput= document.getElementById('detail-link-input');
    const saveBtn    = document.getElementById('detail-save-link');
    const cancelBtnE = document.getElementById('detail-cancel-edit');

    if (linkField) linkField.style.display = 'block';

    const canEdit = !event.readOnly && (
      (CURRENT_USER_ROLE === 'ADMIN') ||
      (CURRENT_USER_ID !== null && event.idAdmin !== null && parseInt(event.idAdmin, 10) === parseInt(CURRENT_USER_ID, 10)) ||
      (CURRENT_USER_ID !== null && event.createdBy !== null && parseInt(event.createdBy, 10) === parseInt(CURRENT_USER_ID, 10))
    );
    const hasUrl = !!(event.link && event.link.startsWith('http'));

    if (hasUrl) {
      if (linkA) { linkA.href = event.link; linkA.style.display = 'inline-block'; }
      genBtn && (genBtn.style.display = canEdit ? 'inline-block' : 'none');
      delBtn && (delBtn.style.display = canEdit ? 'inline-block' : 'none');
      editorBox && (editorBox.style.display = 'none');
    } else {
      linkA && (linkA.style.display = 'none');
      genBtn && (genBtn.style.display = canEdit ? 'inline-block' : 'none');
      delBtn && (delBtn.style.display = 'none');
      editorBox && (editorBox.style.display = canEdit ? 'block' : 'none');
      editorInput && (editorInput.value = '');
    }

    if (canEdit && genBtn) genBtn.onclick = async () => {
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
          if (typeof mostrarNotificacion==='function') mostrarNotificacion('Enlace generado correctamente','success');
        }
      } catch (err) {
        console.error(err);
        if (window.Swal) Swal.fire({icon:'error',title:'No se pudo generar el enlace'});
        else if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo generar el enlace','error');
        else alert('No se pudo generar el enlace.');
      }
    };

    if (canEdit && saveBtn) saveBtn.onclick = async () => {
      const url = (editorInput?.value || '').trim();
      if (!url) { if (window.Swal) Swal.fire({icon:'info',title:'Ingresa un enlace válido'}); else if (typeof mostrarNotificacion==='function') mostrarNotificacion('Ingresa un enlace válido','info'); else alert('Ingresa un enlace válido'); return; }
      try {
        await fetchJSON(`${ROUTES.baseEventos}/${event.id}`, {
          method: 'PUT',
          body: JSON.stringify({ link_virtual: url }),
        });
        if (linkA) { linkA.href = url; linkA.style.display = 'inline-block'; }
        delBtn && (delBtn.style.display = 'inline-block');
        editorBox && (editorBox.style.display = 'none');
        if (typeof mostrarNotificacion==='function') mostrarNotificacion('Enlace guardado correctamente','success');
      } catch (err) {
        console.error(err);
        if (window.Swal) Swal.fire({icon:'error',title:'No se pudo guardar el enlace'});
        else if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo guardar el enlace','error');
        else alert('No se pudo guardar el enlace.');
      }
    };

    if (canEdit && cancelBtnE) cancelBtnE.onclick = () => { editorBox && (editorBox.style.display = 'none'); };

    if (canEdit && delBtn) delBtn.onclick = async () => {
      if (window.Swal) {
        const r = await Swal.fire({
          title: '¿Eliminar enlace?',
          text: '¿Eliminar el enlace de la reunión?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar'
        });
        if (!r.isConfirmed) return;
      } else {
        if (!confirm('¿Eliminar el enlace de la reunión?')) return;
      }
      try {
        await fetchJSON(`${ROUTES.baseEventos}/${event.id}`, {
          method: 'PUT',
          body: JSON.stringify({ link_virtual: null }),
        });
        linkA && (linkA.style.display = 'none');
        delBtn.style.display = 'none';
        editorBox && (editorBox.style.display = 'block');
        editorInput && (editorInput.value = '');
        if (typeof mostrarNotificacion==='function') mostrarNotificacion('Enlace eliminado correctamente','success');
      } catch (err) {
        console.error(err);
        if (window.Swal) Swal.fire({icon:'error',title:'No se pudo eliminar el enlace'});
        else if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo eliminar el enlace','error');
        else alert('No se pudo eliminar el enlace.');
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
    const projSelect = document.getElementById('event-proyecto');
    const partsSelect = document.getElementById('event-participants');

    if (dateInput)  { dateInput.setAttribute('min', formatDate(new Date())); dateInput.value = event.date; }
    if (dateInput && dateInput.value) { updateDateBanner(dateInput.value); }
    if (timeInput)  { timeInput.value  = event.time; updateSelectedTimeDisplay(event.time); }
    if (titleInput) titleInput.value = event.title;
    if (durationInput) durationInput.value = event.duration;
    if (descInput)  descInput.value  = event.description || '';

    if (projSelect && event.id_proyecto) {
      projSelect.value = String(event.id_proyecto);
    }

    if (partsSelect) {
      const ids = new Set((event.participantsIds || []).map(x => String(x)));
      Array.from(partsSelect.options).forEach(o => { o.selected = ids.has(String(o.value)); });
      updateParticipantsUIFromSelect();
      rebuildChips();
    }

    eventModal?.classList.add('active');
  }

  async function deleteEvent() {
    if (!editingEventId) return;
    if (window.Swal) {
      const r = await Swal.fire({
        title: '¿Eliminar reunión?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
      });
      if (!r.isConfirmed) return;
    } else {
      if (!confirm('¿Estás seguro de que quieres eliminar esta reunión?')) return;
    }
    try {
      await fetchJSON(`${ROUTES.baseEventos}/${editingEventId}`, { method: 'DELETE' });
      closeEventModal();
      closeDetailDrawer();
      await loadEventsForCurrentPeriod();
      renderCalendar();
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('Reunión eliminada','success');
    } catch (err) {
      console.error('Error eliminando evento', err);
      if (window.Swal) Swal.fire({icon:'error',title:'No se pudo eliminar el evento'});
      else if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo eliminar el evento','error');
      else alert('No se pudo eliminar.');
    }
  }

  async function updateEventOnServer(id, newDateStr, newTime) {
    const fecha_hora = `${newDateStr}T${newTime}:00`;
    try {
      const local = events.find(e => e.id === id);
      if (local?.readOnly) {
        if (typeof mostrarNotificacion==='function') mostrarNotificacion('Este evento es solo lectura','info');
        return;
      }
      await fetchJSON(`${ROUTES.baseEventos}/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ fecha_hora }),
      });
    } catch (err) {
      console.error('Error al actualizar (drop)', err);
      if (typeof mostrarNotificacion==='function') mostrarNotificacion('No se pudo mover el evento','error'); else alert('No se pudo mover el evento.');
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
    const MONTH_ABBR = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
    const MONTH_LONG = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    if (currentView === 'month') {
      currentPeriodEl.textContent = `${MONTH_LONG[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    } else if (currentView === 'week') {
      const ws = getWeekStart(currentDate);
      const we = new Date(ws); we.setDate(ws.getDate() + 6);
      const label = `${ws.getDate()} ${MONTH_ABBR[ws.getMonth()]} - ${we.getDate()} ${MONTH_ABBR[we.getMonth()]} ${we.getFullYear()}`;
      currentPeriodEl.textContent = label;
    } else {
      currentPeriodEl.textContent = `${currentDate.getDate()} de ${MONTH_LONG[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
  }

  function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay(); // domingo inicio
    const diff = d.getDate() - day;
    return new Date(d.setDate(diff));
  }

  function isWeekend(date) { const dow = date.getDay(); return dow === 0 || dow === 6; }
  // Formateo local YYYY-MM-DD (evita desfases por UTC de toISOString)
  function formatDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }
  function formatDateLong(date) {
    const days  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const months= ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    return `${days[date.getDay()]}, ${date.getDate()} de ${months[date.getMonth()]} ${date.getFullYear()}`;
  }
  function getDayName(date) { const days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; return days[date.getDay()]; }
  function formatHour(hour) {
    return `${String(hour).padStart(2, '0')}:00`;
  }
  function formatTime12(timeHHMM) {
    if (!timeHHMM || typeof timeHHMM !== 'string') return '--:-- --';
    const [hStr, mStr] = timeHHMM.split(':');
    let h = parseInt(hStr, 10); const m = mStr || '00';
    const period = h >= 12 ? 'PM' : 'AM';
    const displayH = h % 12 || 12;
    return `${displayH}:${m} ${period}`;
  }
  function updateSelectedTimeDisplay(timeHHMM) {
    const el = document.getElementById('display-time');
    if (el) el.textContent = formatTime12(timeHHMM);
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
    if (hm < 800 || hm >= 1700) return false; // hasta antes de 17:00
    if (hm >= 1200 && hm <= 1355) return false;
    return true;
  }

  // === Helpers: disponibilidad por día ===
  function getBusyTimesForDate(date) {
    const dateStr = formatDate(date);
    const set = new Set();
    events.filter(e => e.date === dateStr).forEach(e => set.add(e.time));
    return set;
  }
  function isWorkingDayLocal(date) {
    const d = date.getDay();
    const dateStr = formatDate(date);
    if (d === 0 || d === 6) return false;
    if (HOLIDAYS.includes(dateStr)) return false;
    return true;
  }
  function firstAvailableTime(date) {
    const ALLOWED = [8,9,10,11,14,15,16];
    const busy = getBusyTimesForDate(date);
    const pad = n => String(n).padStart(2,'0');
    const now = new Date();
    const sameDay = date.toDateString() === now.toDateString();
    for (const h of ALLOWED) {
      if (sameDay && (h < now.getHours() || (h === now.getHours() && now.getMinutes() > 0))) {
        continue;
      }
      const hhmm = `${pad(h)}:00`;
      if (!busy.has(hhmm)) return hhmm;
    }
    // Si todo está ocupado, devolver un fallback válido
    return '10:00';
  }
})();
