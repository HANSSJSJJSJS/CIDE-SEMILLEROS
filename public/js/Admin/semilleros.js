// ============================================================
// HELPERS GLOBALES PARA NOTIFICACIONES (éxito / error)
// Se usan desde el Blade con: swalSuccess('...'), swalError('...')
// ============================================================
window.swalSuccess = function (msg) {
    Swal.fire({
        icon: "success",
        title: "Operación exitosa",
        html: msg,
        showCancelButton: false,
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
        showCancelButton: false,
        confirmButtonText: "Aceptar",
        customClass: {
            confirmButton: "custom-confirm swal2-confirm"
        }
    });
};

// ============================================================
// LÓGICA DE SEMILLEROS (editar, buscador de líder, eliminar)
// ============================================================
document.addEventListener("DOMContentLoaded", () => {

    // -----------------------------
    // Referencias básicas del modal
    // -----------------------------
    const modal       = document.getElementById("modalEditarSemillero");
    const form        = document.getElementById("formEditarSemillero");
    const editNombre  = document.getElementById("editNombre");
    const editLinea   = document.getElementById("editLinea");
    const liderRO     = document.getElementById("liderNombreRO");
    const correoRO    = document.getElementById("liderCorreoRO");
    const idLiderInp  = document.getElementById("editIdLider");

    const buscarInp   = document.getElementById("buscarLider");
    const listaRes    = document.getElementById("resultadosLider");

    // Puede que este JS se cargue en una vista donde NO exista el modal
    if (modal && form && buscarInp && listaRes) {

        // -----------------------------------------------------
        // Abrir modal y cargar datos del semillero vía AJAX
        // -----------------------------------------------------
        modal.addEventListener("show.bs.modal", async (e) => {
            const mNuevo = document.getElementById("modalNuevoSemillero");
            const instNuevo = mNuevo ? bootstrap.Modal.getInstance(mNuevo) : null;
            instNuevo?.hide();

            const id = e.relatedTarget?.dataset.id;
            if (!id) return;

            buscarInp.value = "";
            listaRes.innerHTML = "";

            try {
                const res  = await fetch(`/admin/semilleros/${id}/edit`);
                const data = await res.json();

                form.action       = `/admin/semilleros/${id}`;
                editNombre.value  = data.nombre || "";
                editLinea.value   = data.linea_investigacion || "";
                liderRO.value     = data.lider_nombre || "—";
                correoRO.value    = data.lider_correo || "—";
                idLiderInp.value  = data.id_lider_semi || "";
            } catch (err) {
                console.error("Error cargando semillero:", err);
                swalError("No se pudo cargar la información del semillero.");
            }
        });

        // -----------------------------------------------------
        // Buscador interno de líderes (input + lista clicable)
        // -----------------------------------------------------
        let t;
        buscarInp.addEventListener("input", () => {
            clearTimeout(t);

            t = setTimeout(async () => {
                const q = buscarInp.value.trim();
                if (q.length < 2) {
                    listaRes.innerHTML = "";
                    return;
                }

                const includeCurrent = idLiderInp.value
                    ? `&include_current=${encodeURIComponent(idLiderInp.value)}`
                    : "";

                const url = `/admin/semilleros/lideres-disponibles?q=${encodeURIComponent(q)}${includeCurrent}`;

                try {
                    const res   = await fetch(url);
                    const items = await res.json();

                    listaRes.innerHTML = "";
                    if (!Array.isArray(items)) return;

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
                            idLiderInp.value = r.id_lider_semi;
                            liderRO.value    = r.nombre;
                            correoRO.value   = r.correo || "—";
                            listaRes.innerHTML = "";
                            buscarInp.value = "";
                        });

                        listaRes.appendChild(a);
                    });
                } catch (err) {
                    console.error("Error cargando líderes:", err);
                }

            }, 250);
        });

        // -----------------------------------------------------
        // Evitar backdrops duplicados de Bootstrap (cuando
        // se abre/cierra crear/editar)
        // -----------------------------------------------------
        const cleanupBackdrop = () => {
            document.body.classList.remove("modal-open");
            document.body.style.removeProperty("padding-right");
            document.querySelectorAll(".modal-backdrop").forEach((b) => b.remove());
        };

        modal.addEventListener("hidden.bs.modal", cleanupBackdrop);

        const modalNuevo = document.getElementById("modalNuevoSemillero");
        modalNuevo?.addEventListener("hidden.bs.modal", cleanupBackdrop);
    }

    // ========================================================
    // CONFIRMAR ELIMINAR SEMILLERO (delegación de eventos)
    // ========================================================
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-eliminar-semillero");
        if (!btn) return;

        e.preventDefault();

        const url    = btn.dataset.url;
        const nombre = btn.dataset.nombre || "este semillero";

        // El formulario real ya existe en el DOM (en Blade), solo lo buscamos:
        const formDelete = btn.closest("form");

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
                if (formDelete) {
                    formDelete.submit();
                } else {
                    // Plan B: construir un form en runtime
                    let form = document.createElement("form");
                    form.method = "POST";
                    form.action = url;

                    let token = document.querySelector('meta[name="csrf-token"]').content;

                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${token}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    });

});
