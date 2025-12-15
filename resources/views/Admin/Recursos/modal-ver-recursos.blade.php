{{-- MODAL VER RECURSOS (ESTILO PROFESIONAL INSTITUCIONAL) --}}
<div class="modal fade" id="modalActividades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content modal-recursos">

            {{-- HEADER --}}
            <div class="p-3 d-flex justify-content-between align-items-center"
                 style="background: var(--brand-primary); border-radius: 12px 12px 0 0;">
                
                <h4 class="m-0 fw-bold text-white" id="tituloModalActividades">
                    Recursos del Semillero
                </h4>

                <button class="btn btn-light btn-sm" data-bs-dismiss="modal"
                        style="border-radius: 50%; width: 32px; height: 32px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body" id="contenedorActividades" style="padding: 25px;">
                {{-- contenido dinámico --}}
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer border-0">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
