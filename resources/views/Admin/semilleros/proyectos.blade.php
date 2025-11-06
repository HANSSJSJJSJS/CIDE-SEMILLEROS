@extends('layouts.admin')

@section('content')

{{-- Bloques para errores del modal y reapertura automática --}}
@if ($errors->crearProyecto->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->crearProyecto->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if (session('openModal') === 'modalCrearProyecto')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const el = document.getElementById('modalCrearProyecto');
      if (el) new bootstrap.Modal(el).show();
    });
  </script>
@endif

<div class="container-fluid mt-4 px-4">
  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  {{-- Encabezado --}}
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h2 class="fw-bold mb-1" style="color:#2d572c;">{{ $semillero->nombre }}</h2>
      <div class="text-muted">
        <div class="mb-1">
          <strong>Línea de investigación:</strong> {{ $semillero->linea_investigacion ?? '—' }}
        </div>
        <div>
          <strong>Líder del semillero:</strong>
          @if($semillero->lider)
            {{ trim($semillero->lider->nombres.' '.$semillero->lider->apellidos) }}
            <span class="text-muted"> · {{ $semillero->lider->correo_institucional }}</span>
          @else
            <em>Sin asignar</em>
          @endif
        </div>
      </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  {{-- Barra de acciones --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Proyectos</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearProyecto">
      <i class="bi bi-plus-lg me-1"></i> Crear proyecto
    </button>
  </div>

  {{-- Tabla --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-white" style="background-color:#2d572c;">
            <tr>
              <th class="px-3 py-3">Nombre</th>
              <th>Estado</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Descripción</th>
              <th class="text-end pe-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
          @forelse($proyectos as $p)
            <tr>
              <td class="px-3 py-3 fw-semibold">{{ $p->nombre_proyecto }}</td>
              <td>
                @php
                  $map = [
                    'EN_FORMULACION' => ['En formulación','bg-secondary'],
                    'EN_EJECUCION'   => ['En ejecución','bg-primary'],
                    'FINALIZADO'     => ['Finalizado','bg-success'],
                    'ARCHIVADO'      => ['Archivado','bg-dark'],
                  ];
                  $b = $map[$p->estado] ?? [$p->estado,'bg-light text-dark'];
                @endphp
                <span class="badge {{ $b[1] }}">{{ $b[0] }}</span>
              </td>
              <td>{{ optional($p->fecha_inicio)->format('Y-m-d') ?? '—' }}</td>
              <td>{{ optional($p->fecha_fin)->format('Y-m-d') ?? '—' }}</td>
              <td class="text-truncate" style="max-width:360px;">{{ $p->descripcion }}</td>
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
              <td colspan="6" class="text-center py-5 text-muted">No hay proyectos registrados.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal crear proyecto --}}
<div class="modal fade" id="modalCrearProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST"
          action="{{ route('admin.semilleros.proyectos.store', $semillero->id_semillero) }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Crear proyecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Estado</label>
            <select name="estado" class="form-select @error('estado','crearProyecto') is-invalid @enderror" required>
              @foreach(['EN_FORMULACION'=>'En formulación','EN_EJECUCION'=>'En ejecución','FINALIZADO'=>'Finalizado','ARCHIVADO'=>'Archivado'] as $val=>$txt)
                <option value="{{ $val }}" @selected(old('estado')===$val)>{{ $txt }}</option>
              @endforeach
            </select>
            @error('estado','crearProyecto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Nombre del proyecto</label>
            <input type="text" name="nombre_proyecto"
                   class="form-control @error('nombre_proyecto','crearProyecto') is-invalid @enderror"
                   value="{{ old('nombre_proyecto') }}" required>
            @error('nombre_proyecto','crearProyecto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea name="descripcion" class="form-control @error('descripcion','crearProyecto') is-invalid @enderror"
                      rows="3">{{ old('descripcion') }}</textarea>
            @error('descripcion','crearProyecto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Fecha de inicio</label>
            <input type="date" name="fecha_inicio"
                   class="form-control @error('fecha_inicio','crearProyecto') is-invalid @enderror"
                   value="{{ old('fecha_inicio') }}">
            @error('fecha_inicio','crearProyecto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Fecha fin</label>
            <input type="date" name="fecha_fin"
                   class="form-control @error('fecha_fin','crearProyecto') is-invalid @enderror"
                   value="{{ old('fecha_fin') }}">
            @error('fecha_fin','crearProyecto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cerrar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection
