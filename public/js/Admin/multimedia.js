// ========================================
// BADGE POR EXTENSIÓN
// ========================================
function badgeInfo(ext) {
    switch (ext) {
        case 'pdf':  return { label: 'PDF', color: 'bg-danger' };
        case 'doc':
        case 'docx': return { label: 'WORD', color: 'bg-primary' };
        case 'ppt':
        case 'pptx': return { label: 'PPT', color: 'bg-warning' };
        case 'xls':
        case 'xlsx': return { label: 'EXCEL', color: 'bg-success' };
        case 'jpg':
        case 'jpeg':
        case 'png':  return { label: 'IMAGEN', color: 'bg-info' };
        default:     return { label: 'ARCHIVO', color: 'bg-secondary' };
    }
}

// ========================================
// CARGAR MULTIMEDIA
// ========================================
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

        contPlantillas.innerHTML = '';
        contManuales.innerHTML   = '';
        contOtros.innerHTML      = '';

        recursos.forEach(item => {
            const ext = item.extension || '';
            const badge = badgeInfo(ext);

            const card = `
                <div class="col-md-3">
                    <div class="card-recurso">
                        <span class="badge ${badge.color} mb-2">${badge.label}</span>
                        <h6>${item.nombre_archivo}</h6>

                        <div class="d-flex gap-2 mt-2">
                            <a class="btn btn-outline-primary btn-sm"
                               href="/storage/${item.archivo}"
                               target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>

                            <button class="btn btn-danger btn-sm"
                                onclick="eliminarMultimedia(${item.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            if (item.categoria === 'plantillas') {
                contPlantillas.innerHTML += card;
            } else if (item.categoria === 'manuales') {
                contManuales.innerHTML += card;
            } else {
                contOtros.innerHTML += card;
            }
        });

    } catch (error) {
        console.error('Error cargando multimedia:', error);
    }
}

// ========================================
// ELIMINAR
// ========================================
async function eliminarMultimedia(id) {
    if (!confirm('¿Eliminar este archivo?')) return;

    await fetch(`${window.MultimediaConfig.deleteBaseUrl}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    cargarMultimedia();
}

window.eliminarMultimedia = eliminarMultimedia;

// ========================================
document.addEventListener('DOMContentLoaded', cargarMultimedia);
