// Variables globales
let currentDate = new Date();
let eventos = [];
let editingEventId = null;

// Elementos del DOM
const calendarGrid = document.getElementById('calendar-grid');
const currentMonthElement = document.getElementById('current-period'); // Cambiado de current-month a current-period
const prevMonthButton = document.getElementById('prev-period'); // Cambiado de prev-month a prev-period
const nextMonthButton = document.getElementById('next-period'); // Cambiado de next-month a next-period
const eventsList = document.getElementById('events-list');
const emptyEvents = document.getElementById('empty-events');
const eventModal = document.getElementById('event-modal');
const eventForm = document.getElementById('event-form');
const closeModal = document.getElementById('close-modal');
const cancelEvent = document.getElementById('btn-cancel'); // Cambiado de cancel-event a btn-cancel
const modalTitle = document.getElementById('modal-title');
// Drawer de detalle
const drawer = document.getElementById('event-detail-drawer');
const drawerOverlay = document.getElementById('event-detail-overlay');

// Inicializar calendario
document.addEventListener('DOMContentLoaded', function() {
    // Primero cargar eventos, luego renderizar (cargarEventos ya llama a renderCalendar)
    cargarEventos();
    
    // Event listeners (con verificación de existencia)
    
    if (closeModal) closeModal.addEventListener('click', closeEventModal);
    if (cancelEvent) cancelEvent.addEventListener('click', closeEventModal);
    if (eventForm) eventForm.addEventListener('submit', saveEvent);
    // Cerrar drawer
    const closeDrawerBtn = document.getElementById('drawer-close-btn');
    const closeCta = document.getElementById('drawer-close-cta');
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeEventDrawer);
    if (closeCta) closeCta.addEventListener('click', closeEventDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeEventDrawer);
    
    // Event listeners para opciones de notificación
    document.querySelectorAll('.notification-option-modal').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.notification-option-modal').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });
    
    // Establecer fecha mínima como hoy
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];
    const eventDateInput = document.getElementById('event-date');
    if (eventDateInput) {
        eventDateInput.min = formattedDate;
    }

    // Parser local para fechas 'YYYY-MM-DD' (evita parseo UTC por defecto)
    window.parseLocalYMD = function(str) {
        if (!str) return null;
        const parts = str.split('-').map(Number);
        if (parts.length !== 3) return null;
        return new Date(parts[0], parts[1]-1, parts[2], 0, 0, 0, 0);
    };

// Reprogramar evento (mover fecha manteniendo la hora original)
function updateEventDate(eventId, targetDate) {
    const ev = eventos.find(e => e.id == eventId);
    if (!ev) return;

    const oldDate = new Date(ev.fecha_hora);
    const newDateTime = new Date(targetDate);
    newDateTime.setHours(oldDate.getHours(), oldDate.getMinutes(), 0, 0);

    // Formato local YYYY-MM-DD HH:MM:SS para evitar desfase UTC
    const formatLocalDateTime = (d) => {
        const pad = (n) => String(n).padStart(2, '0');
        const yyyy = d.getFullYear();
        const mm = pad(d.getMonth() + 1);
        const dd = pad(d.getDate());
        const HH = pad(d.getHours());
        const MM = pad(d.getMinutes());
        const SS = '00';
        return `${yyyy}-${mm}-${dd} ${HH}:${MM}:${SS}`;
    };

    const url = `/lider_semi/eventos/${eventId}`;
    const method = 'PUT';

    // Enviar todos los campos que el backend espera, cambiando solo fecha_hora
    const payload = {
        titulo: ev.titulo || 'Reunión',
        tipo: ev.tipo || 'general',
        descripcion: ev.descripcion ?? null,
        id_proyecto: ev.proyecto?.id_proyecto ?? ev.id_proyecto ?? null,
        fecha_hora: formatLocalDateTime(newDateTime),
        duracion: parseInt(ev.duracion ?? 60),
        ubicacion: ev.ubicacion ?? 'virtual',
        link_virtual: ev.link_virtual ?? null,
        recordatorio: ev.recordatorio ?? 'none',
        participantes: Array.isArray(ev.participantes) ? ev.participantes.map(p => p.id ?? p) : []
    };

    fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Actualizar estado local inmediatamente
            ev.fecha_hora = payload.fecha_hora.replace(' ', 'T');
            showNotification('Reunión reprogramada', data.message || 'Se actualizó la fecha correctamente', 'success');
            cargarEventos();
        } else {
            const detail = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Intenta nuevamente');
            console.warn('Respuesta actualización inválida:', data);
            showNotification('No se pudo reprogramar', detail, 'error');
        }
    })
    .catch(err => {
        console.error('Error al reprogramar:', err);
        showNotification('Error de Conexión', 'No se pudo reprogramar el evento', 'error');
    });
}

