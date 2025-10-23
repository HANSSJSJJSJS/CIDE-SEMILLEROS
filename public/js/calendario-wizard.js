// Sistema de Wizard paso a paso para crear reuniones
let currentStep = 1;
const totalSteps = 3;

document.addEventListener('DOMContentLoaded', function() {
    initializeWizard();
});

function initializeWizard() {
    // Botones de navegación
    const btnNext = document.getElementById('btn-next-step');
    const btnPrev = document.getElementById('btn-prev-step');
    const btnCancel = document.getElementById('btn-cancel');
    const eventForm = document.getElementById('event-form');
    
    if (btnNext) btnNext.addEventListener('click', nextStep);
    if (btnPrev) btnPrev.addEventListener('click', prevStep);
    if (btnCancel) btnCancel.addEventListener('click', closeEventModal);
    if (eventForm) eventForm.addEventListener('submit', handleFormSubmit);
    
    // Mostrar/ocultar campo de link virtual
    const locationSelect = document.getElementById('event-location');
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            const virtualLinkGroup = document.getElementById('virtual-link-group');
            if (this.value === 'virtual') {
                virtualLinkGroup.style.display = 'block';
                document.getElementById('event-virtual-link').required = true;
            } else {
                virtualLinkGroup.style.display = 'none';
                document.getElementById('event-virtual-link').required = false;
            }
        });
    }
    
    // Seleccionar todos los participantes
    const selectAllBtn = document.getElementById('select-all-participants');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.participant-checkbox-modal');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            
            this.innerHTML = allChecked ? 
                '<i class="fas fa-users"></i> Seleccionar Todos' : 
                '<i class="fas fa-user-times"></i> Deseleccionar Todos';
            
            updateParticipantsCount();
        });
    }
    
    // Contador de participantes
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox-modal');
    participantCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateParticipantsCount);
    });
    
    // Búsqueda de participantes
    const searchInput = document.getElementById('search-participants');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const participants = document.querySelectorAll('.participant-item-modal');
            
            participants.forEach(item => {
                const name = item.getAttribute('data-aprendiz-name');
                if (name.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Filtrar participantes por proyecto
    const proyectoSelect = document.getElementById('event-proyecto');
    if (proyectoSelect) {
        proyectoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const aprendicesIds = selectedOption.getAttribute('data-aprendices');
            
            if (aprendicesIds && aprendicesIds !== '[]') {
                const ids = JSON.parse(aprendicesIds);
                
                // Desmarcar todos
                document.querySelectorAll('.participant-checkbox-modal').forEach(cb => {
                    cb.checked = false;
                });
                
                // Marcar solo los del proyecto
                ids.forEach(id => {
                    const checkbox = document.getElementById(`participant-${id}`);
                    if (checkbox) checkbox.checked = true;
                });
                
                updateParticipantsCount();
            }
        });
    }
    
    // Actualizar resumen en tiempo real
    setupRealtimeSummary();
}

function nextStep() {
    // Validar paso actual
    if (!validateStep(currentStep)) {
        return;
    }
    
    if (currentStep < totalSteps) {
        // Marcar paso como completado
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('completed');
        
        currentStep++;
        showStep(currentStep);
        updateButtons();
        updateSummary();
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateButtons();
    }
}

function showStep(step) {
    // Ocultar todos los pasos
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Mostrar paso actual
    const currentContent = document.getElementById(`step-${step}`);
    if (currentContent) {
        currentContent.style.display = 'block';
    }
    
    // Actualizar indicadores
    document.querySelectorAll('.wizard-step').forEach(stepEl => {
        stepEl.classList.remove('active');
    });
    
    const activeStep = document.querySelector(`.wizard-step[data-step="${step}"]`);
    if (activeStep) {
        activeStep.classList.add('active');
    }
}

function updateButtons() {
    const btnPrev = document.getElementById('btn-prev-step');
    const btnNext = document.getElementById('btn-next-step');
    const btnSubmit = document.getElementById('btn-submit');
    
    // Botón anterior
    if (currentStep === 1) {
        btnPrev.style.display = 'none';
    } else {
        btnPrev.style.display = 'inline-flex';
    }
    
    // Botón siguiente / enviar
    if (currentStep === totalSteps) {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-flex';
    } else {
        btnNext.style.display = 'inline-flex';
        btnSubmit.style.display = 'none';
    }
}

