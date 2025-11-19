<div class="modal fade" id="modalEditarSemillero" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Semillero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" id="formEditarSemillero">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nombre del semillero</label>
              <input type="text" name="nombre" id="editNombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Línea de investigación</label>
              <input type="text" name="linea_investigacion" id="editLinea" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Líder asignado</label>
              <input type="text" id="liderNombreRO" class="form-control" value="—" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo líder</label>
              <input type="text" id="liderCorreoRO" class="form-control" value="—" readonly>
            </div>

            <input type="hidden" name="id_lider_semi" id="editIdLider">

            <div class="col-12">
              <label class="form-label fw-semibold">Buscar líder</label>
              <input type="text" id="buscarLider" class="form-control" placeholder="Escribe para buscar...">
              <div id="resultadosLider" class="list-group mt-2"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
