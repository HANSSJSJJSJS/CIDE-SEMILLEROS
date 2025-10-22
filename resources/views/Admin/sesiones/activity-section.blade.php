<section id="activity" class="content-section">
  <h2 class="section-title">Actividad del Sistema</h2>
  <p class="section-subtitle">Registro de acciones del sistema</p>

  <div class="toolbar d-flex gap-2 mb-3">
    <div class="position-relative" style="max-width: 360px; width:100%;">
      <input type="text" class="form-control" placeholder="Buscar en el registro...">
    </div>
    <select class="form-select w-auto">
      <option>Todas las acciones</option>
      <option>Creación</option>
      <option>Edición</option>
      <option>Eliminación</option>
      <option>Inicio de sesión</option>
    </select>
    <select class="form-select w-auto">
      <option>Últimas 24 horas</option>
      <option>Última semana</option>
      <option>Último mes</option>
    </select>
  </div>

  <div class="activity-log d-flex flex-column gap-3">
    <div class="activity-item d-flex gap-3">
      <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center"
           style="width:40px;height:40px;background:#DCFCE7;color:#16A34A;">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
      </div>
      <div>
        <div class="fw-semibold">Nuevo usuario registrado</div>
        <div class="text-muted small">María González creó una cuenta de administrador</div>
        <div class="text-muted small">Hace 2 horas</div>
      </div>
    </div>

    <div class="activity-item d-flex gap-3">
      <div class="activity-icon rounded-circle d-flex align-items-center justify-content-center"
           style="width:40px;height:40px;background:#DBEAFE;color:#1E40AF;">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
          <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
      </div>
      <div>
        <div class="fw-semibold">Semillero actualizado</div>
        <div class="text-muted small">Carlos Ramírez modificó el semillero "Desarrollo Web"</div>
        <div class="text-muted small">Hace 3 horas</div>
      </div>
    </div>
  </div>
</section>
