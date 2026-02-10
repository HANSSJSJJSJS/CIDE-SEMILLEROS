/* ======================================================
   CONFIGURACIÓN GLOBAL
====================================================== */
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ======================================================
   NOTIFICADOR
====================================================== */
function notify(title, message, type = 'success') {
    const container = document.getElementById('notificationContainer');

    if (!container) {
        Swal.fire({
            icon: type,
            title,
            text: message,
            timer: 2500,
            showConfirmButton: false
        });
        return;
    }

    const el = document.createElement('div');
    el.className = `notification ${type}`;

    el.innerHTML = `
        <div class="notification-icon">
            <i class="bi bi-${type === 'error' ? 'x-circle-fill' : 'check-circle-fill'}"></i>
        </div>
        <div class="notification-body">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close">
            <i class="bi bi-x"></i>
        </button>
    `;

    container.appendChild(el);
    el.querySelector('.notification-close').onclick = () => el.remove();
    setTimeout(() => el.remove(), 4000);
}

/* ======================================================
   BADGE SEGÚN TIPO DE ARCHIVO
====================================================== */
function badgeInfo(tipo) {
    const t = (tipo || '').toLowerCase();

    switch (t) {
        case 'pdf': return { label: 'PDF', color: 'bg-danger', preview: true };
        case 'word':
        case 'doc':
        case 'docx': return { label: 'WORD', color: 'bg-primary', preview: false };
        case 'excel':
        case 'xls':
        case 'xlsx': return { label: 'EXCEL', color: 'bg-success', preview: false };
        case 'ppt':
        case 'pptx':
        case 'powerpoint': return { label: 'PRESENTACIÓN', color: 'bg-warning', preview: false };
        case 'imagen':
        case 'jpg':
        case 'jpeg':
        case 'png': return { label: 'IMAGEN', color: 'bg-info', preview: true };
        default: return { label: 'ARCHIVO', color: 'bg-secondary', preview: false };
    }
}

/* ======================================================
   CARGAR MULTIMEDIA
====================================================== */
async function cargarMultimedia() {
    try {
        const resp = await fetch('/admin/recursos/multimedia/list', {
            headers: { 'Accept': 'application/json' }
        });

        if (!resp.ok) throw new Error();

        const recursos = await resp.json();

        const contPlantillas = document.getElementById('contenedorPlantillas');
        const contManuales   = document.getElementById('contenedorManuales');
        const contOtros      = document.getElementById('contenedorOtros');

        contPlantillas.innerHTML = '';
        contManuales.innerHTML   = '';
        contOtros.innerHTML      = '';

        recursos.forEach(item => {

            const id = item.id_recurso ?? item.id;
            if (!id) return;

            const badge = badgeInfo(item.tipo_documento);
            const icono = badge.preview ? 'bi-eye' : 'bi-download';

            // URL real del archivo en storage (admita también item.url si viene del backend)
            const fileUrl = item.url || (`/storage/${item.archivo}`);

            const descripcion =
                typeof item.descripcion === 'string' && item.descripcion.trim() !== ''
                    ? item.descripcion
                    : 'Sin descripción';

            const card = `
                <div class="col-md-3 archivo-item">
                    <div class="card-recurso shadow-sm">
                        <span class="badge ${badge.color} mb-2">${badge.label}</span>

                        <h6 class="text-truncate" title="${item.nombre_archivo || ''}">
                            ${item.nombre_archivo || 'Archivo sin nombre'}
                        </h6>

                        <p class="text-muted small mt-1 descripcion-recurso">
                            ${descripcion}
                        </p>

                        <div class="d-flex gap-2 mt-3">
                            <a class="btn btn-outline-primary btn-sm" href="${fileUrl}" download>
                                <i class="bi ${icono}"></i>
                            </a>

                            <button
                                class="btn btn-danger btn-sm btnEliminarMultimedia"
                                data-id="${id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            if (item.categoria === 'plantillas') {
                contPlantillas.insertAdjacentHTML('beforeend', card);
            } else if (item.categoria === 'manuales') {
                contManuales.insertAdjacentHTML('beforeend', card);
            } else {
                contOtros.insertAdjacentHTML('beforeend', card);
            }
        });

    } catch (err) {
        console.error(err);
        notify('Error', 'No se pudo cargar la multimedia', 'error');
    }
}

/* ======================================================
   ELIMINAR MULTIMEDIA (delegado)
====================================================== */
async function eliminarMultimedia(id) {
    if (!id) return;

    const confirm = await Swal.fire({
        icon: 'warning',
        title: 'Eliminar archivo',
        text: '¿Deseas eliminar este archivo?',
        showCancelButton: true,
        confirmButtonText: 'Eliminar'
    });

    if (!confirm.isConfirmed) return;

    try {
        const resp = await fetch(`/admin/recursos/multimedia/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message);

        notify('Eliminado', 'Archivo eliminado correctamente');
        cargarMultimedia();

    } catch (err) {
        notify('Error', err.message || 'No se pudo eliminar', 'error');
    }
}

/* ======================================================
   DOM READY (TODO AQUÍ)
====================================================== */
document.addEventListener('DOMContentLoaded', () => {

    cargarMultimedia();

    const btnAbrir    = document.getElementById('btnAbrirModalMultimedia');
    const modal       = document.getElementById('modalSubirMultimedia');
    const btnCerrar   = document.getElementById('cerrarModalMultimedia');
    const btnCancelar = document.getElementById('cancelarModal');
    const form        = document.getElementById('formSubirMultimedia');

    // Abrir modal
    btnAbrir?.addEventListener('click', () => {
        modal.classList.remove('d-none');
    });

    // Cerrar modal
    btnCerrar?.addEventListener('click', () => modal.classList.add('d-none'));
    btnCancelar?.addEventListener('click', () => modal.classList.add('d-none'));

    // Click fuera
    modal?.addEventListener('click', e => {
        if (e.target === modal) modal.classList.add('d-none');
    });

    // Eliminar (delegación)
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btnEliminarMultimedia');
        if (btn) eliminarMultimedia(btn.dataset.id);
    });

    // SUBIR MULTIMEDIA
    form?.addEventListener('submit', async e => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const resp = await fetch(window.MultimediaConfig.storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await resp.json();
            if (!resp.ok || !data.success) {
                throw new Error(data.message || 'Error al subir');
            }

            notify('Archivo subido', 'El documento se cargó correctamente');
            modal.classList.add('d-none');
            form.reset();
            cargarMultimedia();

        } catch (err) {
            notify('Error', err.message || 'No se pudo subir', 'error');
        }
    });
});
