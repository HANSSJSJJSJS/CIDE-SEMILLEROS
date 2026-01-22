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

    const modalCrearEl = document.getElementById("modalSubirRecurso");
    const modalVerEl   = document.getElementById("modalActividades");
    const modalEditarEl = document.getElementById("modalEditarRecurso");

    if (!modalCrearEl) {
        console.error("❌ No existe #modalSubirRecurso en el DOM");
        return;
    }

    if (typeof bootstrap === "undefined") {
        console.error("❌ Bootstrap JS NO está cargado");
        return;
    }

    modalCrear = new bootstrap.Modal(modalCrearEl);

    if (modalVerEl) {
        modalVer = new bootstrap.Modal(modalVerEl);
    }

    if (modalEditarEl) {
        modalEditar = new bootstrap.Modal(modalEditarEl);
    }

    initBotonesCrear();
    initBotonesVerRecursos();
    initFormularioCrear();

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

    if (!id) {
        console.error("❌ semilleroId vacío, no se puede cargar recursos");
        return;
    }

    const url = buildUrl(window.ACT_ACTIVIDADES_POR_SEMILLERO_URL, id);
    console.log("URL FINAL:", url);

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.json();
    })
    .then(data => {
        renderRecursos(Array.isArray(data) ? data : []);
    })
    .catch(err => {
        console.error("Error cargando recursos:", err);
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

    cont.innerHTML = lista.map(r => {
        const estado = (r.estado ?? "pendiente").toLowerCase();
        const puedeEditar = estado !== "aprobado";
        const puedeAprobarRechazar = estado === "pendiente";
        const esVencido = estado === "vencido";
        const titulo = r.titulo ?? "";
        const fecha = r.fecha_limite ?? "—";
        const lider = r.lider_nombre ?? "N/A";
        const descripcion = r.descripcion ?? "";
        const archivo = r.archivo ?? null;
        const archivoRespuesta = r.archivo_respuesta ?? null; // 👈 ESTA LÍNEA FALTABA
        const enlaceRespuesta = r.enlace_respuesta ?? null;
        const tipo = (r.tipo_recurso || "").toUpperCase();
        const badgeText = estado.toUpperCase();
        const fechaAsignacion = r.fecha_asignacion
            ? new Date(r.fecha_asignacion).toLocaleDateString("es-CO")
            : "—";

        // =========================
        // BOTÓN ARCHIVO / ENLACE
        // =========================
               let btnArchivo = `
    <button type="button" class="btn btn-recurso-file" disabled>
        <i class="bi bi-file-earmark"></i>
        Sin evidencia
    </button>
`;

// 1️⃣ RESPUESTA DEL LÍDER
if (archivoRespuesta && archivoRespuesta !== 'sin_archivo') {
    const fileUrl = `/storage/${archivoRespuesta}`;

    if (["PDF", "IMAGEN", "VIDEO"].includes(tipo)) {
        btnArchivo = `
            <a class="btn btn-recurso-file"
               href="${fileUrl}"
               target="_blank"
               rel="noopener">
               <i class="bi bi-eye"></i>
               Ver respuesta
               <span class="badge bg-info ms-1">Líder</span>
            </a>
        `;
    } else {
        btnArchivo = `
            <a class="btn btn-recurso-file"
               href="${fileUrl}"
               download>
               <i class="bi bi-download"></i>
               Descargar respuesta
               <span class="badge bg-info ms-1">Líder</span>
            </a>
        `;
    }
}

// 2️⃣ ENLACE
else if (tipo === "ENLACE" && enlaceRespuesta) {
    btnArchivo = `
        <a class="btn btn-recurso-file"
           href="${enlaceRespuesta}"
           target="_blank"
           rel="noopener">
           <i class="bi bi-box-arrow-up-right"></i>
           Abrir enlace
        </a>
    `;
}

// 3️⃣ ARCHIVO DEL ADMIN
else if (archivo && archivo !== 'sin_archivo') {
    const fileUrl = `/storage/${archivo}`;

    if (["PDF", "IMAGEN", "VIDEO"].includes(tipo)) {
        btnArchivo = `
            <a class="btn btn-recurso-file"
               href="${fileUrl}"
               target="_blank"
               rel="noopener">
               <i class="bi bi-eye"></i>
               Ver recurso
            </a>
        `;
    } else {
        btnArchivo = `
            <a class="btn btn-recurso-file"
               href="${fileUrl}"
               download>
               <i class="bi bi-download"></i>
               Descargar recurso
            </a>
        `;
    }
}


        // =========================
        // ACCIÓN EDITAR / ELIMINAR
        // =========================
        const accion = esVencido
            ? `<button type="button"
                        class="recurso-action btn-eliminar-recurso"
                        data-id="${r.id}">
                    <i class="bi bi-trash"></i>
               </button>`
            : `<button type="button"
                        class="recurso-action btn-editar-recurso"
                        data-id="${r.id}"
                        data-estado="${estado}"
                        data-lider="${encodeURIComponent(lider)}"
                        data-titulo="${encodeURIComponent(titulo)}"
                        data-fecha="${encodeURIComponent(fecha)}"
                        data-descripcion="${encodeURIComponent(descripcion)}"
                        ${!puedeEditar ? "disabled" : ""}>
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

                <div class="recurso-sub">
                    ${titulo} &middot; vence: ${fecha}<br>
                    <small class="text-muted">Asignado: ${fechaAsignacion}</small>
                </div>

                <div class="recurso-desc">${descripcion}</div>

                <div class="recurso-actions">
                    ${btnArchivo}

                    <button class="btn btn-recurso-approve btn-aprobar"
                            data-id="${r.id}"
                            ${!puedeAprobarRechazar ? "disabled" : ""}>
                        Aprobar
                    </button>

                    <button class="btn btn-recurso-reject btn-rechazar"
                            data-id="${r.id}"
                            ${!puedeAprobarRechazar ? "disabled" : ""}>
                        Rechazar
                    </button>
                </div>

                <div id="rechazo-${r.id}" class="recurso-rechazo d-none">
                    <textarea class="form-control" placeholder="Escribe el motivo..."></textarea>
                    <button class="btn btn-danger btn-sm mt-2 btn-confirmar"
                            data-id="${r.id}">
                        Confirmar rechazo
                    </button>
                </div>

            </div>
        `;
    }).join("");

    activarBotonesEstados();
}




function activarBotonesEstados() {

    // =========================
    // APROBAR
    // =========================
    document.querySelectorAll(".btn-aprobar").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;

            // 🔒 VALIDAR EVIDENCIA
            if (!tieneEvidenciaSubida(id)) {
                showNotification(
                    "Evidencia requerida",
                    "No puedes aprobar un recurso sin evidencia del líder.",
                    "error"
                );
                return;
            }

            // ❓ CONFIRMACIÓN
            const result = await Swal.fire({
                icon: 'question',
                title: '¿Aprobar recurso?',
                text: '¿Estás seguro de aprobar este recurso?',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;

            actualizarEstado(id, "aprobado", "");
        });
    });

    // =========================
    // MOSTRAR RECHAZO
    // =========================
    document.querySelectorAll(".btn-rechazar").forEach(btn => {
        btn.addEventListener("click", () => {
            document
                .getElementById(`rechazo-${btn.dataset.id}`)
                .classList.remove("d-none");
        });
    });

    // =========================
    // CONFIRMAR RECHAZO
    // =========================
    document.querySelectorAll(".btn-confirmar").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            const comentario = document
                .querySelector(`#rechazo-${id} textarea`)
                .value;

            if (!comentario.trim()) {
                showNotification("Error", "Debes escribir un comentario", "error");
                return;
            }

            // 🔒 VALIDAR EVIDENCIA
            if (!tieneEvidenciaSubida(id)) {
                showNotification(
                    "Evidencia requerida",
                    "No puedes rechazar un recurso sin evidencia del líder.",
                    "error"
                );
                return;
            }

            // ❓ CONFIRMACIÓN
            const result = await Swal.fire({
                icon: 'warning',
                title: '¿Rechazar recurso?',
                text: '¿Estás seguro de rechazar este recurso?',
                showCancelButton: true,
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33'
            });

            if (!result.isConfirmed) return;

            actualizarEstado(id, "rechazado", comentario);
        });
    });

    // =========================
    // EDITAR / ELIMINAR (SIN CAMBIOS)
    // =========================
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


