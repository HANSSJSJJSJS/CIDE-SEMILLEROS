@extends('layouts.lider_semi')

@php
    $activos = $activos ?? collect();
    $completados = $completados ?? collect();
@endphp

@section('content')
<div class="container-fluid recursos-page py-4">
    <div class="container">
        <h1 class="fs-3 fw-bold mb-4">Recursos de Proyectos</h1>

        {{-- Proyectos activos --}}
        <section class="mb-4">
            <h2 class="seccion-titulo">Proyectos Activos</h2>
            @if($activos->isEmpty())
                <div class="recursos-empty">No hay proyectos activos.</div>
            @else
                <div class="row g-3">
                    @foreach($activos as $p)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="recurso-card shadow-sm">
                                <div class="recurso-card-header d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h3 class="recurso-titulo">{{ $p['nombre'] ?? 'Proyecto' }}</h3>
                                        <p class="recurso-desc mb-0">{{ $p['descripcion'] ?? 'Sin descripción' }}</p>
                                    </div>
                                    <span class="badge-estado badge-estado-activo">ACTIVO</span>
                                </div>

                                <div class="recurso-metricas d-flex justify-content-between text-center mb-3">
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['entregas'] }}</div>
                                        <div class="recurso-metrica-label">Entregas</div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['pendientes'] }}</div>
                                        <div class="recurso-metrica-label">Pendientes</div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['aprobadas'] }}</div>
                                        <div class="recurso-metrica-label">Aprobadas</div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="button" class="btn btn-success recurso-btn">
                                        Ver Entregas
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
{{--condicional--}}
        {{-- Proyectos completados --}}
        <section>
            <h2 class="seccion-titulo">Proyectos Completados</h2>
            @if($completados->isEmpty())
                <div class="recursos-empty">No hay proyectos completados.</div>
            @else
                <div class="row g-3">
                    @foreach($completados as $p)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="recurso-card shadow-sm">
                                <div class="recurso-card-header d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h3 class="recurso-titulo">{{ $p['nombre'] ?? 'Proyecto' }}</h3>
                                        <p class="recurso-desc mb-0">{{ $p['descripcion'] ?? 'Sin descripción' }}</p>
                                    </div>
                                    <span class="badge-estado badge-estado-completado">COMPLETADO</span>
                                </div>

                                <div class="recurso-metricas d-flex justify-content-between text-center mb-3">
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['entregas'] }}</div>
                                        <div class="recurso-metrica-label">Entregas</div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['pendientes'] }}</div>
                                        <div class="recurso-metrica-label">Pendientes</div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="recurso-metrica-num">{{ $p['aprobadas'] }}</div>
                                        <div class="recurso-metrica-label">Aprobadas</div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="button" class="btn btn-success recurso-btn">
                                        Ver Entregas
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    .recursos-page {
        background: #f5f7fb url('{{ asset('img/fondo_robot_semillero.png') }}') no-repeat center center;
        background-size: contain;
        min-height: calc(100vh - 70px);
    }
    .seccion-titulo {
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .75rem;
    }
    .recursos-empty {
        border-radius: 10px;
        border: 1px dashed #d1d5db;
        background: #ffffffcc;
        padding: 1rem 1.25rem;
        color: #6b7280;
        font-size: .95rem;
    }
    .recurso-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1rem 1.25rem 1.1rem;
    }
    .recurso-titulo {
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 .15rem;
    }
    .recurso-desc {
        font-size: .85rem;
        color: #6b7280;
    }
    .badge-estado {
        display: inline-block;
        padding: .2rem .6rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
    }
    .badge-estado-activo {
        background: #e0f2fe;
        color: #0369a1;
    }
    .badge-estado-completado {
        background: #e4ffc9;
        color: #3f6212;
    }
    .recurso-metricas .recurso-metrica-num {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.1;
    }
    .recurso-metricas .recurso-metrica-label {
        font-size: .8rem;
        color: #6b7280;
    }
    .recurso-btn {
        font-weight: 600;
        background-color: #39A900;
        border-color: #39A900;
    }
    .recurso-btn:hover {
        background-color: #2f8a00;
        border-color: #2f8a00;
    }
</style>
@endpush
