{{-- Modal Subir Multimedia --}}
<div class="modal-overlay" id="modalSubirMultimedia">
    <div class="modal-evidencia">

        {{-- BOTÓN CERRAR (X) --}}
        <button class="btn-cerrar-modal" data-close-modal="multimedia">×</button>

        <h2 class="mb-4">Subir Multimedia</h2>

        <form id="formSubirMultimedia" enctype="multipart/form-data">
            @csrf

            {{-- TÍTULO --}}
            <label class="form-label-evidencia">Título *</label>
            <input type="text" class="form-control-evidencia" name="titulo" required>

            {{-- CATEGORÍA --}}
            <label class="form-label-evidencia mt-3">Categoría *</label>
            <select class="form-select-evidencia" name="categoria" required>
                <option value="plantillas">Plantillas / Presentaciones</option>
                <option value="manuales">Manuales</option>
                <option value="otros">Otros</option>
            </select>

            {{-- DESTINO --}}
            <label class="form-label-evidencia mt-3">Destino *</label>
            <select class="form-select-evidencia" name="destino" id="destinoSeleccion" required>
                <option value="todos">Para todos los líderes</option>
                <option value="semillero">Para un semillero específico</option>
            </select>

            {{-- SEMILLERO --}}
            <div id="campoSemillero" class="d-none mt-2">
                <label class="form-label-evidencia">Selecciona Semillero *</label>
                <select class="form-select-evidencia" name="semillero_id">
                    <option value="">Seleccione...</option>

                    @foreach($semilleros as $s)
                        <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- DESCRIPCIÓN --}}
            <label class="form-label-evidencia mt-3">Descripción (opcional)</label>
            <textarea class="form-control-evidencia form-textarea-evidencia"
                      name="descripcion"
                      rows="3">
            </textarea>

            {{-- ARCHIVO --}}
            <label class="form-label-evidencia mt-3">Archivo *</label>
            <input type="file" class="form-control-evidencia" name="archivo" required>

            {{-- BOTONES --}}
            <div class="modal-botones mt-4">
                <button type="button" class="btn-cancelar-modal" data-close-modal="multimedia">
                    Cancelar
                </button>

                <button type="submit" class="btn-guardar-modal">
                    <i class="bi bi-upload"></i> Subir
                </button>
            </div>

        </form>
    </div>
</div>
