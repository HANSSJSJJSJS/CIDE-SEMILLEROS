// Sistema de vistas de calendario y selección de horarios
let currentView = 'month';
let selectedDate = null;

// Horarios disponibles (8am a 5pm, excluyendo 12pm a 2pm)
const AVAILABLE_HOURS = [
    '08:00', '09:00', '10:00', '11:00', 
    // 12:00 - 14:00 bloqueado (almuerzo)
    '14:00', '15:00', '16:00', '17:00'
];

// Días festivos de Colombia 2025-2026 (formato: 'YYYY-MM-DD')
const FESTIVOS_COLOMBIA = [
    // 2025
    '2025-01-01', '2025-01-06', '2025-03-24', '2025-04-17', '2025-04-18',
    '2025-05-01', '2025-06-02', '2025-06-09', '2025-06-23', '2025-06-30',
    '2025-07-20', '2025-08-07', '2025-08-18', '2025-10-13', '2025-11-03',
    '2025-11-17', '2025-12-08', '2025-12-25',
    // 2026
    '2026-01-01', '2026-01-12', '2026-03-23', '2026-04-02', '2026-04-03',
    '2026-05-01', '2026-05-18', '2026-06-08', '2026-06-15', '2026-06-29',
    '2026-07-20', '2026-08-07', '2026-08-17', '2026-10-12', '2026-11-02',
    '2026-11-16', '2026-12-08', '2026-12-25'
];

// Función para verificar si es fin de semana
function isWeekend(date) {
    const day = date.getDay();
    return day === 0 || day === 6; // 0 = Domingo, 6 = Sábado
}

// Función para verificar si es festivo
function isFestivo(date) {
    const dateStr = date.toISOString().split('T')[0];
    return FESTIVOS_COLOMBIA.includes(dateStr);
}

// Función para verificar si es día laborable
function isWorkingDay(date) {
    return !isWeekend(date) && !isFestivo(date);
}

// Función para verificar si la fecha/hora ya pasó
function isPastDateTime(date, hour = null) {
    const now = new Date();
    const checkDate = new Date(date);
    
    if (hour) {
        const [h, m] = hour.split(':');
        checkDate.setHours(parseInt(h), parseInt(m), 0, 0);
        return checkDate < now;
    }
    
    // Solo comparar fecha (sin hora)
    checkDate.setHours(0, 0, 0, 0);
    now.setHours(0, 0, 0, 0);
    return checkDate < now;
}

// Función para convertir hora militar a formato 12 horas
function formatTime12Hour(time24) {
    const [hours, minutes] = time24.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notif = document.createElement('div');
    notif.className = `notificacion notificacion-${tipo}`;
    notif.innerHTML = `
        <i class="fas fa-${tipo === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${mensaje}</span>
    `;
    
    // Agregar al body
    document.body.appendChild(notif);
    
    // Mostrar con animación
    setTimeout(() => notif.classList.add('show'), 10);
    
    // Ocultar después de 4 segundos
    setTimeout(() => {
        notif.classList.remove('show');
        setTimeout(() => notif.remove(), 300);
    }, 4000);
}

// Inicializar vistas adicionales
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners para cambio de vista
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            switchView(view);
        });
    });
    
    // Botón "Hoy"
    const todayBtn = document.getElementById('today-btn');
    if (todayBtn) {
        todayBtn.addEventListener('click', goToToday);
    }
    
    // Actualizar navegación para que funcione con todas las vistas
    const prevBtn = document.getElementById('prev-period');
    const nextBtn = document.getElementById('next-period');
    
    if (prevBtn) prevBtn.addEventListener('click', goToPreviousPeriod);
    if (nextBtn) nextBtn.addEventListener('click', goToNextPeriod);
    
    // Modal de selección de horario
    const closeTimeSlotModal = document.getElementById('close-time-slot-modal');
    if (closeTimeSlotModal) {
        closeTimeSlotModal.addEventListener('click', closeTimeSlotModalFunc);
    }
    
    // Cerrar modal al hacer clic fuera
    const timeSlotModal = document.getElementById('time-slot-modal');
    if (timeSlotModal) {
        window.addEventListener('click', function(event) {
            if (event.target === timeSlotModal) {
                closeTimeSlotModalFunc();
            }
        });
    }
});