// Reprogramar evento cambiando fecha y hora explícitamente
// targetDate puede ser Date o string 'YYYY-MM-DD'; targetHour es 'HH:MM'
function updateEventDateTime(eventId, targetDate, targetHour) {
    const ev = eventos.find(e => e.id == eventId);
    if (!ev) return;

    const baseDate = (targetDate instanceof Date) ? new Date(targetDate) : parseLocalYMD(String(targetDate));
    if (!baseDate) return;

    const [h, m] = (targetHour || '00:00').split(':').map(Number);
    baseDate.setHours(h || 0, m || 0, 0, 0);

    const formatLocalDateTime = (d) => {
        const pad = (n) => String(n).padStart(2, '0');
        const yyyy = d.getFullYear();
        const mm = pad(d.getMonth() + 1);
        const dd = pad(d.getDate());
        const HH = pad(d.getHours());
        const MM = pad(d.getMinutes());
        return `${yyyy}-${mm}-${dd} ${HH}:${MM}:00`;
    };

    const payload = {
        titulo: ev.titulo || 'Reunión',
        tipo: ev.tipo || 'general',
        descripcion: ev.descripcion ?? null,
        id_proyecto: ev.proyecto?.id_proyecto ?? ev.id_proyecto ?? null,
        fecha_hora: formatLocalDateTime(baseDate),
        duracion: parseInt(ev.duracion ?? 60),
        ubicacion: ev.ubicacion ?? 'virtual',
        link_virtual: ev.link_virtual ?? null,
        recordatorio: ev.recordatorio ?? 'none',
        participantes: Array.isArray(ev.participantes) ? ev.participantes.map(p => p.id ?? p) : []
    };

    fetch(`/lider_semi/eventos/${eventId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const iso = payload.fecha_hora.replace(' ', 'T');
            ev.fecha_hora = iso;
            showNotification('Reunión reprogramada', data.message || 'Se actualizó la fecha y hora', 'success');
            cargarEventos();
        } else {
            const detail = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Intenta nuevamente');
            showNotification('No se pudo reprogramar', detail, 'error');
        }
    })
    .catch(err => {
        console.error('Error al reprogramar (fecha y hora):', err);
        showNotification('Error de Conexión', 'No se pudo reprogramar el evento', 'error');
    });
}
    
    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        if (event.target === eventModal) {
            closeEventModal();
        }
    });
});

// Funciones del calendario
function renderCalendar() {
    // Limpiar calendario
    calendarGrid.innerHTML = '';
    
    // Encabezados de días
    const dayHeaders = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    dayHeaders.forEach(day => {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day-header';
        dayElement.textContent = day;
        calendarGrid.appendChild(dayElement);
    });
    
    // Obtener primer día del mes y último día del mes
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    
    // Días del mes anterior
    const firstDayOfWeek = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
    const prevMonthLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0).getDate();
    
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day other-month';
        const prevDayNum = prevMonthLastDay - i;
        dayElement.innerHTML = `<div class="day-number">${prevDayNum}</div>`;
        // Fecha real del día del mes anterior
        const prevDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, prevDayNum);
        dayElement.dataset.date = formatDateForInput(prevDate);
        // Habilitar drop en el día
        dayElement.addEventListener('dragover', (e) => {
            e.preventDefault();
            dayElement.classList.add('drag-over');
        });
        dayElement.addEventListener('dragleave', () => {
            dayElement.classList.remove('drag-over');
        });
        dayElement.addEventListener('drop', (e) => {
            e.preventDefault();
            dayElement.classList.remove('drag-over');
            const eventId = e.dataTransfer.getData('text/event-id');
            if (!eventId) return;
            const targetDate = parseLocalYMD(dayElement.dataset.date);
            // Validaciones: no pasado y día laborable
            if (typeof isPastDateTime === 'function' && isPastDateTime(targetDate)) {
                showNotification('No permitido', 'No puedes mover reuniones a fechas pasadas', 'error');
                return;
            }
            if (typeof isWorkingDay === 'function' && !isWorkingDay(targetDate)) {
                showNotification('No permitido', 'Solo se pueden programar reuniones en días laborables', 'error');
                return;
            }
            updateEventDate(eventId, targetDate);
        });

        // Listeners para arrastrar cada evento
        const previews = dayElement.querySelectorAll('.event-preview[draggable="true"]');
        previews.forEach(prev => {
            prev.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/event-id', prev.getAttribute('data-event-id'));
                e.dataTransfer.effectAllowed = 'move';
                prev.classList.add('dragging');
            });
            prev.addEventListener('dragend', () => {
                prev.classList.remove('dragging');
            });
        });

        calendarGrid.appendChild(dayElement);
    }
    
    // Días del mes actual
    const today = new Date();
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Verificar si es hoy
        const cellDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
        // Guardar fecha destino para drag & drop
        dayElement.dataset.date = formatDateForInput(cellDate);
        if (cellDate.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        // Marcar fines de semana y festivos (si las funciones existen)
        if (typeof isWeekend === 'function' && isWeekend(cellDate)) {
            dayElement.classList.add('weekend');
        }
        
        if (typeof isFestivo === 'function' && isFestivo(cellDate)) {
            dayElement.classList.add('festivo');
        }
        
        // Marcar días pasados
        if (typeof isPastDateTime === 'function' && isPastDateTime(cellDate)) {
            dayElement.classList.add('past-day');
        }
        
        // Verificar si hay eventos en este día
        const dayEvents = getEventsForDay(cellDate);
        
        if (dayEvents.length > 0) {
            console.log(`Día ${i}: ${dayEvents.length} evento(s)`, dayEvents.map(e => e.titulo));
        }
        
        dayElement.innerHTML = `
            <div class="day-number">${i}</div>
            ${dayEvents.length > 0 ? `<div class="event-indicator"></div>` : ''}
            ${dayEvents.slice(0, 2).map(event => `
                <div class="event-preview" data-event-id="${event.id}" draggable="true">${event.titulo}</div>
            `).join('')}
            ${dayEvents.length > 2 ? `<div class="event-preview">+${dayEvents.length - 2} más</div>` : ''}
        `;

        // Listeners de clic y drag en eventos renderizados
        const previews = dayElement.querySelectorAll('.event-preview[draggable="true"]');
        previews.forEach(prev => {
            prev.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/event-id', prev.getAttribute('data-event-id'));
                e.dataTransfer.effectAllowed = 'move';
                prev.classList.add('dragging');
            });
            prev.addEventListener('dragend', () => prev.classList.remove('dragging'));
            prev.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = prev.getAttribute('data-event-id');
                if (id) openEventDrawerById(id);
            });
        });
        
        // Agregar evento de clic solo si es día laborable y no pasado
        const isClickable = typeof isWorkingDay === 'function' ? 
            isWorkingDay(cellDate) && !isPastDateTime(cellDate) : true;
        
        if (isClickable) {
            dayElement.addEventListener('click', () => openTimeSlotModal(cellDate));
        } else {
            dayElement.style.cursor = 'not-allowed';
        }
        
        calendarGrid.appendChild(dayElement);
    }
    
    // Días del mes siguiente
    const totalCells = 42; // 6 filas de 7 días
    const remainingCells = totalCells - (firstDayOfWeek + lastDay.getDate());
    
    for (let i = 1; i <= remainingCells; i++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day other-month';
        dayElement.innerHTML = `<div class="day-number">${i}</div>`;
        const nextDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, i);
        dayElement.dataset.date = formatDateForInput(nextDate);
        // Habilitar drop en el día del mes siguiente
        dayElement.addEventListener('dragover', (e) => {
            e.preventDefault();
            dayElement.classList.add('drag-over');
        });
        dayElement.addEventListener('dragleave', () => {
            dayElement.classList.remove('drag-over');
        });
        dayElement.addEventListener('drop', (e) => {
            e.preventDefault();
            dayElement.classList.remove('drag-over');
            const eventId = e.dataTransfer.getData('text/event-id');
            if (!eventId) return;
            const targetDate = parseLocalYMD(dayElement.dataset.date);
            if (typeof isPastDateTime === 'function' && isPastDateTime(targetDate)) {
                showNotification('No permitido', 'No puedes mover reuniones a fechas pasadas', 'error');
                return;
            }
            if (typeof isWorkingDay === 'function' && !isWorkingDay(targetDate)) {
                showNotification('No permitido', 'Solo se pueden programar reuniones en días laborables', 'error');
                return;
            }
            updateEventDate(eventId, targetDate);
        });
        calendarGrid.appendChild(dayElement);
    }
    
    // Actualizar texto del mes actual
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    if (currentMonthElement) {
        const mesActual = monthNames[currentDate.getMonth()];
        const anioActual = currentDate.getFullYear();
        console.log(`Actualizando título del calendario: ${mesActual} ${anioActual}`);
        currentMonthElement.textContent = `${mesActual} ${anioActual}`;
    }
}

function goToPreviousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    cargarEventos();
}

function goToNextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    cargarEventos();
}

// Funciones de eventos
function getEventsForDay(date) {
    return eventos.filter(event => {
        const eventDate = new Date(event.fecha_hora);
        return eventDate.getDate() === date.getDate() &&
               eventDate.getMonth() === date.getMonth() &&
               eventDate.getFullYear() === date.getFullYear();
    });
}

function cargarEventos() {
    const mes = currentDate.getMonth() + 1;
    const anio = currentDate.getFullYear();
    
    console.log(`Cargando eventos para: ${mes}/${anio}`);
    
    fetch(`/lider_semi/eventos?mes=${mes}&anio=${anio}`)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                eventos = data.eventos;
                console.log(`Eventos cargados: ${eventos.length}`);
                if (typeof renderEventsList === 'function' && eventsList && emptyEvents) {
                    renderEventsList();
                }
                renderCalendar();
            }
        })
        .catch(error => {
            console.error('Error al cargar eventos:', error);
            showNotification('Error al Cargar', 'No se pudieron cargar los eventos', 'error');
        });
}

function renderEventsList() {
    // Si no existen los nodos del sidebar, no hacer nada
    if (!eventsList || !emptyEvents) return;
    console.log('renderEventsList - Total eventos:', eventos.length);
    eventsList.innerHTML = '';
    
    if (eventos.length === 0) {
        console.log('No hay eventos para mostrar');
        emptyEvents.style.display = 'block';
        return;
    }
    
    emptyEvents.style.display = 'none';
    
    // Ordenar eventos por fecha
    const sortedEvents = [...eventos].sort((a, b) => new Date(a.fecha_hora) - new Date(b.fecha_hora));
    
    // Mostrar solo los próximos 5 eventos
    const upcomingEvents = sortedEvents.filter(event => new Date(event.fecha_hora) >= new Date());
    
    console.log('Próximos eventos:', upcomingEvents.length);
    
    if (upcomingEvents.length === 0) {
        console.log('No hay eventos próximos');
        emptyEvents.style.display = 'block';
        return;
    }
    
    upcomingEvents.slice(0, 5).forEach(event => {
        const eventElement = document.createElement('li');
        eventElement.className = 'event-item';
        
        const eventDate = new Date(event.fecha_hora);
        const timeString = eventDate.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const participantesText = event.participantes && event.participantes.length > 0 
            ? `${event.participantes.length} participante(s)` 
            : 'Sin participantes';
        
        eventElement.innerHTML = `
            <div class="event-title">${event.titulo}</div>
            <div class="event-details">
                <div class="event-time">
                    <i class="far fa-clock"></i> ${timeString}
                </div>
                <div>|</div>
                <div>${eventDate.toLocaleDateString('es-ES')}</div>
            </div>
            <div class="event-description" style="font-size: 13px; margin-top: 5px; color: #666;">
                ${event.descripcion || 'Sin descripción'}
            </div>
            <div style="font-size: 12px; margin-top: 5px; color: #999;">
                <i class="fas fa-users"></i> ${participantesText}
            </div>
            <div class="event-actions">
                <button class="btn-calendario btn-primary-calendario btn-small edit-event" data-id="${event.id}">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn-calendario btn-danger-calendario btn-small delete-event" data-id="${event.id}">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        `;
        
        eventsList.appendChild(eventElement);
    });
    
    // Agregar event listeners a los botones de editar y eliminar
    document.querySelectorAll('.edit-event').forEach(button => {
        button.addEventListener('click', (e) => {
            const eventId = e.target.closest('button').getAttribute('data-id');
            editEvent(eventId);
        });
    });
    
    document.querySelectorAll('.delete-event').forEach(button => {
        button.addEventListener('click', (e) => {
            const eventId = e.target.closest('button').getAttribute('data-id');
            deleteEvent(eventId);
        });
    });
}

