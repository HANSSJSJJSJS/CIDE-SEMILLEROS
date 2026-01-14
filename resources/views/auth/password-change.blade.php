@php
    $layout = match(auth()->user()->role) {
        'ADMIN', 'LIDER_INVESTIGACION' => 'layouts.admin',
        'LIDER_SEMILLERO'              => 'layouts.lider',
        'APRENDIZ'                     => 'layouts.aprendiz',
        default                        => 'layouts.app',
    };
@endphp

@extends($layout)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/aprendiz/perfil.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/perfil-admin.css') }}">
@endpush

@push('scripts')
<script>
(function () {

  // 👁️ Mostrar / ocultar contraseña
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.target;
      const input = document.getElementById(id);
      if (!input) return;

      input.type = input.type === 'password' ? 'text' : 'password';
      btn.querySelector('i')?.classList.toggle('bi-eye');
      btn.querySelector('i')?.classList.toggle('bi-eye-slash');
    });
  });

  // 🔐 Barra de fuerza
  const pwd = document.getElementById('pf_password');
  const bar = document.getElementById('pf_strength_bar');
  const msg = document.getElementById('pf_strength_msg');

  function score(val) {
    let s = 0;
    if (!val) return s;
    if (val.length >= 10) s += 30;
    if (/[A-Z]/.test(val)) s += 25;
    if (/[a-z]/.test(val)) s += 15;
    if (/\d/.test(val)) s += 20;
    if (/[^A-Za-z0-9]/.test(val)) s += 10;
    return Math.min(s, 100);
  }

  function renderStrength() {
    if (!pwd) return;
    const sc = score(pwd.value);

    bar.style.width = sc + '%';
    bar.className = 'progress-bar';

    if (sc < 40) {
      bar.classList.add('bg-danger');
      msg.textContent = 'Contraseña débil';
    } else if (sc < 70) {
      bar.classList.add('bg-warning');
      msg.textContent = 'Contraseña media';
    } else {
      bar.classList.add('bg-success');
      msg.textContent = 'Contraseña fuerte';
    }
  }

  pwd?.addEventListener('input', renderStrength);
  renderStrength();

})();
</script>

{{-- ✅ Toast éxito --}}
@if (session('success'))
<script>
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: @json(session('success')),
  showConfirmButton: false,
  timer: 2600,
  timerProgressBar: true
});
</script>
@endif

{{-- ❌ Toast error --}}
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
<div class="container mt-4 perfil-admin" style="max-width:720px;">

  <div class="pf-block">
    <div class="pf-head pass">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-key-fill fs-5"></i>
        <strong>Cambio obligatorio de contraseña</strong>
      </div>
      <span class="pf-badge" style="background:#fff3cd;color:#7a4d00;">
        Seguridad
      </span>
    </div>

    <div class="p-3 p-md-4">
      <p class="text-muted mb-3">
        Por seguridad, debes establecer una nueva contraseña antes de continuar.
      </p>

      <form method="POST" action="{{ route('password.change.update') }}" novalidate>
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label pf-label">Nueva contraseña *</label>
            <div class="input-group">
              <input type="password"
                     name="password"
                     id="pf_password"
                     class="form-control pf-input @error('password') is-invalid @enderror"
                     required>
              <button class="btn btn-outline-secondary toggle-pass"
                      type="button"
                      data-target="pf_password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label pf-label">Confirmar contraseña *</label>
            <div class="input-group">
              <input type="password"
                     name="password_confirmation"
                     id="pf_password_confirmation"
                     class="form-control pf-input"
                     required>
              <button class="btn btn-outline-secondary toggle-pass"
                      type="button"
                      data-target="pf_password_confirmation">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="alert alert-warning mt-3 py-2 px-3">
          La contraseña debe tener:
          <ul class="mb-1">
            <li>Mínimo 10 caracteres</li>
            <li>Al menos una mayúscula</li>
            <li>Al menos un número</li>
          </ul>

          <div class="progress pf-progress mt-2">
            <div class="progress-bar" id="pf_strength_bar" style="width:0%"></div>
          </div>
          <div class="mt-1" id="pf_strength_msg"></div>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <button class="btn pf-btn" type="submit">
            <i class="bi bi-lock-fill me-1"></i>
            Guardar contraseña
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
