{{-- MODAL EDITAR SEMILLERO --}}
<div class="modal fade" id="modalEditarSemillero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Semillero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarSemillero"
                  method="POST"
                  action=""   {{-- la llenamos en JS --}}
                  class="needs-validation"
                  novalidate>

                @csrf
                @method('PUT')   {{-- IMPORTANTE para que la ruta update funcione --}}

                <div class="modal-body">

                    <div class="row g-3">
                        {{-- Nombre --}}
                        <div class="col-md-6">
                            <label class="form-label">Nombre del semillero</label>
                            <input type="text"
                                   id="editNombre"
                                   name="nombre"
                                   class="form-control"
                                   required
                                   autocomplete="off"
                                   spellcheck="false">
                        </div>

                        {{-- Línea --}}
                        <div class="col-md-6">
                            <label class="form-label">Línea de investigación</label>
                            <input type="text"
                                   id="editLinea"
                                   name="linea_investigacion"
                                   class="form-control"
                                   required
                                   autocomplete="off"
                                   spellcheck="false">
                        </div>

                        {{-- Líder asignado (solo lectura) --}}
                        <div class="col-md-6">
                            <label class="form-label">Líder asignado</label>
                            <input type="text"
                                   id="liderNombreRO"
                                   class="form-control"
                                   readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo líder</label>
                            <input type="text"
                                   id="liderCorreoRO"
                                   class="form-control"
                                   readonly>
                        </div>

                        {{-- Hidden con el id del líder --}}
                        <input type="hidden" id="editIdLider" name="id_lider_semi">
                    </div>

                    {{-- buscador de líder (esto ya lo tienes) --}}
                    {{-- ... --}}

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
