// ======================================================
// NOTIFICACIONES INSTITUCIONALES (GLOBAL)
// ======================================================
window.showNotification = function (title, message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `notification ${type}`;

    el.innerHTML = `
        <div class="notification-icon">
            <i class="bi bi-${type === 'error' ? 'x-circle-fill' : 'check-circle-fill'}"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close"><i class="bi bi-x"></i></button>
    `;

    container.appendChild(el);

    el.querySelector('.notification-close').addEventListener('click', () => el.remove());
    setTimeout(() => el.remove(), 4000);
};





/* ======================================================
   CONFIGURACIÓN GLOBAL
====================================================== */

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

window.buildUrl = function (base, id) {
    return base.replace(':id', id);
};


/* ======================================================
   MODAL BOOTSTRAP
====================================================== */
let modalCrear = null;
let modalVer = null;
let modalEditar = null;
let semilleroActualId = null;
 

document.addEventListener("DOMContentLoaded", () => {

    modalCrear = new bootstrap.Modal(document.getElementById("modalSubirRecurso"));
    modalVer = new bootstrap.Modal(document.getElementById("modalActividades"));
    const modalEditarEl = document.getElementById("modalEditarRecurso");
    if (modalEditarEl) {
        modalEditar = new bootstrap.Modal(modalEditarEl);
    }

    initBotonesCrear();
    initBotonesVerRecursos();
    initFormularioCrear();
    initFormularioEditar();
});


/* ======================================================
   BOTONES: ABRIR MODAL CREAR RECURSO
====================================================== */

function initBotonesCrear() {
    const btnGlobal = document.getElementById("btnAbrirModalActividad");

    if (btnGlobal) {
        btnGlobal.addEventListener("click", () => abrirModalCrear(null));
    }

    document.querySelectorAll(".btn-crear-actividad-card").forEach(btn => {
        btn.addEventListener("click", () => {
            abrirModalCrear(btn.dataset.semilleroId);
        });
    });
}

window.abrirModalCrear = abrirModalCrear;

function abrirModalCrear(semilleroId = null) {

    console.log("✔ abrirModalCrear ejecutada con ID:", semilleroId);

    const form = document.getElementById("formSubirRecurso");
    const selSem = document.getElementById("semillero_id");
    const fixSem = document.getElementById("semillero_id_fijo");

    form.reset();

    if (semilleroId) {
        fixSem.value = semilleroId;
        selSem.value = semilleroId;
        selSem.disabled = true;

        cargarLider(semilleroId);
        cargarProyectos(semilleroId);

        document.getElementById("tituloModalRecurso").textContent = "Crear recurso para su líder";

    } else {
        selSem.disabled = false;
        fixSem.value = "";
        document.getElementById("tituloModalRecurso").textContent = "Crear recurso";
    }

    modalCrear.show();
}


/* ======================================================
   BOTÓN "VER RECURSOS"
====================================================== */

function initBotonesVerRecursos() {
    document.querySelectorAll(".btn-ver-actividades").forEach(btn => {
        btn.addEventListener("click", () => {
            abrirModalVerRecursos(btn.dataset.semilleroId, btn.dataset.semilleroNombre);
        });
    });
}

window.abrirModalVerRecursos = abrirModalVerRecursos;

function abrirModalVerRecursos(id, nombre) {

    console.log("✔ abrirModalVerRecursos ejecutada:", id, nombre);

    document.getElementById("tituloModalActividades").textContent =
        `Recursos - ${nombre}`;

    semilleroActualId = id;
    cargarRecursos(id);

    modalVer.show();
}


/* ======================================================
   CARGAR RECURSOS (AJAX)
====================================================== */

