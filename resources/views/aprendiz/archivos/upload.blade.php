@extends('layouts.aprendiz')

@section('title','Subir documentos')
@section('module-title','Subir documentos')
@section('module-subtitle','Carga tus archivos para el proyecto seleccionado')

@section('content')
<div class="container-xxl py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <form action="{{ route('aprendiz.archivos.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="proyecto_id" class="form-label fw-semibold">Proyecto</label>
              <select id="proyecto_id" name="proyecto_id" class="form-select" required>
                <option value="" disabled {{ empty($proyectoSeleccionado) ? 'selected' : '' }}>Seleccione un proyecto</option>
                @foreach($proyectos as $p)
                  <option value="{{ $p->id_proyecto }}" {{ (string)$proyectoSeleccionado === (string)$p->id_proyecto ? 'selected' : '' }}>
                    {{ $p->nombre_proyecto ?? 'Proyecto #'.$p->id_proyecto }}
                  </option>
                @endforeach
              </select>
              @error('proyecto_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Documentos (PDF, máx 10MB)</label>
              <input type="file" name="documentos[]" class="form-control" accept="application/pdf" multiple required>
              @error('documentos')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              @error('documentos.*')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn" style="background-color:#39A900;border-color:#39A900;color:#ffffff;">Subir</button>
              <a href="{{ route('aprendiz.archivos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
