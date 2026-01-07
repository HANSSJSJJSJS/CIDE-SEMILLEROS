// Sistema de Wizard paso a paso para crear reuniones
let currentStep = 1;
const totalSteps = 3;
let closeModalCallback = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar solo si el wizard existe en esta página
    const hasWizard = !!document.getElementById('btn-next-step')
        && !!document.getElementById('btn-prev-step')
        && !!document.getElementById('btn-submit')
        && !!document.getElementById('event-type');
    if (!hasWizard) return;

    // Inicializar con la función global por defecto
    initializeWizard(window.closeEventModal);
});

function initializeWizard(closeEventModal) {
    // Guardar el callback
    closeModalCallback = closeEventModal || window.closeEventModal;
    
    // Botones de navegación
    const btnNext = document.getElementById('btn-next-step');
    const btnPrev = document.getElementById('btn-prev-step');
    const btnCancel = document.getElementById('btn-cancel');
    const eventForm = document.getElementById('event-form');
    
    if (btnNext) btnNext.addEventListener('click', nextStep);
    if (btnPrev) btnPrev.addEventListener('click', prevStep);
    if (btnCancel) btnCancel.addEventListener('click', () => closeModalCallback());
    if (eventForm) eventForm.addEventListener('submit', handleFormSubmit);
    
    // El enlace virtual se generará automáticamente en el backend
    
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

    if (!btnPrev || !btnNext || !btnSubmit) return;
    
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
            const titleEl = document.getElementById('event-title');
            const typeEl = document.getElementById('event-type');
            const locationEl = document.getElementById('event-location');

            if (!titleEl || !typeEl || !locationEl) {
                return true;
            }

            const title = titleEl.value.trim();
            const type = typeEl.value;
            const location = locationEl.value;
            
            if (!title) {
                errorMessage = 'Por favor ingresa el título de la reunión';
                isValid = false;
            } else if (!type) {
                errorMessage = 'Por favor selecciona el tipo de reunión';
                isValid = false;
            } else if (!location) {
                errorMessage = 'Por favor selecciona la ubicación';
                isValid = false;
            }
            // Ya no validamos link virtual porque se genera automáticamente
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
    const titleEl = document.getElementById('event-title');
    const summaryTitleEl = document.getElementById('summary-title');
    if (titleEl && summaryTitleEl) {
        summaryTitleEl.textContent = titleEl.value || '--';
    }
    
    // Tipo
    const typeSelect = document.getElementById('event-type');
    const summaryTypeEl = document.getElementById('summary-type');
    if (typeSelect && summaryTypeEl) {
        const type = typeSelect.options[typeSelect.selectedIndex]?.text || '--';
        summaryTypeEl.textContent = type;
    }
    
    // Fecha y hora
    const dateInput = document.getElementById('event-date');
    const timeInput = document.getElementById('event-time');
    const summaryDtEl = document.getElementById('summary-datetime');
    if (!summaryDtEl) {
        // No hay sección resumen en esta página
    } else if (dateInput && timeInput && dateInput.value && timeInput.value) {
        const date = new Date(dateInput.value + 'T' + timeInput.value);
        const dateStr = date.toLocaleDateString('es-CO', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const timeStr = formatTime12Hour(timeInput.value);
        summaryDtEl.textContent = `${dateStr} a las ${timeStr}`;
    } else {
        summaryDtEl.textContent = '--';
    }
    
    // Duración
    const durationSelect = document.getElementById('event-duration');
    const summaryDurEl = document.getElementById('summary-duration');
    if (durationSelect && summaryDurEl) {
        const duration = durationSelect.options[durationSelect.selectedIndex]?.text || '--';
        summaryDurEl.textContent = duration;
    }
    
    // Ubicación
    const locationSelect = document.getElementById('event-location');
    const summaryLocEl = document.getElementById('summary-location');
    if (locationSelect && summaryLocEl) {
        const location = locationSelect.options[locationSelect.selectedIndex]?.text || '--';
        summaryLocEl.textContent = location;
    }
    
    // Participantes
    const selectedParticipants = document.querySelectorAll('.participant-checkbox-modal:checked');
    const participantNames = Array.from(selectedParticipants).map(cb => {
        const label = document.querySelector(`label[for="${cb.id}"] .participant-name-modal`);
        return label ? label.textContent : '';
    });
    
    if (participantNames.length > 0) {
        const sumPartsEl = document.getElementById('summary-participants');
        if (sumPartsEl) {
            if (participantNames.length <= 3) {
                sumPartsEl.textContent = participantNames.join(', ');
            } else {
                sumPartsEl.textContent = `${participantNames.slice(0, 3).join(', ')} y ${participantNames.length - 3} más`;
            }
        }
    } else {
        const sumPartsEl = document.getElementById('summary-participants');
        if (sumPartsEl) sumPartsEl.textContent = '--';
    }
}

function handleFormSubmit(e) {
    e.preventDefault();

    const eventDateEl = document.getElementById('event-date');
    const eventTimeEl = document.getElementById('event-time');
    const titleEl = document.getElementById('event-title');
    const typeEl = document.getElementById('event-type');
    const durationEl = document.getElementById('event-duration');
    const locationEl = document.getElementById('event-location');
    const reminderEl = document.querySelector('input[name="reminder"]:checked');
    const proyectoEl = document.getElementById('event-proyecto');
    const descEl = document.getElementById('event-description');

    if (!eventDateEl || !eventTimeEl || !titleEl || !typeEl || !durationEl || !locationEl) {
        return;
    }
    
    // Validar paso final
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Obtener fecha y hora
    const eventDate = eventDateEl.value;
    const eventTime = eventTimeEl.value;
    
    // Validar que fecha y hora existan
    if (!eventDate || !eventTime) {
        mostrarNotificacion('Error: Fecha u hora no válida. Por favor selecciona un horario del calendario.', 'error');
        return;
    }
    
    // Recopilar todos los datos
    const formData = {
        titulo: titleEl.value,
        tipo: typeEl.value,
        fecha_hora: eventDate + 'T' + eventTime,
        duracion: parseInt(durationEl.value),
        ubicacion: locationEl.value,
        recordatorio: reminderEl ? reminderEl.value : 'none',
        id_proyecto: proyectoEl && proyectoEl.value ? proyectoEl.value : null,
        descripcion: descEl ? descEl.value : null,
        participantes: Array.from(document.querySelectorAll('.participant-checkbox-modal:checked')).map(cb => cb.value)
    };
    
    // Si es virtual, generar enlace automático con Teams
    if (formData.ubicacion === 'virtual') {
        formData.generar_enlace = 'teams';
    }
    
    // Enviar al servidor
    const eventId = document.getElementById('event-id').value;
    const url = eventId ? `/lider_semi/eventos/${eventId}` : '/lider_semi/eventos';
    const method = eventId ? 'PUT' : 'POST';
    
    // Deshabilitar botones para evitar doble envío
    const btnSubmit = document.getElementById('btn-submit');
    const btnNext = document.getElementById('btn-next-step');
    const btnPrev = document.getElementById('btn-prev-step');
    const btnCancel = document.getElementById('btn-cancel');
    [btnSubmit, btnNext, btnPrev, btnCancel].forEach(b=>{ if(b){ b.disabled = true; b.classList.add('is-loading'); }});

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
            // Mensaje personalizado si se generó enlace virtual
            let mensaje = '¡Reunión programada exitosamente!';
            if (data.evento && data.evento.link_virtual) {
                mensaje = '¡Reunión virtual creada! El enlace está disponible en los detalles.';
            }
            mostrarNotificacion(mensaje, 'info');
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
    })
    .finally(()=>{
        [btnSubmit, btnNext, btnPrev, btnCancel].forEach(b=>{ if(b){ b.disabled = false; b.classList.remove('is-loading'); }});
    });
}

