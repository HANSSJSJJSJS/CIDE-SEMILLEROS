<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content user-modal">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square"></i>
          Editar usuario
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formEditarUsuario" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="modal-body">

          <div class="card-section mb-3">
            <div class="user-modal-section-title">
              <i class="bi bi-person-badge"></i> Datos principales
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" id="edit_nombre"
                       class="form-control" required>
                <div class="invalid-feedback">Ingresa el nombre.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" id="edit_apellido"
                       class="form-control" required>
                <div class="invalid-feedback">Ingresa el apellido.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" name="email" id="edit_email"
                       class="form-control" required>
                <div class="invalid-feedback">Ingresa un correo válido.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Rol</label>
                @php
                  $rolesList = [
                    'ADMIN'               => 'Líder general',
                    'LIDER_SEMILLERO'     => 'Líder semillero',
                    'LIDER_INVESTIGACION' => 'Líder de investigación',
                    'APRENDIZ'            => 'Aprendiz',
                  ];
                @endphp
                <select class="form-select" id="edit_role" disabled>
                  @foreach($rolesList as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
                <small class="text-muted">El rol no se puede cambiar desde aquí.</small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Semillero</label>
                @php
                  $semillerosList = isset($semilleros) ? $semilleros : collect();
                @endphp
                <select class="form-select" id="edit_semillero" disabled>
                  <option value="">— Ninguno —</option>
                  @foreach($semillerosList as $s)
                    <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="card-section">
            <div class="user-modal-section-title">
              <i class="bi bi-shield-lock"></i> Cambio de contraseña
            </div>
            <p class="text-muted mb-2">
              Si no deseas cambiar la contraseña, deja el campo vacío.
            </p>

            <div class="col-md-6">
              <label class="form-label">Nueva contraseña</label>
              <div class="input-group">
                <input type="password" id="edit_password" name="password"
                       class="form-control"
                       placeholder="Dejar vacío para no cambiar">
                <button class="btn btn-outline-secondary btn-sm"
                        type="button"
                        data-toggle-pass="#edit_password">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-outline-success btn-sm"
                        type="button"
                        data-generate-pass="#edit_password">
                  <i class="bi bi-magic"></i>
                </button>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-user-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-user-primary">
            <i class="bi bi-check2-circle me-1"></i> Guardar cambios
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
