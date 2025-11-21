{{-- resources/views/admin/usuarios/_modal_crear.blade.php --}}

<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-person-plus me-1 text-success"></i>
          Agregar usuario
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        {{-- Errores generales --}}
        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Revisa los campos:</strong>
            <ul class="mb-0 mt-1">
              @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
          </div>
        @endif

        <form id="form-usuario"
              action="{{ route('admin.usuarios.store') }}"
              method="POST"
              class="needs-validation"
              novalidate>
          @csrf

          <div class="row g-3">

            {{-- 1) Rol --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
              <select id="rol" name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">Seleccione...</option>
                <option value="ADMIN"                @selected(old('role')==='ADMIN')>Líder general</option>
                <option value="LIDER_INVESTIGACION"  @selected(old('role')==='LIDER_INVESTIGACION')>Líder de investigación</option>
                <option value="LIDER_SEMILLERO"      @selected(old('role')==='LIDER_SEMILLERO')>Líder semillero</option>
                <option value="APRENDIZ"             @selected(old('role')==='APRENDIZ')>Aprendiz</option>
              </select>
              <div class="invalid-feedback">Selecciona un rol.</div>
              @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- 2) Correo --}}
            <div id="box-correo" class="col-md-6 d-none">
              <label class="form-label fw-semibold">Correo <span class="text-danger">*</span></label>
              <input id="correo" type="email" name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}">
              <div class="invalid-feedback">Ingresa un correo válido.</div>
              <div class="field-note">Usa el institucional si aplica.</div>
              @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- ===== ADMIN / LIDER_INVESTIGACION ===== --}}
            <div id="box-admin" class="row g-3 d-none mt-2">
              <div class="col-12">
                <span class="section-label">
                  <i class="bi bi-shield-lock"></i> Líder general / investigación
                </span>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}">
                <div class="invalid-feedback">Ingresa el nombre.</div>
                @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Apellido</label>
                <input name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}">
                <div class="invalid-feedback">Ingresa el apellido.</div>
                @error('apellido') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                  <input type="password" id="pass-admin" name="password" class="form-control @error('password') is-invalid @enderror">
                  <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-admin">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-admin">
                    <i class="bi bi-magic"></i>
                  </button>
                </div>
                <div class="field-note">Mínimo 8 caracteres.</div>
                <div class="invalid-feedback">La contraseña es obligatoria.</div>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- ===== LÍDER SEMILLERO ===== --}}
            <div id="box-lider" class="row g-3 d-none mt-2">
              <div class="col-12">
                <span class="section-label"><i class="bi bi-mortarboard"></i> Líder semillero</span>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input name="nombre" class="form-control" value="{{ old('nombre') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Apellido</label>
                <input name="apellido" class="form-control" value="{{ old('apellido') }}">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Tipo documento</label>
                <select name="ls_tipo_documento" class="form-select @error('ls_tipo_documento') is-invalid @enderror">
                  <option value="">Seleccione...</option>
                  <option value="CC" @selected(old('ls_tipo_documento')==='CC')>CC</option>
                  <option value="CE" @selected(old('ls_tipo_documento')==='CE')>CE</option>
                </select>
                @error('ls_tipo_documento') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Documento</label>
                <input name="ls_documento" class="form-control" value="{{ old('ls_documento') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                  <input type="password" id="pass-lider" name="password" class="form-control">
                  <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-lider">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-lider">
                    <i class="bi bi-magic"></i>
                  </button>
                </div>
              </div>
            </div>

            {{-- ===== APRENDIZ ===== --}}
            {{-- (tu bloque completo de aprendiz tal cual lo tienes) --}}
            {!! '-- pega aquí tal cual tu bloque #box-aprendiz (no hace falta repetirlo para ahorrar espacio en el mensaje) --' !!}

          </div> {{-- row g-3 --}}

          <div class="mt-4 sticky-actions">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check2-circle me-1"></i> Guardar
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .brand-title{color:#2d572c}
  .section-label{
      font-size:.85rem;font-weight:600;color:#2d572c;
      background:#e9f2ea;border-radius:999px;
      padding:.2rem .6rem;display:inline-flex;gap:.4rem;align-items:center
  }
  .field-note{font-size:.8rem;color:#6c757d}
  .sticky-actions{display:flex;gap:.5rem;justify-content:flex-end}
</style>

@push('scripts')
<script src="{{ asset('js/admin/usuarios.js') }}"></script>
@endpush



@endpush