function openEventModal(date = null) {
    modalTitle.textContent = 'Nueva Reunión';
    eventForm.reset();
    editingEventId = null;
    document.getElementById('event-id').value = '';
    
    if (date) {
        document.getElementById('event-date').value = formatDateForInput(date);
    } else {
        document.getElementById('event-date').value = formatDateForInput(new Date());
    }
    
    document.getElementById('event-time').value = '09:00';
    eventModal.classList.add('active');
}

function closeEventModal() {
    eventModal.classList.remove('active');
    editingEventId = null;
}

function formatDateForInput(date) {
    if (!date) return '';
    return date.toISOString().split('T')[0];
}

function saveEvent(e) {
    e.preventDefault();
    
    const eventId = document.getElementById('event-id').value;
    const titulo = document.getElementById('event-title').value;
    const tipo = document.getElementById('event-type').value;
    const proyecto = document.getElementById('event-proyecto').value;
    const date = document.getElementById('event-date').value;
    const time = document.getElementById('event-time').value;
    const duracion = document.getElementById('event-duration').value;
    const ubicacion = document.getElementById('event-location').value;
    const descripcion = document.getElementById('event-description').value;
    const linkVirtual = document.getElementById('event-virtual-link')?.value || null;
    
    console.log('Valores del formulario:', { date, time, duracion, ubicacion, descripcion, linkVirtual });
    
    // Validar que fecha y hora estén presentes
    if (!date || !time) {
        console.error('Validación fallida - fecha u hora vacía:', { date, time });
        showNotification(
            'Error de Validación',
            'Por favor selecciona una fecha y hora para la reunión',
            'error'
        );
        return;
    }
    
    // Obtener participantes seleccionados (checkboxes)
    const participantesCheckboxes = document.querySelectorAll('.participant-checkbox-modal:checked');
    const participantes = Array.from(participantesCheckboxes).map(checkbox => checkbox.value);
    
    // Obtener recordatorio seleccionado
    const reminderSelected = document.querySelector('input[name="reminder"]:checked');
    const reminder = reminderSelected ? reminderSelected.value : 'none';
    
    const fechaHora = `${date}T${time}:00`;
    
    const eventData = {
        titulo,
        tipo,
        descripcion: descripcion || null,
        id_proyecto: proyecto || null,
        fecha_hora: fechaHora,
        duracion: parseInt(duracion),
        ubicacion,
        link_virtual: linkVirtual,
        recordatorio: reminder,
        participantes
    };
    
    const url = eventId ? `/lider_semi/eventos/${eventId}` : '/lider_semi/eventos';
    const method = eventId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(eventData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(
                eventId ? '¡Evento Actualizado!' : '¡Evento Creado!',
                data.message,
                'success'
            );
            closeEventModal();
            cargarEventos();
        } else {
            showNotification(
                'Error',
                data.message || 'No se pudo guardar el evento',
                'error'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(
            'Error de Conexión',
            'No se pudo guardar el evento. Verifica tu conexión.',
            'error'
        );
    });
}

