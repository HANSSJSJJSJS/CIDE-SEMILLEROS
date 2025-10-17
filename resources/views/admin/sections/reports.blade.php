<h2 class="section-title">Reportes y Estadísticas</h2>
<p class="section-subtitle">Genera y visualiza reportes del sistema</p>

<div class="stats-grid">
  <div class="stat-card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;color:var(--gray-700);">Usuarios por Rol</h3>
    <div style="display:flex;flex-direction:column;gap:.75rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Administradores</span>
        <strong style="color:var(--sena-navy);">5</strong>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Líderes</span>
        <strong style="color:var(--sena-navy);">32</strong>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Aprendices</span>
        <strong style="color:var(--sena-navy);">211</strong>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <h3 style="font-size:1rem;font-weight:600;margin-bottom:1rem;color:var(--gray-700);">Documentos por Estado</h3>
    <div style="display:flex;flex-direction:column;gap:.75rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Aprobados</span>
        <strong style="color:#16A34A;">1,542</strong>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Pendientes</span>
        <strong style="color:var(--yellow-500);">287</strong>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="color:var(--gray-600);">Rechazados</span>
        <strong style="color:var(--red-500);">18</strong>
      </div>
    </div>
  </div>
</div>

<div style="margin-top:2rem;">
  <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:1rem;color:var(--gray-800);">Generar Reportes</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
    <button class="btn btn-primary" style="justify-content:center;">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Reporte de Usuarios
    </button>
    <button class="btn btn-primary" style="justify-content:center;">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Reporte de Semilleros
    </button>
    <button class="btn btn-primary" style="justify-content:center;">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Reporte de Actividad
    </button>
  </div>
</div>
