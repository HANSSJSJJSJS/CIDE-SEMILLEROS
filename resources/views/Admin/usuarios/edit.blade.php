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
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center">
        <div class="me-2 rounded-circle d-flex align-items-center justify-content-center"
             style="width:44px;height:44px;background:#e6f0e6">
          <i class="bi bi-person-lines-fill" style="color:#2d572c;font-size:1.25rem"></i>
        </div>
        <div>
          <h3 class="brand-title mb-0">Editar usuario</h3>
          <small class="text-muted">Actualiza los datos del perfil</small>
        </div>
      </div>
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    {{-- Mensajes de estado --}}
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <strong>Revisa los campos:</strong>
        <ul class="mb-0 mt-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="card card-soft needs-validation" novalidate>
      @csrf
      @method('PUT')

      <div class="card-body row g-3">

        {{-- =========================================
             CAMPOS PRINCIPALES
        ========================================== --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Nombre</label>
          <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                 value="{{ old('nombre', $usuario->name) }}" required>
          <div class="invalid-feedback">Ingresa el nombre.</div>
          @error('nombre') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Apellido</label>
          <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                 value="{{ old('apellido', $usuario->apellidos) }}" required>
          <div class="invalid-feedback">Ingresa el apellido.</div>
          @error('apellido') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Correo</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email', $usuario->email) }}" required>
          <div class="invalid-feedback">Ingresa un correo válido.</div>
          @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        {{-- =========================================
             ROL (solo lectura)
        ========================================== --}}
        <div class="col-md-6">
          <label class="form-label d-flex align-items-center gap-2 fw-semibold">
            Rol <span class="badge text-bg-light">solo lectura</span>
          </label>
          @php
            $rolesList = ['ADMIN'=>'Líder general','LIDER_SEMILLERO'=>'Líder semillero','APRENDIZ'=>'Aprendiz'];
            $rolActual = $usuario->role;
          @endphp
          <select class="form-select" disabled>
            @foreach($rolesList as $key => $label)
              <option value="{{ $key }}" @selected($rolActual === $key)>{{ $label }}</option>
            @endforeach
          </select>
          {{-- Para permitir edición futura: quitar "disabled" y añadir name="role" --}}
        </div>

        {{-- =========================================
             SEMILLERO (solo lectura)
        ========================================== --}}
        <div class="col-md-6">
          <label class="form-label d-flex align-items-center gap-2 fw-semibold">
            Semillero <span class="badge text-bg-light">solo lectura</span>
          </label>
          @php
            $semillerosList = isset($semilleros) ? $semilleros : collect();
            $semilleroIdActual = old('semillero_id', $usuario->semillero_id ?? null);
          @endphp
          <select class="form-select" disabled>
            <option value="">— Ninguno —</option>
            @foreach($semillerosList as $s)
              <option value="{{ $s->id_semillero }}" @selected($semilleroIdActual == $s->id_semillero)>{{ $s->nombre }}</option>
            @endforeach
          </select>
        </div>

        {{-- =========================================
             CAMBIO DE CONTRASEÑA (opcional)
        ========================================== --}}
        <div class="col-12">
          <span class="section-label"><i class="bi bi-shield-lock"></i> Cambio de contraseña (opcional)</span>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Nueva contraseña</label>
          <div class="input-group">
            <input type="password" id="pass-new" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Dejar vacío para no cambiar">
            <button class="btn btn-outline-secondary" type="button" data-toggle-pass="#pass-new" aria-label="Ver/ocultar">
              <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-outline-success" type="button" data-generate-pass="#pass-new" aria-label="Generar">
              <i class="bi bi-magic"></i>
            </button>
          </div>
          <div class="field-note">Mínimo 8 caracteres. Si no deseas cambiarla, deja el campo vacío.</div>
          @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

      </div>

      <div class="card-footer sticky-actions">
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check2-circle me-1"></i> Guardar cambios
        </button>
      </div>
    </form>
  </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
  'use strict';
  // Validación Bootstrap
  document.querySelectorAll('.needs-validation').forEach(form=>{
    form.addEventListener('submit',e=>{
      if(!form.checkValidity()){ e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    },false);
  });

  // Ver/ocultar y generar contraseña
  function randPass(len=10){ const c='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%'; return Array.from(crypto.getRandomValues(new Uint32Array(len))).map(n=>c[n%c.length]).join(''); }
  document.querySelectorAll('[data-toggle-pass]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const i=document.querySelector(btn.dataset.togglePass);
      if(!i) return; i.type = i.type==='password' ? 'text' : 'password';
    });
  });
  document.querySelectorAll('[data-generate-pass]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const i=document.querySelector(btn.dataset.generatePass);
      if(i){ i.value = randPass(); i.type='text'; i.focus(); }
    });
  });
})();
</script>
@endpush