/* ======================================================
   FORMULARIO CREAR RECURSO
====================================================== */

function initFormularioCrear() {

    const form = document.getElementById("formSubirRecurso");
    const selSem = document.getElementById("semillero_id");

    if (!form || !selSem) return;

    selSem.addEventListener("change", () => {
        cargarLider(selSem.value);
        cargarProyectos(selSem.value);
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const data = new FormData(form);
        const sem = document.getElementById("semillero_id_fijo")?.value || selSem.value;

        data.set("semillero_id", sem);

        fetch(window.ACT_STORE_URL, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": CSRF_TOKEN,
                "Accept": "application/json"
            },
            body: data
        })
        .then(async r => {
            if (!r.ok) {
                const text = await r.text();
                throw new Error(text);
            }
            return r.json();
        })
        .then(() => {
            showNotification("Correcto", "Recurso creado correctamente", "success");
            modalCrear.hide();
            setTimeout(() => location.reload(), 900);
        })
        .catch(err => {
            console.error("Error al guardar:", err);
            showNotification(
                "Error",
                "No se pudo guardar el recurso. Revisa los datos.",
                "error"
            );
        });
    }); // ✅ ESTE ERA EL QUE FALTABA
}



/* ======================================================
   AJAX CARGAR LÍDER / PROYECTOS
====================================================== */

