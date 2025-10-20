@extends('layouts.lider_semi')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/mis-proyectos.css') }}">
@endsection

@section('content')
<div class="container mt-4">
    <div class="page-header">
        <h4 class="fw-bold mb-4">Mis Proyectos</h4>
    </div>
    <p class="text-muted">Gestiona y supervisa todos tus semilleros activos</p>

    <div class="row">
        @foreach($semilleros as $semillero)
        <div class="col-md-4 mb-4">
            <div class="card card-project">
                <div class="card-header text-white fw-bold">
                    {{ $semillero->nombre }}
                </div>
                <div class="card-body">
                    <span class="badge badge-status">{{ $semillero->estado }}</span>
                    <small class="text-muted float-end">{{ $semillero->aprendices }} aprendices</small>
                    <p class="mt-3 text-secondary">{{ $semillero->descripcion }}</p>

                    <div class="mt-3">
                        <small>Progreso</small>
                        <div class="progress">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $semillero->progreso }}%;"
                                aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted float-end mt-1">{{ $semillero->progreso }}%</small>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="#" class="btn btn-details w-100">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
