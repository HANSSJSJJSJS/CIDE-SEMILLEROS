<div class="modal fade" id="modalNuevoSemillero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Crear nuevo semillero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formNuevoSemillero"
                  method="POST"
                  action="{{ route('admin.semilleros.store') }}"
                  autocomplete="off">
                @csrf

                <div class="modal-body">

                    {{-- ============================
                         DATOS BÁSICOS DEL SEMILLERO
                    ============================= --}}
                    <div class="row g-3 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nombre del semillero</label>
                            <input type="text"
                                   name="nombre"
                                   class="form-control"
                                   placeholder="Ej: GEDS, InnovaTech"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Línea de investigación</label>
                            <input type="text"
                                   name="linea_investigacion"
                                   class="form-control"
                                   placeholder="Ej: Videojuegos, Energías renovables"
                                   required>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ============================
                         SELECCIÓN DE LÍDER
                    ============================= --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-0">Líder de semillero (opcional)</label>
                        <small class="text-muted d-block">
                            Puedes escoger un líder libre, escribir para buscar o usar el botón "Buscar líder".
                        </small>
                    </div>

                    {{-- === LISTA DESPLEGABLE DE LÍDERES DISPONIBLES === --}}
                    <div class="row g-2 align-items-stretch mb-2">
                        <div class="col-12 col-md-9">
                            <select id="selectLiderDisponible" class="form-select">
                                <option value="">-- Seleccionar líder disponible --</option>

                                @foreach($lideresDisponibles as $l)
                                    <option value="{{ $l->id_lider_semi }}"
                                            data-nombre="{{ $l->nombre_completo }}"
                                            data-correo="{{ $l->correo_institucional }}">
                                        {{ $l->nombre_completo }} ({{ $l->correo_institucional }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3 d-grid">
                            <button type="button"
                                    id="btnLimpiarLiderNuevo"
                                    class="btn btn-outline-secondary">
                                Quitar líder
                            </button>
                        </div>
                    </div>

                    {{-- === BÚSQUEDA MANUAL DE LÍDER === --}}
                    <div class="row g-2 align-items-stretch mb-2">
                        <div class="col-12 col-md-9">
                            <input type="text"
                                   id="buscarLiderNuevo"
                                   class="form-control"
                                   placeholder="Nombre, apellido, documento o correo"
                                   autocomplete="off">
                        </div>

                        <div class="col-12 col-md-3 d-grid">
                            <button type="button"
                                    id="btnBuscarLiderNuevo"
                                    data-url-lideres="{{ route('admin.semilleros.lideresDisponibles') }}"
                                    class="btn btn-outline-secondary">
                                <i class="bi bi-search me-1"></i> Buscar líder
                            </button>
                        </div>
                    </div>

                    {{-- === RESULTADOS DE BÚSQUEDA === --}}
                    <div id="resultadosLiderNuevo"
                         class="list-group mb-3"
                         style="max-height: 220px; overflow-y:auto;"></div>

                    {{-- === LÍDER SELECCIONADO === --}}
                    <input type="hidden" name="id_lider_semi" id="idLiderNuevo">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nombre completo</label>
                            <input type="text"
                                   id="nuevoLiderNombreRO"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Correo institucional</label>
                            <input type="text"
                                   id="nuevoLiderCorreoRO"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>
                    </div>

                </div> {{-- /modal-body --}}

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>

                    <button type="submit"
                            class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Guardar semillero
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
