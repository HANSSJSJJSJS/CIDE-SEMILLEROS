{{-- MODAL EDITAR SEMILLERO --}}
<div class="modal fade" id="modalEditarSemillero" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <h5 class="modal-title">Editar semillero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      {{-- FORM --}}
      <form id="formEditarSemillero"
            method="POST"
            action="" {{-- lo rellena JS con data-update-url --}}
            class="needs-validation"
            novalidate
            autocomplete="off"
      >
        @csrf
        @method('PUT')

        <div class="modal-body">

          {{-- ============================
               DATOS BÁSICOS
          ============================= --}}
          <div class="row g-3 mb-3">

            {{-- Nombre --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nombre del semillero</label>
              <input type="text"
                     id="editNombre"
                     name="nombre"
                     class="form-control"
                     required>
              <div class="invalid-feedback">Ingrese el nombre del semillero.</div>
            </div>

            {{-- Línea --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Línea de investigación</label>
              <input type="text"
                     id="editLinea"
                     name="linea_investigacion"
                     class="form-control"
                     required>
              <div class="invalid-feedback">Ingrese la línea de investigación.</div>
            </div>

          </div>

          <hr class="my-3">

          {{-- ============================
               LÍDER ACTUAL
          ============================= --}}
          <div class="mb-2">
            <label class="form-label fw-semibold mb-0">Líder asignado actualmente</label>
            <small class="text-muted d-block">Datos del líder actual.</small>
          </div>

          <div class="row g-3 mb-2">
            <div class="col-md-6">
              <label class="form-label">Nombre completo</label>
              <input type="text"
                     id="liderNombreRO"
                     class="form-control"
                     value="—"
                     readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <input type="text"
                     id="liderCorreoRO"
                     class="form-control"
                     value="—"
                     readonly>
            </div>
          </div>

          <input type="hidden" id="editIdLider" name="id_lider_semi">

          <hr class="my-3">

          {{-- ============================
               CAMBIAR LÍDER — SOLO LISTA
          ============================= --}}
          <div class="mb-2">
            <label class="form-label fw-semibold mb-0">Cambiar líder (opcional)</label>
            <small class="text-muted d-block">Selecciona un líder libre o deja vacío.</small>
          </div>

          <div class="row g-2 align-items-center mb-3">

            {{-- LISTA DESPLEGABLE --}}
            <div class="col-12 col-md-9">
              <select id="selectLiderEditar" class="form-select">
                <option value="">-- Seleccionar líder disponible --</option>

                @foreach($lideresDisponibles as $l)
                  <option value="{{ $l->id_lider_semi }}"
                          data-nombre="{{ $l->nombre_completo }}"
                          data-correo="{{ $l->correo_institucional }}">
                    {{ $l->nombre_completo }} ({{ $l->correo_institucional }})
                  </option>
                @endforeach

              </select>
            </div>

            {{-- BOTÓN LIMPIAR --}}
            <div class="col-12 col-md-3 d-grid">
              <button type="button"
                      id="btnLimpiarLiderEditar"
                      class="btn btn-outline-secondary">
                Quitar líder
              </button>
            </div>

          </div>

          {{-- ============================
               NUEVO LÍDER SELECCIONADO
          ============================= --}}
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Nuevo líder seleccionado</label>
              <input type="text"
                     id="nuevoLiderNombreRO"
                     class="form-control"
                     value="—"
                     readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo institucional (nuevo)</label>
              <input type="text"
                     id="nuevoLiderCorreoRO"
                     class="form-control"
                     value="—"
                     readonly>
            </div>

          </div>

        </div>{{-- /modal-body --}}

        {{-- FOOTER --}}
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
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
