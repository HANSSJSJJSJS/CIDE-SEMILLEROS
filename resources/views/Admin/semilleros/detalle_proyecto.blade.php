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
    <td>{{ trim(($i->nombres ?? '').' '.($i->apellidos ?? '')) }}</td>
    <td>{{ $i->correo_institucional ?? $i->correo_personal ?? 'Sin correo' }}</td>
    <td>{{ $i->celular ?? 'N/A' }}</td>
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


  {{-- Seguimiento de reuniones (Ejemplo de datos) --}}
<div class="card shadow-sm mt-4">
  <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <span><i class="bi bi-calendar-check me-1"></i> Seguimiento de reuniones</span>
    <a class="btn btn-light btn-sm disabled" href="#">
      <i class="bi bi-plus-circle"></i> Nueva reunión
    </a>
  </div>

  <div class="card-body p-0">
    <table class="table table-hover mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th style="width: 140px;">Fecha</th>
          <th style="width: 160px;">Tipo de reunión</th>
          <th>Aprendiz</th>
          <th style="width: 140px;">Asistencia</th>
          <th style="width: 35%;">Comentario del líder</th>
        </tr>
      </thead>
      <tbody>
        {{-- Reunión 1 --}}
        <tr>
          <td rowspan="3">2025-11-05</td>
          <td rowspan="3">Reunión de avance</td>
          <td>Laura Rodríguez</td>
          <td><span class="badge text-bg-success">Asistió</span></td>
          <td class="text-muted">Excelente participación.</td>
        </tr>
        <tr>
          <td>Carlos Pérez</td>
          <td><span class="badge text-bg-danger">No asistió</span></td>
          <td class="text-muted">Justificó inasistencia por enfermedad.</td>
        </tr>
        <tr>
          <td>Valentina Gómez</td>
          <td><span class="badge text-bg-success">Asistió</span></td>
          <td class="text-muted">Aportó ideas sobre metodología.</td>
        </tr>

        {{-- Reunión 2 --}}
        <tr>
          <td rowspan="2">2025-10-22</td>
          <td rowspan="2">Sesión virtual</td>
          <td>Laura Rodríguez</td>
          <td><span class="badge text-bg-success">Asistió</span></td>
          <td class="text-muted">Presentó informe de avances.</td>
        </tr>
        <tr>
          <td>Carlos Pérez</td>
          <td><span class="badge text-bg-success">Asistió</span></td>
          <td class="text-muted">Hizo preguntas sobre recolección de datos.</td>
        </tr>

        {{-- Reunión 3 --}}
        <tr>
          <td>2025-10-10</td>
          <td>Planeación inicial</td>
          <td colspan="3" class="text-muted">Sin registros de asistencia (solo reunión informativa).</td>
        </tr>
      </tbody>
    </table>
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
