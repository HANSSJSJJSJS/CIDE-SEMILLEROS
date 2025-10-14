@extends('layouts.app')

@section('title','Registro de Usuarios')

@section('content')
<div class="card card-sena w-100">
  <div class="card-body p-4 p-md-5">

    <h2 class="mb-4 text-white fw-bold" style="background:#00733B;border-radius:10px;padding:12px 16px;">
      Registro de usuarios · SENA
    </h2>

    {{-- Mensajes --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <form method="POST" action="{{ route('usuarios.store') }}" id="form-usuario" novalidate>
  @csrf

  {{-- ===== 1. Selección de rol ===== --}}
  <div class="row g-3">
    <div class="col-md-6">
      <label for="rol" class="form-label fw-semibold">Rol del usuario</label>
      <select id="rol" name="rol" class="form-select" required>
        <option value="" disabled selected>Seleccione un rol</option>
        <option value="ADMINISTRADOR">Administrador</option>
        <option value="LIDER_SEMILLERO">Líder de semillero</option>
        <option value="APRENDIZ">Aprendiz</option>
      </select>
    </div>
  </div>

  {{-- ===== 2. Correo (se muestra luego de seleccionar rol) ===== --}}
  <div id="box-correo" class="row g-3 mt-3 d-none">
    <div class="col-md-6">
      <label for="correo" class="form-label fw-semibold">Correo del usuario</label>
      <input type="email" id="correo" name="correo" class="form-control" required>
    </div>
  </div>

      

    {{-- ===== ADMINISTRADOR ===== --}}
<div id="box-admin" class="mt-4 d-none">
  <div class="p-3 border rounded-3 bg-white">
    <h5 class="fw-bold text-success mb-3">Datos de Administrador</h5>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="admin_nombre">Nombre</label>
        <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Nombre del administrador">
      </div>
    </div>
  </div>
</div>

      {{-- ===== LÍDER DE SEMILLERO ===== --}}
      <div id="box-lider" class="mt-4 d-none">
        <div class="p-3 border rounded-3 bg-white">
          <h5 class="fw-bold text-success mb-3">Datos de Líder de Semillero</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" id="lider_nombre" name="lider_nombre" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellido</label>
              <input type="text" id="lider_apellido" name="lider_apellido" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Tipo de documento</label>
              <select id="lider_tipo_doc" name="lider_tipo_doc" class="form-select">
                <option value="" disabled selected>Seleccione</option>
                <option value="CC">CC</option>
                <option value="CE">CE</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Documento</label>
              <input type="text" id="lider_documento" name="lider_documento" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Contacto de emergencia (nombre)</label>
              <input type="text" id="lider_contacto_emerg" name="lider_contacto_emerg" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Celular del contacto</label>
              <input type="tel" id="lider_cel_contacto" name="lider_cel_contacto" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <input type="email" id="lider_correo_institucional" name="lider_correo_institucional"
                     class="form-control" placeholder="ej: nombre@misena.edu.co">
            </div>
          </div>
        </div>
      </div>

      {{-- ===== APRENDIZ ===== --}}
      <div id="box-aprendiz" class="mt-4 d-none">
        <div class="p-3 border rounded-3 bg-white">
          <h5 class="fw-bold text-success mb-3">Datos de Aprendiz</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" id="aprendiz_nombre" name="aprendiz_nombre" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellido</label>
              <input type="text" id="aprendiz_apellido" name="aprendiz_apellido" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Ficha</label>
              <input type="text" id="aprendiz_ficha" name="aprendiz_ficha" class="form-control">
            </div>
            <div class="col-md-8">
              <label class="form-label">Programa</label>
              <input type="text" id="aprendiz_programa" name="aprendiz_programa" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Tipo de documento</label>
              <select id="aprendiz_tipo_doc" name="aprendiz_tipo_doc" class="form-select">
                <option value="" disabled selected>Seleccione</option>
                <option value="TI">TI</option>
                <option value="CC">CC</option>
                <option value="CE">CE</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Documento</label>
              <input type="text" id="aprendiz_documento" name="aprendiz_documento" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Celular</label>
              <input type="tel" id="aprendiz_celular" name="aprendiz_celular" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo institucional</label>
              <input type="email" id="aprendiz_correo_institucional" name="aprendiz_correo_institucional"
                     class="form-control" placeholder="ej: nombre@misena.edu.co">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contacto de emergencia</label>
              <input type="text" id="aprendiz_contacto_emerg" name="aprendiz_contacto_emerg" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Celular del contacto</label>
              <input type="tel" id="aprendiz_cel_contacto" name="aprendiz_cel_contacto" class="form-control">
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success px-4">Guardar</button>
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary px-4">Volver</a>
      </div>
    </form>
  </div>
</div>

{{-- ===== Script dinámico ===== --}}
<script>
(function () {
  const rol = document.getElementById('rol');
  const correoBox = document.getElementById('box-correo');
  const correoInput = document.getElementById('correo');
  const boxAdmin = document.getElementById('box-admin');
  const boxLider = document.getElementById('box-lider');
  const boxAprendiz = document.getElementById('box-aprendiz');

  // Mostrar/ocultar secciones
  function toggleSections(value) {
    // Mostrar campo correo cuando elija un rol
    correoBox.classList.remove('d-none');
    // Placeholder distinto por rol
    if (value === 'ADMINISTRADOR') {
      correoInput.placeholder = 'Ej: admin@empresa.com';
    } else {
      correoInput.placeholder = 'Ej: nombre@misena.edu.co';
    }

    // Ocultar todos los bloques
    [boxAdmin, boxLider, boxAprendiz].forEach(b => b.classList.add('d-none'));
    // Mostrar el correspondiente
    if (value === 'ADMINISTRADOR') boxAdmin.classList.remove('d-none');
    if (value === 'LIDER_SEMILLERO') boxLider.classList.remove('d-none');
    if (value === 'APRENDIZ') boxAprendiz.classList.remove('d-none');
  }

  rol.addEventListener('change', e => toggleSections(e.target.value));
})();
</script>

@endsection
