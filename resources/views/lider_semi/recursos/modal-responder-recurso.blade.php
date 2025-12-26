<div class="modal fade" id="modalResponderRecurso" tabindex="-1">
    <div class="modal-dialog modal-lg">
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

                    <label class="form-label" style="display:none;">Comentario</label>

                    <!-- ✔ ID corregido -->
                    <textarea class="form-control" name="respuesta" id="resp_respuesta" rows="3" style="display:none;"></textarea>

                    <div class="mt-3" id="resp_tipo_hint" style="display:none;">
                        <small class="text-muted">Tipo solicitado: <strong id="resp_tipo_label"></strong></small>
                    </div>

                    <label class="form-label mt-2" id="resp_archivo_label">Archivo *</label>
                    <div class="resp-dropzone" id="resp_dropzone" role="button" tabindex="0">
                        <div class="resp-dropzone-icon">
                            <i class="bi bi-upload"></i>
                        </div>
                        <div class="resp-dropzone-title">Arrastra aquí tu archivo para subir</div>
                        <div class="resp-dropzone-sub" id="resp_dropzone_sub">
                            Tipo asignado por tu líder: <strong id="resp_dropzone_tipo"></strong>
                        </div>
                        <div class="resp-dropzone-filename" id="resp_file_name">Sin archivos seleccionados</div>
                    </div>
                    <input type="file" class="form-control" name="archivo_respuesta" id="resp_archivo" style="display:none;">

                    <label class="form-label mt-3" style="display:none;" id="resp_enlace_label">Enlace</label>
                    <input type="url" class="form-control" name="enlace_respuesta" id="resp_enlace" style="display:none;" placeholder="https://...">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="enviarRespuesta()">Enviar respuesta</button>
                </div>

            </div>
        </form>
    </div>
</div>
