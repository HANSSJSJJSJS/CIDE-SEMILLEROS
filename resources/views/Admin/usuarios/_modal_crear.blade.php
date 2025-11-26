<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content user-modal">

      {{-- HEADER --}}
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-person-plus"></i>
          Agregar usuario
        </h5> 
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      {{-- FORM --}}
      <form id="formCrearUsuario" method="POST"
            action="{{ route('admin.usuarios.store') }}"
            class="needs-validation" novalidate>
        @csrf

        <div class="modal-body">

          {{-- ROL + EMAIL --}}
          <div class="card-section">
            <div class="user-modal-section-title">
              <i class="bi bi-person-badge"></i> Datos básicos
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Rol <span class="text-danger">*</span></label>
                <select name="role" id="select-role" class="form-select" required>
                  <option value="">Seleccionar…</option>
                  <option value="ADMIN">Líder general</option>
                  <option value="LIDER_SEMILLERO">Líder semillero</option>
                  <option value="LIDER_INVESTIGACION">Líder de investigación</option>
                  <option value="APRENDIZ">Aprendiz</option>
                </select>
                <div class="invalid-feedback">Elige un rol.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Correo <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control"
                       placeholder="nombre@correo.com" required>
                <div class="invalid-feedback">Ingresa un correo válido.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control" required>
                <div class="invalid-feedback">Ingresa el nombre.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Apellido <span class="text-danger">*</span></label>
                <input type="text" name="apellido" class="form-control" required>
                <div class="invalid-feedback">Ingresa el apellido.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" name="password" id="create_password"
                         class="form-control" required minlength="6"
                         placeholder="Mínimo 6 caracteres">
                  <button class="btn btn-outline-secondary btn-sm"
                          type="button"
                          data-toggle-pass="#create_password">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-outline-success btn-sm"
                          type="button"
                          data-generate-pass="#create_password">
                    <i class="bi bi-magic"></i>
                  </button>
                </div>
                <small class="text-muted">Se enviará o comunicará por fuera del sistema.</small>
                <div class="invalid-feedback">Ingresa una contraseña válida.</div>
              </div>
            </div>
          </div>

          {{-- LÍDER SEMILLERO --}}
         <div id="box-lider-semillero" class="card-section d-none">
          <div class="user-modal-section-title">
            <i class="bi bi-people"></i> Datos del líder de semillero
          </div>

          <div class="row g-3">
            {{-- Tipo documento --}}
            <div class="col-md-4">
              <label class="form-label">Tipo documento <span class="text-danger">*</span></label>
              <select name="ls_tipo_documento" class="form-select">
                <option value="">Seleccionar…</option>
                <option value="CC">Cédula de ciudadanía</option>
                <option value="TI">Tarjeta de identidad</option>
                <option value="CE">Cédula de extranjería</option>
                <option value="PAS">Pasaporte</option>
              </select>
              <div class="invalid-feedback">Selecciona un tipo de documento.</div>
            </div>

    {{-- Número de documento --}}
    <div class="col-md-4">
      <label class="form-label">Número de documento <span class="text-danger">*</span></label>
      <input type="text" name="ls_documento" class="form-control">
      <div class="invalid-feedback">Ingresa el número de documento.</div>
    </div>
  </div>
</div>


          {{-- LÍDER INVESTIGACIÓN --}}
          <div id="box-lider-investigacion" class="card-section d-none">
            <div class="user-modal-section-title">
              <i class="bi bi-search-heart"></i> Líder de investigación
            </div>
            <p class="text-muted mb-0">
              Este rol no requiere información adicional por ahora.
            </p>
          </div>

          {{-- APRENDIZ --}}
       
<div id="box-aprendiz" class="card-section d-none">
  <div class="user-modal-section-title">
    <i class="bi bi-mortarboard"></i> Datos del aprendiz
  </div>

  <div class="row g-3 mb-3">

    {{-- Tipo documento --}}
    <div class="col-md-4">
      <label class="form-label">Tipo documento <span class="text-danger">*</span></label>
      <select name="tipo_documento" class="form-select" required>
        <option value="">Seleccionar…</option>
        <option value="CC">Cédula de Ciudadanía</option>
        <option value="TI">Tarjeta de Identidad</option>
        <option value="CE">Cédula de Extranjería</option>
      </select>
      <div class="invalid-feedback">Selecciona un tipo de documento.</div>
    </div>

    {{-- Documento --}}
    <div class="col-md-4">
      <label class="form-label">Número de documento <span class="text-danger">*</span></label>
      <input type="text" name="documento" class="form-control" required>
      <div class="invalid-feedback">Ingresa el número de documento.</div>
    </div>

    {{-- Celular --}}
    <div class="col-md-4">
      <label class="form-label">Celular</label>
      <input type="text" name="celular" class="form-control">
    </div>

  </div>

  <div class="row g-3 mb-3">

    {{-- Semillero --}}
    <div class="col-md-6">
      <label class="form-label">Semillero <span class="text-danger">*</span></label>
      <select name="semillero_id" class="form-select" required>
        <option value="">Seleccionar…</option>
        @foreach($semilleros as $s)
          <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
        @endforeach
      </select>
      <div class="invalid-feedback">Selecciona un semillero.</div>
    </div>

    {{-- Correo institucional --}}
    <div class="col-md-6">
      <label class="form-label">Correo institucional</label>
      <input type="email" name="correo_institucional" class="form-control">
    </div>

  </div>

  {{-- Vinculado Sena --}}
  <div class="mb-3">
    <label class="form-label">¿Vinculado al SENA?</label>
    <div class="d-flex gap-4">
      <label class="form-check">
       <input class="form-check-input" type="radio"
       name="vinculado_sena" value="1" checked>
        <span class="form-check-label">Sí</span>
      </label>

      <label class="form-check">
       <input class="form-check-input" type="radio"
       name="vinculado_sena" value="0">
        <span class="form-check-label">No</span>
      </label>
    </div>
  </div>

  {{-- Ficha + Programa --}}
  <div id="box-aprendiz-sena" class="row g-3 mb-3">
    <div class="col-md-6">
      <label class="form-label">Ficha</label>
      <input type="text" name="ficha" class="form-control">
    </div>

    <div class="col-md-6">
      <label class="form-label">Programa</label>
      <input type="text" name="programa" class="form-control">
    </div>
  </div>

  {{-- Otra institución --}}
  <div id="box-aprendiz-otra" class="mb-2 d-none">
    <label class="form-label">Institución</label>
    <input type="text" name="institucion" class="form-control">
  </div>

</div>


        <div class="modal-footer">
          <button type="button" class="btn btn-user-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-user-primary">
            <i class="bi bi-check-circle me-1"></i> Guardar
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
