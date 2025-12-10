// ======================================================
// FUNCION PARA MOSTRAR MULTIMEDIA  (DEBE IR ARRIBA!!!)
// ======================================================
async function cargarMultimedia() {

    const resp = await fetch("/admin/recursos/multimedia/list");
    const recursos = await resp.json();

    const contPlantillas = document.getElementById("contenedorPlantillas");
    const contManuales = document.getElementById("contenedorManuales");
    const contOtros = document.getElementById("contenedorOtros");

    contPlantillas.innerHTML = "";
    contManuales.innerHTML = "";
    contOtros.innerHTML = "";

    recursos.forEach(item => {

      const card = `
    <div class="col-md-3">
        <div class="card-recurso">
            <h6>${item.nombre_archivo}</h6>

            <button class="btn-recurso btn-ver-archivo"
                    onclick="window.open('/storage/${item.archivo}', '_blank')">
                Ver archivo
            </button>

            <button class="btn-recurso btn-eliminar-archivo mt-2"
                    onclick="eliminarMultimedia(${item.id})">
                Eliminar
            </button>
        </div>
    </div>
`;


        if (item.categoria === "plantillas") contPlantillas.innerHTML += card;
        else if (item.categoria === "manuales") contManuales.innerHTML += card;
        else contOtros.innerHTML += card;
    });
}


// ======================================================
// ELIMINAR MULTIMEDIA
// ======================================================
async function eliminarMultimedia(id) {
    if (!confirm("¿Eliminar este archivo?")) return;

    const resp = await fetch(`/admin/recursos/multimedia/delete/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content
        }
    });

    const data = await resp.json();

    if (data.success) {
        alert("Archivo eliminado");
        cargarMultimedia();
    } else {
        alert("No se pudo eliminar");
    }
}


// ======================================================
// DOMContentLoaded  (DEBE IR DESPUÉS)
// ======================================================
document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("modalSubirMultimedia");
    const btnAbrir = document.getElementById("btnAbrirModalMultimedia");
    const form = document.getElementById("formSubirMultimedia");
    const CSRF = document.querySelector("meta[name='csrf-token']").content;

    // Cargar al inicio ahora sí funciona ✔
    cargarMultimedia();

    // ABRIR MODAL
    btnAbrir.addEventListener("click", () => {
        modal.classList.add("active");
    });

    // CERRAR MODAL
    document.querySelectorAll("[data-close-modal='multimedia']").forEach(btn => {
        btn.addEventListener("click", () => modal.classList.remove("active"));
    });

    modal.addEventListener("click", e => {
        if (e.target === modal) modal.classList.remove("active");
    });

    const destino = document.getElementById("destinoSeleccion");
    const campoSemillero = document.getElementById("campoSemillero");

    destino.addEventListener("change", () => {
        campoSemillero.classList.toggle("d-none", destino.value !== "semillero");
    });

    // SUBIR FORMULARIO
    form.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = new FormData(form);

        const response = await fetch(window.MULTIMEDIA_STORE_URL, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": CSRF },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert("✔ Multimedia subida correctamente");
            modal.classList.remove("active");
            form.reset();
            cargarMultimedia();
        } else {
            alert("❌ Error al subir multimedia");
        }
    });

});