function cargarRecursos(id) {

    const url = buildUrl(window.ACT_ACTIVIDADES_POR_SEMILLERO_URL, id);
    const cont = document.getElementById("contenedorActividades");

    cont.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary"></div>
        </div>
    `;

    fetch(url)
        .then(r => r.json())
        .then(data => renderRecursos(data.actividades ?? []))
        .catch(() => {
            cont.innerHTML = `
                <div class="text-center text-danger p-4">
                    <i class="bi bi-x-circle fs-1"></i>
                    <p>Error al cargar los recursos.</p>
                </div>`;
        });
}


/* ======================================================
   DIBUJAR TARJETAS DE RECURSOS
====================================================== */

function renderRecursos(lista) {

    const cont = document.getElementById("contenedorActividades");

    if (!lista.length) {
        cont.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1"></i>
                <p>No hay recursos asignados.</p>
            </div>`;
        return;
    }

    cont.innerHTML = lista
        .map(r => {
            const estado = (r.estado ?? "pendiente").toString().toLowerCase();
            const puedeEditar = estado !== "aprobado";
            const puedeAprobarRechazar = estado === "pendiente";
            const esVencido = estado === 'vencido';
            const titulo = r.titulo ?? "";
            const fecha = r.fecha_limite ?? "—";
            const lider = r.lider_nombre ?? "N/A";
            const descripcion = r.descripcion ?? "";
            const archivo = r.archivo ?? null;
            const archivoRespuesta = r.archivo_respuesta ?? null;
            const enlaceRespuesta = r.enlace_respuesta ?? null;
            const comentarios = r.comentarios ?? "";
            const respondidoEn = r.respondido_en ?? null;
            const tipoDocumento = r.tipo_documento ?? "";
            const badgeText = estado.toUpperCase();

            const computeViewUrl = () => {
                if (enlaceRespuesta) return enlaceRespuesta;
                if (archivoRespuesta) return `/storage/${archivoRespuesta}`;

                const txt = (comentarios || '').toString();
                if (txt) {
                    let m = txt.match(/Enlace respuesta:\s*(https?:\/\/\S+)/i);
                    if (m && m[1]) return m[1];
                    m = txt.match(/Archivo respuesta:\s*([^\s]+)/i);
                    if (m && m[1]) return `/storage/${m[1].trim()}`;
                }

                // Fallback: en algunos esquemas el archivo de respuesta termina en la columna `archivo`
                // (p.ej. cuando no existe `archivo_respuesta`).
                if (archivo) {
                    return `/storage/${archivo}`;
                }
                return null;
            };

            const viewUrl = computeViewUrl();

            const btnArchivo = viewUrl
                ? `<a class="btn btn-recurso-file" href="${viewUrl}" target="_blank" rel="noopener">
                        <i class="bi bi-eye"></i>
                        Ver
                   </a>`
                : `<button type="button" class="btn btn-recurso-file" disabled>
                        <i class="bi bi-file-earmark"></i>
                        Sin Evidencia
                   </button>`;

            const accion = esVencido
                ? `<button type="button" class="recurso-action btn-eliminar-recurso"
                                data-id="${r.id}"
                                aria-label="Eliminar recurso">
                            <i class="bi bi-trash"></i>
                        </button>`
                : `<button type="button" class="recurso-action btn-editar-recurso"
                                data-id="${r.id}"
                                data-estado="${estado}"
                                data-lider="${encodeURIComponent(lider)}"
                                data-titulo="${encodeURIComponent(titulo)}"
                                data-fecha="${encodeURIComponent(fecha)}"
                                data-descripcion="${encodeURIComponent(descripcion)}"
                                data-tipo-documento="${encodeURIComponent(tipoDocumento)}"
                                data-archivo="${encodeURIComponent(archivo ?? '')}"
                                ${!puedeEditar ? "disabled" : ""}
                                aria-label="Editar recurso">
                            <i class="bi bi-pencil-square"></i>
                        </button>`;

            return `
                <div class="recurso-item">

                    <div class="recurso-top">
                        <div class="recurso-lider">${lider}</div>

                        <div class="recurso-top-right">
                            <span class="badge badge-${estado} recurso-badge">${badgeText}</span>
                            ${accion}
                        </div>
                    </div>

                    <div class="recurso-sub">${titulo} &middot; ${fecha}</div>
                    <div class="recurso-desc">${descripcion}</div>

                    <div class="recurso-actions">
                        ${btnArchivo}

                        <button class="btn btn-recurso-approve btn-aprobar" data-id="${r.id}" ${!puedeAprobarRechazar ? "disabled" : ""}>
                            Aprobar
                        </button>

                        <button class="btn btn-recurso-reject btn-rechazar" data-id="${r.id}" ${!puedeAprobarRechazar ? "disabled" : ""}>
                            Rechazar
                        </button>
                    </div>

                    <div id="rechazo-${r.id}" class="recurso-rechazo d-none">
                        <textarea class="form-control" placeholder="Escribe el motivo..."></textarea>
                        <button class="btn btn-danger btn-sm mt-2 btn-confirmar" data-id="${r.id}">
                            Confirmar rechazo
                        </button>
                    </div>

                </div>
            `;
        })
        .join("");

    activarBotonesEstados();
}


