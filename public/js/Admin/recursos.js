// public/js/admin/recursos.js

// ===== Utilidades =====
const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
const CSRF_TOKEN = csrfTokenMeta ? csrfTokenMeta.content : null;

function buildUrl(template, id) {
    return template.replace(':id', id);
}

// ===== Notificaciones =====
function showNotification(title, message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const iconMap = {
        success: '<i class="bi bi-check-circle-fill"></i>',
        error: '<i class="bi bi-x-circle-fill"></i>',
        warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
        info: '<i class="bi bi-info-circle-fill"></i>'
    };

    notification.innerHTML = `
        <div class="notification-icon">
            ${iconMap[type] || iconMap.success}
        </div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close">
            <i class="bi bi-x"></i>
        </button>
    `;

    container.appendChild(notification);

    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => closeNotification(notification));

    setTimeout(() => closeNotification(notification), 5000);
}

function closeNotification(notificationEl) {
    if (!notificationEl) return;
    notificationEl.classList.add('hiding');
    setTimeout(() => notificationEl.remove(), 300);
}

// ===== Lógica principal =====
document.addEventListener('DOMContentLoaded', () => {
    const modalActividades = document.getElementById('modalActividades');
    const contenedorActividades = document.getElementById('contenedorActividades');
    const tituloModalActividades = document.getElementById('tituloModalActividades');

    const modalActividadLider = document.getElementById('modalActividadLider');
    const btnAbrirModalActividad = document.getElementById('btnAbrirModalActividad');
    const btnCancelarActividad = document.getElementById('btnCancelarActividad');
    const formActividadLider = document.getElementById('formActividadLider');

    const selectSemillero = document.getElementById('semillero_id');
    const inputLiderNombre = document.getElementById('lider_nombre');
    const inputLiderId = document.getElementById('lider_id');
    const mensajeLider = document.getElementById('mensaje-lider');

    const selectTipoActividad = document.getElementById('tipo_actividad');
    const infoTipoActividad = document.getElementById('info-tipo-actividad');
    const inputFechaLimite = document.getElementById('fecha_limite_actividad');

    const hoy = new Date().toISOString().split('T')[0];
    if (inputFechaLimite) {
        inputFechaLimite.value = hoy;
        inputFechaLimite.min = hoy;
    }

    // ==== Abrir modal actividades (ver) ====
    document.querySelectorAll('.btn-ver-actividades').forEach(btn => {
        btn.addEventListener('click', () => {
            const semilleroId = btn.dataset.semilleroId;
            const semilleroNombre = btn.dataset.semilleroNombre;
            abrirModalActividades(semilleroId, semilleroNombre);
        });
    });

    function abrirModalActividades(semilleroId, semilleroNombre) {
        if (!modalActividades) return;
        tituloModalActividades.textContent = `Actividades - ${semilleroNombre}`;
        modalActividades.classList.add('active');
        cargarActividades(semilleroId);
    }

    function cerrarModalActividades() {
        if (!modalActividades) return;
        modalActividades.classList.remove('active');
        if (contenedorActividades) contenedorActividades.innerHTML = '';
    }

    // Cerrar modal actividades (botón X)
    document.querySelectorAll('[data-close-modal="actividades"]').forEach(btn => {
        btn.addEventListener('click', cerrarModalActividades);
    });

    if (modalActividades) {
        modalActividades.addEventListener('click', e => {
            if (e.target === modalActividades) cerrarModalActividades();
        });
    }

    function cargarActividades(semilleroId) {
        if (!contenedorActividades) return;

        const url = buildUrl(window.ACT_ACTIVIDADES_POR_SEMILLERO_URL, semilleroId);
        contenedorActividades.innerHTML =
            '<div class="text-center py-4"><i class="bi bi-arrow-repeat spin fs-3 text-muted"></i></div>';

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const actividades = data.actividades || [];
                if (!actividades.length) {
                    contenedorActividades.innerHTML = `
                        <div class="actividades-vacio">
                            <i class="bi bi-inbox"></i>
                            <p>No hay actividades asignadas a este líder</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                actividades.forEach(act => {
                    const estado = act.estado || 'pendiente';
                    const badgeClass =
                        estado === 'pendiente' ? 'badge-pendiente' :
                        estado === 'en_proceso' ? 'badge-en-proceso' :
                        'badge-completado';

                    const estadoTexto =
                        estado === 'pendiente' ? 'PENDIENTE' :
                        estado === 'en_proceso' ? 'EN PROCESO' :
                        'COMPLETADA';

                    html += `
                        <div class="actividad-card">
                            <div class="actividad-header">
                                <h3 class="actividad-titulo">${act.titulo || 'Sin título'}</h3>
                                <span class="${badgeClass}">${estadoTexto}</span>
                            </div>
                            <div class="actividad-info">
                                Líder: ${act.lider_nombre || 'N/A'} ·
                                Fecha límite: ${act.fecha_limite || 'Sin definir'}
                            </div>
                            <p class="actividad-descripcion">${act.descripcion || 'Sin descripción'}</p>
                        </div>
                    `;
                });

                contenedorActividades.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                contenedorActividades.innerHTML = `
                    <div class="actividades-vacio">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>Error al cargar las actividades</p>
                    </div>
                `;
            });
    }

    // ==== Abrir modal actividad (crear) ====
    function limpiarFormActividad() {
        if (!formActividadLider) return;
        formActividadLider.reset();
        if (inputFechaLimite) inputFechaLimite.value = hoy;
        if (infoTipoActividad) infoTipoActividad.innerHTML = '';
        if (inputLiderNombre) inputLiderNombre.value = '';
        if (inputLiderId) inputLiderId.value = '';
        if (mensajeLider) {
            mensajeLider.textContent = 'El líder se cargará automáticamente al seleccionar el semillero.';
            mensajeLider.className = 'text-muted';
        }
    }

    function abrirModalActividad(preselectSemilleroId = null) {
        if (!modalActividadLider) return;
        limpiarFormActividad();
        modalActividadLider.classList.add('active');

        if (preselectSemilleroId && selectSemillero) {
            selectSemillero.value = preselectSemilleroId;
            selectSemillero.dispatchEvent(new Event('change'));
        }
    }

    if (btnAbrirModalActividad && window.ACT_CAN_CREATE) {
        btnAbrirModalActividad.addEventListener('click', () => abrirModalActividad());
    }

    // Abrir modal desde card concreta
    document.querySelectorAll('.btn-crear-actividad-card').forEach(btn => {
        btn.addEventListener('click', () => {
            const semilleroId = btn.dataset.semilleroId;
            abrirModalActividad(semilleroId);
        });
    });

    function cerrarModalActividad() {
        if (!modalActividadLider) return;
        modalActividadLider.classList.remove('active');
    }

    if (btnCancelarActividad) {
        btnCancelarActividad.addEventListener('click', cerrarModalActividad);
    }

    if (modalActividadLider) {
        modalActividadLider.addEventListener('click', e => {
            if (e.target === modalActividadLider) cerrarModalActividad();
        });
    }

    // ==== Al cambiar semillero, cargar líder ====
    if (selectSemillero) {
        selectSemillero.addEventListener('change', () => {
            const semilleroId = selectSemillero.value;
            if (!semilleroId) {
                if (inputLiderNombre) inputLiderNombre.value = '';
                if (inputLiderId) inputLiderId.value = '';
                if (mensajeLider) {
                    mensajeLider.textContent = 'El líder se cargará automáticamente al seleccionar el semillero.';
                    mensajeLider.className = 'text-muted';
                }
                return;
            }

            if (inputLiderNombre) inputLiderNombre.value = 'Cargando líder...';
            if (mensajeLider) {
                mensajeLider.textContent = 'Buscando líder asociado al semillero...';
                mensajeLider.className = 'text-muted';
            }

            const url = buildUrl(window.ACT_SEMILLERO_LIDER_URL, semilleroId);

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    const lider = data.lider || null;
                    if (lider) {
                        if (inputLiderNombre) inputLiderNombre.value = lider.nombre_completo;
                        if (inputLiderId) inputLiderId.value = lider.id;
                        if (mensajeLider) {
                            mensajeLider.textContent = 'Actividad será asignada directamente a este líder.';
                            mensajeLider.className = 'text-success';
                        }
                    } else {
                        if (inputLiderNombre) inputLiderNombre.value = 'Sin líder asignado';
                        if (inputLiderId) inputLiderId.value = '';
                        if (mensajeLider) {
                            mensajeLider.textContent = 'Este semillero no tiene líder asignado. No podrás crear la actividad.';
                            mensajeLider.className = 'text-warning';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (inputLiderNombre) inputLiderNombre.value = 'Error al cargar líder';
                    if (inputLiderId) inputLiderId.value = '';
                    if (mensajeLider) {
                        mensajeLider.textContent = 'No se pudo cargar el líder del semillero.';
                        mensajeLider.className = 'text-danger';
                    }
                });
        });
    }

    // ==== Info por tipo de actividad ====
    if (selectTipoActividad && infoTipoActividad) {
        selectTipoActividad.addEventListener('change', () => {
            const tipo = selectTipoActividad.value;
            infoTipoActividad.innerHTML = '';
            if (!tipo) return;

            let descripcionTipo = '';
            switch (tipo) {
                case 'reunion':
                    descripcionTipo = 'El líder deberá programar o asistir a una reunión con el semillero.';
                    break;
                case 'informe':
                    descripcionTipo = 'El líder deberá entregar un informe (avance, resultados, etc.).';
                    break;
                case 'seguimiento':
                    descripcionTipo = 'El líder deberá realizar seguimiento a los proyectos o actividades del semillero.';
                    break;
                case 'planeacion':
                    descripcionTipo = 'El líder deberá planear actividades, cronogramas o metas del semillero.';
                    break;
                case 'otro':
                    descripcionTipo = 'Actividad personalizada. Describe claramente lo que se espera del líder.';
                    break;
            }

            infoTipoActividad.innerHTML = `
                <div class="mb-3">
                    <div class="alert" style="background-color:#e8f5e9;border-left:4px solid #5aa72e;border-radius:8px;padding:12px 16px;">
                        <i class="bi bi-info-circle-fill text-success me-2"></i>
                        <strong>${descripcionTipo}</strong>
                    </div>
                </div>
            `;
        });
    }

    // ==== Enviar formulario ====
    if (formActividadLider) {
        formActividadLider.addEventListener('submit', e => {
            e.preventDefault();

            const fechaSeleccionada = inputFechaLimite ? inputFechaLimite.value : null;
            const fechaHoy = new Date().toISOString().split('T')[0];

            if (fechaSeleccionada && fechaSeleccionada < fechaHoy) {
                showNotification(
                    'Fecha inválida',
                    'No se pueden crear actividades con fecha límite anterior a hoy.',
                    'error'
                );
                return;
            }

            if (!inputLiderId || !inputLiderId.value) {
                showNotification(
                    'Líder no asignado',
                    'No es posible crear una actividad para un semillero sin líder asignado.',
                    'error'
                );
                return;
            }

            const formData = new FormData(formActividadLider);
            const btnGuardar = formActividadLider.querySelector('.btn-guardar-modal');

            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.textContent = 'Guardando...';
            }

            fetch(window.ACT_STORE_URL, {
                method: 'POST',
                body: formData,
                headers: CSRF_TOKEN ? { 'X-CSRF-TOKEN': CSRF_TOKEN } : {}
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showNotification(
                            '¡Actividad creada!',
                            'La actividad se ha asignado exitosamente al líder del semillero.',
                            'success'
                        );
                        cerrarModalActividad();
                        setTimeout(() => {
                            window.location.href =
                                window.location.href.split('?')[0] + '?t=' + new Date().getTime();
                        }, 1500);
                    } else {
                        showNotification(
                            'Error al guardar',
                            data.message || 'No se pudo guardar la actividad. Intenta nuevamente.',
                            'error'
                        );
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification(
                        'Error de conexión',
                        'Hubo un problema al conectar con el servidor. Verifica tu conexión.',
                        'error'
                    );
                })
                .finally(() => {
                    if (btnGuardar) {
                        btnGuardar.disabled = false;
                        btnGuardar.textContent = 'Guardar Actividad';
                    }
                });
        });
    }
});
