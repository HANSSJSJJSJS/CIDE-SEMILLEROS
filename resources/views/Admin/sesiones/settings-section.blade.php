<section id="settings" class="content-section">
  <h2 class="section-title">Configuración del Sistema</h2>
  <p class="section-subtitle">Administra la configuración general del sistema</p>

  <div class="d-flex flex-column gap-4" style="max-width: 800px;">
    <div class="stat-card p-3 border rounded-3">
      <h3 class="fs-5 fw-semibold mb-3 text-dark">Configuración General</h3>
      <div class="mb-3">
        <label class="form-label">Nombre del Sistema</label>
        <input type="text" class="form-control" value="CIDE SEMILLERO">
      </div>
      <div class="mb-3">
        <label class="form-label">Email de Contacto</label>
        <input type="email" class="form-control" value="contacto@sena.edu.co">
      </div>
      <div class="mb-3">
        <label class="form-label">Límite de Usuarios por Semillero</label>
        <input type="number" class="form-control" value="30">
      </div>
      <button class="btn btn-primary">Guardar Cambios</button>
    </div>

    <div class="stat-card p-3 border rounded-3">
      <h3 class="fs-5 fw-semibold mb-3 text-dark">Seguridad</h3>
      <div class="mb-3">
        <label class="form-label">Tiempo de Sesión (minutos)</label>
        <input type="number" class="form-control" value="60">
      </div>
      <div class="mb-3">
        <label class="form-label">Intentos de Inicio de Sesión</label>
        <input type="number" class="form-control" value="3">
      </div>
      <button class="btn btn-primary">Guardar Cambios</button>
    </div>

    <div class="stat-card p-3 border rounded-3">
      <h3 class="fs-5 fw-semibold mb-3 text-dark">Notificaciones</h3>
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="n1" checked>
        <label class="form-check-label" for="n1">Notificar nuevos usuarios</label>
      </div>
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="n2" checked>
        <label class="form-check-label" for="n2">Notificar nuevos semilleros</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="n3" checked>
        <label class="form-check-label" for="n3">Notificar documentos pendientes</label>
      </div>
      <button class="btn btn-primary mt-3">Guardar Cambios</button>
    </div>
  </div>
</section>