/* ======================================================
   APROBAR / RECHAZAR
====================================================== */

function activarBotonesEstados() {

    document.querySelectorAll(".btn-aprobar").forEach(btn => {
        btn.addEventListener("click", () => {
            actualizarEstado(btn.dataset.id, "aprobado", "");
        });
    });

    document.querySelectorAll(".btn-rechazar").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById(`rechazo-${btn.dataset.id}`).classList.remove("d-none");
        });
    });

    document.querySelectorAll(".btn-confirmar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const comentario = document.querySelector(`#rechazo-${id} textarea`).value;

            if (!comentario.trim()) {
                showNotification("Error", "Debes escribir un comentario", "error");
                return;
            }

            actualizarEstado(id, "rechazado", comentario);
        });
    });

    document.querySelectorAll(".btn-editar-recurso").forEach(btn => {
        btn.addEventListener("click", () => {
            abrirModalEditar(btn);
        });
    });

    document.querySelectorAll(".btn-eliminar-recurso").forEach(btn => {
        btn.addEventListener("click", () => {
            eliminarRecursoVencido(btn.dataset.id);
        });
    });
}

function eliminarRecursoVencido(id) {
    if (!id) return;
    if (!window.ACT_DELETE_RECURSO_URL) {
        showNotification('Error', 'No se encontró la ruta para eliminar.', 'error');
        return;
    }

    const runDelete = () => {
        const url = buildUrl(window.ACT_DELETE_RECURSO_URL, id);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(async (r) => {
                const data = await r.json().catch(() => ({}));
                return { ok: r.ok, data };
            })
            .then(({ ok, data }) => {
                if (!ok) {
                    showNotification('Error', data.message ?? 'No se pudo eliminar el recurso', 'error');
                    return;
                }
                showNotification('Eliminado', 'Recurso eliminado correctamente', 'success');
                if (semilleroActualId) {
                    cargarRecursos(semilleroActualId);
                }
            })
            .catch(() => {
                showNotification('Error', 'No se pudo eliminar el recurso', 'error');
            });
    };

    if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire({
            title: 'Eliminar recurso vencido',
            html: '<div class="cide-swal-text">¿Deseas eliminar este recurso vencido?</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'cide-swal-popup',
                title: 'cide-swal-title',
                htmlContainer: 'cide-swal-html',
                confirmButton: 'cide-swal-confirm',
                cancelButton: 'cide-swal-cancel',
            }
        }).then((result) => {
            if (result && result.isConfirmed) {
                runDelete();
            }
        });
        return;
    }

    const ok = confirm('¿Deseas eliminar este recurso vencido?');
    if (!ok) return;
    runDelete();
}


function initFormularioEditar() {
    const form = document.getElementById("formEditarRecurso");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const id = document.getElementById("edit_recurso_id").value;
        const tipoDocumento = document.getElementById("edit_tipo_documento").value;
        const fechaLimite = document.getElementById("edit_fecha_limite").value;
        const descripcion = document.getElementById("edit_descripcion").value;

        const url = buildUrl(window.ACT_UPDATE_RECURSO_URL, id);

        fetch(url, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
            body: JSON.stringify({
                tipo_documento: tipoDocumento,
                fecha_limite: fechaLimite,
                descripcion,
            })
        })
            .then(async (r) => {
                const data = await r.json().catch(() => ({}));
                return { ok: r.ok, data };
            })
            .then(({ ok, data }) => {
                if (!ok) {
                    showNotification("Error", data.message ?? "No se pudo actualizar el recurso", "error");
                    return;
                }

                showNotification("Actualizado", "Recurso actualizado correctamente", "success");
                modalEditar?.hide();
                if (semilleroActualId) {
                    cargarRecursos(semilleroActualId);
                }
            })
            .catch(() => {
                showNotification("Error", "No se pudo actualizar el recurso", "error");
            });
    });
}


