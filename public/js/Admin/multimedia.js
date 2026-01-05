// ======================================================
// BADGE SEGÚN TIPO DE ARCHIVO (ESPAÑOL)
// ======================================================
function badgeInfo(ext) {
    switch (ext) {
        case 'pdf':
            return { label: 'PDF', color: 'bg-danger', preview: true };

        case 'doc':
        case 'docx':
            return { label: 'WORD', color: 'bg-primary', preview: false };

        case 'ppt':
        case 'pptx':
            return { label: 'PRESENTACIÓN', color: 'bg-warning', preview: false };

        case 'xls':
        case 'xlsx':
            return { label: 'EXCEL', color: 'bg-success', preview: false };

        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return { label: 'IMAGEN', color: 'bg-info', preview: true };

        case 'zip':
        case 'rar':
            return { label: 'COMPRIMIDO', color: 'bg-dark', preview: false };

        default:
            return { label: 'ARCHIVO', color: 'bg-secondary', preview: false };
    }
}

// ======================================================
// CARGAR MULTIMEDIA
// ======================================================
async function cargarMultimedia() {
    try {
        const resp = await fetch('/admin/recursos/multimedia/list');

        if (!resp.ok) {
            throw new Error('Respuesta inválida del servidor');
        }

        const recursos = await resp.json();

        const contPlantillas = document.getElementById('contenedorPlantillas');
        const contManuales   = document.getElementById('contenedorManuales');
        const contOtros      = document.getElementById('contenedorOtros');

        // Limpiar contenedores
        contPlantillas.innerHTML = '';
        contManuales.innerHTML   = '';
        contOtros.innerHTML      = '';

        if (!Array.isArray(recursos)) return;

        recursos.forEach(item => {

            // =========================
            // EXTENSIÓN SEGURA
            // =========================
            let ext = '';
            if (item.extension && item.extension.length <= 5) {
                ext = item.extension.toLowerCase();
            } else if (item.archivo && item.archivo.includes('.')) {
                ext = item.archivo.split('.').pop().toLowerCase();
            }

            const badge = badgeInfo(ext);
            const iconoAccion = badge.preview ? 'bi-eye' : 'bi-download';
            const textoAccion = badge.preview ? 'Ver archivo' : 'Descargar archivo';

            const card = `
                <div class="col-md-3">
                    <div class="card-recurso">
                        <span class="badge ${badge.color} mb-2">${badge.label}</span>

                        <h6 class="mt-1 text-truncate" title="${item.nombre_archivo}">
                            ${item.nombre_archivo}
                        </h6>

                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm"
                                title="${textoAccion}"
                                onclick="window.open('/storage/${item.archivo}', '_blank')">
                                <i class="bi ${iconoAccion}"></i>
                            </button>

                            <button class="btn btn-danger btn-sm"
                                title="Eliminar archivo"
                                onclick="eliminarMultimedia(${item.id})">
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

    } catch (error) {
        console.error('Error cargando multimedia:', error);
        alert('Error al cargar los archivos multimedia');
    }
}

// ======================================================
// ELIMINAR MULTIMEDIA
// ======================================================
async function eliminarMultimedia(id) {
    if (!confirm('¿Eliminar este archivo?')) return;

    try {
        const resp = await fetch(`/admin/recursos/multimedia/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await resp.json();

        if (data.success) {
            alert('✔ Archivo eliminado correctamente');
            cargarMultimedia();
        } else {
            alert(data.message || 'No se pudo eliminar el archivo');
        }

    } catch (error) {
        console.error('Error eliminando archivo:', error);
        alert('Error al eliminar el archivo');
    }
}

// Necesario porque se llama desde HTML dinámico
window.eliminarMultimedia = eliminarMultimedia;

// ======================================================
// DOM READY
// ======================================================
document.addEventListener('DOMContentLoaded', () => {

    const modal       = document.getElementById('modalSubirMultimedia');
    const btnAbrir    = document.getElementById('btnAbrirModalMultimedia');
    const btnCerrar   = document.getElementById('cerrarModalMultimedia');
    const btnCancelar = document.getElementById('cancelarModal');
    const form        = document.getElementById('formSubirMultimedia');

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // Cargar multimedia al iniciar
    cargarMultimedia();

    // -------------------------
    // MODAL
    // -------------------------
    btnAbrir?.addEventListener('click', () => {
        modal.classList.remove('d-none');
    });

    btnCerrar?.addEventListener('click', cerrarModal);
    btnCancelar?.addEventListener('click', cerrarModal);

    modal?.addEventListener('click', e => {
        if (e.target === modal) cerrarModal();
    });

    function cerrarModal() {
        modal.classList.add('d-none');
    }

    // -------------------------
    // SUBIR MULTIMEDIA
    // -------------------------
    form?.addEventListener('submit', async e => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(window.MultimediaConfig.storeUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body: formData,
            });

            const text = await response.text();
            let data;

            try {
                data = JSON.parse(text);
            } catch {
                console.error('Respuesta NO JSON:', text);
                alert('Error del servidor');
                return;
            }

            if (data.success) {
                alert('✔ Multimedia subida correctamente');
                cerrarModal();
                form.reset();
                cargarMultimedia();
            } else {
                alert(data.message || 'Error al subir multimedia');
            }

        } catch (error) {
            console.error('Error subiendo multimedia:', error);
            alert('Error de conexión');
        }
    });
});
