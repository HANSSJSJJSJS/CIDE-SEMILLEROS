<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content user-modal">

      {{-- =======================================================
           HEADER
      ======================================================== --}}
      <div class="modal-header">
        <div>
          <h5 class="modal-title">
            <i class="bi bi-person-plus"></i> Agregar usuario
          </h5>
          <div class="mt-1">
            {{-- Badge con el rol actual --}}
            <span id="rol-actual-label"
                  class="badge rounded-pill px-3 py-2"
                  style="background:#2d572c; font-size:0.9rem; display:none;">
            </span>
          </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      {{-- =======================================================
           FORM
      ======================================================== --}}
      <form id="formCrearUsuario"
            action="{{ route('admin.usuarios.store') }}"
            method="POST"
            class="needs-validation" novalidate>
        @csrf

        <div class="modal-body">

          {{-- =======================================================
               ÁREA GENERAL (ADMIN / LÍDERES)
          ======================================================== --}}
          <div id="area-general">

            {{-- NAV WIZARD GENERAL (ARRIBA) --}}
            <div id="user-steps-nav"
                 class="d-flex justify-content-between align-items-center mb-3 d-none">
              <span id="user-step-label" class="text-muted small">
                Paso 1 de 2
              </span>

              <div class="btn-group">
                <button type="button"
                        class="btn btn-outline-secondary btn-sm"
                        id="btn-user-prev">
                  <i class="bi bi-chevron-left"></i> Anterior
                </button>
                <button type="button"
                        class="btn btn-success btn-sm"
                        id="btn-user-next">
                  Siguiente <i class="bi bi-chevron-right"></i>
                </button>
              </div>
            </div>

            {{-- CARD DATOS BÁSICOS (PASOS 1 y 2) --}}
            <div class="card-section" id="card-datos-basicos">
              <div class="user-modal-section-title">
                <i class="bi bi-person-vcard"></i> Datos básicos
              </div>

              {{-- Paso 1 --}}
              <div class="user-step" data-step="1">
                <div class="row g-3">

                  {{-- Rol --}}
                  <div class="col-md-6">
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

                  <div class="col-md-6">
                    <label class="form-label">Correo <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="nombre@correo.com">
                    <div class="invalid-feedback">Ingresa un correo válido.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control">
                    <div class="invalid-feedback">Ingresa el nombre.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control">
                    <div class="invalid-feedback">Ingresa los apellidos.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="password" name="password" id="create_password"
                             class="form-control" placeholder="Mínimo 6 caracteres">
                      <button class="btn btn-outline-secondary btn-sm" type="button"
                              data-toggle-pass="#create_password">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button class="btn btn-outline-success btn-sm" type="button"
                              data-generate-pass="#create_password">
                        <i class="bi bi-magic"></i>
                      </button>
                    </div>
                  </div>

                </div>
              </div>

              {{-- Paso 2 --}}
              <div class="user-step d-none" data-step="2">
                <div class="row g-3">

                  <div class="col-md-4">
                    <label class="form-label">Tipo documento <span class="text-danger">*</span></label>
                    <select name="tipo_documento" class="form-select">
                      <option value="">Seleccionar…</option>
                      <option value="CC">Cédula ciudadanía</option>
                      <option value="TI">Tarjeta identidad</option>
                      <option value="CE">Cédula extranjería</option>
                      <option value="PAS">Pasaporte</option>
                      <option value="PEP">Permiso especial</option>
                      <option value="RC">Registro civil</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Número documento <span class="text-danger">*</span></label>
                    <input type="text" name="documento" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control">
                  </div>

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
                    <label class="form-label">Tipo RH</label>
                    <select name="tipo_rh" class="form-select">
                      <option value="">Seleccionar…</option>
                      <option>A+</option><option>A-</option>
                      <option>B+</option><option>B-</option>
                      <option>AB+</option><option>AB-</option>
                      <option>O+</option><option>O-</option>
                    </select>
                  </div>

                </div>
              </div>
            </div> {{-- /card datos básicos --}}

            {{-- LÍDER DE SEMILLERO (Paso 3 solo para ese rol) --}}
            <div class="card-section d-none" id="box-lider-semillero">
              <div class="user-modal-section-title">
                <i class="bi bi-people"></i> Datos del líder de semillero
              </div>

              <div class="user-step d-none" data-step="3">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Correo institucional</label>
                    <input type="email" name="ls_correo_institucional" class="form-control">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Semillero que lidera <span class="text-danger">*</span></label>
                    <select name="ls_semillero_id" class="form-select">
                      <option value="">Seleccionar…</option>
                      @foreach($semilleros as $s)
                        <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>

            {{-- LÍDER INVESTIGACIÓN (sin pasos extra) --}}
            <div class="card-section d-none" id="box-lider-investigacion">
              <div class="user-modal-section-title">
                <i class="bi bi-search-heart"></i> Líder de investigación
              </div>
              <p class="text-muted mb-0">
                Este rol no requiere pasos adicionales por ahora.
              </p>
            </div>

          </div> {{-- /area-general --}}



          {{-- =======================================================
               ÁREA APRENDIZ (SOLO 1 SECCIÓN, 8 PASOS)
          ======================================================== --}}
          <div id="area-aprendiz" class="d-none">

            <div class="card-section">
              <div class="user-modal-section-title">
                <i class="bi bi-mortarboard"></i> Registro del aprendiz
              </div>

              {{-- Paso 1: datos básicos --}}
              <div class="apr-step d-none" data-step="1">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Correo personal *</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="nombre@correo.com">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contraseña *</label>
                    <div class="input-group">
                      <input type="password" name="password" id="apr_password"
                             class="form-control" placeholder="Mínimo 6 caracteres">
                      <button class="btn btn-outline-secondary btn-sm" type="button"
                              data-toggle-pass="#apr_password">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button class="btn btn-outline-success btn-sm" type="button"
                              data-generate-pass="#apr_password">
                        <i class="bi bi-magic"></i>
                      </button>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="nombre" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="apellido" class="form-control">
                  </div>
                </div>
              </div>

              {{-- Paso 2: doc --}}
              <div class="apr-step d-none" data-step="2">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Tipo de documento *</label>
                    <select name="tipo_documento" class="form-select">
                      <option value="">Seleccionar…</option>
                      <option value="CC">Cédula de Ciudadanía</option>
                      <option value="TI">Tarjeta de Identidad</option>
                      <option value="CE">Cédula de Extranjería</option>
                      <option value="PAS">Pasaporte</option>
                      <option value="PEP">Permiso Especial</option>
                      <option value="RC">Registro Civil</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Número de documento *</label>
                    <input type="text" name="documento" class="form-control"
                           placeholder="Solo números">
                  </div>
                </div>
              </div>

              {{-- Paso 3: contacto --}}
              <div class="apr-step d-none" data-step="3">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control"
                           placeholder="Solo números">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Correo institucional</label>
                    <input type="email" name="correo_institucional" class="form-control"
                           placeholder="correo@misena.edu.co">
                  </div>
                </div>
              </div>

              {{-- Paso 4: género + RH --}}
              <div class="apr-step d-none" data-step="4">
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
                    <label class="form-label">Tipo RH</label>
                    <select name="tipo_rh" class="form-select">
                      <option value="">Seleccionar…</option>
                      <option>A+</option><option>A-</option>
                      <option>B+</option><option>B-</option>
                      <option>AB+</option><option>AB-</option>
                      <option>O+</option><option>O-</option>
                    </select>
                  </div>
                </div>
              </div>

              {{-- Paso 5: semillero + nivel educativo --}}
              <div class="apr-step d-none" data-step="5">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Semillero *</label>
                    <select name="semillero_id" class="form-select">
                      <option value="">Seleccionar…</option>
                      @foreach($semilleros as $s)
                        <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nivel educativo *</label>
                    <select name="nivel_educativo" class="form-select">
                      <option value="">Seleccionar…</option>
                      <option value="ARTICULACION_MEDIA_10_11">
                        Articulación con la media (10° - 11°)
                      </option>
                      <option value="TECNOACADEMIA_7_9">
                        Tecnoacademia (7° - 9°)
                      </option>
                      <option value="TECNICO">Técnico</option>
                      <option value="TECNOLOGO">Tecnólogo</option>
                      <option value="PROFESIONAL">Profesional</option>
                    </select>
                  </div>
                </div>
              </div>

              {{-- Paso 6: vinculado SENA --}}
              <div class="apr-step d-none" data-step="6">
                <label class="form-label">¿Vinculado al SENA?</label>
                <div class="d-flex gap-4 mt-1">
                  <label class="form-check">
                    <input type="radio" name="vinculado_sena" value="1"
                           class="form-check-input" checked>
                    <span class="form-check-label">Sí</span>
                  </label>
                  <label class="form-check">
                    <input type="radio" name="vinculado_sena" value="0"
                           class="form-check-input">
                    <span class="form-check-label">No</span>
                  </label>
                </div>
              </div>

              {{-- Paso 7: ficha/programa o institución --}}
              <div class="apr-step d-none" data-step="7" id="apr-sena">
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

              <div class="apr-step d-none" data-step="7" id="apr-no-sena">
                <label class="form-label">Institución educativa</label>
                <input type="text" name="institucion" class="form-control">
              </div>

              {{-- Paso 8: contacto de emergencia --}}
              <div class="apr-step d-none" data-step="8">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Contacto de emergencia</label>
                    <input type="text" name="contacto_nombre" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Celular del contacto</label>
                    <input type="text" name="contacto_celular" class="form-control">
                  </div>
                </div>
              </div>

            </div> {{-- /card-section aprendiz --}}
          </div> {{-- /area-aprendiz --}}

        </div> {{-- /modal-body --}}

        {{-- =======================================================
             NAV APRENDIZ ABAJO (ÚNICO JUEGO DE BOTONES)
        ======================================================== --}}
        <div id="apr-steps-nav"
             class="d-flex justify-content-between align-items-center px-3 py-2 d-none">
          <button type="button" id="btn-apr-prev" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-left"></i> Anterior
          </button>

          <div class="text-muted small" id="apr-step-label">Paso 1 de 8</div>

          <button type="button" id="btn-apr-next" class="btn btn-success btn-sm">
            Siguiente <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        {{-- =======================================================
             FOOTER
        ======================================================== --}}
        <div class="modal-footer">
          <button type="button" class="btn btn-user-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-user-primary">
            <i class="bi bi-check-circle me-1"></i> Guardar usuario
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
