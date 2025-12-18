{{-- MODAL VER RECURSOS (ESTILO PROFESIONAL INSTITUCIONAL) --}}
<div class="modal fade" id="modalActividades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content modal-recursos">

            {{-- HEADER --}}
            <div class="modal-recursos-header p-3 d-flex justify-content-between align-items-center">
                
                <h4 class="m-0 fw-bold text-white" id="tituloModalActividades">
                    Recursos del Semillero
                </h4>

                <button class="btn btn-light btn-sm modal-recursos-close" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body" id="contenedorActividades">
                {{-- contenido dinámico --}}
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarRecurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-recursos">

            <div class="modal-recursos-header p-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 fw-bold text-white">Editar recurso</h4>

                <button class="btn btn-light btn-sm modal-recursos-close" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="formEditarRecurso">
                <div class="modal-body">

                    <input type="hidden" id="edit_recurso_id" name="recurso_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Líder</label>
                            <input type="text" class="form-control" id="edit_lider" readonly disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" id="edit_estado" readonly disabled>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" id="edit_titulo" readonly disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo de evidencia <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_tipo_documento" name="tipo_documento" required>
                                <option value="">Selecciona el tipo de recurso</option>
                                <option value="pdf">Documento PDF</option>
                                <option value="enlace">Enlace</option>
                                <option value="documento">Documento Word</option>
                                <option value="presentacion">Presentación</option>
                                <option value="video">Video</option>
                                <option value="imagen">Imagen</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha límite <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_fecha_limite" name="fecha_limite" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="4" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">Guardar cambios</button>
                </div>
            </form>

        </div>
    </div>
</div>
