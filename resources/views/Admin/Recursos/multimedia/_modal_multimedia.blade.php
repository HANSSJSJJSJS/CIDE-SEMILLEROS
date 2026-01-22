<div id="modalSubirMultimedia" class="modal-overlay d-none">

    <div class="modal-subir-multimedia">
        <button type="button" class="modal-close" id="cerrarModalMultimedia">×</button>

        <h3>Subir Multimedia</h3>

        <form id="formSubirMultimedia" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Categoría *</label>
                    <select name="categoria" class="form-select" required>
                        <option value="plantillas">Plantillas / Presentaciones</option>
                        <option value="manuales">Manuales</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Destino *</label>
                    <select name="destino" class="form-select" required>
                        <option value="todos">Para todos los líderes</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción (opcional)</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Archivo *</label>
                    <input type="file" name="archivo" class="form-control" required>
                </div>

            </div>

            <div class="mt-4 d-flex justify-content-end gap-3">
                <button type="button" class="btn btn-secondary" id="cancelarModal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload"></i> Subir
                </button>
            </div>
        </form>
    </div>

</div>
