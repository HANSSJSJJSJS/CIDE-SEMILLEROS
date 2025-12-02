// ============================================================
// Helpers globales de notificación
// ============================================================
window.swalSuccess = function (msg) {
    Swal.fire({
        icon: "success",
        title: "Operación exitosa",
        html: msg,
        confirmButtonText: "Aceptar",
        customClass: {
            confirmButton: "custom-confirm swal2-confirm"
        }
    });
};

window.swalError = function (msg) {
    Swal.fire({
        icon: "error",
        title: "Ha ocurrido un error",
        html: msg,
        confirmButtonText: "Aceptar",
        customClass: {
            confirmButton: "custom-confirm swal2-confirm"
        }
    });
};

document.addEventListener("DOMContentLoaded", () => {

    // ========================================================
    // EDITAR SEMILLERO (modal + buscador de líder)
    // ========================================================
    const modalEdit      = document.getElementById("modalEditarSemillero");
    const formEdit       = document.getElementById("formEditarSemillero");
    const editNombre     = document.getElementById("editNombre");
    const editLinea      = document.getElementById("editLinea");
    const liderRO        = document.getElementById("liderNombreRO");
    const correoRO       = document.getElementById("liderCorreoRO");
    const idLiderEditInp = document.getElementById("editIdLider");
    const buscarEditInp  = document.getElementById("buscarLider");
    const listaEdit      = document.getElementById("resultadosLider");

    if (modalEdit && formEdit && buscarEditInp && listaEdit) {

        // Quitar autocompletado / sugerencias en los campos de texto
        [editNombre, editLinea].forEach(inp => {
            if (!inp) return;
            inp.setAttribute("autocomplete", "off");
            inp.setAttribute("spellcheck", "false");
        });

        modalEdit.addEventListener("show.bs.modal", async (e) => {
            // Cerrar modal "nuevo semillero" si estuviera abierto
            const mNuevo    = document.getElementById("modalNuevoSemillero");
            const instNuevo = mNuevo ? bootstrap.Modal.getInstance(mNuevo) : null;
            instNuevo?.hide();

            const id = e.relatedTarget?.dataset.id;
            if (!id) return;

            // Limpiar buscador y resultados de líder
            buscarEditInp.value = "";
            listaEdit.innerHTML = "";

            try {
                const res = await fetch(`/admin/semilleros/${id}/edit-ajax`);
                let json  = null;

                try {
                    json = await res.json();
                } catch (_) {
                    // si no viene JSON, lo manejamos abajo
                }

                if (!res.ok) {
                    const detalle = json && json.message
                        ? json.message
                        : `Error HTTP ${res.status}`;
                    swalError(
                        `No se pudo cargar la información del semillero.<br><small>${detalle}</small>`
                    );
                    return;
                }

                // El backend devuelve directamente el objeto semillero
                const data = json || {};

                formEdit.action      = `/admin/semilleros/${id}`;
                editNombre.value     = data.nombre || "";
                editLinea.value      = data.linea_investigacion || "";
                liderRO.value        = data.lider_nombre || "—";
                correoRO.value       = data.lider_correo || "—";
                idLiderEditInp.value = data.id_lider_semi || "";

            } catch (err) {
                console.error(err);
                swalError("No se pudo cargar la información del semillero.");
            }
        });

        // Buscador de líderes dentro del modal de editar
        let tEdit;
        buscarEditInp.addEventListener("input", () => {
            clearTimeout(tEdit);
            tEdit = setTimeout(async () => {
                const q = buscarEditInp.value.trim();
                if (q.length < 2) {
                    listaEdit.innerHTML = "";
                    return;
                }

                const includeCurrent = idLiderEditInp.value
                    ? `&include_current=${encodeURIComponent(idLiderEditInp.value)}`
                    : "";

                const url = `/admin/semilleros/lideres-disponibles?q=${encodeURIComponent(q)}${includeCurrent}`;

                try {
                    const res  = await fetch(url, { headers: { "Accept": "application/json" } });
                    const data = await res.json();

                    if (!data.ok) {
                        console.error("Error en lideres-disponibles:", data.message);
                        listaEdit.innerHTML = "";
                        return;
                    }

                    const items = data.items || [];
                    listaEdit.innerHTML = "";
                    if (!items.length) return;

                    items.forEach((r) => {
                        const a = document.createElement("a");
                        a.href = "#";
                        a.className = "list-group-item list-group-item-action";
                        a.innerHTML = `
                            <div class="fw-semibold">${r.nombre}</div>
                            <small class="text-muted">${r.correo || "—"}</small>
                        `;
                        a.addEventListener("click", (ev) => {
                            ev.preventDefault();
                            idLiderEditInp.value = r.id_lider_semi;
                            liderRO.value        = r.nombre;
                            correoRO.value       = r.correo || "—";
                            listaEdit.innerHTML  = "";
                            buscarEditInp.value  = "";
                        });
                        listaEdit.appendChild(a);
                    });

                } catch (err) {
                    console.error("Error cargando líderes:", err);
                }
            }, 250);
        });
    }

    // ========================================================
    // NUEVO SEMILLERO – buscador de líder
    // ========================================================
    const modalNuevo       = document.getElementById("modalNuevoSemillero");
    const buscarNuevo      = document.getElementById("buscarLiderNuevo");
    const btnBuscarNuevo   = document.getElementById("btnBuscarLiderNuevo");
    const listaNuevo       = document.getElementById("resultadosLiderNuevo");
    const idLiderNuevoInp  = document.getElementById("idLiderNuevo");
    const liderNuevoNombre = document.getElementById("nuevoLiderNombreRO");
    const liderNuevoCorreo = document.getElementById("nuevoLiderCorreoRO");

    async function fetchLideresNuevo(q) {
        if (!q || q.trim().length < 2) {
            listaNuevo.innerHTML = "";
            return;
        }

        const url = `/admin/semilleros/lideres-disponibles?q=${encodeURIComponent(q)}`;

        try {
            const res  = await fetch(url, { headers: { "Accept": "application/json" } });
            const data = await res.json();

            if (!data.ok) {
                console.error("Error en lideres-disponibles:", data.message);
                listaNuevo.innerHTML = "";
                return;
            }

            const items = data.items || [];
            listaNuevo.innerHTML = "";
            if (!items.length) return;

            items.forEach((r) => {
                const a = document.createElement("a");
                a.href = "#";
                a.className = "list-group-item list-group-item-action";
                a.innerHTML = `
                    <div class="fw-semibold">${r.nombre}</div>
                    <small class="text-muted">${r.correo || "—"}</small>
                `;
                a.addEventListener("click", (ev) => {
                    ev.preventDefault();
                    idLiderNuevoInp.value  = r.id_lider_semi;
                    liderNuevoNombre.value = r.nombre;
                    liderNuevoCorreo.value = r.correo || "—";
                    listaNuevo.innerHTML   = "";
                    buscarNuevo.value      = "";
                });
                listaNuevo.appendChild(a);
            });

        } catch (err) {
            console.error("Error cargando líderes (nuevo):", err);
            swalError("No se pudieron cargar los líderes disponibles.");
        }
    }

    let tNuevo;
    if (buscarNuevo && listaNuevo) {
        buscarNuevo.addEventListener("input", () => {
            clearTimeout(tNuevo);
            tNuevo = setTimeout(() => fetchLideresNuevo(buscarNuevo.value), 250);
        });
    }

    if (btnBuscarNuevo) {
        btnBuscarNuevo.addEventListener("click", (e) => {
            e.preventDefault();
            fetchLideresNuevo(buscarNuevo.value);
        });
    }

    if (modalNuevo) {
        modalNuevo.addEventListener("hidden.bs.modal", () => {
            buscarNuevo.value        = "";
            listaNuevo.innerHTML     = "";
            idLiderNuevoInp.value    = "";
            liderNuevoNombre.value   = "—";
            liderNuevoCorreo.value   = "—";
        });
    }

    // ========================================================
    // CONFIRMAR ELIMINAR SEMILLERO
    // ========================================================
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-eliminar-semillero");
        if (!btn) return;

        e.preventDefault();

        const url    = btn.dataset.url;
        const nombre = btn.dataset.nombre || "este semillero";
        const form   = btn.closest("form");

        Swal.fire({
            icon: "warning",
            title: "¿Eliminar semillero?",
            html: `¿Seguro que deseas eliminar el semillero <strong>"${nombre}"</strong>?<br><small>Esta acción no se puede deshacer.</small>`,
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "custom-danger swal2-confirm",
                cancelButton: "custom-cancel swal2-cancel"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) {
                    form.submit();
                }
            }
        });
    });
});
