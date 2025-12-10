<div class="col-md-4">
    <div class="card proyecto-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="proyecto-titulo">{{ $semillero->nombre }}</h5>
                <span class="{{ $semillero->estado === 'inactivo' ? 'badge badge-completado' : 'badge badge-activo' }}">
                    {{ $semillero->estado === 'inactivo' ? 'INACTIVO' : 'ACTIVO' }}
                </span>
            </div>

            <p class="proyecto-descripcion">
                {{ $semillero->descripcion ?: 'Sin descripción' }}
            </p>

            <div class="semillero-lider mb-3">
                <i class="bi bi-person-badge me-2"></i>
                <strong>Líder:</strong> {{ $semillero->lider_nombre ?? 'Sin líder asignado' }}
            </div>

            <div class="row text-center proyecto-estadisticas">
                <div class="col-4">
                    <div class="estadistica-numero">{{ $semillero->actividades_total }}</div>
                    <small class="estadistica-label">Recursos</small>
                </div>
                <div class="col-4">
                    <div class="estadistica-numero">{{ $semillero->actividades_pendientes }}</div>
                    <small class="estadistica-label">Pendientes</small>
                </div>
                <div class="col-4">
                    <div class="estadistica-numero">{{ $semillero->actividades_aprobadas }}</div>
                    <small class="estadistica-label">Completados</small>
                </div>
            </div>

            <div class="mt-3 d-grid gap-2">
                <button
                    class="btn btn-ver-entregas btn-ver-actividades"
                    data-semillero-id="{{ $semillero->id_semillero }}"
                    data-semillero-nombre="{{ $semillero->nombre }}">
                    <i class="bi bi-card-checklist me-2"></i>Ver Recursos
                </button>

                @if($semillero->id_lider_semi && $canCreate)
                    <button
                        class="btn btn-editar-proyecto btn-crear-actividad-card"
                        data-semillero-id="{{ $semillero->id_semillero }}"
                        data-semillero-nombre="{{ $semillero->nombre }}">
                        <i class="bi bi-plus-circle me-2"></i>Crear Recurso para su Líder
                    </button>
                @else
                    <button class="btn btn-editar-proyecto" disabled>
                        <i class="bi bi-exclamation-circle me-2"></i>Sin líder asignado
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
