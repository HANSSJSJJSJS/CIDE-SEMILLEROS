{{-- Modal crear usuario --}}
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <div class="d-flex flex-column flex-md-row w-100 justify-content-between align-items-md-center">
          <div>
            <h4 class="modal-title mb-1">
              <i class="bi bi-person-plus"></i>
              Agregar usuario
            </h4>
            <small class="text-muted">
              Complete la información del usuario paso a paso.
            </small>
          </div>

          <div class="text-md-end">
            <span class="small text-muted d-block">Rol actual</span>
            <span id="rol-actual-label"
                  class="badge bg-primary fs-5 px-3 py-2"
                  style="display:none;"></span>
          </div>
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

        <div class="modal-body" style="max-height: calc(100vh - 260px); overflow-y: auto;">

          {{-- ===========================================================
               SELECCIÓN DE ROL – SIEMPRE ARRIBA
          ============================================================ --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
              <h6 class="fw-semibold mb-2">
                <i class="bi bi-person-badge me-1"></i>
                Rol del usuario
              </h6>
              <div class="row justify-content-center">
                <div class="col-md-6">
                  <select name="role" id="select-role" class="form-select" required>
                    <option value="">Seleccionar…</option>
                    <option value="ADMIN">Líder general</option>
                    <option value="LIDER_SEMILLERO">Líder de semillero</option>
                    <option value="LIDER_INVESTIGACION">Líder de investigación</option>
                    <option value="APRENDIZ">Aprendiz</option>
                  </select>
                  <div class="invalid-feedback">Seleccione un rol.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- ===========================================================
               ÁREA GENERAL – PASOS COMUNES / LÍDER
          ============================================================ --}}
          <div id="area-general">

            {{-- PASO 1: DATOS BÁSICOS (TODOS LOS ROLES) --}}
            <div class="user-step" data-step="1">
              <div class="mb-2 text-center">
                <span class="text-muted small text-uppercase fw-semibold">
                  Paso 1 de 1
                </span>
              </div>

              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Datos básicos del usuario
                  </h6>

                  <div class="row g-3">

                    {{-- Tipo de documento --}}
                    <div class="col-md-4">
                      <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                      <select name="tipo_documento" class="form-select" required>
                        <option value="">Seleccione…</option>
                        <option value="CC">Cédula de ciudadanía</option>
                        <option value="TI">Tarjeta de identidad</option>
                        <option value="CE">Cédula de extranjería</option>
                        <option value="PASAPORTE">Pasaporte</option>
                        <option value="PERMISO ESPECIAL">Permiso especial</option>
                        <option value="REGISTRO CIVIL">Registro civil</option>
                      </select>
                      <div class="invalid-feedback">Seleccione un tipo de documento.</div>
                    </div>

                    {{-- Documento --}}
                    <div class="col-md-4">
                      <label class="form-label">Número de documento <span class="text-danger">*</span></label>
                      <input type="text" name="documento" class="form-control" required>
                      <div class="invalid-feedback">Ingrese el número de documento.</div>
                    </div>

                    {{-- Celular --}}
                    <div class="col-md-4">
                      <label class="form-label">Celular <span class="text-danger">*</span></label>
                      <input type="text" name="celular" class="form-control" required>
                      <div class="invalid-feedback">Ingrese el celular.</div>
                    </div>

                    {{-- Nombres --}}
                    <div class="col-md-4">
                      <label class="form-label">Nombres <span class="text-danger">*</span></label>
                      <input type="text" name="nombre" class="form-control" required>
                      <div class="invalid-feedback">Ingrese los nombres.</div>
                    </div>

                    {{-- Apellidos --}}
                    <div class="col-md-4">
                      <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                      <input type="text" name="apellido" class="form-control" required>
                      <div class="invalid-feedback">Ingrese los apellidos.</div>
                    </div>

                    {{-- Género --}}
                    <div class="col-md-4">
                      <label class="form-label">Género</label>
                      <select name="genero" class="form-select">
                        <option value="">Seleccionar…</option>
                        <option value="HOMBRE">Hombre</option>
                        <option value="MUJER">Mujer</option>
                        <option value="NO DEFINIDO">No definido</option>
                      </select>
                    </div>

                    {{-- Tipo de RH --}}
                    <div class="col-md-4">
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

                    {{-- Correo (login users.email) --}}
                    <div class="col-md-4">
                      <label class="form-label">Correo de acceso <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control"
                             placeholder="correo@ejemplo.com" required>
                      <div class="invalid-feedback">Ingrese un correo válido.</div>
                    </div>


                  </div>
                </div>
              </div>
            </div>

            {{-- PASO 2: SOLO LÍDER DE SEMILLERO --}}
            <div class="user-step d-none" data-step="2">
              <div class="mb-2 text-center">
                <span class="text-muted small text-uppercase fw-semibold">
                  Paso 2 de 2
                </span>
              </div>

              <div id="box-lider-semillero" class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-people me-1"></i>
                    Datos del líder de semillero
                  </h6>

                  <div class="row g-3">

                    {{-- Correo institucional (extra, NO login) --}}
                    <div class="col-md-6">
                      <label class="form-label">Correo institucional <span class="text-danger">*</span></label>
                      <input type="email" name="ls_correo_institucional" class="form-control"
                             placeholder="correo@misena.edu.co" required>
                      <div class="invalid-feedback">Ingrese el correo institucional.</div>
                    </div>

                    {{-- Semillero que lidera --}}
                    <div class="col-md-6">
                      <label class="form-label">Semillero que lidera</label>
                      <select name="ls_semillero_id" class="form-select">
                        <option value="">Ninguno (asignar después)</option>
                        @foreach($semillerosSinLider as $s)
                          <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted">
                        Si eliges “Ninguno”, podrás asignar el semillero luego desde
                        <strong>Gestión de semilleros</strong>.
                      </small>
                    </div>

                  </div>
                </div>
              </div>
            </div>

          </div>{{-- /area-general --}}

          {{-- ===========================================================
               WIZARD APRENDIZ – 4 PASOS
          ============================================================ --}}
          <div id="area-aprendiz" class="mt-4 d-none">
            <div class="card border-0 shadow-sm">
              <div class="card-body">

                {{-- Paso 1: Formación actual / SENA --}}
                <div class="apr-step" data-step="1">
                  <h6 class="fw-semibold mb-2">
                    <i class="bi bi-building me-1"></i>
                    Formación actual
                  </h6>
                  <p class="text-muted small mb-3">
                    ¿Actualmente está cursando algún programa o curso en el SENA?
                  </p>

                  <div class="mb-3">
                    <div class="d-flex gap-4">
                      <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="vinculado_sena"
                               id="vinculado_sena_si"
                               value="1"
                               checked>
                        <label class="form-check-label" for="vinculado_sena_si">Sí</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="vinculado_sena"
                               id="vinculado_sena_no"
                               value="0">
                        <label class="form-check-label" for="vinculado_sena_no">No</label>
                      </div>
                    </div>
                  </div>

                  {{-- SI está cursando en el SENA --}}
                  <div id="apr-sena">
                    <div class="row g-3">
                      <div class="col-md-4">
                        <label class="form-label">Nivel de Formación</label>
                        <select name="nivel_educativo" class="form-select">
                          <option value="">Seleccionar…</option>
                          <option value="ARTICULACION_MEDIA_10_11">
                            Articulación con la media – Décimo / Once
                          </option>
                            Tecnoacademia – Séptimo / Octavo / Noveno
                          </option>
                          <option value="TECNICO">Técnico</option>
                          <option value="TECNOLOGO">Tecnólogo</option>
                          <option value="PROFESIONAL">Profesional</option>
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label class="form-label">Ficha</label>
                        <input type="text" name="ficha" class="form-control">
                      </div>

                      <div class="col-md-4">
                        <label class="form-label">Programa</label>
                        <input type="text" name="programa" class="form-control">
                      </div>
                    </div>
                  </div>

                  {{-- NO está cursando en el SENA --}}
                  <div id="apr-no-sena" class="d-none mt-3">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Nivel educativo</label>
                        <select name="nivel_educativo" class="form-select">
                          <option value="">Seleccionar…</option>
                          <option value="TECNICO">Técnico</option>
                          <option value="TECNOLOGO">Tecnólogo</option>
                          <option value="PROFESIONAL">Profesional</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">¿A qué institución pertenece?</label>
                        <input type="text" name="institucion" class="form-control"
                               placeholder="Nombre de la institución">
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Paso 2: Correo institucional + semillero --}}
                <div class="apr-step d-none" data-step="2">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-flower3 me-1"></i>
                    Información académica del aprendiz
                  </h6>

                  <div class="row g-3">
                    {{-- Correo institucional (aprendices.correo_institucional) --}}
                    <div class="col-md-6">
                      <label class="form-label">Correo institucional <span class="text-danger">*</span></label>
                      <input type="email"
                             name="correo_institucional"
                             class="form-control"
                             placeholder="correo@misena.edu.co"
                             required>
                      <div class="invalid-feedback">Ingrese el correo institucional del aprendiz.</div>
                    </div>

                    {{-- Semillero --}}
                    <div class="col-md-6">
                      <label class="form-label">Semillero <span class="text-danger">*</span></label>
                      <select name="semillero_id" class="form-select" required>
                        <option value="">Seleccionar…</option>
                        @foreach($semilleros as $s)
                          <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">Seleccione el semillero del aprendiz.</div>
                    </div>
                  </div>
                </div>

                {{-- Paso 3: Contacto de emergencia --}}
                <div class="apr-step d-none" data-step="3">
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

                {{-- Paso 4: Revisión final --}}
                <div class="apr-step d-none" data-step="4">
                  <h6 class="fw-semibold mb-3">
                    <i class="bi bi-check2-circle me-1"></i>
                    Revisión final
                  </h6>
                  <p class="text-muted mb-1">
                    Verifique que los datos del aprendiz sean correctos.
                  </p>
                  <p class="mb-0">
                    Cuando esté seguro, haga clic en <strong>Guardar usuario</strong> para finalizar el registro.
                  </p>
                </div>

              </div>
            </div>
          </div>{{-- /area-aprendiz --}}

          {{-- ===========================================================
               NAVEGACIÓN DE PASOS (SIEMPRE ABAJO, CENTRADA)
          ============================================================ --}}
          <div id="user-steps-nav"
               class="d-flex justify-content-center align-items-center gap-3 mt-3 d-none">
            <button type="button" id="btn-user-prev" class="btn btn-outline-secondary">
              Anterior
            </button>
            <span class="text-muted small" id="user-step-label"></span>
            <button type="button" id="btn-user-next" class="btn btn-success">
              Siguiente
            </button>
          </div>

          <div id="apr-steps-nav"
               class="d-flex justify-content-center align-items-center gap-3 mt-3 d-none">
            <button type="button" id="btn-apr-prev" class="btn btn-outline-secondary">
              Anterior
            </button>
            <span class="text-muted small" id="apr-step-label"></span>
            <button type="button" id="btn-apr-next" class="btn btn-success">
              Siguiente
            </button>
          </div>

        </div>{{-- /modal-body --}}

        {{-- FOOTER (DENTRO DEL FORM) --}}
        <div class="modal-footer justify-content-center gap-2">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success" id="btn-save-user">
            <i class="bi bi-check-circle me-1"></i>
            Guardar usuario
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