function validateStep(step) {
    let isValid = true;
    let errorMessage = '';
    
    switch(step) {
        case 1:
            // Validar información básica
            const title = document.getElementById('event-title').value.trim();
            const type = document.getElementById('event-type').value;
            const location = document.getElementById('event-location').value;
            
            if (!title) {
                errorMessage = 'Por favor ingresa el título de la reunión';
                isValid = false;
            } else if (!type) {
                errorMessage = 'Por favor selecciona el tipo de reunión';
                isValid = false;
            } else if (!location) {
                errorMessage = 'Por favor selecciona la ubicación';
                isValid = false;
            } else if (location === 'virtual') {
                const virtualLink = document.getElementById('event-virtual-link').value.trim();
                if (!virtualLink) {
                    errorMessage = 'Por favor ingresa el link de la reunión virtual';
                    isValid = false;
                } else if (!isValidUrl(virtualLink)) {
                    errorMessage = 'Por favor ingresa un link válido (debe comenzar con http:// o https://)';
                    isValid = false;
                }
            }
            break;
            
        case 2:
            // Validar configuración (al menos un participante)
            const selectedParticipants = document.querySelectorAll('.participant-checkbox-modal:checked');
            if (selectedParticipants.length === 0) {
                errorMessage = 'Por favor selecciona al menos un participante';
                isValid = false;
            }
            break;
            
        case 3:
            // Paso 3 no tiene validaciones obligatorias
            break;
    }
    
    if (!isValid) {
        mostrarNotificacion(errorMessage, 'error');
    }
    
    return isValid;
}

function isValidUrl(string) {
    try {
        const url = new URL(string);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

function updateParticipantsCount() {
    const selectedCount = document.querySelectorAll('.participant-checkbox-modal:checked').length;
    const countElement = document.getElementById('selected-count');
    if (countElement) {
        countElement.textContent = selectedCount;
    }
}

function setupRealtimeSummary() {
    // Actualizar resumen cuando cambien los campos
    const fieldsToWatch = [
        'event-title',
        'event-type',
        'event-duration',
        'event-location'
    ];
    
    fieldsToWatch.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', updateSummary);
            field.addEventListener('input', updateSummary);
        }
    });
}

