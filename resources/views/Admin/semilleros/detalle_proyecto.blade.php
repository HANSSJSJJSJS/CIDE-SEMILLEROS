@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4 px-4">

  {{-- Encabezado: semillero fijo + subtítulo con nombre de proyecto --}}
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h2 class="fw-bold text-success mb-1">{{ $semillero->nombre }}</h2>
      <h5 class="text-muted mb-0">{{ $proyecto->nombre_proyecto }}</h5>
    </div>
    <a href="{{ route('admin.semilleros.proyectos.index', $semillero->id_semillero) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver a proyectos
    </a>
  </div>

  {{-- Descripción --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="fw-semibold mb-2">Descripción del proyecto</h5>
      <p class="text-muted mb-0">{{ $proyecto->descripcion ?? 'Sin descripción disponible.' }}</p>
    </div>
  </div>

  <div class="row g-4">
    {{-- Integrantes --}}
    <div class="col-12 col-xl-7">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white">
          <i class="bi bi-people-fill me-1"></i> Integrantes del proyecto
        </div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
              </tr>
            </thead>
            <tbody>
            @forelse(($integrantes ?? []) as $i)
              <tr>
                <td>{{ $i->nombre }}</td>
                <td>{{ $i->correo }}</td>
                <td>{{ $i->telefono }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center py-3 text-muted">Sin integrantes registrados.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Documentación --}}
    <div class="col-12 col-xl-5">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white">
          <i class="bi bi-folder2-open me-1"></i> Documentación del proyecto
        </div>
        <div class="card-body p-0">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Archivo</th>
                <th>Fecha</th>
                <th class="text-end pe-3">Acciones</th>
              </tr>
            </thead>
            <tbody>
            @forelse(($documentacion ?? []) as $doc)
              <tr>
                <td>{{ $doc->nombre }}</td>
                <td>{{ $doc->fecha }}</td>
                <td class="text-end pe-3">
                  <button class="btn btn-sm btn-outline-primary" disabled>
                    <i class="bi bi-download"></i> Descargar
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center py-3 text-muted">No hay documentación subida.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Observaciones --}}
  <div class="card shadow-sm mt-4">
    <div class="card-header bg-success text-white">
      <i class="bi bi-chat-text me-1"></i> Observaciones del líder
    </div>
    <div class="card-body">
      <form action="#" method="POST">
        <textarea class="form-control mb-3" rows="4" placeholder="Escribe observaciones aquí...">{{ $observaciones ?? '' }}</textarea>
        <div class="text-end">
          <button class="btn btn-success" disabled>
            <i class="bi bi-save"></i> Guardar cambios (demo)
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