// Cambiar vista
function switchView(view) {
    currentView = view;
    
    // Actualizar botones activos
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`.view-btn[data-view="${view}"]`).classList.add('active');
    
    // Ocultar todas las vistas
    document.querySelectorAll('.calendar-view').forEach(v => {
        v.style.display = 'none';
        v.classList.remove('active');
    });
    
    // Mostrar vista seleccionada
    const viewElement = document.getElementById(`${view}-view`);
    if (viewElement) {
        viewElement.style.display = 'block';
        viewElement.classList.add('active');
    }
    
    // Renderizar la vista correspondiente
    switch(view) {
        case 'year':
            renderYearView();
            break;
        case 'month':
            renderCalendar(); // Función existente
            break;
        case 'week':
            renderWeekView();
            break;
        case 'day':
            renderDayView();
            break;
    }
    
    updatePeriodLabel();
}

// Actualizar etiqueta del período
function updatePeriodLabel() {
    const periodLabel = document.getElementById('current-period');
    if (!periodLabel) return;
    
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    
    switch(currentView) {
        case 'year':
            periodLabel.textContent = currentDate.getFullYear();
            break;
        case 'month':
            periodLabel.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            break;
        case 'week':
            const weekStart = getWeekStart(currentDate);
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekEnd.getDate() + 6);
            
            // Formato: "Semana del 20/10/2025 al 26/10/2025"
            const formatDate = (date) => {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            };
            
            periodLabel.textContent = `Semana del ${formatDate(weekStart)} al ${formatDate(weekEnd)}`;
            break;
        case 'day':
            periodLabel.textContent = `${dayNames[currentDate.getDay()]}, ${currentDate.getDate()} de ${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            break;
    }
}

// Navegación de períodos
function goToPreviousPeriod() {
    switch(currentView) {
        case 'year':
            currentDate.setFullYear(currentDate.getFullYear() - 1);
            break;
        case 'month':
            currentDate.setMonth(currentDate.getMonth() - 1);
            break;
        case 'week':
            currentDate.setDate(currentDate.getDate() - 7);
            break;
        case 'day':
            currentDate.setDate(currentDate.getDate() - 1);
            break;
    }
    switchView(currentView);
    cargarEventos();
}

function goToNextPeriod() {
    switch(currentView) {
        case 'year':
            currentDate.setFullYear(currentDate.getFullYear() + 1);
            break;
        case 'month':
            currentDate.setMonth(currentDate.getMonth() + 1);
            break;
        case 'week':
            currentDate.setDate(currentDate.getDate() + 7);
            break;
        case 'day':
            currentDate.setDate(currentDate.getDate() + 1);
            break;
    }
    switchView(currentView);
    cargarEventos();
}

function goToToday() {
    currentDate = new Date();
    switchView(currentView);
    cargarEventos();
}

// Obtener inicio de semana (lunes)
function getWeekStart(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

// Renderizar vista de año
function renderYearView() {
    const yearGrid = document.getElementById('year-grid');
    if (!yearGrid) return;
    
    yearGrid.innerHTML = '';
    const year = currentDate.getFullYear();
    
    console.log(`Renderizando vista de año: ${year}`);
    
    for (let month = 0; month < 12; month++) {
        const monthDiv = document.createElement('div');
        monthDiv.className = 'year-month';
        
        const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        
        console.log(`Renderizando mes ${month}: ${monthNames[month]}`);
        
        monthDiv.innerHTML = `
            <div class="year-month-name">${monthNames[month]}</div>
            <div class="year-month-grid" id="year-month-${month}"></div>
        `;
        
        yearGrid.appendChild(monthDiv);
        
        // Renderizar días del mes
        renderYearMonth(month, year);
        
        // Click en el mes para ir a vista mensual
        monthDiv.addEventListener('click', () => {
            currentDate = new Date(year, month, 1);
            switchView('month');
            cargarEventos();
        });
    }
    
    console.log(`Total de meses renderizados: ${yearGrid.children.length}`);
}

function renderYearMonth(month, year) {
    const grid = document.getElementById(`year-month-${month}`);
    if (!grid) return;
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const firstDayOfWeek = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
    
    // Días vacíos al inicio
    for (let i = 0; i < firstDayOfWeek; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'year-month-day';
        grid.appendChild(emptyDay);
    }
    
    // Días del mes
    const today = new Date();
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'year-month-day';
        dayDiv.textContent = day;
        
        const cellDate = new Date(year, month, day);
        
        // Marcar hoy
        if (cellDate.toDateString() === today.toDateString()) {
            dayDiv.classList.add('today');
        }
        
        // Verificar si tiene eventos
        const hasEvents = eventos.some(event => {
            const eventDate = new Date(event.fecha_hora);
            return eventDate.getDate() === day &&
                   eventDate.getMonth() === month &&
                   eventDate.getFullYear() === year;
        });
        
        if (hasEvents) {
            dayDiv.classList.add('has-event');
        }
        
        grid.appendChild(dayDiv);
    }
}

// Renderizar vista de semana
function renderWeekView() {
    const weekContainer = document.getElementById('week-container');
    if (!weekContainer) return;
    
    weekContainer.innerHTML = '';
    
    const weekStart = getWeekStart(currentDate);
    const dayNames = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
    
    // Header
    const header = document.createElement('div');
    header.className = 'week-header';
    header.innerHTML = '<div class="week-time-label">Hora</div>';
    
    for (let i = 0; i < 7; i++) {
        const day = new Date(weekStart);
        day.setDate(day.getDate() + i);
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'week-day-header';
        
        const today = new Date();
        if (day.toDateString() === today.toDateString()) {
            dayHeader.classList.add('today');
        }
        
        dayHeader.innerHTML = `
            ${dayNames[day.getDay()]}
            <span class="day-number">${day.getDate()}</span>
        `;
        header.appendChild(dayHeader);
    }
    
    weekContainer.appendChild(header);
    
    // Body con horarios
    const body = document.createElement('div');
    body.className = 'week-body';
    
    AVAILABLE_HOURS.forEach(hour => {
        const row = document.createElement('div');
        row.className = 'week-time-row';
        
        row.innerHTML = `<div class="week-time-cell">${hour}</div>`;
        
        for (let i = 0; i < 7; i++) {
            const day = new Date(weekStart);
            day.setDate(day.getDate() + i);
            
            const cell = document.createElement('div');
            cell.className = 'week-hour-cell';
            // Hacer celda droppable para mover eventos a esta fecha/hora
            cell.addEventListener('dragover', (e) => {
                e.preventDefault();
                cell.classList.add('drag-over');
            });
            cell.addEventListener('dragleave', () => cell.classList.remove('drag-over'));
            
            // Verificar si hay evento en este horario
            const cellDateTime = new Date(day);
            const [h, m] = hour.split(':');
            cellDateTime.setHours(parseInt(h), parseInt(m), 0, 0);
            
            const eventInSlot = eventos.find(event => {
                const eventDate = new Date(event.fecha_hora);
                // Comparar fecha, hora y minuto (ignorar segundos y milisegundos)
                return eventDate.getFullYear() === cellDateTime.getFullYear() &&
                       eventDate.getMonth() === cellDateTime.getMonth() &&
                       eventDate.getDate() === cellDateTime.getDate() &&
                       eventDate.getHours() === cellDateTime.getHours() &&
                       eventDate.getMinutes() === cellDateTime.getMinutes();
            });
            
            if (eventInSlot) {
                cell.classList.add('has-event');
                const evDiv = document.createElement('div');
                evDiv.className = 'week-event';
                evDiv.textContent = eventInSlot.titulo;
                evDiv.setAttribute('draggable', 'true');
                evDiv.dataset.eventId = eventInSlot.id;
                evDiv.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/event-id', String(eventInSlot.id));
                    e.dataTransfer.effectAllowed = 'move';
                    evDiv.classList.add('dragging');
                });
                evDiv.addEventListener('dragend', () => evDiv.classList.remove('dragging'));
                evDiv.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (typeof openEventDrawerById === 'function') {
                        openEventDrawerById(eventInSlot.id);
                    }
                });
                cell.appendChild(evDiv);
            } else {
                cell.addEventListener('click', () => {
                    openTimeSlotModal(day, hour);
                });
            }

            // Drop para reprogramar a esta fecha/hora
            cell.addEventListener('drop', (e) => {
                e.preventDefault();
                cell.classList.remove('drag-over');
                const eventId = e.dataTransfer.getData('text/event-id');
                if (!eventId) return;
                // Validaciones
                if (typeof isPastDateTime === 'function' && isPastDateTime(day, hour)) {
                    mostrarNotificacion('No puedes mover reuniones a horas pasadas', 'error');
                    return;
                }
                if (typeof isWorkingDay === 'function' && !isWorkingDay(day)) {
                    mostrarNotificacion('Solo se pueden programar reuniones en días laborables', 'error');
                    return;
                }
                if (typeof updateEventDateTime === 'function') {
                    updateEventDateTime(eventId, day, hour);
                }
            });
            
            row.appendChild(cell);
        }
        
        body.appendChild(row);
    });
    
    // Agregar fila de almuerzo bloqueada
    const lunchRow = document.createElement('div');
    lunchRow.className = 'week-time-row';
    lunchRow.innerHTML = '<div class="week-time-cell">12:00-14:00</div>';
    
    for (let i = 0; i < 7; i++) {
        const cell = document.createElement('div');
        cell.className = 'week-hour-cell blocked';
        cell.title = 'Hora de almuerzo';
        lunchRow.appendChild(cell);
    }
    
    // Insertar después de las 11:00
    const rows = body.querySelectorAll('.week-time-row');
    if (rows.length >= 4) {
        body.insertBefore(lunchRow, rows[4]);
    }
    
    weekContainer.appendChild(body);
}

// Renderizar vista de día
function renderDayView() {
    const dayContainer = document.getElementById('day-container');
    if (!dayContainer) return;
    
    dayContainer.innerHTML = '';
    
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    
    // Header
    const header = document.createElement('div');
    header.className = 'day-header';
    header.innerHTML = `
        <div class="day-name">${dayNames[currentDate.getDay()]}</div>
        <div class="day-date">${currentDate.getDate()}</div>
        <div>${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}</div>
    `;
    dayContainer.appendChild(header);
    
    // Body con horarios
    const body = document.createElement('div');
    body.className = 'day-body';
    
    AVAILABLE_HOURS.forEach(hour => {
        const slot = document.createElement('div');
        slot.className = 'day-time-slot';
        
        const timeLabel = document.createElement('div');
        timeLabel.className = 'day-time-label';
        timeLabel.textContent = hour;
        
        const content = document.createElement('div');
        content.className = 'day-time-content';
        // Permitir drop para mover eventos a esta hora
        content.addEventListener('dragover', (e) => {
            e.preventDefault();
            content.classList.add('drag-over');
        });
        content.addEventListener('dragleave', () => content.classList.remove('drag-over'));
        
        // Verificar si hay evento en este horario
        const cellDateTime = new Date(currentDate);
        const [h, m] = hour.split(':');
        cellDateTime.setHours(parseInt(h), parseInt(m), 0, 0);
        
        const eventsInSlot = eventos.filter(event => {
            const eventDate = new Date(event.fecha_hora);
            // Comparar fecha, hora y minuto (ignorar segundos y milisegundos)
            return eventDate.getFullYear() === cellDateTime.getFullYear() &&
                   eventDate.getMonth() === cellDateTime.getMonth() &&
                   eventDate.getDate() === cellDateTime.getDate() &&
                   eventDate.getHours() === cellDateTime.getHours() &&
                   eventDate.getMinutes() === cellDateTime.getMinutes();
        });
        
        if (eventsInSlot.length > 0) {
            eventsInSlot.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'day-event';
                eventDiv.innerHTML = `
                    <div class="day-event-title">${event.titulo}</div>
                    <div class="day-event-time">${hour} - ${event.duracion} minutos</div>
                    <div class="day-event-participants">
                        <i class="fas fa-users"></i> ${event.participantes ? event.participantes.length : 0} participantes
                    </div>
                `;
                eventDiv.setAttribute('draggable', 'true');
                eventDiv.dataset.eventId = event.id;
                eventDiv.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/event-id', String(event.id));
                    e.dataTransfer.effectAllowed = 'move';
                    eventDiv.classList.add('dragging');
                });
                eventDiv.addEventListener('dragend', () => eventDiv.classList.remove('dragging'));
                eventDiv.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (typeof openEventDrawerById === 'function') {
                        openEventDrawerById(event.id);
                    }
                });
                content.appendChild(eventDiv);
            });
        } else {
            content.addEventListener('click', () => {
                openTimeSlotModal(currentDate, hour);
            });
        }

        // Drop para reprogramar en esta hora
        content.addEventListener('drop', (e) => {
            e.preventDefault();
            content.classList.remove('drag-over');
            const eventId = e.dataTransfer.getData('text/event-id');
            if (!eventId) return;
            if (typeof isPastDateTime === 'function' && isPastDateTime(currentDate, hour)) {
                mostrarNotificacion('No puedes mover reuniones a horas pasadas', 'error');
                return;
            }
            if (typeof isWorkingDay === 'function' && !isWorkingDay(currentDate)) {
                mostrarNotificacion('Solo se pueden programar reuniones en días laborables', 'error');
                return;
            }
            if (typeof updateEventDateTime === 'function') {
                updateEventDateTime(eventId, currentDate, hour);
            }
        });
        
        slot.appendChild(timeLabel);
        slot.appendChild(content);
        body.appendChild(slot);
    });
    
    // Agregar slot de almuerzo bloqueado
    const lunchSlot = document.createElement('div');
    lunchSlot.className = 'day-time-slot';
    lunchSlot.innerHTML = `
        <div class="day-time-label">12:00 - 14:00</div>
        <div class="day-time-content blocked"></div>
    `;
    
    // Insertar después de las 11:00
    const slots = body.querySelectorAll('.day-time-slot');
    if (slots.length >= 4) {
        body.insertBefore(lunchSlot, slots[4]);
    }
    
    dayContainer.appendChild(body);
}

// Abrir modal de selección de horario
function openTimeSlotModal(date, preselectedHour = null) {
    selectedDate = new Date(date);
    
    // Validar que no sea día pasado
    if (isPastDateTime(selectedDate)) {
        mostrarNotificacion('No puedes agendar reuniones en fechas pasadas', 'error');
        return;
    }
    
    // Validar que sea día laborable
    if (!isWorkingDay(selectedDate)) {
        let mensaje = 'No se pueden agendar reuniones en ';
        if (isWeekend(selectedDate)) {
            mensaje += 'fines de semana';
        } else if (isFestivo(selectedDate)) {
            mensaje += 'días festivos';
        }
        mostrarNotificacion(mensaje, 'error');
        return;
    }
    
    const modal = document.getElementById('time-slot-modal');
    const dateLabel = document.getElementById('time-slot-date');
    
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    
    dateLabel.textContent = `${dayNames[selectedDate.getDay()]}, ${selectedDate.getDate()} de ${monthNames[selectedDate.getMonth()]} ${selectedDate.getFullYear()}`;
    
    renderTimeSlots(preselectedHour);
    
    modal.classList.add('active');
}

function closeTimeSlotModalFunc() {
    const modal = document.getElementById('time-slot-modal');
    modal.classList.remove('active');
    selectedDate = null;
}

// Renderizar slots de tiempo disponibles
function renderTimeSlots(preselectedHour) {
    const container = document.getElementById('time-slots-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="time-slots-legend" style="grid-column: 1 / -1;">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Disponible</span>
            </div>
            <div class="legend-item">
                <div class="legend-color blocked"></div>
                <span>Hora de almuerzo</span>
            </div>
            <div class="legend-item">
                <div class="legend-color occupied"></div>
                <span>Ocupado</span>
            </div>
        </div>
    `;
    
    // Generar todos los horarios (8am a 5pm)
    const allHours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
    
    allHours.forEach(hour => {
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        
        // Verificar si es hora de almuerzo
        const hourNum = parseInt(hour.split(':')[0]);
        const isLunchTime = hourNum >= 12 && hourNum < 14;
        
        // Verificar si la hora ya pasó
        const hasPassed = isPastDateTime(selectedDate, hour);
        
        // Verificar si ya hay evento en este horario
        const slotDateTime = new Date(selectedDate);
        const [h, m] = hour.split(':');
        slotDateTime.setHours(parseInt(h), parseInt(m), 0);
        
        const isOccupied = eventos.some(event => {
            const eventDate = new Date(event.fecha_hora);
            return eventDate.getTime() === slotDateTime.getTime();
        });
        
        let status = 'Disponible';
        if (hasPassed) {
            slot.classList.add('blocked');
            status = 'Hora pasada';
        } else if (isLunchTime) {
            slot.classList.add('blocked');
            status = 'Almuerzo';
        } else if (isOccupied) {
            slot.classList.add('occupied');
            status = 'Ocupado';
        }
        
        // Usar formato de 12 horas
        const time12 = formatTime12Hour(hour);
        
        slot.innerHTML = `
            <div class="time-slot-time">${time12}</div>
            <div class="time-slot-status">${status}</div>
        `;
        
        // Solo permitir click en horarios disponibles (no pasados, no almuerzo, no ocupados)
        if (!hasPassed && !isLunchTime && !isOccupied) {
            slot.addEventListener('click', () => {
                selectTimeSlot(hour);
            });
        }
        
        container.appendChild(slot);
    });
}

// Seleccionar horario y abrir modal de evento
function selectTimeSlot(hour) {
    // Guardar la fecha ANTES de cerrar el modal (porque closeTimeSlotModalFunc pone selectedDate = null)
    const dateToUse = new Date(selectedDate);
    
    console.log('selectTimeSlot llamada con:', { hour, selectedDate: dateToUse });
    
    closeTimeSlotModalFunc();
    
    // Usar la función del wizard si está disponible, sino usar la función local
    if (typeof window.openEventModalWithTime === 'function') {
        window.openEventModalWithTime(dateToUse, hour);
    } else {
        // Fallback: establecer fecha y hora directamente
        const dateInput = document.getElementById('event-date');
        const timeInput = document.getElementById('event-time');
        
        if (dateInput && dateToUse) {
            dateInput.value = formatDateForInput(dateToUse);
        }
        
        if (timeInput && hour) {
            timeInput.value = hour;
        }
        
        // Abrir modal de evento
        if (typeof openEventModal === 'function') {
            openEventModal(dateToUse);
        }
    }
}
