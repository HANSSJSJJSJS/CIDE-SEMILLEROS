{{-- MODAL EDITAR SEMILLERO --}}
<div class="modal fade" id="modalEditarSemillero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar semillero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarSemillero"
                  method="POST"
                  action="" {{-- se rellena desde JS con /admin/semilleros/{id} --}}
                  class="needs-validation"
                  novalidate
                  autocomplete="off">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- ============================
                         DATOS BÁSICOS DEL SEMILLERO
                    ============================= --}}
                    <div class="row g-3 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nombre del semillero</label>
                            <input type="text"
                                   id="editNombre"
                                   name="nombre"
                                   class="form-control"
                                   placeholder="Ej: GEDS, InnovaTech"
                                   required>
                            <div class="invalid-feedback">Ingrese el nombre del semillero.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Línea de investigación</label>
                            <input type="text"
                                   id="editLinea"
                                   name="linea_investigacion"
                                   class="form-control"
                                   placeholder="Ej: Videojuegos, Energías renovables"
                                   required>
                            <div class="invalid-feedback">Ingrese la línea de investigación.</div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ============================
                         LÍDER ACTUAL DEL SEMILLERO
                    ============================= --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-0">Líder asignado actualmente</label>
                        <small class="text-muted d-block">
                            Estos datos muestran el líder que tiene el semillero en este momento.
                        </small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text"
                                   id="editarLiderNombreActual"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo institucional</label>
                            <input type="text"
                                   id="editarLiderCorreoActual"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ============================
                         CAMBIAR LÍDER (OPCIONAL)
                    ============================= --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-0">Cambiar líder de semillero (opcional)</label>
                        <small class="text-muted d-block">
                            Puedes escoger un líder libre, escribir para buscar o usar el botón
                            <strong>"Buscar líder"</strong>. Si no seleccionas ninguno, se conservará el líder actual
                            (o quedará sin líder).
                        </small>
                    </div>

                    {{-- === LISTA DESPLEGABLE DE LÍDERES DISPONIBLES === --}}
                    <div class="row g-2 align-items-stretch mb-2">
                        <div class="col-12 col-md-9">
                            <select id="selectLiderEditarDisponible" class="form-select">
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
                                    id="btnLimpiarLiderEditar"
                                    class="btn btn-outline-secondary">
                                Quitar líder
                            </button>
                        </div>
                    </div>

                    {{-- === BÚSQUEDA MANUAL DE LÍDER === --}}
                    <div class="row g-2 align-items-stretch mb-2">
                        <div class="col-12 col-md-9">
                            <input type="text"
                                   id="buscarLiderEditar"
                                   class="form-control"
                                   placeholder="Nombre, apellido, documento o correo"
                                   autocomplete="off">
                        </div>

                        <div class="col-12 col-md-3 d-grid">
                            <button type="button"
                                    id="btnBuscarLiderEditar"
                                    data-url-lideres="{{ route('admin.semilleros.lideresDisponibles') }}"
                                    class="btn btn-outline-secondary">
                                <i class="bi bi-search me-1"></i> Buscar líder
                            </button>
                        </div>
                    </div>

                    {{-- === RESULTADOS DE BÚSQUEDA === --}}
                    <div id="resultadosLiderEditar"
                         class="list-group mb-3"
                         style="max-height: 220px; overflow-y:auto;"></div>

                    {{-- === LÍDER SELECCIONADO (NUEVO) === --}}
                    <input type="hidden" name="id_lider_semi" id="idLiderEditar">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Nuevo líder seleccionado</label>
                            <input type="text"
                                   id="editarLiderNombreRO"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Correo institucional (nuevo)</label>
                            <input type="text"
                                   id="editarLiderCorreoRO"
                                   class="form-control"
                                   value="—"
                                   readonly>
                        </div>
                    </div>

                </div> {{-- /modal-body --}}

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-user-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
