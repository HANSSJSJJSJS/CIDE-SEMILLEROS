<!-- MODAL CREAR RECURSO (BOOTSTRAP) -->
<div class="modal fade" id="modalSubirRecurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content modal-evidencia">

            <div class="modal-header border-0">
                <h4 class="modal-title fw-bold text-primary" id="tituloModalRecurso">
                    Crear Recurso
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="formSubirRecurso">
                    @csrf

                    <!-- SEMILLERO -->
                    <div class="mb-3">
                        <label class="form-label">Semillero</label>
                        <select class="form-select" id="semillero_id" name="semillero_id">
                            <option value="">Selecciona…</option>
                            @foreach($semilleros ?? [] as $sem)
                                <option value="{{ $sem->id_semillero }}">{{ $sem->nombre }}</option>
                            @endforeach
                        </select>

                        <!-- SE USA CUANDO EL BOTÓN ES DE UNA TARJETA -->
                        <input type="hidden" id="semillero_id_fijo">
                    </div>

                    <!-- PROYECTO -->
                    <div class="mb-3">
                        <label class="form-label">Proyecto</label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id" disabled required>
                            <option value="">Seleccione un semillero…</option>
                        </select>
                    </div>

                    <!-- LÍDER -->
                    <div class="mb-3">
                        <label class="form-label">Líder del Semillero</label>
                        <input type="text" class="form-control" id="lider_nombre" readonly>
                        <input type="hidden" id="lider_id" name="lider_id">
                    </div>

                    <!-- TÍTULO -->
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" required></textarea>
                    </div>

                    <!-- FECHA LÍMITE -->
                    <div class="mb-3">
                        <label class="form-label">Fecha Límite</label>
                        <input type="date" class="form-control" name="fecha_limite" required>
                    </div>

                    <!-- BOTONES -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success rounded-pill">
                            Guardar Recurso
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