function updateSummary() {
    // Título
    const title = document.getElementById('event-title').value || '--';
    document.getElementById('summary-title').textContent = title;
    
    // Tipo
    const typeSelect = document.getElementById('event-type');
    const type = typeSelect.options[typeSelect.selectedIndex]?.text || '--';
    document.getElementById('summary-type').textContent = type;
    
    // Fecha y hora
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');
    if (dateInput.value && timeInput.value) {
        const date = new Date(dateInput.value + 'T' + timeInput.value);
        const dateStr = date.toLocaleDateString('es-CO', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const timeStr = formatTime12Hour(timeInput.value);
        document.getElementById('summary-datetime').textContent = `${dateStr} a las ${timeStr}`;
    } else {
        document.getElementById('summary-datetime').textContent = '--';
    }
    
    // Duración
    const durationSelect = document.getElementById('event-duration');
    const duration = durationSelect.options[durationSelect.selectedIndex]?.text || '--';
    document.getElementById('summary-duration').textContent = duration;
    
    // Ubicación
    const locationSelect = document.getElementById('event-location');
    const location = locationSelect.options[locationSelect.selectedIndex]?.text || '--';
    document.getElementById('summary-location').textContent = location;
    
    // Participantes
    const selectedParticipants = document.querySelectorAll('.participant-checkbox-modal:checked');
    const participantNames = Array.from(selectedParticipants).map(cb => {
        const label = document.querySelector(`label[for="${cb.id}"] .participant-name-modal`);
        return label ? label.textContent : '';
    });
    
    if (participantNames.length > 0) {
        if (participantNames.length <= 3) {
            document.getElementById('summary-participants').textContent = participantNames.join(', ');
        } else {
            document.getElementById('summary-participants').textContent = 
                `${participantNames.slice(0, 3).join(', ')} y ${participantNames.length - 3} más`;
        }
    } else {
        document.getElementById('summary-participants').textContent = '--';
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    // Validar paso final
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Obtener fecha y hora
    const eventDate = document.getElementById('event-date').value;
    const eventTime = document.getElementById('event-time').value;
    
    // Validar que fecha y hora existan
    if (!eventDate || !eventTime) {
        mostrarNotificacion('Error: Fecha u hora no válida. Por favor selecciona un horario del calendario.', 'error');
        return;
    }
    
    // Recopilar todos los datos
    const formData = {
        titulo: document.getElementById('event-title').value,
        tipo: document.getElementById('event-type').value,
        fecha_hora: eventDate + 'T' + eventTime,
        duracion: parseInt(document.getElementById('event-duration').value),
        ubicacion: document.getElementById('event-location').value,
        recordatorio: document.querySelector('input[name="reminder"]:checked').value,
        id_proyecto: document.getElementById('event-proyecto').value || null,
        descripcion: document.getElementById('event-description').value,
        participantes: Array.from(document.querySelectorAll('.participant-checkbox-modal:checked')).map(cb => cb.value)
    };
    
    // Si es virtual, agregar el link
    if (formData.ubicacion === 'virtual') {
        formData.link_virtual = document.getElementById('event-virtual-link').value;
    }
    
    // Enviar al servidor
    const eventId = document.getElementById('event-id').value;
    const url = eventId ? `/lider_semi/eventos/${eventId}` : '/lider_semi/eventos';
    const method = eventId ? 'PUT' : 'POST';
    
    console.log('Enviando datos:', formData); // Debug
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug
        if (data.success) {
            mostrarNotificacion('¡Reunión programada exitosamente!', 'info');
            closeEventModal();
            if (typeof cargarEventos === 'function') cargarEventos();
            if (typeof renderCalendar === 'function') renderCalendar();
        } else {
            mostrarNotificacion(data.message || 'Error al programar la reunión', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        mostrarNotificacion('Error al programar la reunión: ' + error.message, 'error');
    });
}

// Función para abrir el modal del evento (llamada desde calendario-views.js)
function openEventModal(date) {
    // Resetear wizard
    currentStep = 1;
    showStep(1);
    updateButtons();
    
    // Limpiar formulario
    document.getElementById('event-form').reset();
    document.getElementById('event-id').value = '';
    
    // Establecer fecha y hora
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');
    
    if (date) {
        dateInput.value = formatDateForInput(date);
    }
    
    if (timeInput.value) {
        // Mostrar hora seleccionada
        const displayTime = document.getElementById('display-time');
        if (displayTime) {
            displayTime.textContent = formatTime12Hour(timeInput.value);
        }
    }
    
    // Resetear participantes
    document.querySelectorAll('.participant-checkbox-modal').forEach(cb => {
        cb.checked = false;
    });
    updateParticipantsCount();
    
    // Resetear pasos completados
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.classList.remove('completed');
    });
    
    // Ocultar campo de link virtual
    document.getElementById('virtual-link-group').style.display = 'none';
    
    // Abrir modal
    const modal = document.getElementById('event-modal');
    if (modal) {
        modal.classList.add('active');
    }
}

// Esta función será llamada desde calendario-views.js
// No necesitamos sobrescribirla aquí, solo asegurarnos de que openEventModalWithTime esté disponible
window.openEventModalWithTime = function(date, hour) {
    console.log('openEventModalWithTime llamada con:', { date, hour });
    
    // Primero abrir el modal (que hace reset)
    openEventModal(date);
    
    // DESPUÉS establecer fecha y hora (para que no se borren con el reset)
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');
    
    if (dateInput && date) {
        dateInput.value = formatDateForInput(date);
        console.log('Fecha establecida:', dateInput.value);
    } else {
        console.error('No se pudo establecer la fecha', { dateInput, date });
    }
    
    if (timeInput && hour) {
        timeInput.value = hour;
        console.log('Hora establecida:', timeInput.value);
    } else {
        console.error('No se pudo establecer la hora', { timeInput, hour });
    }
    
    // Mostrar hora seleccionada en el display
    const displayTime = document.getElementById('display-time');
    if (displayTime && hour) {
        displayTime.textContent = formatTime12Hour(hour);
        console.log('Display de hora actualizado:', displayTime.textContent);
    }
};
