@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider_semi/recursos.css') }}">
@endpush

@section('content')
<div class="container py-4">

    {{-- TÍTULO PRINCIPAL --}}
    <h2 class="mb-4 fw-bold text-primary">Recursos asignados a mi Semillero</h2>

    {{-- DATOS DEL SEMILLERO --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <strong>Semillero:</strong> {{ $semillero->nombre }} <br>
            <strong>Líder:</strong> {{ Auth::user()->name }}
        </div>
    </div>

    {{-- SI NO HAY RECURSOS --}}
    @if ($recursos->isEmpty())
        <div class="estado-vacio text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: .3;"></i>
            <p class="mt-3 text-muted">No tienes recursos asignados aún.</p>
        </div>
    @endif

    {{-- LISTA DE RECURSOS --}}
    <div class="row">
        @foreach($recursos as $r)
        <div class="col-md-6 mb-4">
            <div class="proyecto-card">

                <div class="card-body">

                    {{-- TÍTULO --}}
                    <h5 class="proyecto-titulo">{{ $r->titulo }}</h5>

                    {{-- ESTADO --}}
                    <p class="proyecto-descripcion">
                        @if($r->estado == 'pendiente')
                            <span class="badge badge-pendiente">Pendiente</span>
                        @elseif($r->estado == 'aprobado')
                            <span class="badge badge-aprobado">Aprobado</span>
                        @else
                            <span class="badge badge-rechazado">Rechazado</span>
                        @endif
                    </p>

                    {{-- DESCRIPCIÓN --}}
                    <p class="text-muted small">{{ $r->descripcion ?? 'Sin descripción.' }}</p>

                    {{-- ARCHIVO DEL LÍDER GENERAL --}}
                    <p class="mb-2">
                        <strong>Documento entregado:</strong><br>
                        <a href="{{ route('lider_semi.recursos.download', $r->id) }}"
                           class="btn-ver-documento mt-1">
                            <i class="bi bi-file-earmark-arrow-down"></i> Descargar
                        </a>
                    </p>

                    <hr>

                    {{-- RESPUESTA DEL LÍDER DE SEMILLERO --}}
                    <div class="entrega-acciones">

                        {{-- SUBIR/EDITAR RESPUESTA --}}
                        @if($r->estado == 'pendiente')
                            <a href="{{ route('lider_semi.recursos.responder', $r->id) }}"
                               class="btn-aprobar w-100">
                                <i class="bi bi-upload"></i> Enviar evidencia
                            </a>
                        @endif

                        {{-- VER OBSERVACIONES --}}
                        @if($r->estado == 'rechazado')
                            <p class="mt-3 text-danger fw-semibold">
                                <i class="bi bi-x-circle"></i> Rechazado: {{ $r->comentarios }}
                            </p>
                        @endif

                        @if($r->estado == 'aprobado')
                            <p class="mt-3 text-success fw-semibold">
                                <i class="bi bi-check-circle"></i> Recurso aprobado ✔
                            </p>
                        @endif

                    </div>

                </div>

            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
