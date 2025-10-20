@extends('layouts.lider_semi')

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold mb-4">Mis Semilleros</h4>
    <p class="text-muted">Gestiona y supervisa todos tus semilleros activos</p>

    <div class="row">
        @foreach($semilleros as $semillero)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header text-white fw-bold"
                    style="background: linear-gradient(90deg, #2ecc71, #16a085);">
                    {{ $semillero->nombre }}
                </div>
                <div class="card-body">
                    <span class="badge bg-success">{{ $semillero->estado }}</span>
                    <small class="text-muted float-end">{{ $semillero->aprendices }} aprendices</small>
                    <p class="mt-3 text-secondary">{{ $semillero->descripcion }}</p>

                    <div class="mt-3">
                        <small>Progreso</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $semillero->progreso }}%;"
                                aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted float-end mt-1">{{ $semillero->progreso }}%</small>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="#" class="btn btn-dark w-100">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
