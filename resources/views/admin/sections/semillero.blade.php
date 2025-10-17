<h2 class="section-title">Gestión de Semilleros</h2>
<p class="section-subtitle">Administra todos los semilleros de investigación</p>

<div class="toolbar">
  <div class="search-box">
    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" placeholder="Buscar semilleros...">
  </div>
  <select>
    <option>Todas las áreas</option>
    <option>Tecnología</option>
    <option>Ciencias</option>
    <option>Ingeniería</option>
  </select>
  <button class="btn btn-primary" onclick="openModal('addSemillero')">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Nuevo Semillero
  </button>
</div>

<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Nombre</th><th>Líder</th><th>Aprendices</th><th>Área</th><th>Estado</th><th>Fecha creación</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Desarrollo Web Avanzado</strong></td>
        <td>Carlos Ramírez</td>
        <td>24</td>
        <td>Tecnología</td>
        <td><span class="badge active">Activo</span></td>
        <td>15/01/2025</td>
        <td>
          <div class="action-buttons">
            <button class="btn btn-icon btn-edit" title="Editar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button class="btn btn-icon btn-delete" title="Eliminar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>
      <tr>
        <td><strong>Inteligencia Artificial</strong></td>
        <td>Ana Martínez</td>
        <td>18</td>
        <td>Tecnología</td>
        <td><span class="badge active">Activo</span></td>
        <td>20/01/2025</td>
        <td>
          <div class="action-buttons">
            <button class="btn btn-icon btn-edit" title="Editar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button class="btn btn-icon btn-delete" title="Eliminar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>
      <tr>
        <td><strong>Robótica Educativa</strong></td>
        <td>Jorge Pérez</td>
        <td>15</td>
        <td>Ingeniería</td>
        <td><span class="badge active">Activo</span></td>
        <td>10/02/2025</td>
        <td>
          <div class="action-buttons">
            <button class="btn btn-icon btn-edit" title="Editar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button class="btn btn-icon btn-delete" title="Eliminar">
              <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>

{{-- Modal: Nuevo Semillero --}}
<div id="addSemillero" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title">Nuevo Semillero</h3>
      <button class="close-modal" onclick="closeModal('addSemillero')">×</button>
    </div>
    <form>
      <div class="form-group">
        <label class="form-label">Nombre del Semillero</label>
        <input type="text" class="form-input" placeholder="Ingrese el nombre del semillero">
      </div>
      <div class="form-group">
        <label class="form-label">Descripción</label>
        <textarea class="form-input" rows="3" placeholder="Descripción del semillero"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Líder Asignado</label>
        <select class="form-input">
          <option>Seleccione un líder</option>
          <option>Carlos Ramírez</option>
          <option>Ana Martínez</option>
          <option>Jorge Pérez</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Área</label>
        <select class="form-input">
          <option>Seleccione un área</option>
          <option>Tecnología</option>
          <option>Ciencias</option>
          <option>Ingeniería</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addSemillero')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear Semillero</button>
      </div>
    </form>
  </div>
</div>
