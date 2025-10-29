@extends('layouts.admin')

@section('content')
@php
  // ===== Mock para poder previsualizar sin controlador =====
  $semillero = $semillero ?? (object)[
    'nombre' => 'Semillero Innovación 4.0',
    'linea'  => 'Inteligencia Artificial aplicada',
    'lider'  => 'María Fernanda Rojas',
  ];

  $proyectos = $proyectos ?? collect([
    (object)[ 'nombre' => 'Visión por Computador para Control de Calidad',
              'integrantes' => 'Ana C., Luis P., Mateo G.',
              'estado' => 'activo', 'progreso' => 72 ],
    (object)[ 'nombre' => 'Chatbots para Atención al Cliente',
              'integrantes' => 'Camila R., Sofía M.',
              'estado' => 'inactivo', 'progreso' => 20 ],
    (object)[ 'nombre' => 'Predicción de Demanda con ML',
              'integrantes' => 'Julián S., Paula V., Andrés L.',
              'estado' => 'activo', 'progreso' => 48 ],
  ]);
@endphp

<div class="container-fluid mt-4 px-4">
  {{-- Encabezado --}}
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h2 class="fw-bold mb-1" style="color:#2d572c;">
        {{ $semillero->nombre }}
      </h2>
      <div class="text-muted">
        <div class="mb-1"><strong>Línea de investigación:</strong> {{ $semillero->linea }}</div>
        <div><strong>Líder del semillero:</strong> {{ $semillero->lider }}</div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  {{-- Barra de acciones sobre la tabla --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Proyectos</h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearProyecto">
      <i class="bi bi-plus-lg me-1"></i> Crear proyecto
    </button>
  </div>

  {{-- Tabla de proyectos --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-white" style="background-color:#2d572c;">
            <tr>
              <th class="px-3 py-3">Nombre del proyecto</th>
              <th>Integrantes</th>
              <th>Estado</th>
              <th style="width:220px;">Progreso</th>
              <th class="text-end pe-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
          @forelse($proyectos as $p)
            <tr>
              <td class="px-3 py-3 fw-semibold">{{ $p->nombre }}</td>
              <td>{{ $p->integrantes }}</td>
              <td>
                @php $isActive = strtolower($p->estado) === 'activo'; @endphp
                <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                  {{ $isActive ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:10px;">
                    <div class="progress-bar" role="progressbar"
                        
                         aria-valuenow="{{ $p->progreso }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                  </div>
                  <small class="text-muted">{{ $p->progreso }}%</small>
                </div>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-primary" disabled>
                  <i class="bi bi-pencil-square"></i> Editar
                </button>
                <button class="btn btn-sm btn-outline-danger" disabled>
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                No hay proyectos registrados para este semillero.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal crear proyecto (solo UI, sin funcionalidad) --}}
<div class="modal fade" id="modalCrearProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear proyecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Nombre del proyecto</label>
            <input type="text" class="form-control" placeholder="Ej: Sistema de monitoreo IoT" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Estado</label>
            <select class="form-select" disabled>
              <option>Activo</option>
              <option>Inactivo</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Integrantes</label>
            <textarea class="form-control" rows="2" placeholder="Nombres separados por coma" disabled></textarea>
            <div class="form-text">Este formulario es ilustrativo (sin guardar).</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Progreso</label>
            <input type="range" class="form-range" min="0" max="100" value="0" disabled>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button class="btn btn-success" disabled>Guardar (demo)</button>
      </div>
    </div>
  </div>
</div>
@endsection
