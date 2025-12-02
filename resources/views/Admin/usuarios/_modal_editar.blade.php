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
                       value="{{ old('name', $usuario->nombre) }}"
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

              {{-- TIPO DOCUMENTO --}}
              <div class="col-md-4">
                <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                @php
                  $tiposDoc = [
                    'CC'              => 'Cédula de ciudadanía',
                    'TI'              => 'Tarjeta de identidad',
                    'CE'              => 'Cédula de extranjería',
                    'PASAPORTE'       => 'Pasaporte',
                    'PERMISO ESPECIAL'=> 'Permiso especial',
                    'REGISTRO CIVIL'  => 'Registro civil',
                  ];
                  $tipoActual = old('tipo_documento', $usuario->tipo_documento);
                @endphp
                <select name="tipo_documento" class="form-select" required>
                  <option value="">Seleccione…</option>
                  @foreach($tiposDoc as $value => $text)
                    <option value="{{ $value }}" @selected($tipoActual === $value)>
                      {{ $text }}
                    </option>
                  @endforeach
                </select>
                <div class="invalid-feedback">Selecciona el tipo de documento.</div>
              </div>

              {{-- DOCUMENTO --}}
              <div class="col-md-4">
                <label class="form-label">Documento <span class="text-danger">*</span></label>
                <input type="text"
                       name="documento"
                       class="form-control"
                       value="{{ old('documento', $usuario->documento) }}"
                       required>
                <div class="invalid-feedback">Ingresa el número de documento.</div>
              </div>

              {{-- CELULAR --}}
              <div class="col-md-4">
                <label class="form-label">Celular</label>
                <input type="text"
                       name="celular"
                       class="form-control"
                       value="{{ old('celular', $usuario->celular) }}">
              </div>

              {{-- GÉNERO --}}
              <div class="col-md-4">
                <label class="form-label">Género</label>
                @php
                  $generoActual = old('genero', $usuario->genero);
                @endphp
                <select name="genero" class="form-select">
                  <option value="">Sin especificar</option>
                  <option value="HOMBRE" @selected($generoActual === 'HOMBRE')>Hombre</option>
                  <option value="MUJER" @selected($generoActual === 'MUJER')>Mujer</option>
                  <option value="NO DEFINIDO" @selected($generoActual === 'NO DEFINIDO')>No definido</option>
                </select>
              </div>

              {{-- TIPO RH --}}
              <div class="col-md-4">
                <label class="form-label">Tipo RH</label>
                @php
                  $rhActual = old('tipo_rh', $usuario->tipo_rh);
                  $tiposRh  = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                @endphp
                <select name="tipo_rh" class="form-select">
                  <option value="">Sin especificar</option>
                  @foreach($tiposRh as $rh)
                    <option value="{{ $rh }}" @selected($rhActual === $rh)>{{ $rh }}</option>
                  @endforeach
                </select>
              </div>

              {{-- SEMILLERO (para aprendiz / líder semillero, etc.) --}}
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

          {{-- Aquí podrías agregar secciones extra para:
               - Datos de líder de semillero (correo institucional, semillero)
               - Datos de aprendiz (ficha, programa, nivel_educativo, etc.)
             si quieres editar también el perfil. --}}

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
