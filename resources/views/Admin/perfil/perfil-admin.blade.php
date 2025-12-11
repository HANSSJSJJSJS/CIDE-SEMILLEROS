@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/aprendiz/perfil.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/perfil-admin.css') }}">
@endpush

@push('scripts')
<script>
  (function(){
    document.querySelectorAll('.toggle-pass').forEach(btn=>{
      btn.addEventListener('click',()=>{
        const id = btn.getAttribute('data-target');
        const el = document.getElementById(id);
        if(!el) return;
        el.type = el.type === 'password' ? 'text' : 'password';
        btn.querySelector('i')?.classList.toggle('bi-eye');
        btn.querySelector('i')?.classList.toggle('bi-eye-slash');
      });
    });
    const pwd = document.getElementById('pf_password');
    const bar = document.getElementById('pf_strength_bar');
    const msg = document.getElementById('pf_strength_msg');
    function score(s){
      if(!s) return 0;
      let v = 0; if(s.length>=8) v+=25; if(s.length>=12) v+=15; if(/[a-z]/.test(s)) v+=20; if(/[A-Z]/.test(s)) v+=20; if(/\d/.test(s)) v+=20; if(/[^A-Za-z0-9]/.test(s)) v+=10; return Math.min(v,100);
    }
    function render(){
      if(!pwd||!bar||!msg) return;
      const sc = score(pwd.value);
      bar.style.width = sc+'%';
      bar.className = 'progress-bar';
      if(sc<40){ bar.classList.add('bg-danger'); msg.textContent='Contraseña débil'; }
      else if(sc<70){ bar.classList.add('bg-warning'); msg.textContent='Contraseña media'; }
      else { bar.classList.add('bg-success'); msg.textContent='Contraseña fuerte'; }
    }
    pwd?.addEventListener('input', render);
    render();
  })();
</script>
@if (session('status'))
<script>
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: @json(session('status')),
    showConfirmButton: false,
    timer: 2600,
    timerProgressBar: true
  });
</script>
@endif
@if ($errors->any())
<script>
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: @json($errors->first()),
    showConfirmButton: false,
    timer: 3200,
    timerProgressBar: true
  });
</script>
@endif
@endpush

@section('content')
<div class="container mt-4 perfil-admin" style="max-width:1080px;">
  @if ($errors->any())
    <div class="alert alert-danger mb-3" role="alert">
      <div class="fw-semibold mb-1"><i class="bi bi-exclamation-octagon me-2"></i>Corrige lo siguiente:</div>
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="text-center pf-hero mb-3">
    <h3>Mi Perfil</h3>
    <p>Gestiona tu información personal y de seguridad</p>
  </div>

  <div class="pf-note mb-3"><i class="bi bi-info-circle me-2"></i>Solo puedes actualizar tu correo de acceso y contraseña. Los demás datos son de solo lectura.</div>

  @php
    $u = auth()->user();
    $td = ['CC'=>'Cédula de Ciudadanía','TI'=>'Tarjeta de Identidad','CE'=>'Cédula de Extranjería','PAS'=>'Pasaporte','PEP'=>'Permiso Especial de Permanencia','RC'=>'Registro Civil'];
    $tipoDoc = $td[$u->tipo_documento ?? ''] ?? ($u->tipo_documento ?? '—');
    $gmap = ['HOMBRE'=>'Masculino','MUJER'=>'Femenino','MASCULINO'=>'Masculino','FEMENINO'=>'Femenino','NO DEFINIDO'=>'No definido'];
    $genero = $gmap[strtoupper($u->genero ?? '')] ?? ($u->genero ?? '—');
  @endphp

  <div class="pf-block mb-4">
    <div class="pf-head personal">
      <div class="d-flex align-items-center gap-2"><i class="bi bi-person-badge fs-4"></i><strong>Información Personal</strong></div>
      <span class="pf-badge">Solo lectura</span>
    </div>
    <div class="p-3 p-md-4">
      <div class="row g-3">
        <div class="col-md-6"><div class="pf-label mb-1">Tipo de Documento</div><div class="pf-ro"><span>{{ $tipoDoc }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Número de Documento</div><div class="pf-ro"><span>{{ $u->documento ?? '—' }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Nombres</div><div class="pf-ro"><span>{{ $u->nombre ?? '—' }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Apellidos</div><div class="pf-ro"><span>{{ $u->apellidos ?? '—' }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Género</div><div class="pf-ro"><span>{{ $genero }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Celular</div><div class="pf-ro"><span>{{ $u->celular ?? '—' }}</span><i class="bi bi-lock-fill lock"></i></div></div>
        <div class="col-md-6"><div class="pf-label mb-1">Tipo de RH</div><div class="pf-ro"><span>{{ $u->tipo_rh ?? '—' }}</span><i class="bi bi-lock-fill lock"></i></div></div>
      </div>
    </div>
  </div>

  <div class="pf-block mb-4">
    <div class="pf-head mail">
      <div class="d-flex align-items-center gap-2"><i class="bi bi-envelope-fill fs-5"></i><strong>Correo de Acceso</strong></div>
      <span class="pf-badge" style="background:#e7f5e9;color:#1b5e20;">Editable</span>
    </div>
    <div class="p-3 p-md-4">
      <form method="POST" action="{{ route('admin.perfil.update') }}" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="name" value="{{ $u->nombre }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label pf-label">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email', $u->email) }}" class="form-control pf-input @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label pf-label">Confirmar correo *</label>
            <input type="email" name="email_confirmation" value="{{ old('email', $u->email) }}" class="form-control pf-input" required>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a class="btn pf-btn-outline" href="{{ url()->current() }}">Cancelar</a>
          <button class="btn pf-btn" type="submit"><i class="bi bi-save2 me-1"></i>Actualizar Correo</button>
        </div>
      </form>
    </div>
  </div>

  <div class="pf-block">
    <div class="pf-head pass">
      <div class="d-flex align-items-center gap-2"><i class="bi bi-key-fill fs-5"></i><strong>Cambio de Contraseña</strong></div>
      <span class="pf-badge" style="background:#fff3cd;color:#7a4d00;">Editable</span>
    </div>
    <div class="p-3 p-md-4">
      <form method="POST" action="{{ route('admin.perfil.password.update') }}" novalidate>
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label pf-label">Contraseña actual *</label>
            <div class="input-group">
              <input type="password" name="current_password" class="form-control pf-input @error('current_password') is-invalid @enderror" required id="pf_current_password">
              <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="pf_current_password"><i class="bi bi-eye"></i></button>
            </div>
            @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label pf-label">Nueva contraseña *</label>
            <div class="input-group">
              <input type="password" name="password" class="form-control pf-input @error('password') is-invalid @enderror" required minlength="8" id="pf_password">
              <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="pf_password"><i class="bi bi-eye"></i></button>
            </div>
            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label pf-label">Confirmar nueva contraseña *</label>
            <div class="input-group">
              <input type="password" name="password_confirmation" class="form-control pf-input" required minlength="8" id="pf_password_confirmation">
              <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="pf_password_confirmation"><i class="bi bi-eye"></i></button>
            </div>
          </div>
        </div>
        <div class="alert alert-warning mt-3 py-2 px-3">
          La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.
          <div class="progress pf-progress mt-2">
            <div class="progress-bar" id="pf_strength_bar" role="progressbar" style="width:0%"></div>
          </div>
          <div class="mt-1" id="pf_strength_msg"></div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2">
          <a class="btn pf-btn-outline" href="{{ url()->current() }}">Cancelar</a>
          <button class="btn pf-btn" type="submit"><i class="bi bi-lock-fill me-1"></i>Cambiar Contraseña</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
