{{-- Modal: Crear / Editar Usuario --}}
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold" id="modalNuevoUsuarioLabel">Registrar usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formNuevoUsuario" method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        <div class="modal-body bg-light">
          <div class="container-fluid">

            {{-- Rol --}}
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <label class="form-label fw-semibold mb-1">Rol</label>
                <select name="role" id="selectRol" class="form-select" required>
                  <option value="">Seleccione un rol...</option>
                  <option value="ADMIN">Administrador</option>
                  <option value="LIDER_GENERAL">Líder General</option>
                  <option value="LIDER_SEMILLERO">Líder de Semillero</option>
                  <option value="APRENDIZ">Aprendiz</option>
                </select>
              </div>
            </div>

            {{-- Comunes --}}
            <div id="commonFields" class="d-none">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Nombre</label>
                      <input type="text" name="nombre" class="form-control" placeholder="María">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Apellidos</label>
                      <input type="text" name="apellido" class="form-control" placeholder="Gómez Pérez">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo (login)</label>
                      <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                      <small id="emailHint" class="text-muted"></small>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contraseña</label>
                      <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Por rol --}}

            {{-- Líder General: sin extras --}}
            <div class="role-fields d-none" data-role="LIDER_GENERAL"></div>

            {{-- Líder Semillero --}}
            <div class="role-fields d-none" data-role="LIDER_SEMILLERO">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ls_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ls_documento" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Aprendiz --}}
            <div class="role-fields d-none" data-role="APRENDIZ">
              <div class="card border-0 shadow-sm mb-1">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Ficha</label>
                      <input type="text" name="ap_ficha" class="form-control">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Programa</label>
                      <input type="text" name="ap_programa" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ap_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ap_documento" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Celular</label>
                      <input type="text" name="ap_celular" class="form-control">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Correo institucional</label>
                      <input type="email" name="ap_correo_institucional" class="form-control" placeholder="usuario@sena.edu.co">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contacto emergencia (nombre)</label>
                      <input type="text" name="ap_contacto_nombre" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Celular de contacto</label>
                      <input type="text" name="ap_contacto_celular" class="form-control">
                    </div>

                    {{-- Personal = login (oculto) --}}
                    <input type="hidden" name="ap_correo_personal">
                  </div>
                </div>
              </div>
            </div>

            {{-- Admin: sin extras --}}
            <div class="role-fields d-none" data-role="ADMIN"></div>

          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>
