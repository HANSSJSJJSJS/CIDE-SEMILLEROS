// Funcionalidad de arrastrar y soltar eventos
let draggedEvent = null;

function initializeDragAndDrop() {
    // Hacer los eventos arrastrables
    document.addEventListener('dragstart', handleDragStart);
    document.addEventListener('dragend', handleDragEnd);
    document.addEventListener('dragover', handleDragOver);
    document.addEventListener('drop', handleDrop);
}

function handleDragStart(e) {
    const eventElement = e.target.closest('.calendar-event');
    if (!eventElement) return;

    draggedEvent = {
        id: eventElement.dataset.eventId,
        element: eventElement
    };
    
    eventElement.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    if (draggedEvent) {
        draggedEvent.element.classList.remove('dragging');
        draggedEvent = null;
    }
}

function handleDragOver(e) {
    if (!draggedEvent) return;
    
    const dropTarget = findDropTarget(e.target);
    if (!dropTarget) return;

    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDrop(e) {
    e.preventDefault();
    if (!draggedEvent) return;

    const dropTarget = findDropTarget(e.target);
    if (!dropTarget) return;

    const targetDate = dropTarget.dataset.date;
    if (!targetDate) return;

    // Abrir modal de selección de hora
    openTimeSlotModal(new Date(targetDate), null, draggedEvent.id);
}

function findDropTarget(element) {
    // Buscar el contenedor de día más cercano
    return element.closest('.calendar-day');
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeDragAndDrop);

// Función para validar si una fecha y hora son válidas para el evento
function validateEventDateTime(date, hour) {
    const targetDateTime = new Date(date);
    const [h, m] = hour.split(':').map(Number);
    targetDateTime.setHours(h, m, 0, 0);
    
    // Validar día laborable
    if (!isWorkingDay(targetDateTime)) {
        showNotification(
            'Día no permitido',
            'Solo se pueden programar reuniones en días laborables',
            'error'
        );
        return false;
    }
    
    // Validar horario (8am - 5pm, excluyendo 12pm - 2pm)
    if (h < 8 || h >= 17 || (h >= 12 && h < 14)) {
        showNotification(
            'Horario no permitido',
            'Las reuniones solo se pueden agendar entre 8:00 AM y 5:00 PM (excluyendo 12:00 PM - 2:00 PM)',
            'error'
        );
        return false;
    }
    
    return true;
}

// Función para mover un evento
function moveEvent(eventId, targetDate, targetHour) {
    if (!validateEventDateTime(targetDate, targetHour)) return;
    
    const event = eventos.find(e => e.id == eventId);
    if (!event) {
        console.error('Evento no encontrado:', eventId);
        return;
    }
    
    updateEventDateTime(eventId, targetDate, targetHour);
}