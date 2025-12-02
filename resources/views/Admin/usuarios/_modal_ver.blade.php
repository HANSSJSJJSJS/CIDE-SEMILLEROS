{{-- Modal VER DATOS USUARIO (solo lectura, se llena por AJAX) --}}
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <div class="d-flex flex-column">
          <h5 class="modal-title mb-1">
            <i class="bi bi-eye"></i>
            Datos del usuario
          </h5>
          <small class="text-muted">
            Rol:
            <span class="badge bg-secondary ms-1" id="ver-role-label">—</span>
          </small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        {{-- DATOS BÁSICOS --}}
        <h6 class="fw-semibold mb-2">
          <i class="bi bi-person-badge me-1"></i>
          Datos básicos
        </h6>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Nombres y apellidos</label>
            <p class="form-control-plaintext mb-0" data-field="nombre_completo">—</p>
          </div>

          <div class="col-md-6">
            <label class="form-label">Correo de acceso</label>
            <p class="form-control-plaintext mb-0" data-field="email">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Tipo de documento</label>
            <p class="form-control-plaintext mb-0" data-field="tipo_documento">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Número de documento</label>
            <p class="form-control-plaintext mb-0" data-field="documento">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Celular</label>
            <p class="form-control-plaintext mb-0" data-field="celular">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Género</label>
            <p class="form-control-plaintext mb-0" data-field="genero">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Tipo de RH</label>
            <p class="form-control-plaintext mb-0" data-field="tipo_rh">—</p>
          </div>

          <div class="col-md-4">
            <label class="form-label">Estado</label>
            <p class="form-control-plaintext mb-0" data-field="estado">—</p>
          </div>
        </div>

        <hr>

        {{-- BLOQUES ESPECÍFICOS POR ROL --}}
        <div id="ver-bloque-admin" class="d-none">
          <h6 class="fw-semibold mb-2">
            <i class="bi bi-gear me-1"></i>
            Datos de líder general
          </h6>
          <p class="text-muted mb-0">
            Sin datos adicionales específicos.
          </p>
        </div>

        <div id="ver-bloque-lider-semi" class="d-none">
          <h6 class="fw-semibold mb-2">
            <i class="bi bi-people me-1"></i>
            Datos de líder de semillero
          </h6>

          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <p class="form-control-plaintext mb-0" data-field="ls_correo_institucional">—</p>
            </div>
            <div class="col-md-6">
              <label class="form-label">Semillero que lidera</label>
              <p class="form-control-plaintext mb-0" data-field="ls_semillero_nombre">—</p>
            </div>
          </div>
        </div>

        <div id="ver-bloque-lider-inv" class="d-none">
          <h6 class="fw-semibold mb-2">
            <i class="bi bi-layers me-1"></i>
            Datos de líder de investigación
          </h6>
          <p class="text-muted mb-0">
            Este perfil solo maneja permisos de investigación.
          </p>
        </div>

        <div id="ver-bloque-aprendiz" class="d-none">
          <h6 class="fw-semibold mb-2">
            <i class="bi bi-mortarboard me-1"></i>
            Datos del aprendiz
          </h6>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Semillero</label>
              <p class="form-control-plaintext mb-0" data-field="ap_semillero_nombre">—</p>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nivel educativo</label>
              <p class="form-control-plaintext mb-0" data-field="nivel_educativo">—</p>
            </div>

            <div class="col-md-4">
              <label class="form-label">Vinculado al SENA</label>
              <p class="form-control-plaintext mb-0" data-field="vinculado_sena">—</p>
            </div>

            <div class="col-md-4">
              <label class="form-label">Ficha</label>
              <p class="form-control-plaintext mb-0" data-field="ficha">—</p>
            </div>

            <div class="col-md-4">
              <label class="form-label">Programa</label>
              <p class="form-control-plaintext mb-0" data-field="programa">—</p>
            </div>

            <div class="col-md-6">
              <label class="form-label">Institución (si NO está en el SENA)</label>
              <p class="form-control-plaintext mb-0" data-field="institucion">—</p>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <p class="form-control-plaintext mb-0" data-field="correo_institucional">—</p>
            </div>

            <div class="col-md-6">
              <label class="form-label">Contacto de emergencia</label>
              <p class="form-control-plaintext mb-0" data-field="contacto_nombre">—</p>
            </div>

            <div class="col-md-6">
              <label class="form-label">Celular de contacto</label>
              <p class="form-control-plaintext mb-0" data-field="contacto_celular">—</p>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>

    </div>
  </div>
</div>
