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

    // =====================================================================
    // UTILIDAD COMÚN: buscar líderes disponibles (crear / editar)
    // =====================================================================
    async function fetchLideres(baseUrl, q, includeCurrentId = null) {
        if (!baseUrl) return { ok: false, items: [] };

        const params = new URLSearchParams();
        if (q && q.trim() !== "") params.append("q", q.trim());
        if (includeCurrentId) params.append("include_current", includeCurrentId);

        const url = `${baseUrl}?${params.toString()}`;

        const res  = await fetch(url, { headers: { "Accept": "application/json" } });
        const data = await res.json();
        if (!data.ok) {
            console.error("Error en lideres-disponibles:", data.message);
            return { ok: false, items: [] };
        }
        return data;
    }

    // =====================================================================
    // NUEVO SEMILLERO – Select + buscador de líder
    // =====================================================================
    const modalNuevo       = document.getElementById("modalNuevoSemillero");
    const selectLiderDisp  = document.getElementById("selectLiderDisponible");
    const btnLimpiarLider  = document.getElementById("btnLimpiarLiderNuevo");
    const buscarNuevo      = document.getElementById("buscarLiderNuevo");
    const btnBuscarNuevo   = document.getElementById("btnBuscarLiderNuevo");
    const listaNuevo       = document.getElementById("resultadosLiderNuevo");
    const idLiderNuevoInp  = document.getElementById("idLiderNuevo");
    const liderNuevoNombre = document.getElementById("nuevoLiderNombreRO");
    const liderNuevoCorreo = document.getElementById("nuevoLiderCorreoRO");

    function limpiarSeleccionNuevo() {
        if (selectLiderDisp) selectLiderDisp.value = "";
        if (idLiderNuevoInp) idLiderNuevoInp.value = "";
        if (liderNuevoNombre) liderNuevoNombre.value = "—";
        if (liderNuevoCorreo) liderNuevoCorreo.value = "—";
        if (listaNuevo) listaNuevo.innerHTML = "";
        if (buscarNuevo) buscarNuevo.value = "";
    }

    // Cuando el usuario selecciona un líder del select
    if (selectLiderDisp) {
        selectLiderDisp.addEventListener("change", () => {
            const opt = selectLiderDisp.options[selectLiderDisp.selectedIndex];
            if (!opt || !opt.value) {
                limpiarSeleccionNuevo();
                return;
            }

            const id     = opt.value;
            const nombre = opt.dataset.nombre || "";
            const correo = opt.dataset.correo || "—";

            if (idLiderNuevoInp)  idLiderNuevoInp.value  = id;
            if (liderNuevoNombre) liderNuevoNombre.value = nombre;
            if (liderNuevoCorreo) liderNuevoCorreo.value = correo;

            // limpiamos resultados de búsqueda manual para evitar confusión
            if (listaNuevo) listaNuevo.innerHTML = "";
            if (buscarNuevo) buscarNuevo.value = "";
        });
    }

    // Botón "Quitar líder"
    if (btnLimpiarLider) {
        btnLimpiarLider.addEventListener("click", (e) => {
            e.preventDefault();
            limpiarSeleccionNuevo();
        });
    }

    // Renderizar resultados en el modal de "Nuevo"
    function renderResultadosNuevo(items) {
        if (!listaNuevo) return;
        listaNuevo.innerHTML = "";

        if (!items.length) {
            const empty = document.createElement("div");
            empty.className = "list-group-item text-muted small";
            empty.textContent = "No se encontraron líderes disponibles.";
            listaNuevo.appendChild(empty);
            return;
        }

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
                if (idLiderNuevoInp)  idLiderNuevoInp.value  = r.id_lider_semi;
                if (liderNuevoNombre) liderNuevoNombre.value = r.nombre;
                if (liderNuevoCorreo) liderNuevoCorreo.value = r.correo || "—";

                // limpiamos búsqueda manual
                listaNuevo.innerHTML = "";
                if (buscarNuevo) buscarNuevo.value = "";

                // deseleccionamos el select para indicar que el líder vino por búsqueda
                if (selectLiderDisp) selectLiderDisp.value = "";
            });
            listaNuevo.appendChild(a);
        });
    }

    // Buscar líderes (nuevo semillero)
    async function buscarLideresNuevo() {
        if (!btnBuscarNuevo || !listaNuevo) return;
        const baseUrl = btnBuscarNuevo.dataset.urlLideres;
        if (!baseUrl) return;

        const q = (buscarNuevo?.value || "").trim();
        if (q.length < 2) {
            listaNuevo.innerHTML = "";
            return;
        }

        try {
            const data = await fetchLideres(baseUrl, q);
            renderResultadosNuevo(data.items || []);
        } catch (err) {
            console.error("Error cargando líderes (nuevo):", err);
            listaNuevo.innerHTML = "";
            const error = document.createElement("div");
            error.className = "list-group-item text-danger small";
            error.textContent = "No se pudieron cargar los líderes disponibles.";
            listaNuevo.appendChild(error);
        }
    }

    let tNuevo = null;
    if (buscarNuevo) {
        buscarNuevo.addEventListener("input", () => {
            clearTimeout(tNuevo);
            tNuevo = setTimeout(buscarLideresNuevo, 300);
        });
    }

    if (btnBuscarNuevo) {
        btnBuscarNuevo.addEventListener("click", (e) => {
            e.preventDefault();
            buscarLideresNuevo();
        });
    }

    if (modalNuevo) {
        modalNuevo.addEventListener("hidden.bs.modal", () => {
            limpiarSeleccionNuevo();
        });
    }

    // =====================================================================
    // EDITAR SEMILLERO – Rellenar datos + buscador de líder
    // =====================================================================
    const modalEdit      = document.getElementById("modalEditarSemillero");
    const formEdit       = document.getElementById("formEditarSemillero");
    const editNombre     = document.getElementById("editNombre");
    const editLinea      = document.getElementById("editLinea");
    const liderRO        = document.getElementById("liderNombreRO");
    const correoRO       = document.getElementById("liderCorreoRO");
    const idLiderEditInp = document.getElementById("editIdLider");
    const buscarEditInp  = document.getElementById("buscarLiderEditar");
    const btnBuscarEdit  = document.getElementById("btnBuscarLiderEditar");
    const listaEdit      = document.getElementById("resultadosLiderEditar");

    // Para saber cuál es el líder actual y permitir incluirlo en la búsqueda
    let currentLiderId = null;

    // Cuando se abre el modal de editar
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", async (e) => {
            const button = e.relatedTarget;
            const id     = button?.dataset.id;
            if (!id || !formEdit) return;

            // limpiar estados de búsqueda
            if (buscarEditInp) buscarEditInp.value = "";
            if (listaEdit) listaEdit.innerHTML = "";

            try {
                const res  = await fetch(`/admin/semilleros/${id}/edit-ajax`, {
                    headers: { "Accept": "application/json" }
                });
                const data = await res.json();

                if (!res.ok) {
                    const detalle = data && data.message
                        ? data.message
                        : `Error HTTP ${res.status}`;
                    swalError(
                        `No se pudo cargar la información del semillero.<br><small>${detalle}</small>`
                    );
                    return;
                }

                // Rellenar datos básicos
                if (editNombre) editNombre.value = data.nombre || "";
                if (editLinea)  editLinea.value  = data.linea_investigacion || "";

                // Líder actual
                if (liderRO)        liderRO.value        = data.lider_nombre || "—";
                if (correoRO)       correoRO.value       = data.lider_correo || "—";
                if (idLiderEditInp) idLiderEditInp.value = data.id_lider_semi || "";

                currentLiderId = data.id_lider_semi || null;

                // Action del form
                formEdit.action = `/admin/semilleros/${id}`;

            } catch (err) {
                console.error(err);
                swalError("No se pudo cargar la información del semillero.");
            }
        });
    }

    // Renderizar resultados en el modal de "Editar"
    function renderResultadosEditar(items) {
        if (!listaEdit) return;
        listaEdit.innerHTML = "";

        if (!items.length) {
            const empty = document.createElement("div");
            empty.className = "list-group-item text-muted small";
            empty.textContent = "No se encontraron líderes disponibles.";
            listaEdit.appendChild(empty);
            return;
        }

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
                if (idLiderEditInp) idLiderEditInp.value = r.id_lider_semi;
                if (liderRO)        liderRO.value        = r.nombre;
                if (correoRO)       correoRO.value       = r.correo || "—";

                listaEdit.innerHTML = "";
                if (buscarEditInp) buscarEditInp.value = "";
            });
            listaEdit.appendChild(a);
        });
    }

    // Buscar líderes (editar semillero)
    async function buscarLideresEditar() {
        if (!btnBuscarEdit || !listaEdit) return;

        const baseUrl = btnBuscarEdit.dataset.urlLideres;
        if (!baseUrl) return;

        const q = (buscarEditInp?.value || "").trim();
        if (q.length < 2) {
            listaEdit.innerHTML = "";
            return;
        }

        try {
            const data = await fetchLideres(baseUrl, q, idLiderEditInp?.value || null);
            renderResultadosEditar(data.items || []);
        } catch (err) {
            console.error("Error cargando líderes (editar):", err);
            listaEdit.innerHTML = "";
            const error = document.createElement("div");
            error.className = "list-group-item text-danger small";
            error.textContent = "No se pudieron cargar los líderes disponibles.";
            listaEdit.appendChild(error);
        }
    }

    let tEdit = null;
    if (buscarEditInp) {
        buscarEditInp.addEventListener("input", () => {
            clearTimeout(tEdit);
            tEdit = setTimeout(buscarLideresEditar, 300);
        });
    }

    if (btnBuscarEdit) {
        btnBuscarEdit.addEventListener("click", (e) => {
            e.preventDefault();
            buscarLideresEditar();
        });
    }

    // =====================================================================
    // ELIMINAR SEMILLERO (SweetAlert + form dinámico si hace falta)
    // =====================================================================
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-eliminar-semillero");
        if (!btn) return;

        e.preventDefault();

        const url    = btn.dataset.url;
        const nombre = btn.dataset.nombre || "este semillero";

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
            if (!result.isConfirmed || !url) return;

            // Si el botón está dentro de un form, usamos el form
            const formParent = btn.closest("form");
            if (formParent) {
                formParent.submit();
                return;
            }

            // Si no hay form, creamos uno dinámico (Laravel)
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token     = tokenMeta ? tokenMeta.getAttribute("content") : null;

            const form = document.createElement("form");
            form.method = "POST";
            form.action = url;

            const methodInp = document.createElement("input");
            methodInp.type  = "hidden";
            methodInp.name  = "_method";
            methodInp.value = "DELETE";
            form.appendChild(methodInp);

            if (token) {
                const tokenInp = document.createElement("input");
                tokenInp.type  = "hidden";
                tokenInp.name  = "_token";
                tokenInp.value = token;
                form.appendChild(tokenInp);
            }

            document.body.appendChild(form);
            form.submit();
        });
    });

});
