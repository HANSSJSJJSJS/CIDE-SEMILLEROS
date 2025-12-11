{{-- MODAL UNIVERSAL PARA SUBIR RECURSO --}}
<div class="modal-overlay" id="modalSubirRecurso">
    <div class="modal-evidencia">
        
        {{-- TÍTULO --}}
        <h2 id="tituloModalRecurso">Subir Recurso</h2>

        <form id="formSubirRecurso">
            @csrf

            {{-- SEMILLERO --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Semillero</label>

                <select class="form-select-evidencia" id="semillero_id" name="semillero_id">
                    <option value="">Selecciona el semillero...</option>
                    @foreach($semilleros ?? [] as $sem)
                        <option value="{{ $sem->id_semillero }}">{{ $sem->nombre }}</option>
                    @endforeach
                </select>

                {{-- input hidden si viene desde tarjeta --}}
                <input type="hidden" id="semillero_id_fijo">
            </div>

            {{-- PROYECTO --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Proyecto del Semillero</label>

                <select class="form-select-evidencia" id="proyecto_id" name="proyecto_id" required disabled>
                    <option value="">Selecciona el semillero primero…</option>
                </select>
            </div>

            {{-- LÍDER --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Líder del Semillero</label>

                <input type="text" class="form-control-evidencia" id="lider_nombre" readonly>
                <input type="hidden" id="lider_id" name="lider_id">
            </div>

            {{-- TÍTULO --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Título del Recurso</label>
                <input type="text" class="form-control-evidencia" name="titulo" required>
            </div>

            {{-- DESCRIPCIÓN --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Descripción</label>
                <textarea class="form-control-evidencia" name="descripcion" required></textarea>
            </div>

            {{-- FECHA LÍMITE --}}
            <div class="mb-3">
                <label class="form-label-evidencia">Fecha límite</label>
                <input type="date" class="form-control-evidencia" name="fecha_limite" required>
            </div>

            {{-- BOTONES --}}
            <div class="modal-botones">
                <button type="button" class="btn-cancelar-modal" id="btnCancelarRecurso">
                    Cancelar
                </button>

                <button type="submit" class="btn-guardar-modal">
                    <i class="bi bi-check-circle me-1"></i>
                    Guardar Recurso
                </button>
            </div>

        </form>
    </div>
</div>
