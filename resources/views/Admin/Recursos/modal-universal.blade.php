<!-- MODAL CREAR RECURSO (BOOTSTRAP) -->
<div class="modal fade" id="modalSubirRecurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content modal-evidencia">

            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="tituloModalRecurso">
                    Crear Recurso
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="formSubirRecurso">
                    @csrf

                    <!-- SEMILLERO -->
                    <div class="mb-3">
                        <label class="form-label">Semillero <span class="text-danger">*</span></label>
                        <select class="form-select" id="semillero_id" name="semillero_id" required>
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
                        <label class="form-label">Proyecto <span class="text-danger">*</span></label>
                        <select class="form-select" id="proyecto_id" name="proyecto_id" disabled required>
                            <option value="">Seleccione un semillero…</option>
                        </select>
                    </div>

                    <!-- TIPO DE RECURSO -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de Recurso <span class="text-danger">*</span></label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="">Selecciona el tipo…</option>
                            <option value="plantillas">Plantillas / Presentaciones</option>
                            <option value="manuales">Manuales</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>

                    <!-- TIPO DE DOCUMENTO -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Selecciona el tipo de recurso</option>
                            <option value="pdf">Documento PDF</option>
                            <option value="enlace">Enlace</option>
                            <option value="documento">Documento Word</option>
                            <option value="presentacion">Presentación</option>
                            <option value="video">Video</option>
                            <option value="imagen">Imagen</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- LÍDER -->
                    <div class="mb-3">
                        <label class="form-label">Líder del Semillero</label>
                        <input type="text" class="form-control" id="lider_nombre" placeholder="Nombre del líder" readonly>
                        <input type="hidden" id="lider_id" name="lider_id">
                    </div>

                    <!-- TÍTULO -->
                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titulo" placeholder="Título del recurso" required>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" placeholder="Describe el recurso..." rows="4" required></textarea>
                    </div>

                    <!-- FECHA LÍMITE -->
                    <div class="mb-3">
                        <label class="form-label">Fecha Límite <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_limite" required>
                    </div>

                    <!-- BOTONES -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success">
                            Guardar Recurso
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
