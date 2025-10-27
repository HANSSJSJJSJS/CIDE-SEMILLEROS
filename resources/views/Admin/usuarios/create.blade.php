@extends('layouts.admin-lider')
@section('content')
<div class="container mt-4">
  <h3 class="fw-bold mb-4" style="color:#2d572c;">Agregar usuario</h3>
  <form action="{{ route('admin.usuarios.store') }}" method="POST">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Nombre</label>
        <input name="nombre" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Apellido</label>
        <input name="apellido" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Correo</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Rol</label>
        <select name="role" class="form-select" required>
          <option value="ADMIN">ADMIN</option>
          <option value="LIDER_GENERAL">LIDER_GENERAL</option>
          <option value="LIDER_SEMILLERO">LIDER_SEMILLERO</option>
          <option value="APRENDIZ">APRENDIZ</option>
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-success">Guardar</button>
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
@endsection
