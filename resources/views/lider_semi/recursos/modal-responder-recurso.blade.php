<div class="modal fade" id="modalResponderRecurso" tabindex="-1">
    <div class="modal-dialog">
        <form id="formResponderRecurso" enctype="multipart/form-data">
            @csrf

            <!-- ✔ ID corregido -->
            <input type="hidden" id="resp_id_recurso" name="id_recurso">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Responder Recurso: <span id="resp_titulo"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label class="form-label">Comentario</label>

                    <!-- ✔ ID corregido -->
                    <textarea class="form-control" name="respuesta" id="resp_respuesta" rows="3"></textarea>

                    <label class="form-label mt-3">Subir archivo (opcional)</label>
                    <input type="file" class="form-control" name="archivo_respuesta" id="resp_archivo">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="enviarRespuesta()">Enviar respuesta</button>
                </div>

            </div>
        </form>
    </div>
</div>
