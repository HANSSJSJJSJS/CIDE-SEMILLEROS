/* ======================================================
                CONFIGURACIÓN GLOBAL
====================================================== */

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || null;

/* Reemplaza el :id en rutas tipo Laravel */
function buildUrl(base, id) {
    return base.replace(':id', id);
}

/* ======================================================
                NOTIFICACIONES
====================================================== */

function showNotification(title, message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `notification ${type}`;

    el.innerHTML = `
        <div class="notification-icon">
            <i class="bi bi-${type === 'error' ? 'x' : 'check'}-circle-fill"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close"><i class="bi bi-x"></i></button>
    `;

    container.appendChild(el);
    el.querySelector('.notification-close').addEventListener('click', () => el.remove());
    setTimeout(() => el.remove(), 5000);
}

/* ======================================================
                EJECUCIÓN PRINCIPAL
====================================================== */

document.addEventListener("DOMContentLoaded", () => {

    /* ======================================================
                    MODAL VER RECURSOS
    ======================================================= */

    const modalActividades = document.getElementById("modalActividades");
    const contenedorActividades = document.getElementById("contenedorActividades");
    const tituloModal = document.getElementById("tituloModalActividades");

    document.querySelectorAll(".btn-ver-actividades").forEach(btn => {
        btn.addEventListener("click", () => {
            abrirModalActividades(btn.dataset.semilleroId, btn.dataset.semilleroNombre);
        });
    });

    function abrirModalActividades(id, nombre) {
        tituloModal.textContent = `Recursos - ${nombre}`;
        modalActividades.classList.add("active");
        cargarActividades(id);
    }

    document.querySelectorAll('[data-close-modal="actividades"]').forEach(btn => {
        btn.addEventListener("click", () => modalActividades.classList.remove("active"));
    });

    modalActividades.addEventListener("click", e => {
        if (e.target === modalActividades) modalActividades.classList.remove("active");
    });


    /* ======================================================
                    CARGAR RECURSOS POR SEMILLERO
    ======================================================= */

    function cargarActividades(id) {
        const url = buildUrl(window.ACT_ACTIVIDADES_POR_SEMILLERO_URL, id);

        contenedorActividades.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-arrow-repeat spin fs-3 text-muted"></i>
            </div>
        `;

        fetch(url)
            .then(r => r.json())
            .then(data => renderActividades(data.actividades ?? []))
            .catch(() => {
                contenedorActividades.innerHTML = `
                    <div class="actividades-vacio">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>Error al cargar recursos</p>
                    </div>`;
            });
    }

    /* ======================================================
                        TARJETAS DE RECURSOS
    ======================================================= */

    function renderActividades(lista) {

        if (!lista.length) {
            contenedorActividades.innerHTML = `
                <div class="actividades-vacio">
                    <i class="bi bi-inbox"></i>
                    <p>No hay recursos asignados</p>
                </div>`;
            return;
        }

        contenedorActividades.innerHTML = lista.map(r => `
            <div class="recurso-card" data-id="${r.id}">

                <div class="recurso-header">
                    <h3 class="recurso-titulo">${r.titulo}</h3>
                    <span class="badge-${r.estado}">${r.estado.toUpperCase()}</span>
                </div>

                <div class="recurso-info">
                    <strong>Líder:</strong> ${r.lider_nombre || "N/A"}<br>
                    <strong>Fecha límite:</strong> ${r.fecha_limite}
                </div>

                <div class="recurso-descripcion">${r.descripcion}</div>

                <div class="recurso-archivo">
                    ${
                        r.archivo && r.archivo !== "sin_archivo"
                        ? `<a href="/storage/${r.archivo}" target="_blank" class="btn-ver-documento">
                             <i class="bi bi-eye"></i> Ver documento
                           </a>`
                        : `<span class="texto-sin-archivo">Sin archivo cargado</span>`
                    }
                </div>

                <div class="recurso-acciones">
                    <button class="btn-aprobar btn-aprobar-item"
                        data-id="${r.id}"
                        ${r.estado !== "pendiente" ? "disabled":""}>
                        <i class="bi bi-check2-circle"></i> Aprobar
                    </button>

                    <button class="btn-rechazar btn-rechazar-item"
                        data-id="${r.id}"
                        ${r.estado !== "pendiente" ? "disabled":""}>
                        <i class="bi bi-x-circle"></i> Rechazar
                    </button>
                </div>

                <div class="rechazo-box d-none" id="rechazo-${r.id}">
                    <textarea class="comentario-rechazo form-control" placeholder="Motivo..."></textarea>
                    <button class="btn-confirmar-rechazo" data-id="${r.id}">Confirmar rechazo</button>
                </div>

            </div>
        `).join("");

        activarBotonesIndividuales();
    }

    /* ======================================================
                APROBAR / RECHAZAR
    ======================================================= */

    function activarBotonesIndividuales() {

        document.querySelectorAll(".btn-aprobar-item").forEach(btn => {
            btn.addEventListener("click", () => actualizarEstado(btn.dataset.id, "aprobado", ""));
        });

        document.querySelectorAll(".btn-rechazar-item").forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById(`rechazo-${btn.dataset.id}`).classList.remove("d-none");
            });
        });

        document.querySelectorAll(".btn-confirmar-rechazo").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const comentario = document.querySelector(`#rechazo-${id} .comentario-rechazo`).value;

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
            showNotification("Actualizado", "El estado fue actualizado", "success");
            setTimeout(() => location.reload(), 800);
        });
    }

    /* ======================================================
                    MODAL UNIVERSAL CREAR RECURSO
    ======================================================= */

    const modalR = document.getElementById("modalSubirRecurso");
    const formR = document.getElementById("formSubirRecurso");

    const selSem = document.getElementById("semillero_id");
    const fixSem = document.getElementById("semillero_id_fijo");
    const selProyecto = document.getElementById("proyecto_id");
    const liderNombre = document.getElementById("lider_nombre");
    const liderId = document.getElementById("lider_id");

    const btnCrear = document.getElementById("btnAbrirModalActividad");
    if (btnCrear) btnCrear.addEventListener("click", () => abrirModalUniversal(null));

    document.querySelectorAll(".btn-crear-actividad-card").forEach(btn => {
        btn.addEventListener("click", () => abrirModalUniversal(btn.dataset.semilleroId));
    });

    function abrirModalUniversal(semillero) {

        modalR.classList.add("active");
        formR.reset();

        if (semillero) {
            fixSem.value = semillero;
            selSem.value = semillero;
            selSem.disabled = true;

            cargarLider(semillero);
            cargarProyectos(semillero);

        } else {
            selSem.disabled = false;
            fixSem.value = "";
        }
    }

    document.getElementById("btnCancelarRecurso")
        ?.addEventListener("click", () => modalR.classList.remove("active"));

    modalR.addEventListener("click", e => {
        if (e.target === modalR) modalR.classList.remove("active");
    });

    selSem?.addEventListener("change", () => {
        const id = selSem.value;
        if (id) {
            cargarLider(id);
            cargarProyectos(id);
        }
    });

    function cargarLider(id) {
        const url = buildUrl(window.ACT_SEMILLERO_LIDER_URL, id);

        liderNombre.value = "Cargando…";

        fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.lider) {
                liderNombre.value = data.lider.nombre_completo;
                liderId.value = data.lider.id;
            } else {
                liderNombre.value = "Sin líder";
                liderId.value = "";
            }
        });
    }

    function cargarProyectos(id) {

        selProyecto.disabled = true;
        selProyecto.innerHTML = `<option>Cargando…</option>`;

        const url = buildUrl(window.URL_PROYECTOS_SEMILLERO, id);

        fetch(url)
        .then(r => r.json())
        .then(list => {
            selProyecto.innerHTML = `
                <option value="">Seleccione…</option>
                ${list.map(p => `<option value="${p.id_proyecto}">${p.nombre_proyecto}</option>`).join("")}
            `;
            selProyecto.disabled = false;
        });
    }

    formR.addEventListener("submit", e => {
        e.preventDefault();

        const sem = fixSem.value || selSem.value;

        if (!sem) {
            showNotification("Error", "Selecciona un semillero", "error");
            return;
        }

        if (!liderId.value) {
            showNotification("Error", "Este semillero no tiene líder", "error");
            return;
        }

        const data = new FormData(formR);
        data.set("semillero_id", sem);

        fetch(window.ACT_STORE_URL, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": CSRF_TOKEN },
            body: data
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showNotification("Correcto", "Recurso creado", "success");
                modalR.classList.remove("active");
                setTimeout(() => location.reload(), 1200);
            }
        });
    });

    /* ======================================================
                    MODAL SUBIR MULTIMEDIA
    ======================================================= */

    const modalMult = document.getElementById("modalSubirMultimedia");
    const btnMult = document.getElementById("btnAbrirModalMultimedia");

    if (btnMult) {
        btnMult.addEventListener("click", () => modalMult.classList.add("active"));
    }

    document.querySelectorAll('[data-close-modal="multimedia"]').forEach(btn => {
        btn.addEventListener("click", () => modalMult.classList.remove("active"));
    });

    modalMult?.addEventListener("click", e => {
        if (e.target === modalMult) modalMult.classList.remove("active");
    });

});
