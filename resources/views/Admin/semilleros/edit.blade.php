@extends('layouts.lider_semi')

@section('content')
<div class="container mt-4">
  <h4 class="fw-bold mb-3" style="color:#2d572c;">Editar Semillero</h4>

  <form class="card border-0 shadow-sm p-3"
        action="{{ route('admin.semilleros.update',$id) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Nombre</label>
        <input name="nombre" class="form-control" value="{{ old('nombre',$nombre) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Línea de investigación</label>
        <input name="linea_investigacion" class="form-control" value="{{ old('linea_investigacion',$linea) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Líder</label>
        <select name="id_lider_usuario" class="form-select">
          <option value="">— Sin asignar —</option>
          @foreach($lideres as $l)
            <option value="{{ $l->id_usuario }}" @selected(old('id_lider_usuario',$lider_id)==$l->id_usuario)>
              {{ $l->nombre }} {{ isset($l->correo_institucional) && $l->correo_institucional ? '(' . $l->correo_institucional . ')' : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-12 mt-2">
        <button class="btn btn-success">Guardar cambios</button>
        <a href="{{ route('admin.semilleros.index') }}" class="btn btn-secondary">Volver</a>
      </div>
    </div>
  </form>
</div>
@endsection
