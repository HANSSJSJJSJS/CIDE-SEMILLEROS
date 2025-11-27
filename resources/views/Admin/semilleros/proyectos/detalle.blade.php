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
         DESCRIPCIÓN
    ================================== --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h5 class="fw-semibold mb-2" style="color:#2d572c;">
                Descripción del proyecto
            </h5>
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
                <div class="card-header text-white"
                     style="background-color:#2d572c;">
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
                                    <td>{{ $i->celular ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">
                                        Sin integrantes registrados.
                                    </td>
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
                <div class="card-header text-white"
                     style="background-color:#2d572c;">
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
                                      <a  href="{{ asset($doc->ruta_archivo) }}"   {{-- ajusta si tu ruta va en /storage --}}
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
         SEGUIMIENTO REUNIONES (DEMO)
    ================================== --}}
    <div class="card shadow-sm mt-4 border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
             style="background-color:#2d572c;">
            <span>
                <i class="bi bi-calendar-check me-1"></i>
                Seguimiento de reuniones
            </span>

            <button class="btn btn-nuevo-semillero btn-sm" disabled>
                <i class="bi bi-plus-circle me-1"></i> Nueva reunión (demo)
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
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
                        {{-- DEMO DATA --}}
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
                            <td class="text-muted">Justificó por enfermedad.</td>
                        </tr>
                        <tr>
                            <td>Valentina Gómez</td>
                            <td><span class="badge text-bg-success">Asistió</span></td>
                            <td class="text-muted">Ideas sobre metodología.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
            <form action="#" method="POST">
                <textarea class="form-control mb-3" rows="4" placeholder="Observaciones...">{{ $observaciones ?? '' }}</textarea>

                <div class="text-end">
                    <button class="btn btn-nuevo-semillero" disabled>
                        <i class="bi bi-save me-1"></i> Guardar (demo)
                    </button>
                </div>
            </form>
        </div>
    </div>

</div> {{-- /.container-fluid --}}

@endsection
