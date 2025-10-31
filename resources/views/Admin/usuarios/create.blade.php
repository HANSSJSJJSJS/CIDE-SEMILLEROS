@extends('layouts.admin')

@section('content')
<div class="container mt-4">
  <h3 class="fw-bold mb-4" style="color:#2d572c;">Agregar usuario</h3>

  <form id="form-usuario" action="{{ route('admin.usuarios.store') }}" method="POST" novalidate>
    @csrf

    <div class="row g-3">

      {{-- ====== 1) ROL (primero) ====== --}}
      <div class="col-md-6">
        <label class="form-label fw-semibold">Rol</label>
        <select id="rol" name="role" class="form-select" required>
          <option value="">Seleccione...</option>
          <option value="ADMIN"           {{ old('role')==='ADMIN' ? 'selected' : '' }}>Líder general</option>
          <option value="LIDER_SEMILLERO" {{ old('role')==='LIDER_SEMILLERO' ? 'selected' : '' }}>Líder semillero</option>
          <option value="APRENDIZ"        {{ old('role')==='APRENDIZ' ? 'selected' : '' }}>Aprendiz</option>
        </select>
        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      {{-- ====== 2) Correo (solo si hay rol) ====== --}}
      <div id="box-correo" class="col-md-6 d-none">
        <label class="form-label fw-semibold">Correo</label>
        <input id="correo" type="email" name="email" class="form-control" value="{{ old('email') }}">
        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      {{-- ====== 3) Bloque ADMIN (Líder general) ====== --}}
      <div id="box-admin" class="row g-3 d-none">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre</label>
          <input name="nombre" class="form-control" value="{{ old('nombre') }}">
          @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Apellido</label>
          <input name="apellido" class="form-control" value="{{ old('apellido') }}">
          @error('apellido') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Contraseña</label>
          <input type="password" name="password" class="form-control">
          @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- ====== 4) Bloque LÍDER SEMILLERO ====== --}}
      <div id="box-lider" class="row g-3 d-none">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre</label>
          <input name="nombre" class="form-control" value="{{ old('nombre') }}">
          @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Apellido</label>
          <input name="apellido" class="form-control" value="{{ old('apellido') }}">
          @error('apellido') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Tipo documento</label>
          <input name="ls_tipo_documento" class="form-control" value="{{ old('ls_tipo_documento') }}" placeholder="CC / TI / CE">
          @error('ls_tipo_documento') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Documento</label>
          <input name="ls_documento" class="form-control" value="{{ old('ls_documento') }}">
          @error('ls_documento') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Contraseña</label>
          <input type="password" name="password" class="form-control">
          @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- ====== 5) Bloque APRENDIZ ====== --}}
      <div id="box-aprendiz" class="row g-3 d-none">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre</label>
          <input name="nombre" class="form-control" value="{{ old('nombre') }}">
          @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Apellido</label>
          <input name="apellido" class="form-control" value="{{ old('apellido') }}">
          @error('apellido') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Ficha</label>
          <input name="ap_ficha" class="form-control" value="{{ old('ap_ficha') }}">
          @error('ap_ficha') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Programa</label>
          <input name="ap_programa" class="form-control" value="{{ old('ap_programa') }}">
          @error('ap_programa') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Tipo documento</label>
          <input name="ap_tipo_documento" class="form-control" value="{{ old('ap_tipo_documento') }}" placeholder="CC / TI / CE">
          @error('ap_tipo_documento') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Documento</label>
          <input name="ap_documento" class="form-control" value="{{ old('ap_documento') }}">
          @error('ap_documento') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Celular</label>
          <input name="ap_celular" class="form-control" value="{{ old('ap_celular') }}">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Correo institucional</label>
          <input name="ap_correo_institucional" type="email" class="form-control" value="{{ old('ap_correo_institucional') }}">
          @error('ap_correo_institucional') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Contraseña</label>
          <input type="password" name="password" class="form-control">
          @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Contacto nombre</label>
          <input name="ap_contacto_nombre" class="form-control" value="{{ old('ap_contacto_nombre') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Contacto celular</label>
          <input name="ap_contacto_celular" class="form-control" value="{{ old('ap_contacto_celular') }}">
        </div>
      </div>

      {{-- Errores generales --}}
      @if ($errors->any())
        <div class="col-12">
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

    </div>

    <div class="mt-3 d-flex gap-2">
      <button type="submit" class="btn btn-success">Guardar</button>
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));

  const rol         = $('#rol');
  const correoBox   = $('#box-correo');
  const correoInput = $('#correo');

  const boxAdmin    = $('#box-admin');
  const boxLider    = $('#box-lider');
  const boxAprendiz = $('#box-aprendiz');

  if (!rol) return;

  const show = el => el && el.classList.remove('d-none');
  const hide = el => el && el.classList.add('d-none');

  // ✅ Habilita/deshabilita todos los inputs dentro de un contenedor
  function setDisabled(container, disabled = true) {
    if (!container) return;
    container.querySelectorAll('input,select,textarea').forEach(el => {
      if (disabled) {
        el.setAttribute('disabled','disabled');
        el.removeAttribute('required'); // evita validaciones fuera de rol
      } else {
        el.removeAttribute('disabled');
      }
    });
  }

  function clearRequiredAll() {
    $$('#box-admin [name], #box-lider [name], #box-aprendiz [name]')
      .forEach(i => i.removeAttribute('required'));
    correoInput?.removeAttribute('required');
  }

  function setRequiredForRole(role) {
    clearRequiredAll();
    if (!role) return;
    correoInput?.setAttribute('required','required');

    if (role === 'ADMIN') {
      $$('#box-admin [name="nombre"], #box-admin [name="apellido"], #box-admin [name="password"]')
        .forEach(i => i.setAttribute('required','required'));
    }
    if (role === 'LIDER_SEMILLERO') {
      ['nombre','apellido','ls_tipo_documento','ls_documento','password'].forEach(n =>
        boxLider?.querySelector(`[name="${n}"]`)?.setAttribute('required','required')
      );
    }
    if (role === 'APRENDIZ') {
      ['nombre','apellido','ap_ficha','ap_programa','ap_documento','ap_correo_institucional','password'].forEach(n =>
        boxAprendiz?.querySelector(`[name="${n}"]`)?.setAttribute('required','required')
      );
    }
  }

  function toggle(role) {
    if (role === 'LIDER_GENERAL') role = 'ADMIN'; // compatibilidad

    // 1) Oculta y DESHABILITA todo
    [boxAdmin, boxLider, boxAprendiz].forEach(c => { hide(c); setDisabled(c, true); });
    hide(correoBox);

    if (!role) return;

    // 2) Muestra correo y bloque del rol + HABILITA sus inputs
    show(correoBox);
    if (correoInput) {
      correoInput.placeholder = (role === 'ADMIN')
        ? 'Ej: lider.general@empresa.com'
        : 'Ej: nombre@misena.edu.co';
    }

    if (role === 'ADMIN')              { show(boxAdmin);     setDisabled(boxAdmin, false); }
    else if (role === 'LIDER_SEMILLERO'){ show(boxLider);     setDisabled(boxLider, false); }
    else if (role === 'APRENDIZ')      { show(boxAprendiz);  setDisabled(boxAprendiz, false); }

    // 3) Required correctos
    setRequiredForRole(role);
  }

  // Estado inicial y cambios
  toggle(rol.value);
  rol.addEventListener('change', e => toggle(e.target.value));
});
</script>
@endpush
