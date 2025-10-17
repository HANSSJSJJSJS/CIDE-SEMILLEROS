

@vite(['resources/css/app.css', 'resources/js/app.js'])


<form method="POST" action="{{ route('usuarios.store') }}">
  @csrf
  <h4 class="fw-bold mb-3 text-success">Registrar nuevo usuario</h4>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Nombres</label>
      <input type="text" name="nombres" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Apellidos</label>
      <input type="text" name="apellidos" class="form-control" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="role" class="form-select" required>
      <option value="">Seleccione...</option>
      <option value="ADMIN">Administrador</option>
      <option value="LIDER_SEMILLERO">Líder Semillero</option>
      <option value="LIDER_GENERAL">Líder General</option>
      <option value="APRENDIZ">Aprendiz</option>
      <option value="INSTRUCTOR">Instructor</option>
    </select>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Confirmar contraseña</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>
  </div>

  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-success">Guardar usuario</button>
  </div>
</form>
@push('scripts')
  <script src="{{ asset('js/form_usuario.js') }}"></script>
@endpush