function abrirModalEditar(btn) {
    if (!modalEditar) return;

    const estado = (btn.dataset.estado ?? "").toLowerCase();
    if (estado === "aprobado") {
        showNotification("No permitido", "No puedes editar un recurso aprobado", "error");
        return;
    }

    const decode = (v) => {
        try { return decodeURIComponent(v ?? ""); } catch { return v ?? ""; }
    };

    const id = btn.dataset.id;
    const lider = decode(btn.dataset.lider);
    const titulo = decode(btn.dataset.titulo);
    const fecha = decode(btn.dataset.fecha);
    const descripcion = decode(btn.dataset.descripcion);
    const tipoDocumento = decode(btn.dataset.tipoDocumento);
    const archivo = decode(btn.dataset.archivo);

    document.getElementById("edit_recurso_id").value = id;
    document.getElementById("edit_lider").value = lider;
    document.getElementById("edit_estado").value = estado.toUpperCase();
    document.getElementById("edit_titulo").value = titulo;
    document.getElementById("edit_tipo_documento").value = tipoDocumento;
    document.getElementById("edit_fecha_limite").value = fecha === "—" ? "" : fecha;
    document.getElementById("edit_descripcion").value = descripcion;

    modalEditar.show();
}


function actualizarEstado(id, estado, comentario) {

    fetch(`/admin/recursos/semillero/${id}/estado`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": CSRF_TOKEN
        },
        body: JSON.stringify({ estado, comentarios: comentario })
    })
        .then(r => r.json())
        .then(() => {
            showNotification("Actualizado", "El estado se modificó correctamente", "success");
            if (semilleroActualId) {
                cargarRecursos(semilleroActualId);
            }
        });
}


/* ======================================================
   FORMULARIO CREAR RECURSO
====================================================== */

function initFormularioCrear() {

    const form = document.getElementById("formSubirRecurso");
    const selSem = document.getElementById("semillero_id");

    selSem.addEventListener("change", () => {
        cargarLider(selSem.value);
        cargarProyectos(selSem.value);
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const data = new FormData(form);
        const sem = document.getElementById("semillero_id_fijo").value || selSem.value;

        data.set("semillero_id", sem);

        fetch(window.ACT_STORE_URL, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
            body: data
        })
            .then(r => r.json())
            .then(() => {
                showNotification("Correcto", "Recurso creado correctamente", "success");
                modalCrear.hide();
                setTimeout(() => location.reload(), 900);
            });
    });
}


/* ======================================================
   AJAX CARGAR LÍDER / PROYECTOS
====================================================== */

function cargarLider(id) {

    const url = buildUrl(window.ACT_SEMILLERO_LIDER_URL, id);
    const nombre = document.getElementById("lider_nombre");
    const idInput = document.getElementById("lider_id");

    nombre.value = "Cargando...";

    fetch(url)
        .then(r => r.json())
        .then(d => {
            nombre.value = d.lider?.nombre_completo ?? "Sin líder";
            idInput.value = d.lider?.id ?? "";
        });
}

function cargarProyectos(id) {

    const url = buildUrl(window.URL_PROYECTOS_SEMILLERO, id);
    const sel = document.getElementById("proyecto_id");

    sel.disabled = true;
    sel.innerHTML = "<option>Cargando...</option>";

    fetch(url)
        .then(r => r.json())
        .then(lista => {
            sel.innerHTML = `
                <option value="">Seleccione…</option>
                ${lista.map(p => `<option value="${p.id_proyecto}">${p.nombre_proyecto}</option>`).join("")}
            `;
            sel.disabled = false;
        });
}
