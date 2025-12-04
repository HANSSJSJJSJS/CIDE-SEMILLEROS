{{-- resources/views/admin/semilleros/proyectos/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">
@endpush

@section('content')

@php
    $user      = auth()->user();
    $canCreate = $user->canManageModule('proyectos','create');
    $canUpdate = $user->canManageModule('proyectos','update');
    $canDelete = $user->canManageModule('proyectos','delete');
@endphp

{{-- ERRORES DEL MODAL CREAR --}}
@if ($errors->crearProyecto->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->crearProyecto->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container-fluid px-4 mt-4 semilleros-wrapper">

    {{-- ENCABEZADO DEL SEMILLERO --}}
    <div class="semilleros-header d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color:#2d572c;">
                {{ $semillero->nombre }}
            </h2>

            <div class="text-muted small">
                <div class="mb-1">
                    <strong>Línea de investigación:</strong>
                    {{ $semillero->linea_investigacion ?? '—' }}
                </div>

                <div>
                    <strong>Líder del semillero:</strong>
                    @if($semillero->lider)
                        {{ trim(($semillero->lider->nombres ?? '').' '.($semillero->lider->apellidos ?? '')) }}
                        <span class="text-muted"> · {{ $semillero->lider->correo_institucional }}</span>
                    @else
                        <em>Sin asignar</em>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.semilleros.index') }}" class="btn btn-accion-ver">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    {{-- TÍTULO + BOTÓN CREAR --}}
    <div class="mb-3">
        <h3 class="fw-bold fs-4" style="color:#2d572c;">Proyectos del semillero</h3>

        @if($canCreate)
            <button class="btn btn-nuevo-semillero mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearProyecto">
                <i class="bi bi-plus-lg me-1"></i> Crear proyecto
            </button>
        @endif
    </div>

    {{-- TABLA DE PROYECTOS --}}
    <div class="table-responsive mt-3">
        <table class="table tabla-semilleros table-hover align-middle">
            <thead>
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

                    <td class="text-truncate" style="max-width:360px;">
                        {{ $p->descripcion }}
                    </td>

                    <td class="pe-3">
                        <div class="acciones-proyecto">

                            {{-- Ver detalle --}}
                            <a href="{{ route('admin.semilleros.proyectos.detalle', [$semillero->id_semillero, $p->id_proyecto]) }}"
                               class="btn btn-sm btn-accion-ver">
                                <i class="bi bi-eye me-1"></i> Ver detalle
                            </a>

                            {{-- Editar --}}
                            @if($canUpdate)
                                <button type="button"
                                        class="btn btn-sm btn-accion-editar btn-edit-proyecto"
                                        data-semillero="{{ $semillero->id_semillero }}"
                                        data-proyecto="{{ $p->id_proyecto }}">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </button>
                            @endif

                            {{-- Eliminar --}}
                            @if($canDelete)
                                <form action="{{ route('admin.semilleros.proyectos.destroy', [$semillero->id_semillero, $p->id_proyecto]) }}"
                                      method="POST"
                                      class="m-0 form-delete-proyecto"
                                      data-nombre="{{ $p->nombre_proyecto }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-accion-eliminar" type="submit">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        No hay proyectos registrados.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $proyectos->links('pagination::bootstrap-5') }}
    </div>

</div> {{-- /.container-fluid --}}

{{-- =======================================================
     MODAL CREAR PROYECTO
======================================================= --}}
@if($canCreate)
<div class="modal fade" id="modalCrearProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content"
              method="POST"
              action="{{ route('admin.semilleros.proyectos.store', $semillero->id_semillero) }}">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Crear proyecto</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select" required>
                            @foreach([
                                'EN_FORMULACION' => 'En formulación',
                                'EN_EJECUCION'   => 'En ejecución',
                                'FINALIZADO'     => 'Finalizado',
                                'ARCHIVADO'      => 'Archivado'
                            ] as $v => $t)
                                <option value="{{ $v }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre del proyecto</label>
                        <input type="text" name="nombre_proyecto" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>   
                    
 {{-- ================================
         OBSERVACIONES
    ================================== --}}
    <div class="card shadow-sm mt-4 mb-5 border-0">
        <div class="card-header text-white"
             style="background-color:#2d572c;">
            <i class="bi bi-chat-text me-1"></i>
            Observaciones del líder
        </div>

        <div class="card-body">

            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.semilleros.proyectos.observaciones', [$semillero->id_semillero, $proyecto->id_proyecto]) }}">
                @csrf

                <div class="mb-3">
                    <textarea
                        name="observaciones"
                        class="form-control @error('observaciones') is-invalid @enderror"
                        rows="4"
                        placeholder="Escribe aquí las observaciones del líder..."
                    >{{ old('observaciones', $observaciones) }}</textarea>

                    @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button class="btn btn-nuevo-semillero" type="submit">
                        <i class="bi bi-save me-1"></i> Guardar observaciones
                    </button>
                </div>
            </form>
        </div>
    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button class="btn btn-nuevo-semillero" type="submit">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>

        </form>
    </div>
</div>
@endif

{{-- =======================================================
     MODAL EDITAR PROYECTO
======================================================= --}}
@if($canUpdate)
<div class="modal fade" id="modalEditarProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditarProyecto" class="modal-content" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Editar proyecto</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" id="e_nombre" name="nombre_proyecto" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado</label>
                        <select id="e_estado" name="estado" class="form-select" required>
                            <option value="EN_FORMULACION">En formulación</option>
                            <option value="EN_EJECUCION">En ejecución</option>
                            <option value="FINALIZADO">Finalizado</option>
                            <option value="ARCHIVADO">Archivado</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea id="e_desc" name="descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha inicio</label>
                        <input type="date" id="e_inicio" name="fecha_inicio" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha fin</label>
                        <input type="date" id="e_fin" name="fecha_fin" class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-nuevo-semillero" type="submit">
                    <i class="bi bi-save me-1"></i> Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>
@endif

@endsection

{{-- =======================================================
     JS
======================================================= --}}
@push('scripts')

    {{-- SweetAlert2 y helpers --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function swalSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: msg,
            });
        }

        function swalError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg,
            });
        }

        window.proyectosSemillero = {
            flashSuccess: @json(session('success') ?? session('ok')),
            flashError  : @json(session('error')),
        };
    </script>

    <script src="{{ asset('js/admin/proyectos-semilleros.js') }}"></script>
@endpush

