<h2 class="section-title">Gestión de Usuarios</h2>
<p class="section-subtitle">Administra todos los usuarios del sistema</p>

<div class="toolbar">
  <div class="search-box">
    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" placeholder="Buscar usuarios por nombre, email o documento...">
  </div>
  <select>
    <option>Todos los roles</option>
    <option>Administrador</option>
    <option>Líder</option>
    <option>Aprendiz</option>
  </select>
  <select>
    <option>Todos los estados</option>
    <option>Activo</option>
    <option>Inactivo</option>
  </select>
  <button class="btn btn-primary" onclick="openModal('addUser')">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Nuevo Usuario
  </button>
</div>

<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Usuario</th><th>Email</th><th>Rol</th><th>Semillero</th><th>Estado</th><th>Último acceso</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>María González</strong></td>
        <td>maria.gonzalez@sena.edu.co</td>
        <td><span class="badge admin">Administrador</span></td>
        <td>-</td>
        <td><span class="badge active">Activo</span></td>
        <td>Hace 2 horas</td>
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
        <td><strong>Carlos Ramírez</strong></td>
        <td>carlos.ramirez@sena.edu.co</td>
        <td><span class="badge leader">Líder</span></td>
        <td>Desarrollo Web</td>
        <td><span class="badge active">Activo</span></td>
        <td>Hace 1 día</td>
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
        <td><strong>Ana Martínez</strong></td>
        <td>ana.martinez@sena.edu.co</td>
        <td><span class="badge leader">Líder</span></td>
        <td>Inteligencia Artificial</td>
        <td><span class="badge active">Activo</span></td>
        <td>Hace 3 horas</td>
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
        <td><strong>Pedro López</strong></td>
        <td>pedro.lopez@sena.edu.co</td>
        <td><span class="badge student">Aprendiz</span></td>
        <td>Desarrollo Web</td>
        <td><span class="badge active">Activo</span></td>
        <td>Hace 5 horas</td>
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
        <td><strong>Laura Sánchez</strong></td>
        <td>laura.sanchez@sena.edu.co</td>
        <td><span class="badge student">Aprendiz</span></td>
        <td>Inteligencia Artificial</td>
        <td><span class="badge inactive">Inactivo</span></td>
        <td>Hace 2 semanas</td>
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

{{-- Modal: Nuevo Usuario --}}
<div id="addUser" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title">Nuevo Usuario</h3>
      <button class="close-modal" onclick="closeModal('addUser')">×</button>
    </div>
    <form>
      <div class="form-group">
        <label class="form-label">Nombre Completo</label>
        <input type="text" class="form-input" placeholder="Ingrese el nombre completo">
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-input" placeholder="usuario@sena.edu.co">
      </div>
      <div class="form-group">
        <label class="form-label">Documento</label>
        <input type="text" class="form-input" placeholder="Número de documento">
      </div>
      <div class="form-group">
        <label class="form-label">Rol</label>
        <select class="form-input">
          <option>Seleccione un rol</option>
          <option>Administrador</option>
          <option>Líder</option>
          <option>Aprendiz</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Contraseña</label>
        <input type="password" class="form-input" placeholder="Contraseña temporal">
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addUser')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
      </div>
    </form>
  </div>
</div>
