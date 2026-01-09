console.log("JS de recursos del líder cargado correctamente");

// ===============================
// CARGAR RECURSOS DEL PROYECTO
// ===============================
window.cargarRecursos = function (proyectoId) {

    const url = window.URL_RECURSOS_PROYECTO.replace(':id', proyectoId);

    fetch(url)
        .then(res => res.json())
        .then(data => {

            const proyecto = data.proyecto;
            const recursos = data.recursos;

            document.getElementById('tituloProyecto').innerText =
                proyecto.nombre_proyecto ?? proyecto.nombre ?? "Proyecto";

            const lista = document.getElementById('listaR0ecursos');
            lista.innerHTML = '';

            if (!recursos.length) {
                lista.innerHTML = '<div class="text-muted p-3">No hay recursos asignados.</div>';
                return;
            }

            recursos.forEach(r => {
                const item = document.createElement('div');
                item.classList.add('list-group-item', 'p-3');

                item.innerHTML = `
                    <h6 class="fw-bold">${r.nombre_archivo ?? r.titulo ?? 'Recurso'}</h6>
                    <p>${r.descripcion ?? 'Sin descripción'}</p>

                    <span class="badge bg-${
                        r.estado === 'pendiente' ? 'warning' :
                        (r.estado === 'respondido' ? 'primary' : 'success')
                    }">
                        ${r.estado}
                    </span>

                    <div class="mt-3 text-end">
                        <button class="btn btn-success btn-sm"
                                onclick="abrirResponder(${r.id}, ${JSON.stringify(r.nombre_archivo ?? r.titulo)})">
                            Responder
                        </button>
                    </div>
                `;

                lista.appendChild(item);
            });

            new bootstrap.Modal(document.getElementById('modalVerRecursos')).show();
        })
        .catch(err => {
            console.error(err);
            Swal.fire("Error", "No se pudieron cargar los recursos.", "error");
        });
};


// ===============================
// ABRIR MODAL PARA RESPONDER
// ===============================
window.abrirResponder = function (id, titulo) {

    document.getElementById('resp_id_recurso').value = id;
    document.getElementById('resp_titulo').innerText = titulo;

    document.getElementById('resp_respuesta').value = "";
    document.getElementById('resp_archivo').value = "";

    new bootstrap.Modal(document.getElementById("modalResponderRecurso")).show();
};


// ===============================
// ENVIAR RESPUESTA DEL RECURSO
// ===============================
window.enviarRespuesta = function () {

    const formData = new FormData();

    const id = document.getElementById("resp_id_recurso").value;
    const texto = document.getElementById("resp_respuesta").value;
    const archivo = document.getElementById("resp_archivo").files[0];

    formData.append("id_recurso", id);
    formData.append("respuesta", texto);

    if (archivo) formData.append("archivo_respuesta", archivo);

    fetch(window.URL_RESPONDER_RECURSO, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            if (data.success) {
                Swal.fire("¡Respuesta enviada!", "El recurso ha sido respondido.", "success");

                bootstrap.Modal.getInstance(document.getElementById("modalResponderRecurso")).hide();

                if (data.proyecto_id) {
                    cargarRecursos(data.proyecto_id);
                }
            } else {
                Swal.fire("Error", "No se pudo enviar la respuesta.", "error");
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire("Error", "Ocurrió un problema al enviar la respuesta.", "error");
        });
};
