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
    // Submit del formulario lo maneja calendario-wizard.js (handleFormSubmit). No duplicar listener aquí.
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

    // Parser local robusto para 'YYYY-MM-DD HH:MM[:SS[.fff]][Z]' o con 'T'
    window.parseLocalDateTime = function(str) {
        if (!str) return null;
        const s = String(str).trim().replace('T', ' ');
        const [datePartRaw, timePartRaw = '00:00:00'] = s.split(' ');
        const [yStr, mStr, dStr] = datePartRaw.split('-');
        const [hStr='00', minStr='00', secStrRaw='00'] = timePartRaw.split(':');
        // Limpiar segundos de milisegundos y zona (e.g., '00.000000Z')
        const secStr = (secStrRaw.match(/^\d+/) || ['00'])[0];
        const y = parseInt(yStr, 10) || new Date().getFullYear();
        const m = parseInt(mStr, 10) || 1;
        const d = parseInt(dStr, 10) || 1;
        const H = parseInt(hStr, 10) || 0;
        const M = parseInt(minStr, 10) || 0;
        const S = parseInt(secStr, 10) || 0;
        return new Date(y, m-1, d, H, M, S, 0);
    };

// Reprogramar evento (mover fecha manteniendo la hora original)
function updateEventDate(eventId, targetDate) {
    const ev = eventos.find(e => e.id == eventId);
    if (!ev) return;

    const oldDate = parseLocalDateTime(ev.fecha_hora);
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
        const eventDate = parseLocalDateTime(event.fecha_hora);
        return eventDate.getDate() === date.getDate() &&
               eventDate.getMonth() === date.getMonth() &&
               eventDate.getFullYear() === date.getFullYear();
    });
}

