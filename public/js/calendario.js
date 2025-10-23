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

// Inicializar calendario
document.addEventListener('DOMContentLoaded', function() {
    // Primero cargar eventos, luego renderizar (cargarEventos ya llama a renderCalendar)
    cargarEventos();
    
    // Event listeners (con verificación de existencia)
    if (prevMonthButton) prevMonthButton.addEventListener('click', goToPreviousMonth);
    if (nextMonthButton) nextMonthButton.addEventListener('click', goToNextMonth);
    if (closeModal) closeModal.addEventListener('click', closeEventModal);
    if (cancelEvent) cancelEvent.addEventListener('click', closeEventModal);
    if (eventForm) eventForm.addEventListener('submit', saveEvent);
    
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
        dayElement.innerHTML = `<div class="day-number">${prevMonthLastDay - i}</div>`;
        calendarGrid.appendChild(dayElement);
    }
    
    // Días del mes actual
    const today = new Date();
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Verificar si es hoy
        const cellDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
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
                <div class="event-preview">${event.titulo}</div>
            `).join('')}
            ${dayEvents.length > 2 ? `<div class="event-preview">+${dayEvents.length - 2} más</div>` : ''}
        `;
        
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
                renderEventsList();
                renderCalendar();
            }
        })
        .catch(error => {
            console.error('Error al cargar eventos:', error);
            showNotification('Error al Cargar', 'No se pudieron cargar los eventos', 'error');
        });
}

function renderEventsList() {
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