// Función para abrir el modal del evento (llamada desde calendario-views.js)
function openTimeSlotModal(date, preselectedHour = null, eventId = null) {
    const timeSlotModal = document.getElementById('time-slot-modal');
    const container = document.getElementById('time-slots-container');
    if (!timeSlotModal || !container) {
        console.error('No se encontró el modal de selección de hora o su contenedor');
        return;
    }

    // Limpiar contenedor
    container.innerHTML = '';
    
    // Formatear fecha para mostrar
    const formattedDate = date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('selected-date-display').textContent = formattedDate;
    
    // Generar slots de tiempo disponibles
    const timeSlots = [
        '08:00', '09:00', '10:00', '11:00',
        '14:00', '15:00', '16:00'
    ];
    
    // Mostrar el modal
    timeSlotModal.classList.add('active');
    
    timeSlots.forEach(time => {
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        if (time === preselectedHour) {
            slot.classList.add('selected');
        }
        
        const [hour] = time.split(':').map(Number);
        const isBlocked = hour < 8 || hour >= 17 || (hour >= 12 && hour < 14); // Validar horario laboral
        
        if (isBlocked) {
            slot.classList.add('disabled');
        } else {
            slot.onclick = () => {
                if (eventId) {
                    // Actualizar evento existente
                    moveEvent(eventId, date, time);
                    timeSlotModal.classList.remove('active');
                } else {
                    // Crear nuevo evento
                    openEventModal(date, time);
                    timeSlotModal.classList.remove('active');
                }
            };
        }
        
        slot.textContent = formatTime12Hour(time);
        container.appendChild(slot);
    });
    
    modal.style.display = 'block';
    
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