function cargarEventos() {
    const mes = currentDate.getMonth() + 1;
    const anio = currentDate.getFullYear();
    
    console.log(`Cargando eventos para: ${mes}/${anio}`);
    
    return fetch(`/lider_semi/eventos?mes=${mes}&anio=${anio}`)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                // Normalizar eventos para asegurar 'fecha_hora'
                const deriveFecha = (e) => e.fecha_hora || e.fecha || e.inicio || e.start || e.start_at || e.fechaInicio || e.fecha_inicio || null;
                eventos = (data.eventos || []).map(e => {
                    const fh = deriveFecha(e);
                    if (!fh) {
                        console.warn('[Eventos] Evento sin fecha detectado:', e);
                    }
                    return { ...e, fecha_hora: fh };
                });
                console.log(`Eventos cargados: ${eventos.length}`);
                if (typeof renderEventsList === 'function' && eventsList && emptyEvents) {
                    renderEventsList();
                }
                renderCalendar();
            }
            return data;
        })
        .catch(error => {
            console.error('Error al cargar eventos:', error);
            showNotification('Error al Cargar', 'No se pudieron cargar los eventos', 'error');
            throw error;
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
    const sortedEvents = [...eventos].sort((a, b) => parseLocalDateTime(a.fecha_hora) - parseLocalDateTime(b.fecha_hora));
    
    // Mostrar solo los próximos 5 eventos
    const upcomingEvents = sortedEvents.filter(event => parseLocalDateTime(event.fecha_hora) >= new Date());
    
    console.log('Próximos eventos:', upcomingEvents.length);
    
    if (upcomingEvents.length === 0) {
        console.log('No hay eventos próximos');
        emptyEvents.style.display = 'block';
        return;
    }
    
    upcomingEvents.slice(0, 5).forEach(event => {
        const eventElement = document.createElement('li');
        eventElement.className = 'event-item';
        
        const eventDate = parseLocalDateTime(event.fecha_hora);
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
                <div>${eventDate.toLocaleDateString('es-ES', { timeZone: 'America/Bogota' })}</div>
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
    
    // Resetear selección de plataforma
    document.querySelectorAll('.link-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
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
    // Construir YYYY-MM-DD en hora local para evitar desfase por UTC
    const d = (date instanceof Date) ? date : new Date(date);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
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
    
    // Obtener plataforma seleccionada para auto-generación
    const selectedPlatform = document.querySelector('.link-option.selected');
    const generarEnlace = selectedPlatform ? selectedPlatform.dataset.platform : null;
    
    // El enlace siempre será null para que se genere automáticamente en el backend
    let linkVirtual = null;
    
    // Si la ubicación es virtual y no se seleccionó plataforma, usar 'teams' por defecto
    if ((ubicacion === 'virtual' || ubicacion === 'hibrido') && !generarEnlace) {
        console.log('Ubicación virtual sin plataforma seleccionada, se usará Teams por defecto');
    }
    
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
    
    const fechaHora = `${date} ${time}:00`;
    
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
        participantes,
        timeZone: 'America/Bogota',
        generar_enlace: generarEnlace // Para auto-generación en backend
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
    // Tipo de reunión
    const typeSel = document.getElementById('event-type');
    if (typeSel) typeSel.value = event.tipo || '';
    
    const eventDate = parseLocalDateTime(event.fecha_hora);
    document.getElementById('event-date').value = formatDateForInput(eventDate);
    document.getElementById('event-time').value = eventDate.toTimeString().slice(0, 5);
    document.getElementById('event-duration').value = event.duracion;
    document.getElementById('event-description').value = event.descripcion || '';
    // Ubicación (mapear a opción existente en el select)
    const locSel = document.getElementById('event-location');
    if (locSel) {
        const val = (event.ubicacion || '').toString();
        const exists = Array.from(locSel.options).some(o => o.value === val);
        locSel.value = exists ? val : (val === 'virtual' ? 'virtual' : (val === 'hibrido' ? 'virtual' : 'otra'));
    }

    // Seleccionar participantes (checkboxes)
    const parts = Array.isArray(event.participantes) ? event.participantes : [];
    const ids = parts.map(p => p.id ?? p);
    document.querySelectorAll('.participant-checkbox-modal').forEach(cb => {
        cb.checked = ids.includes(cb.value) || ids.includes(parseInt(cb.value));
    });
    if (typeof updateParticipantsCount === 'function') updateParticipantsCount();
    
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

    // Guardar evento actual lo antes posible para depuración
    window.currentDrawerEvent = ev;

    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? '--'; };

    // DEBUG: inspeccionar evento que alimenta el Drawer
    try {
        console.log('[Drawer] Evento seleccionado:', JSON.parse(JSON.stringify(ev)));
    } catch (e) {
        console.log('[Drawer] Evento seleccionado (raw):', ev);
    }

    // Fecha y hora legibles
    // Algunas respuestas antiguas pueden venir con otras claves
    const rawFecha = ev.fecha_hora || ev.fecha || ev.inicio || ev.start || ev.start_at || ev.fechaInicio || ev.fecha_inicio;
    if (!rawFecha) {
        console.warn('[Drawer] El evento no tiene fecha_hora. Claves disponibles:', Object.keys(ev || {}));
    }
    // Derivar SIEMPRE desde el string crudo para evitar cualquier shift
    const s = (rawFecha || '').toString();
    const [datePartRaw, timePartRaw = '00:00:00'] = s.replace('T', ' ').split(' ');
    const [yStr, mStr, dStr] = (datePartRaw || '').split('-');
    const [H='00', M='00'] = (timePartRaw || '').split(':');
    const y = parseInt(yStr, 10) || new Date().getFullYear();
    const m = (parseInt(mStr, 10) || 1) - 1;
    const dNum = parseInt(dStr, 10) || 1;
    // Construir fecha a las 12:00 locales para blindar contra desfases
    const d = new Date(y, m, dNum, 12, 0, 0, 0);
    let fechaStr = d.toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'America/Bogota' });
    let horaStr = `${String(H).padStart(2,'0')}:${String(M).padStart(2,'0')}`;

    setText('detail-titulo', ev.titulo || 'Reunión');
    setText('detail-tipo', ev.tipo || 'general');
    setText('detail-fecha', fechaStr);
    setText('detail-hora', horaStr);
    setText('detail-duracion', ev.duracion ? `${ev.duracion} min` : '60 min');
    setText('detail-ubicacion', ev.ubicacion || 'virtual');
    
    // Código de reunión
    const codigoField = document.getElementById('detail-codigo-field');
    const codigoValue = document.getElementById('detail-codigo');
    if (ev.codigo_reunion) {
        if (codigoValue) codigoValue.textContent = ev.codigo_reunion;
        if (codigoField) codigoField.style.display = 'block';
    } else if (codigoField) {
        codigoField.style.display = 'none';
    }

    // Descripción
    const desc = document.getElementById('detail-descripcion');
    if (desc) desc.textContent = ev.descripcion || 'Sin descripción';

    // Enlace virtual
    const linkField = document.getElementById('detail-link-field');
    const link = document.getElementById('detail-link');
    const deleteBtn = document.getElementById('delete-link-btn');
    const linkDisplayContainer = document.getElementById('link-display-container');
    const replaceLinkContainer = document.getElementById('replace-link-container');
    
    // Guardar evento actual
    window.currentDrawerEvent = ev;
    
    // Normalizar ubicación para comparación
    const ubicacionNorm = (ev.ubicacion || '').toLowerCase().trim();
    console.log('🔍 [Drawer DEBUG] Ubicación:', ubicacionNorm, 'Link:', ev.link_virtual);
    
    if (ubicacionNorm === 'virtual' || ubicacionNorm === 'hibrido') {
        if (linkField) linkField.style.display = 'block';
        
        if (ev.link_virtual) {
            // Mostrar enlace con botón de basura
            if (link) {
                link.href = ev.link_virtual;
                link.textContent = 'Abrir reunión';
                link.style.display = 'inline-flex';
            }
            if (deleteBtn) deleteBtn.style.display = 'inline-flex';
            if (linkDisplayContainer) linkDisplayContainer.style.display = 'flex';
            if (replaceLinkContainer) replaceLinkContainer.style.display = 'none';
        } else {
            // NO HAY ENLACE: Ocultar todo
            console.log('⚠️ [Drawer] NO tiene link_virtual');
            if (linkField) linkField.style.display = 'none';
        }
    } else {
        if (linkField) linkField.style.display = 'none';
        console.debug('[Drawer] Enlace oculto. Ubicación:', ubicacionNorm);
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
    // Remover foco de cualquier elemento dentro del drawer antes de ocultarlo
    if (drawer && drawer.contains(document.activeElement)) {
        document.activeElement.blur();
    }
    
    if (drawerOverlay) drawerOverlay.style.display = 'none';
    if (drawer) {
        drawer.setAttribute('aria-hidden', 'true');
        drawer.style.display = 'none';
    }
}

// Función para seleccionar plataforma de reunión
function selectLinkPlatform(platform, event) {
    document.querySelectorAll('.link-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('selected');
    }
    
    // Ya no hay campo de enlace personalizado
    console.log('Plataforma seleccionada:', platform);
}

// Función para mostrar el formulario de reemplazo de enlace
function showReplaceLinkInput() {
    const linkDisplayContainer = document.getElementById('link-display-container');
    const replaceLinkContainer = document.getElementById('replace-link-container');
    const replaceInput = document.getElementById('replace-link-input');
    
    if (linkDisplayContainer) linkDisplayContainer.style.display = 'none';
    if (replaceLinkContainer) replaceLinkContainer.style.display = 'block';
    if (replaceInput) {
        replaceInput.value = '';
        replaceInput.focus();
    }
}

// Función para cancelar el reemplazo de enlace
function cancelReplaceLink() {
    const linkDisplayContainer = document.getElementById('link-display-container');
    const replaceLinkContainer = document.getElementById('replace-link-container');
    
    if (linkDisplayContainer) linkDisplayContainer.style.display = 'flex';
    if (replaceLinkContainer) replaceLinkContainer.style.display = 'none';
}

// Función para guardar el enlace reemplazado
function saveReplacedLink() {
    const ev = window.currentDrawerEvent;
    if (!ev || !ev.id) {
        showNotification('Error', 'No se pudo identificar el evento', 'error');
        return;
    }
    
    const replaceInput = document.getElementById('replace-link-input');
    const newLink = replaceInput ? replaceInput.value.trim() : '';
    
    if (!newLink) {
        showNotification('Error', 'Por favor ingresa un enlace válido', 'error');
        return;
    }
    
    // Validar que sea un enlace válido (debe empezar con http:// o https://)
    if (!newLink.startsWith('http://') && !newLink.startsWith('https://')) {
        showNotification('Error', 'El enlace debe ser una URL válida (debe empezar con https://)', 'error');
        return;
    }
    
    saveMeetingLink(ev.id, newLink);
}

// Función para guardar enlace manual
function saveMeetingLink(eventId, link) {
    const replaceLinkContainer = document.getElementById('replace-link-container');
    if (replaceLinkContainer) {
        replaceLinkContainer.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fas fa-spinner fa-spin"></i> Guardando enlace...</div>';
    }
    
    fetch(`/lider_semi/eventos/${eventId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            link_virtual: link,
            _method: 'PUT'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Enlace Guardado', 'El enlace de reunión se guardó exitosamente', 'success');
            
            // Recargar eventos y actualizar el drawer
            cargarEventos().then(() => {
                // Reabrir el drawer con el evento actualizado
                openEventDrawerById(eventId);
            });
        } else {
            showNotification('Error', data.message || 'No se pudo guardar el enlace', 'error');
            cancelReplaceLink();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de Conexión', 'No se pudo guardar el enlace', 'error');
        cancelReplaceLink();
    });
}

// Función para generar enlace para evento existente (DEPRECADA - mantener por compatibilidad)
function generateMeetingLink(eventId, platform) {
    // Mostrar indicador de carga
    const platformSelector = document.getElementById('platform-selector');
    if (platformSelector) {
        platformSelector.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fas fa-spinner fa-spin"></i> Generando enlace...</div>';
    }
    
    fetch(`/lider_semi/eventos/${eventId}/generar-enlace`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ plataforma: platform })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Enlace Generado', 'Se ha creado el enlace de reunión exitosamente', 'success');
            
            // Recargar eventos y actualizar el drawer con el nuevo enlace
            cargarEventos().then(() => {
                // Reabrir el drawer con el evento actualizado
                openEventDrawerById(eventId);
            });
        } else {
            showNotification('Error', data.message, 'error');
            // Restaurar el selector si hay error
            if (platformSelector) {
                platformSelector.innerHTML = `
                    <div class="drawer-label" style="margin-bottom:10px; font-weight:600; color:#374151;">
                        🔗 Generar enlace con:
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="btn-calendario btn-small platform-option" data-platform="teams" onclick="generateMeetingLinkFromDrawer('teams')" style="flex:1; min-width:120px;">
                            <i class="fab fa-microsoft"></i> Teams
                        </button>
                        <button type="button" class="btn-calendario btn-small platform-option" data-platform="meet" onclick="generateMeetingLinkFromDrawer('meet')" style="flex:1; min-width:120px;">
                            <i class="fab fa-google"></i> Meet
                        </button>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de Conexión', 'No se pudo generar el enlace', 'error');
        // Restaurar el selector si hay error
        if (platformSelector) {
            platformSelector.innerHTML = `
                <div class="drawer-label" style="margin-bottom:10px; font-weight:600; color:#374151;">
                    🔗 Generar enlace con:
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="btn-calendario btn-small platform-option" data-platform="teams" onclick="generateMeetingLinkFromDrawer('teams')" style="flex:1; min-width:120px;">
                        <i class="fab fa-microsoft"></i> Teams
                    </button>
                    <button type="button" class="btn-calendario btn-small platform-option" data-platform="meet" onclick="generateMeetingLinkFromDrawer('meet')" style="flex:1; min-width:120px;">
                        <i class="fab fa-google"></i> Meet
                    </button>
                </div>
            `;
        }
    });
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

// DEBUG: Función temporal para diagnosticar visibilidad del drawer
window.debugDrawerState = function() {
    console.group('🔍 DEBUG: Estado del Drawer');
    const ev = window.currentDrawerEvent;
    console.log('Evento actual:', ev);
    if (ev) {
        console.log('- ID:', ev.id);
        console.log('- Título:', ev.titulo);
        console.log('- Ubicación:', ev.ubicacion);
        console.log('- Link virtual:', ev.link_virtual);
        console.log('- Código reunión:', ev.codigo_reunion);
    }
    
    const elements = {
        'detail-link-field': document.getElementById('detail-link-field'),
        'detail-link': document.getElementById('detail-link'),
        'detail-generate-link': document.getElementById('detail-generate-link'),
        'detail-outlook-link': document.getElementById('detail-outlook-link'),
        'platform-selector': document.getElementById('platform-selector')
    };
    
    console.table(Object.entries(elements).map(([id, el]) => ({
        ID: id,
        Existe: !!el,
        Display: el?.style.display || 'N/A',
        Visible: el ? (el.offsetParent !== null) : false
    })));
    console.groupEnd();
    
    return {
        evento: ev,
        elementos: elements
    };
}
