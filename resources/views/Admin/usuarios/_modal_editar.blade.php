{{-- MODAL EDITAR USUARIO --}}
@php
    // $usuario viene desde el include en index.blade.php
@endphp

<div class="modal fade"
     id="modalEditarUsuario{{ $usuario->id }}"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content user-modal">

      {{-- HEADER --}}
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square"></i>
          Editar usuario
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      {{-- FORM --}}
      <form method="POST"
            action="{{ route('admin.usuarios.update', $usuario->id) }}"
            class="needs-validation"
            novalidate>

        @csrf
        @method('PUT')

        <div class="modal-body">

          {{-- DATOS BÁSICOS --}}
          <div class="card-section">
            <div class="user-modal-section-title">
              <i class="bi bi-person-badge"></i> Datos básicos
            </div>

            <div class="row g-3">

              {{-- ROL --}}
              <div class="col-md-4">
                <label class="form-label">Rol <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                  @foreach(($roles ?? []) as $value => $label)
                    <option value="{{ $value }}"
                      @selected(old('role', $usuario->role) === $value)>
                      {{ $label }}
                    </option>
                  @endforeach
                </select>
                <div class="invalid-feedback">Elige un rol.</div>
              </div>

              {{-- CORREO --}}
              <div class="col-md-4">
                <label class="form-label">Correo <span class="text-danger">*</span></label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email', $usuario->email) }}"
                       required>
                <div class="invalid-feedback">Ingresa un correo válido.</div>
              </div>

              {{-- NOMBRE --}}
              <div class="col-md-4">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $usuario->name) }}"
                       required>
                <div class="invalid-feedback">Ingresa el nombre.</div>
              </div>

              {{-- APELLIDOS --}}
              <div class="col-md-4">
                <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                <input type="text"
                       name="apellidos"
                       class="form-control"
                       value="{{ old('apellidos', $usuario->apellidos) }}"
                       required>
                <div class="invalid-feedback">Ingresa los apellidos.</div>
              </div>

              {{-- SEMILLERO --}}
              <div class="col-md-4">
                <label class="form-label">Semillero</label>
                <select name="semillero_id" class="form-select">
                  <option value="">Sin semillero</option>
                  @foreach(($semilleros ?? collect()) as $s)
                    <option value="{{ $s->id_semillero }}"
                      @selected(old('semillero_id', $usuario->semillero_id) == $s->id_semillero)>
                      {{ $s->nombre }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- CONTRASEÑA OPCIONAL --}}
              <div class="col-md-4">
                <label class="form-label">Nueva contraseña (opcional)</label>
                <div class="input-group">
                  <input type="password"
                         name="password"
                         class="form-control"
                         minlength="6"
                         placeholder="Deja vacío para no cambiar">
                  <button class="btn btn-outline-secondary btn-sm"
                          type="button"
                          data-toggle-pass="input[name='password']">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-outline-success btn-sm"
                          type="button"
                          data-generate-pass="input[name='password']">
                    <i class="bi bi-magic"></i>
                  </button>
                </div>
                <small class="text-muted">
                  Si lo dejas vacío, la contraseña no se modificará.
                </small>
              </div>

            </div>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer">
          <button type="button" class="btn btn-user-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>

          <button type="submit" class="btn btn-user-primary">
            <i class="bi bi-check-circle me-1"></i> Guardar cambios
          </button>
        </div>

      </form>

    </div>
  </div>

</div>
