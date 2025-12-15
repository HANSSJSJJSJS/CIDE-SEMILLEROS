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

document.addEventListener("DOMContentLoaded", () => {

    modalCrear = new bootstrap.Modal(document.getElementById("modalSubirRecurso"));
    modalVer = new bootstrap.Modal(document.getElementById("modalActividades"));

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
        .map(r => `
            <div class="recurso-item p-3 mb-3 rounded shadow-sm bg-white">

                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold text-primary">${r.titulo}</h5>
                    <span class="badge badge-${r.estado}">${r.estado.toUpperCase()}</span>
                </div>

                <div class="small text-muted mb-2">
                    <strong>Líder:</strong> ${r.lider_nombre ?? "N/A"} <br>
                    <strong>Fecha límite:</strong> ${r.fecha_limite}
                </div>

                <p>${r.descripcion}</p>

                ${r.archivo ? `
                    <a href="/storage/${r.archivo}" download class="btn btn-dark rounded-pill mb-3">
                        <i class="bi bi-download"></i> Descargar archivo
                    </a>` 
                : `<span class="text-muted fst-italic">Sin archivo</span>`}

                <div class="d-flex gap-2 mt-2">
                    <button class="btn btn-success rounded-pill flex-fill btn-aprobar" data-id="${r.id}"
                        ${r.estado !== "pendiente" ? "disabled" : ""}>
                        Aprobar
                    </button>

                    <button class="btn btn-danger rounded-pill flex-fill btn-rechazar"
                        data-id="${r.id}"
                        ${r.estado !== "pendiente" ? "disabled" : ""}>
                        Rechazar
                    </button>
                </div>

                <div id="rechazo-${r.id}" class="mt-3 d-none">
                    <textarea class="form-control" placeholder="Escribe el motivo..."></textarea>
                    <button class="btn btn-danger btn-sm mt-2 btn-confirmar" data-id="${r.id}">
                        Confirmar rechazo
                    </button>
                </div>

            </div>
        `)
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
            setTimeout(() => location.reload(), 800);
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
