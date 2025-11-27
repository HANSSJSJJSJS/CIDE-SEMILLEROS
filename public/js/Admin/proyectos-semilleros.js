// public/js/admin/proyectos-semilleros.js
document.addEventListener('DOMContentLoaded', () => {
    const cfg = window.proyectosSemillero || {};

    // ============================
    // Definir swalSuccess / swalError (si no existen ya)
    // ============================
    if (!window.swalSuccess) {
        window.swalSuccess = function (msg) {
            if (!msg) return;
            Swal.fire({
                icon: 'success',
                title: 'Operación exitosa',
                text: msg,
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-usuarios',
                    confirmButton: 'swal-confirmar'
                },
                buttonsStyling: false
            });
        };
    }

    if (!window.swalError) {
        window.swalError = function (msg) {
            if (!msg) return;
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un problema',
                text: msg,
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-usuarios',
                    confirmButton: 'swal-confirmar'
                },
                buttonsStyling: false
            });
        };
    }

    // ============================
    // Reabrir modal "Crear proyecto"
    // ============================
    if (cfg.openCrear) {
        const modalCrear = document.getElementById('modalCrearProyecto');
        if (modalCrear) {
            const m = new bootstrap.Modal(modalCrear);
            m.show();
        }
    }

    // ============================
    // Mostrar notificaciones flash
    // ============================
    if (cfg.flashSuccess) {
        swalSuccess(cfg.flashSuccess);
    }

    if (cfg.flashError) {
        swalError(cfg.flashError);
    }

    // ============================
    // Modal EDITAR PROYECTO
    // ============================
    const modalEditarEl = document.getElementById('modalEditarProyecto');
    const formEditar    = document.getElementById('formEditarProyecto');

    if (modalEditarEl && formEditar) {
        const modalEditar = new bootstrap.Modal(modalEditarEl);

        document.querySelectorAll('.btn-edit-proyecto').forEach(btn => {
            btn.addEventListener('click', async () => {
                const semilleroId = btn.dataset.semillero;
                const proyectoId  = btn.dataset.proyecto;

                const base = document.documentElement.dataset.appUrl || '';
                const urlJson = `${base}/admin/semilleros/${semilleroId}/proyectos/${proyectoId}/json`;
                const urlPut  = `${base}/admin/semilleros/${semilleroId}/proyectos/${proyectoId}`;

                try {
                    const res = await fetch(urlJson, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) throw new Error('No se pudo cargar el proyecto');

                    const p = await res.json();

                    document.getElementById('e_nombre').value = p.nombre_proyecto ?? '';
                    document.getElementById('e_estado').value = p.estado ?? 'EN_FORMULACION';
                    document.getElementById('e_desc').value   = p.descripcion ?? '';
                    document.getElementById('e_inicio').value = p.fecha_inicio ?? '';
                    document.getElementById('e_fin').value    = p.fecha_fin ?? '';

                    formEditar.action = urlPut;
                    modalEditar.show();
                } catch (err) {
                    swalError(err.message || 'Error cargando datos del proyecto');
                }
            });
        });
    }

    // ============================
    // Confirmar ELIMINAR proyecto
    // ============================
    document.addEventListener('click', function (e) {
        const formDelete = e.target.closest('.form-delete-proyecto');
        if (!formDelete) return;

        e.preventDefault();

        const nombre = formDelete.dataset.nombre || 'este proyecto';

        Swal.fire({
            title: `¿Eliminar el proyecto "${nombre}"?`,
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            reverseButtons: true,
            customClass: {
                popup: 'swal-usuarios',
                confirmButton: 'swal-confirmar',
                cancelButton: 'swal-cancelar'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                formDelete.submit();
            }
        });
    });
});

