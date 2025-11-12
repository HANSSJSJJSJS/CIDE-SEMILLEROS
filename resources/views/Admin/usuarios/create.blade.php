@extends('layouts.admin')

@push('styles')
<style>
  .page-wrap{min-height:calc(100vh - 120px);display:grid;place-items:start center;padding-top:2rem}
  .brand-title{color:#2d572c}
  .section-label{font-size:.85rem;font-weight:600;color:#2d572c;background:#e9f2ea;border-radius:999px;padding:.2rem .6rem;display:inline-flex;gap:.4rem;align-items:center}
  .field-note{font-size:.8rem;color:#6c757d}
  .card-soft{border:0;box-shadow:0 6px 20px rgba(0,0,0,.06)}
  .sticky-actions{display:flex;gap:.5rem;justify-content:flex-end}
</style>
@endpush

@section('content')
<div class="container page-wrap">

  <div class="col-12 col-lg-8">
    <div class="card card-soft">
      <div class="card-body p-4">

        <div class="d-flex align-items-center mb-3">
          <div class="me-2 rounded-circle d-flex align-items-center justify-content-center"
               style="width:44px;height:44px;background:#e6f0e6">
            <i class="bi bi-person-plus" style="color:#2d572c;font-size:1.25rem"></i>
          </div>
          <div>
            <h3 class="brand-title mb-0">Agregar usuario</h3>
            <small class="text-muted">Selecciona un rol y completa los datos requeridos</small>
          </div>
        </div>

        {{-- Errores generales --}}
        @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Revisa los campos:</strong>
            <ul class="mb-0 mt-1">
              @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
          </div>
        @endif

        <form id="form-usuario" action="{{ route('admin.usuarios.store') }}" method="POST" class="needs-validation" novalidate>
          @csrf

          <div class="row g-3">

            {{-- 1) Rol --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
              <select id="rol" name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">Seleccione...</option>
                <option value="ADMIN"           @selected(old('role')==='ADMIN')>Líder general</option>
                <option value="LIDER_SEMILLERO" @selected(old('role')==='LIDER_SEMILLERO')>Líder semillero</option>
                <option value="APRENDIZ"        @selected(old('role')==='APRENDIZ')>Aprendiz</option>
              </select>
              <div class="invalid-feedback">Selecciona un rol.</div>
              @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- 2) Correo (se muestra al elegir rol) --}}
            <div id="box-correo" class="col-md-6 d-none">
              <label class="form-label fw-semibold">Correo <span class="text-danger">*</span></label>
              <input id="correo" type="email" name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}">
              <div class="invalid-feedback">Ingresa un correo válido.</div>
              <div class="field-note">Usa el institucional si aplica.</div>
              @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- ===== ADMIN ===== --}}
            <div id="box-admin" class="row g-3 d-none mt-2">
              <div class="col-12"><span class="section-label"><i class="bi bi-shield-lock"></i> Líder general</span></div>

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
                  <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-admin"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-admin"><i class="bi bi-magic"></i></button>
                </div>
                <div class="field-note">Mínimo 8 caracteres.</div>
                <div class="invalid-feedback">La contraseña es obligatoria.</div>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- ===== LÍDER SEMILLERO (Instructor) ===== --}}
            <div id="box-lider" class="row g-3 d-none mt-2">
              <div class="col-12"><span class="section-label"><i class="bi bi-mortarboard"></i> Líder semillero</span></div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input name="nombre" class="form-control" value="{{ old('nombre') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Apellido</label>
                <input name="apellido" class="form-control" value="{{ old('apellido') }}">
              </div>

              {{-- CAMBIO: Select con CC / CE para instructor --}}
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
                  <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-lider"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-lider"><i class="bi bi-magic"></i></button>
                </div>
              </div>
            </div>

            {{-- ===== APRENDIZ ===== --}}
            <div id="box-aprendiz" class="row g-3 d-none mt-2">
              <div class="col-12"><span class="section-label"><i class="bi bi-person-workspace"></i> Aprendiz</span></div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input name="nombre" class="form-control" value="{{ old('nombre') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Apellido</label>
                <input name="apellido" class="form-control" value="{{ old('apellido') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Ficha</label>
                <input name="ap_ficha" class="form-control" value="{{ old('ap_ficha') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Programa</label>
                <input name="ap_programa" class="form-control" value="{{ old('ap_programa') }}">
              </div>

              {{-- CAMBIO: Select con CC / TI / CE para aprendiz --}}
              <div class="col-md-4">
                <label class="form-label fw-semibold">Tipo documento</label>
                <select name="ap_tipo_documento" class="form-select @error('ap_tipo_documento') is-invalid @enderror">
                  <option value="">Seleccione...</option>
                  <option value="CC" @selected(old('ap_tipo_documento')==='CC')>CC</option>
                  <option value="TI" @selected(old('ap_tipo_documento')==='TI')>TI</option>
                  <option value="CE" @selected(old('ap_tipo_documento')==='CE')>CE</option>
                </select>
                @error('ap_tipo_documento') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Documento</label>
                <input name="ap_documento" class="form-control" value="{{ old('ap_documento') }}">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Celular</label>
                <input name="ap_celular" class="form-control" value="{{ old('ap_celular') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Correo institucional</label>
                <input name="ap_correo_institucional" type="email" class="form-control" value="{{ old('ap_correo_institucional') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                  <input type="password" id="pass-ap" name="password" class="form-control">
                  <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-ap"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-ap"><i class="bi bi-magic"></i></button>
                </div>
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

          </div>

          <div class="mt-4 sticky-actions">
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check2-circle me-1"></i> Guardar
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';
  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));

  // Bootstrap validation
  document.querySelectorAll('.needs-validation').forEach(form=>{
    form.addEventListener('submit',e=>{
      if(!form.checkValidity()){ e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    },false);
  });

  const rol         = $('#rol');
  const correoBox   = $('#box-correo');
  const correoInput = $('#correo');
  const boxAdmin    = $('#box-admin');
  const boxLider    = $('#box-lider');
  const boxAprendiz = $('#box-aprendiz');

  const show = el => el?.classList.remove('d-none');
  const hide = el => el?.classList.add('d-none');
  const setDisabled = (c, dis) => c?.querySelectorAll('input,select,textarea').forEach(el=>{
    dis ? el.setAttribute('disabled','disabled') : el.removeAttribute('disabled');
    if(dis) el.removeAttribute('required');
  });

  function clearRequiredAll(){
    $$('#box-admin [name], #box-lider [name], #box-aprendiz [name]').forEach(i=>i.removeAttribute('required'));
    correoInput?.removeAttribute('required');
  }
  function setRequiredForRole(role){
    clearRequiredAll();
    if(!role) return;
    correoInput?.setAttribute('required','required');
    if(role==='ADMIN'){
      ['nombre','apellido','password'].forEach(n=>boxAdmin?.querySelector(`[name="${n}"]`)?.setAttribute('required','required'));
    }
    if(role==='LIDER_SEMILLERO'){
      // Requerimos también el tipo de documento del instructor
      ['nombre','apellido','ls_tipo_documento','ls_documento','password'].forEach(n=>boxLider?.querySelector(`[name="${n}"]`)?.setAttribute('required','required'));
    }
    if(role==='APRENDIZ'){
      // Requerimos también el tipo de documento del aprendiz
      ['nombre','apellido','ap_ficha','ap_programa','ap_tipo_documento','ap_documento','ap_correo_institucional','password'].forEach(n=>boxAprendiz?.querySelector(`[name="${n}"]`)?.setAttribute('required','required'));
    }
  }
  function toggle(role){
    if(role==='LIDER_GENERAL') role='ADMIN';
    [boxAdmin,boxLider,boxAprendiz].forEach(c=>{ hide(c); setDisabled(c,true); });
    hide(correoBox);
    if(!role) return;

    show(correoBox);
    correoInput && (correoInput.placeholder = role==='ADMIN' ? 'lider.general@dominio.com' : 'nombre@misena.edu.co');

    if(role==='ADMIN'){ show(boxAdmin); setDisabled(boxAdmin,false); }
    else if(role==='LIDER_SEMILLERO'){ show(boxLider); setDisabled(boxLider,false); }
    else if(role==='APRENDIZ'){ show(boxAprendiz); setDisabled(boxAprendiz,false); }

    setRequiredForRole(role);
  }

  // init + change
  toggle(rol?.value);
  rol?.addEventListener('change', e=>toggle(e.target.value));

  // ver/ocultar + generar contraseña
  function randPass(len=10){ const c='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%'; return Array.from(crypto.getRandomValues(new Uint32Array(len))).map(n=>c[n % c.length]).join(''); }
  document.querySelectorAll('[data-toggle-pass]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const i = document.querySelector(btn.dataset.togglePass);
      if(!i) return;
      i.type = i.type==='password' ? 'text' : 'password';
      btn.classList.toggle('active');
    });
  });
  document.querySelectorAll('[data-generate-pass]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const i = document.querySelector(btn.dataset.generatePass);
      if(i){ i.value = randPass(); i.type='text'; i.focus(); }
    });
  });
})();
</script>
@endpush