function cargarProyectos(id) {

    if (!id) return;

    const url = buildUrl(window.URL_PROYECTOS_SEMILLERO, id);
    const sel = document.getElementById("proyecto_id");

    if (!sel) return;

    sel.disabled = true;
    sel.innerHTML = "<option>Cargando...</option>";

    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => {
            if (!r.ok) throw new Error('Error cargando proyectos');
            return r.json();
        })
        .then(lista => {
            sel.innerHTML = `
                <option value="">Seleccione…</option>
                ${lista.map(p =>
                    `<option value="${p.id_proyecto}">${p.nombre_proyecto}</option>`
                ).join("")}
            `;
            sel.disabled = false;
        })
        .catch(() => {
            sel.innerHTML = "<option>Error al cargar</option>";
        });
}


function cargarLider(id) {

    if (!id) return;

    const url = buildUrl(window.ACT_SEMILLERO_LIDER_URL, id);
    const nombre = document.getElementById("lider_nombre");
    const idInput = document.getElementById("lider_id");

    if (!nombre || !idInput) return;

    nombre.value = "Cargando...";
    idInput.value = "";

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => {
        if (!r.ok) {
            throw new Error('Error cargando líder');
        }
        return r.json();
    })
    .then(d => {
        if (d.lider) {
            nombre.value = d.lider.nombre_completo;
            idInput.value = d.lider.id;
        } else {
            nombre.value = "Sin líder";
            idInput.value = "";
        }
    })
    .catch(() => {
        nombre.value = "Error al cargar";
        idInput.value = "";
    });
}
/* ======================================================
   VALIDAR EVIDENCIA DEL LÍDER
====================================================== */

function tieneEvidenciaSubida(recursoId) {

    const card = document
        .querySelector(`.btn-aprobar[data-id="${recursoId}"]`)
        ?.closest('.recurso-item');

    if (!card) return false;

    const btnArchivo = card.querySelector('.btn-recurso-file');
    if (!btnArchivo) return false;

    const texto = btnArchivo.textContent.toLowerCase();

    // ✔ solo cuenta si es respuesta del líder
    return texto.includes('respuesta');
}
/* ======================================================
   ACTUALIZAR ESTADO DEL RECURSO (APROBAR / RECHAZAR)
====================================================== */
function actualizarEstado(recursoId, estado, comentario = "") {

    fetch(`/admin/recursos/semillero/${recursoId}/estado`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": CSRF_TOKEN,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            estado: estado,
            comentarios: comentario
        })
    })
    .then(async r => {
        if (!r.ok) {
            const data = await r.json().catch(() => ({}));
            throw new Error(data.message || "Error al actualizar estado");
        }
        return r.json();
    })
    .then(() => {
        showNotification(
            "Actualizado",
            `El recurso fue ${estado} correctamente`,
            "success"
        );

        // 🔄 refrescar recursos sin recargar página
        if (semilleroActualId) {
            cargarRecursos(semilleroActualId);
        } else {
            // fallback de seguridad
            setTimeout(() => location.reload(), 800);
        }
    })
    .catch(err => {
        console.error(err);
        showNotification(
            "Error",
            "No se pudo actualizar el estado del recurso",
            "error"
        );
    });
}
