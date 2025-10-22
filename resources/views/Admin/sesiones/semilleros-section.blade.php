<section id="semilleros" class="content-section">
  <h2 class="section-title">Gestión de Semilleros</h2>
  <p class="section-subtitle">Administra todos los semilleros de investigación</p>

  <div class="toolbar d-flex flex-wrap gap-2 mb-3">
    <div class="position-relative" style="max-width: 360px; width:100%;">
      <input type="text" class="form-control" placeholder="Buscar semilleros...">
    </div>
    <select class="form-select w-auto">
      <option>Todas las áreas</option>
      <option>Tecnología</option>
      <option>Ciencias</option>
      <option>Ingeniería</option>
    </select>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalSemillero">
      Nuevo Semillero
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Nombre</th>
          <th>Líder</hth>
          <th>Aprendices</th>
          <th>Área</th>
          <th>Estado</th>
          <th>Fecha creación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Desarrollo Web Avanzado</strong></td>
          <td>Carlos Ramírez</td>
          <td>24</td>
          <td>Tecnología</td>
          <td><span class="badge bg-success">Activo</span></td>
          <td>2025-01-15</td>
          <td class="d-flex gap-2">
            <button class="btn btn-sm btn-warning">Editar</button>
            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
          </td>
        </tr>
        <tr>
          <td><strong>Inteligencia Artificial</strong></td>
          <td>Ana Martínez</td>
          <td>18</td>
          <td>Tecnología</td>
          <td><span class="badge bg-success">Activo</span></td>
          <td>2025-01-20</td>
          <td class="d-flex gap-2">
            <button class="btn btn-sm btn-warning">Editar</button>
            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Modal semillero (estructura base; puedes moverlo a su propio include si lo expandes) --}}
  <div class="modal fade" id="modalSemillero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Semillero</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <form>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre del Semillero</label>
              <input type="text" class="form-control" placeholder="Ingrese el nombre del semillero">
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control" rows="3" placeholder="Descripción del semillero"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Líder Asignado</label>
              <select class="form-select">
                <option>Seleccione un líder</option>
                <option>Carlos Ramírez</option>
                <option>Ana Martínez</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Área</label>
              <select class="form-select">
                <option>Seleccione un área</option>
                <option>Tecnología</option>
                <option>Ciencias</option>
                <option>Ingeniería</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
            <button class="btn btn-primary" type="submit">Crear Semillero</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
