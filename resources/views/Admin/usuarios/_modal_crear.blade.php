{{-- Modal crear usuario --}}
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <div class="d-flex flex-column">
          <h5 class="modal-title mb-1">
            <i class="bi bi-person-plus"></i>
            Agregar usuario
          </h5>
          <small class="text-muted">
            Rol actual:
            <span id="rol-actual-label"
                  class="badge bg-secondary ms-1"
                  style="display:none;">
            </span>
          </small>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      {{-- FORM --}}
      <form id="formCrearUsuario"
            method="POST"
            action="{{ route('admin.usuarios.store') }}"
            class="needs-validation"
            novalidate
            autocomplete="off">
        @csrf

        <div class="modal-body">

          {{-- ===========================================================
               ÁREA GENERAL (ADMIN, LÍDER SEMILLERO, LÍDER INVESTIGACIÓN)
          ============================================================ --}}
          <div id="area-general">

            {{-- PASO 1: DATOS BÁSICOS --}}
            <div class="user-step" data-step="1">
              <div class="mb-3">
                <span class="text-muted small">Paso 1 de 2</span>
              </div>

              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Datos básicos
                  </h6>

                  <div class="row g-3">
                    {{-- Rol --}}
                    <div class="col-md-3">
                      <label class="form-label">Rol <span class="text-danger">*</span></label>
                      <select name="role" id="select-role" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        <option value="ADMIN">Líder general</option>
                        <option value="LIDER_SEMILLERO">Líder de semillero</option>
                        <option value="LIDER_INVESTIGACION">Líder de investigación</option>
                        <option value="APRENDIZ">Aprendiz</option>
                      </select>
                      <div class="invalid-feedback">Selecciona un rol.</div>
                    </div>

                    {{-- Correo --}}
                    <div class="col-md-3">
                      <label class="form-label">Correo <span class="text-danger">*</span></label>
                      <input type="email"
                             name="email"
                             class="form-control"
                             placeholder="nombre@correo.com"
                             required>
                      <div class="invalid-feedback">Ingresa un correo válido.</div>
                    </div>

                    {{-- Nombre --}}
                    <div class="col-md-3">
                      <label class="form-label">Nombre <span class="text-danger">*</span></label>
                      <input type="text"
                             name="nombre"
                             class="form-control"
                             required>
                      <div class="invalid-feedback">Ingresa el nombre.</div>
                    </div>

                    {{-- Apellido --}}
                    <div class="col-md-3">
                      <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                      <input type="text"
                             name="apellido"
                             class="form-control"
                             required>
                      <div class="invalid-feedback">Ingresa los apellidos.</div>
                    </div>

                    {{-- Contraseña --}}
                    <div class="col-md-4">
                      <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <input type="password"
                               name="password"
                               id="create_password"
                               class="form-control"
                               minlength="6"
                               placeholder="Mínimo 6 caracteres"
                               required>
                        <button class="btn btn-outline-secondary"
                                type="button"
                                data-toggle-pass="#create_password">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-success"
                                type="button"
                                data-generate-pass="#create_password">
                          <i class="bi bi-magic"></i>
                        </button>
                      </div>
                      <small class="text-muted">
                        La contraseña se comunicará por fuera del sistema.
                      </small>
                      <div class="invalid-feedback">Ingresa una contraseña válida.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- PASO 2: (ADMIN / LÍDER INVESTIGACIÓN) --}}
            <div class="user-step d-none" data-step="2">
              <div class="mb-3">
                <span class="text-muted small">Paso 2 de 2</span>
              </div>

              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <h6 class="fw-semibold mb-2">
                    <i class="bi bi-person-badge me-1"></i>
                    Información adicional
                  </h6>
                  <p class="text-muted mb-0">
                    Este rol no requiere información adicional por ahora.  
                    Haz clic en <strong>Guardar usuario</strong> cuando termines.
                  </p>
                </div>
              </div>
            </div>

            {{-- PASO 3: SOLO LÍDER DE SEMILLERO --}}
            <div class="user-step d-none" data-step="3">
              <div class="mb-3">
                <span class="text-muted small">Paso 3 de 3</span>
              </div>

              <div id="box-lider-semillero" class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-people me-1"></i>
                    Datos del líder de semillero
                  </h6>

                  <div class="row g-3">
                    {{-- Correo institucional líder --}}
                    <div class="col-md-6">
                      <label class="form-label">Correo institucional</label>
                      <input type="email"
                             name="ls_correo_institucional"
                             class="form-control"
                             placeholder="correo@misena.edu.co">
                    </div>

                    {{-- Semillero que lidera --}}
                    <div class="col-md-6">
                      <label class="form-label">Semillero que lidera <span class="text-danger">*</span></label>
                      <select name="ls_semillero_id" class="form-select">
                        <option value="">Seleccionar…</option>
                        @foreach($semilleros as $s)
                          <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">Selecciona el semillero que lidera.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>{{-- /area-general --}}

          {{-- ===========================================================
               ÁREA APRENDIZ – WIZARD 8 PASOS
          ============================================================ --}}
          <div id="area-aprendiz" class="mt-4 d-none">

            <div class="card border-0 shadow-sm">
              <div class="card-body">

                {{-- Paso 1: Documento --}}
                <div class="apr-step" data-step="1">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    Registro del aprendiz
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                      <select name="tipo_documento" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="CC">Cédula de ciudadanía</option>
                        <option value="TI">Tarjeta de identidad</option>
                        <option value="CE">Cédula de extranjería</option>
                        <option value="PAS">Pasaporte</option>
                        <option value="PEP">Permiso especial</option>
                        <option value="RC">Registro civil</option>
                      </select>
                      <div class="invalid-feedback">Selecciona el tipo de documento.</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Número de documento <span class="text-danger">*</span></label>
                      <input type="text" name="documento" class="form-control">
                      <div class="invalid-feedback">Ingresa el número de documento.</div>
                    </div>
                  </div>
                </div>

                {{-- Paso 2: Contacto principal --}}
                <div class="apr-step d-none" data-step="2">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-telephone me-1"></i>
                    Contacto principal
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Celular</label>
                      <input type="text" name="celular" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo institucional</label>
                      <input type="email"
                             name="correo_institucional"
                             class="form-control"
                             placeholder="correo@misena.edu.co">
                    </div>
                  </div>
                </div>

                {{-- Paso 3: Datos personales --}}
                <div class="apr-step d-none" data-step="3">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-person-vcard me-1"></i>
                    Datos personales
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Género</label>
                      <select name="genero" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="HOMBRE">Hombre</option>
                        <option value="MUJER">Mujer</option>
                        <option value="NO DEFINIDO">No definido</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Tipo de RH</label>
                      <select name="tipo_rh" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                      </select>
                    </div>
                  </div>
                </div>

                {{-- Paso 4: Nivel educativo --}}
                <div class="apr-step d-none" data-step="4">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-mortarboard me-1"></i>
                    Nivel educativo
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-12">
                      <label class="form-label">Nivel educativo <span class="text-danger">*</span></label>
                      <select name="nivel_educativo" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="ARTICULACION_MEDIA_10_11">
                          Articulación con la media – Décimo 10 / Once 11
                        </option>
                        <option value="TECNOACADEMIA_7_9">
                          Tecnoacademia – Séptimo 7 / Octavo 8 / Noveno 9
                        </option>
                        <option value="TECNICO">Técnico</option>
                        <option value="TECNOLOGO">Tecnólogo</option>
                        <option value="PROFESIONAL">Profesional</option>
                      </select>
                      <div class="invalid-feedback">Selecciona el nivel educativo.</div>
                    </div>
                  </div>
                </div>

                {{-- Paso 5: Semillero --}}
                <div class="apr-step d-none" data-step="5">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-flower3 me-1"></i>
                    Semillero
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-12">
                      <label class="form-label">Semillero <span class="text-danger">*</span></label>
                      <select name="semillero_id" class="form-select">
                        <option value="">Seleccionar…</option>
                        @foreach($semilleros as $s)
                          <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">Selecciona el semillero del aprendiz.</div>
                    </div>
                  </div>
                </div>

                {{-- Paso 6: Vinculación SENA --}}
                <div class="apr-step d-none" data-step="6">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-building me-1"></i>
                    Vinculación al SENA
                  </h6>

                  <div class="mb-3">
                    <label class="form-label d-block">¿Vinculado al SENA?</label>
                    <div class="d-flex gap-4">
                      <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="vinculado_sena"
                               id="vinculado_sena_si"
                               value="1"
                               checked>
                        <label class="form-check-label" for="vinculado_sena_si">
                          Sí
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="vinculado_sena"
                               id="vinculado_sena_no"
                               value="0">
                        <label class="form-check-label" for="vinculado_sena_no">
                          No
                        </label>
                      </div>
                    </div>
                  </div>

                  {{-- Si está vinculado al SENA --}}
                  <div id="apr-sena">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Ficha</label>
                        <input type="text" name="ficha" class="form-control">
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">Programa</label>
                        <input type="text" name="programa" class="form-control">
                      </div>
                    </div>
                  </div>

                  {{-- Si NO está vinculado al SENA --}}
                  <div id="apr-no-sena" class="d-none mt-3">
                    <label class="form-label">Institución</label>
                    <input type="text" name="institucion" class="form-control">
                  </div>
                </div>

                {{-- Paso 7: Contacto de emergencia --}}
                <div class="apr-step d-none" data-step="7">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Contacto de emergencia
                  </h6>

                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Nombre del contacto</label>
                      <input type="text" name="contacto_nombre" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Celular del contacto</label>
                      <input type="text" name="contacto_celular" class="form-control">
                    </div>
                  </div>
                </div>

                {{-- Paso 8: Revisión --}}
                <div class="apr-step d-none" data-step="8">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-check2-circle me-1"></i>
                    Revisión final
                  </h6>

                  <p class="text-muted mb-1">
                    Verifica que los datos del aprendiz sean correctos.
                  </p>
                  <p class="mb-0">
                    Cuando estés seguro, haz clic en <strong>Guardar usuario</strong> para finalizar el registro.
                  </p>
                </div>

              </div>
            </div>
          </div>{{-- /area-aprendiz --}}

          {{-- ===========================================================
               NAVEGACIÓN DE PASOS (SIEMPRE ABAJO)
          ============================================================ --}}
          {{-- Wizard general (Admin / Líderes) --}}
          <div id="user-steps-nav"
               class="d-flex justify-content-between align-items-center mt-4 d-none">
            <button type="button" id="btn-user-prev" class="btn btn-outline-secondary">
              Anterior
            </button>

            <span id="user-step-label" class="text-muted small"></span>

            <button type="button" id="btn-user-next" class="btn btn-success">
              Siguiente
            </button>
          </div>

          {{-- Wizard Aprendiz --}}
          <div id="apr-steps-nav"
               class="d-flex justify-content-between align-items-center mt-4 d-none">
            <button type="button" id="btn-apr-prev" class="btn btn-outline-secondary">
              Anterior
            </button>

            <span id="apr-step-label" class="text-muted small"></span>

            <button type="button" id="btn-apr-next" class="btn btn-success">
              Siguiente
            </button>
          </div>

        </div>{{-- /modal-body --}}

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success" id="btn-save-user" disabled>
            <i class="bi bi-check-circle me-1"></i>
            Guardar usuario
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
