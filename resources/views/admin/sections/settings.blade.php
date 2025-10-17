<h2 class="section-title">Configuración del Sistema</h2>
<p class="section-subtitle">Administra la configuración general del sistema</p>

<div style="display:flex;flex-direction:column;gap:2rem;max-width:800px;">
  <div class="stat-card">
    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:1rem;color:var(--gray-800);">Configuración General</h3>
    <div class="form-group">
      <label class="form-label">Nombre del Sistema</label>
      <input type="text" class="form-input" value="CIDE SEMILLERO">
    </div>
    <div class="form-group">
      <label class="form-label">Email de Contacto</label>
      <input type="email" class="form-input" value="contacto@sena.edu.co">
    </div>
    <div class="form-group">
      <label class="form-label">Límite de Usuarios por Semillero</label>
      <input type="number" class="form-input" value="30">
    </div>
    <button class="btn btn-primary">Guardar Cambios</button>
  </div>

  <div class="stat-card">
    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:1rem;color:var(--gray-800);">Seguridad</h3>
    <div class="form-group">
      <label class="form-label">Tiempo de Sesión (minutos)</label>
      <input type="number" class="form-input" value="60">
    </div>
    <div class="form-group">
      <label class="form-label">Intentos de Inicio de Sesión</label>
      <input type="number" class="form-input" value="3">
    </div>
    <button class="btn btn-primary">Guardar Cambios</button>
  </div>

  <div class="stat-card">
    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:1rem;color:var(--gray-800);">Notificaciones</h3>
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
        <input type="checkbox" checked style="width:20px;height:20px;cursor:pointer;">
        <span style="color:var(--gray-700);">Notificar nuevos usuarios</span>
      </label>
      <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
        <input type="checkbox" checked style="width:20px;height:20px;cursor:pointer;">
        <span style="color:var(--gray-700);">Notificar nuevos semilleros</span>
      </label>
      <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;">
        <input type="checkbox" checked style="width:20px;height:20px;cursor:pointer;">
        <span style="color:var(--gray-700);">Notificar documentos pendientes</span>
      </label>
    </div>
    <button class="btn btn-primary" style="margin-top:1rem;">Guardar Cambios</button>
  </div>
</div>