function editEvent(eventId) {
    const event = eventos.find(ev => ev.id == eventId);
    if (!event) return;
    
    modalTitle.textContent = 'Editar Reunión';
    editingEventId = eventId;
    document.getElementById('event-id').value = event.id;
    document.getElementById('event-title').value = event.titulo;
    document.getElementById('event-proyecto').value = event.proyecto?.id_proyecto || '';
    
    const eventDate = new Date(event.fecha_hora);
    document.getElementById('event-date').value = formatDateForInput(eventDate);
    document.getElementById('event-time').value = eventDate.toTimeString().slice(0, 5);
    document.getElementById('event-duration').value = event.duracion;
    document.getElementById('event-description').value = event.descripcion || '';
    
    // Seleccionar participantes
    const participantesSelect = document.getElementById('event-participants');
    Array.from(participantesSelect.options).forEach(option => {
        option.selected = event.participantes.some(p => p.id == option.value);
    });
    
    eventModal.classList.add('active');
}

function deleteEvent(eventId) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta reunión?')) {
        return;
    }
    
    fetch(`/lider_semi/eventos/${eventId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(
                'Evento Eliminado',
                'El evento ha sido eliminado exitosamente',
                'success'
            );
            cargarEventos();
        } else {
            showNotification(
                'Error',
                'No se pudo eliminar el evento',
                'error'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(
            'Error de Conexión',
            'No se pudo eliminar el evento',
            'error'
        );
    });
}

// ===== Detalle de reunión (Drawer) =====
function openEventDrawerById(eventId) {
    const ev = eventos.find(e => String(e.id) === String(eventId));
    if (!ev) return;

    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? '--'; };

    // Fecha y hora legibles
    const d = new Date(ev.fecha_hora);
    const fechaStr = d.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
    const horaStr = d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

    setText('detail-titulo', ev.titulo || 'Reunión');
    setText('detail-tipo', ev.tipo || 'general');
    setText('detail-fecha', fechaStr);
    setText('detail-hora', horaStr);
    setText('detail-duracion', ev.duracion ? `${ev.duracion} min` : '60 min');
    setText('detail-ubicacion', ev.ubicacion || 'virtual');

    // Descripción
    const desc = document.getElementById('detail-descripcion');
    if (desc) desc.textContent = ev.descripcion || 'Sin descripción';

    // Enlace virtual (si no existe, ofrecer deep link para crear reunión en Teams)
    const linkField = document.getElementById('detail-link-field');
    const link = document.getElementById('detail-link');
    const genBtn = document.getElementById('detail-generate-link');
    const outlookBtn = document.getElementById('detail-outlook-link');
    if (ev.ubicacion === 'virtual') {
        if (linkField) {
            linkField.style.display = 'block';
            console.debug('[Drawer] Enlace visible (virtual). link_virtual:', ev.link_virtual);
        }
        if (ev.link_virtual) {
            if (link) {
                link.href = ev.link_virtual;
                link.textContent = 'Abrir reunión';
            }
            if (linkField) linkField.style.display = 'block';
            if (genBtn) genBtn.style.display = 'none';
            if (outlookBtn) {
                outlookBtn.style.display = 'inline-flex';
                outlookBtn.onclick = () => openOutlookCompose(ev);
            }
        } else {
            // Mostrar botón para generar enlace
            if (genBtn) {
                genBtn.style.display = 'inline-flex';
                genBtn.onclick = async () => {
                    const meetingId = generateUniqueId();
                    const encodedTitle = encodeURIComponent(ev.titulo || 'Reunión');
                    const meetingLink = `https://teams.microsoft.com/l/meetup-join/19%3A${meetingId}%40thread.v2/0?context=%7B%22Tid%22%3A%22public%22%2C%22Oid%22%3A%22public%22%7D&subject=${encodedTitle}`;

                    // Persistir en backend como link_virtual
                    try {
                        const payload = {
                            titulo: ev.titulo || 'Reunión',
                            tipo: ev.tipo || 'general',
                            descripcion: ev.descripcion ?? null,
                            id_proyecto: ev.proyecto?.id_proyecto ?? ev.id_proyecto ?? null,
                            fecha_hora: ev.fecha_hora.replace('T',' '),
                            duracion: parseInt(ev.duracion ?? 60),
                            ubicacion: 'virtual',
                            link_virtual: meetingLink,
                            recordatorio: ev.recordatorio ?? 'none',
                            participantes: Array.isArray(ev.participantes) ? ev.participantes.map(p => p.id ?? p) : []
                        };
                        const res = await fetch(`/lider_semi/eventos/${ev.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (data.success) {
                            ev.link_virtual = meetingLink;
                            if (link) {
                                link.href = meetingLink;
                                link.textContent = 'Abrir reunión';
                            }
                            genBtn.style.display = 'none';
                            showNotification('Enlace generado', 'Se creó y guardó el enlace de Teams', 'success');
                            if (outlookBtn) {
                                outlookBtn.style.display = 'inline-flex';
                                outlookBtn.onclick = () => openOutlookCompose(ev);
                            }
                        } else {
                            showNotification('No se pudo guardar el enlace', data.message || 'Intenta nuevamente', 'error');
                        }
                    } catch (e) {
                        console.error('Error guardando link_virtual:', e);
                        showNotification('Error de Conexión', 'No se pudo guardar el enlace', 'error');
                    }
                };
            }
            if (link) {
                const deep = buildTeamsDeepLink(ev);
                link.href = deep;
                link.textContent = 'Crear reunión en Teams';
            }
            if (outlookBtn) {
                outlookBtn.style.display = 'inline-flex';
                outlookBtn.onclick = () => openOutlookCompose(ev);
            }
        }
    } else if (linkField) {
        linkField.style.display = 'none';
        console.debug('[Drawer] Enlace oculto (no virtual).');
    }

    // Participantes chips
    const cont = document.getElementById('detail-participantes');
    if (cont) {
        cont.innerHTML = '';
        const parts = Array.isArray(ev.participantes) ? ev.participantes : [];
        if (parts.length === 0) cont.textContent = 'Sin participantes';
        else parts.forEach(p => {
            const chip = document.createElement('span');
            chip.className = 'chip';
            chip.textContent = p.nombre_completo || p.nombre || p.name || `ID ${p.id ?? p}`;
            cont.appendChild(chip);
        });
    }

    // Avatar con iniciales del título
    const av = document.getElementById('detail-avatar');
    if (av) {
        const initials = (ev.titulo || 'RM').split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
        av.textContent = initials || 'RM';
    }

    openEventDrawer();
}

function openEventDrawer() {
    if (drawerOverlay) drawerOverlay.style.display = 'block';
    if (drawer) {
        drawer.style.display = 'flex';
        drawer.setAttribute('aria-hidden', 'false');
    }
}

function closeEventDrawer() {
    if (drawerOverlay) drawerOverlay.style.display = 'none';
    if (drawer) {
        drawer.style.display = 'none';
        drawer.setAttribute('aria-hidden', 'true');
    }
}

// Función de notificaciones (reutilizando la del módulo de documentos)
function showNotification(title, message, type = 'success') {
    // Verificar si existe el contenedor de notificaciones
    let container = document.getElementById('notificationContainer');
    
    // Si no existe, crearlo
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'notification-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        document.body.appendChild(container);
    }
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: flex-start;
        gap: 15px;
        animation: slideIn 0.4s ease-out;
        border-left: 4px solid ${type === 'success' ? '#5aa72e' : '#dc3545'};
    `;
    
    const iconMap = {
        success: '<i class="fas fa-check-circle"></i>',
        error: '<i class="fas fa-exclamation-circle"></i>',
        warning: '<i class="fas fa-exclamation-triangle"></i>',
        info: '<i class="fas fa-info-circle"></i>'
    };
    
    notification.innerHTML = `
        <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; background: ${type === 'success' ? '#e8f5e9' : '#ffebee'}; color: ${type === 'success' ? '#5aa72e' : '#dc3545'};">
            ${iconMap[type] || iconMap.success}
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 700; font-size: 16px; color: #1e4620; margin-bottom: 5px;">${title}</div>
            <div style="font-size: 14px; color: #666; line-height: 1.5;">${message}</div>
        </div>
        <button onclick="this.closest('.notification').remove()" style="flex-shrink: 0; background: none; border: none; color: #999; font-size: 20px; cursor: pointer; padding: 0; width: 24px; height: 24px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(notification);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
