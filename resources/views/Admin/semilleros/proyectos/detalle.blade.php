{{-- resources/views/admin/semilleros/proyectos/detalle.blade.php --}}
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">
@endpush

@section('content')

@php
    $user      = auth()->user();
    $canUpdate = $user->canManageModule('proyectos','update');
    $canDelete = $user->canManageModule('proyectos','delete');
@endphp

<div class="container-fluid px-4 mt-4 semilleros-wrapper">

    {{-- ================================
         ENCABEZADO
    ================================== --}}
    <div class="semilleros-header d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color:#2d572c;">
                {{ $semillero->nombre }}
            </h2>
            <h4 class="text-muted mb-0">
                {{ $proyecto->nombre_proyecto }}
            </h4>
        </div>

        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin.semilleros.proyectos.index', $semillero->id_semillero) }}"
               class="btn btn-accion-ver">
                <i class="bi bi-arrow-left me-1"></i> Volver a proyectos
            </a>
        </div>
    </div>

    {{-- ================================
         DESCRIPCIÓN DEL PROYECTO
    ================================== --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h5 class="fw-semibold mb-2" style="color:#2d572c;">Descripción del proyecto</h5>
            <p class="text-muted mb-0">
                {{ $proyecto->descripcion ?? 'Sin descripción disponible.' }}
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- ================================
             INTEGRANTES
        ================================== --}}
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header text-white" style="background-color:#2d572c;">
                    <i class="bi bi-people-fill me-1"></i>
                    Integrantes del proyecto
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($integrantes ?? [] as $i)
                                    <tr>
                                        <td>{{ trim(($i->nombres ?? '').' '.($i->apellidos ?? '')) }}</td>
                                        <td>{{ $i->correo_institucional ?? $i->correo_personal ?? 'Sin correo' }}</td>
                                        <td>{{ optional($i->user)->celular ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Sin integrantes registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================
             DOCUMENTACIÓN
        ================================== --}}
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header text-white" style="background-color:#2d572c;">
                    <i class="bi bi-folder2-open me-1"></i>
                    Documentación del proyecto
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Archivo</th>
                                    <th>Fecha</th>
                                    <th class="text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentacion ?? [] as $doc)
                                    <tr>
                                        <td>{{ $doc->nombre }}</td>
                                        <td>{{ $doc->fecha }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ asset($doc->ruta_archivo) }}"
                                               class="btn btn-sm btn-descargar-doc"
                                               target="_blank">
                                                <i class="bi bi-download me-1"></i> Descargar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            No hay documentación subida.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- /.row --}}

    {{-- ================================
         OBSERVACIONES
    ================================== --}}
    <div class="card shadow-sm mt-4 mb-5 border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
             style="background-color:#2d572c;">
            <span>
                <i class="bi bi-chat-text me-1"></i>
                Observaciones del líder
            </span>
        </div>

        <div class="card-body">

            {{-- ALERTAS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            {{-- ================================
                 TABLA DE HISTORIAL
            ================================== --}}
            <h6 class="fw-bold mb-2" style="color:#2d572c;">Historial de observaciones</h6>

            <div class="table-responsive mb-4">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 160px;">Fecha</th>
                            <th style="width: 180px;">Autor</th>
                            <th>Observación</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($observacionesHistorial as $obs)
                            <tr>
                                <td>{{ $obs['fecha'] ?? 'N/D' }}</td>
                                <td>{{ $obs['autor'] ?? 'N/D' }}</td>
                                <td>{{ $obs['texto'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    No hay observaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================================
                 FORMULARIO NUEVA OBSERVACIÓN
            ================================== --}}
            <form method="POST"
                    action="{{ route('admin.semilleros.proyectos.observaciones', [$semillero->id_semillero, $proyecto->id_proyecto]) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="nueva_observacion">
                            Agregar nueva observación
                        </label>

                        <textarea
                            name="nueva_observacion"
                            id="nueva_observacion"
                            class="form-control @error('nueva_observacion') is-invalid @enderror"
                            rows="3"
                            placeholder="Escribe aquí una nueva observación..."
                        >{{ old('nueva_observacion') }}</textarea>

                        @error('nueva_observacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button class="btn btn-nuevo-semillero" type="submit">
                            <i class="bi bi-plus-circle me-1"></i> Agregar observación
                        </button>
                    </div>
                </form>


        </div>
    </div>

</div> {{-- /.container-fluid --}}

@endsection
