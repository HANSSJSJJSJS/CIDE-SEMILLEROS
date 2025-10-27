@extends('layouts.admin')

@section('content')
<div class="container mt-4">
  <h4 class="fw-bold mb-3" style="color:#2d572c;">Editar Semillero</h4>

  <form class="card border-0 shadow-sm p-3"
        action="{{ route('admin.semilleros.update',$s->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Nombre</label>
        <input name="nombre" class="form-control" value="{{ old('nombre',$s->nombre) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Línea de investigación</label>
        <input name="linea_investigacion" class="form-control" value="{{ old('linea_investigacion',$s->linea_investigacion) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Líder</label>
        <select name="id_lider_semi" class="form-select">
          <option value="">— Sin asignar —</option>
          @foreach($lideres as $l)
            <option value="{{ $l->id_lider_semi }}" @selected(old('id_lider_semi',$s->id_lider_semi)==$l->id_lider_semi)>
              {{ $l->nombre }}
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
