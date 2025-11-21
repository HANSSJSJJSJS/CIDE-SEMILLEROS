{{-- Partial SOLO del modal. No layout, no section, no HTML entero --}}
<div class="modal fade"
     id="recUploadModal"
     data-bs-backdrop="false"
     data-bs-keyboard="true"
     tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="recForm" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Subir recurso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Nombre del archivo *</label>
              <input type="text" name="nombre_archivo" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Categoría *</label>
              <select name="categoria" class="form-select" required>
                <option value="plantillas">Plantillas</option>
                <option value="manuales">Manuales</option>
                <option value="otros">Otros</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción (opcional)</label>
              <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Archivo *</label>
              <input type="file" name="archivo" class="form-control" required>
              <small class="text-muted">Máx 20MB. Se admite cualquier tipo de archivo.</small>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-save"></i> Guardar
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
