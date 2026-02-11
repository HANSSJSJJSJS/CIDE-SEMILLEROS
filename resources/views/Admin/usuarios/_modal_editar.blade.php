{{-- resources/views/admin/usuarios/_modal_editar.blade.php --}}
@php
    // Solo para mostrar etiquetas de rol en el badge (lo llena JS)
    $rolesLabels = [
        'ADMIN'               => 'Líder general',
        'LIDER_SEMILLERO'     => 'Líder de semillero',
        'LIDER_INVESTIGACION' => 'Líder de investigación',
        'APRENDIZ'            => 'Aprendiz',
    ];
@endphp

<div class="modal fade"
     id="modalEditarUsuario"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <div class="d-flex flex-column flex-md-row w-100 justify-content-between align-items-md-center">
                    <div>
                        <h4 class="modal-title mb-1">
                            <i class="bi bi-pencil-square"></i>
                            Editar usuario
                        </h4>
                        <small class="text-muted">
                            Algunos datos básicos están bloqueados y solo pueden modificarse por el administrador del sistema.
                        </small>
                    </div>

                    <div class="text-md-end">
                        <span class="small text-muted d-block">Rol</span>
                        <span id="editar-rol-label"
                              class="badge bg-primary fs-6 px-3 py-2">
                            {{-- Lo rellena JS --}}
                        </span>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- FORM --}}
            <form id="formEditarUsuario"
                  method="POST"
                  action=""   {{-- la action la pone JS /admin/usuarios/{id} --}}
                  class="needs-validation"
                  novalidate
                  autocomplete="off">
                @csrf
                @method('PUT')

                <div class="modal-body" style="max-height: calc(100vh - 260px); overflow-y: auto;">

                    {{-- ================= DATOS BÁSICOS (SOLO LECTURA) ================= --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Datos básicos del usuario
                            </h6>

                            <div class="row g-3">

                                {{-- Tipo documento (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de documento</label>
                                    <input type="text"
                                           id="edit_tipo_documento"
                                           name="tipo_documento"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Documento (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Número de documento</label>
                                    <input type="text"
                                           id="edit_documento"
                                           name="documento"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Nombres (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Nombres</label>
                                    <input type="text"
                                           id="edit_nombre"
                                           name="nombre"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Apellidos (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text"
                                           id="edit_apellidos"
                                           name="apellido"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Género (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Género</label>
                                    <input type="text"
                                           id="edit_genero"
                                           name="genero"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Tipo RH (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de RH</label>
                                    <input type="text"
                                           id="edit_tipo_rh"
                                           name="tipo_rh"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Celular (EDITABLE) --}}
                                <div class="col-md-3">
                                    <label class="form-label">
                                        Celular <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="celular"
                                           id="edit_celular"
                                           class="form-control"
                                           required>
                                    <div class="invalid-feedback">Ingrese el celular.</div>
                                </div>

                                {{-- Correo personal / acceso (EDITABLE) --}}
                                <div class="col-md-3">
                                    <label class="form-label">
                                        Correo de acceso <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="edit_email"
                                           class="form-control"
                                           required>
                                    <div class="invalid-feedback">Ingrese un correo válido.</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ============ BLOQUE LÍDER / LÍDER INVESTIGACIÓN (INSTITUCIONAL) ============ --}}
                    <div id="editar-bloque-lider" class="card border-0 shadow-sm mb-3 d-none">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-building me-1"></i>
                                Información institucional del líder
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Correo institucional <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="ls_correo_institucional"
                                           id="edit_ls_correo_institucional"
                                           class="form-control">
                                    <div class="invalid-feedback">Ingrese el correo institucional.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Semillero que lidera</label>
                                    <input type="text"
                                           id="edit_ls_semillero_nombre"
                                           class="form-control form-control-sm bg-light text-muted border-0"
                                           readonly>
                                    {{-- Si luego quieres permitir cambiar el semillero, aquí
                                         podrías sustituir por un <select>. --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===================== BLOQUE APRENDIZ ===================== --}}
                    <div id="editar-bloque-aprendiz" class="card border-0 shadow-sm mb-3 d-none">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-mortarboard me-1"></i>
                                Información del aprendiz
                            </h6>

                            <div class="row g-3">
                                {{-- Semillero (editable) --}}
                                <div class="col-md-6">
                                    <label class="form-label">Semillero</label>
                                    <select
                                        id="edit_ap_semillero_select"
                                        name="semillero_id"
                                        class="form-select form-select-sm"
                                        disabled>
                                        <option value="">Seleccione un semillero</option>
                                        @foreach(($semilleros ?? collect()) as $s)
                                            <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Correo institucional (EDITABLE) --}}
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Correo institucional <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="correo_institucional"
                                           id="edit_ap_correo_institucional"
                                           class="form-control">
                                    <div class="invalid-feedback">Ingrese el correo institucional.</div>
                                </div>

                                {{-- Nivel educativo (RO) --}}
                                <div class="col-md-4">
                                    <label class="form-label">Nivel educativo</label>
                                    <input type="text"
                                           id="edit_ap_nivel_educativo"
                                           class="form-control form-control-sm bg-light text-muted border-0"
                                           readonly>
                                </div>

                                {{-- Ficha (editable) --}}
                                <div class="col-md-4">
                                    <label class="form-label">Ficha</label>
                                    <input type="text"
                                           id="edit_ap_ficha"
                                           name="ficha"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Programa (editable) --}}
                                <div class="col-md-4">
                                    <label class="form-label">Programa</label>
                                    <input type="text"
                                           id="edit_ap_programa"
                                           name="programa"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Institución (editable) --}}
                                <div class="col-md-6">
                                    <label class="form-label">Institución</label>
                                    <input type="text"
                                           id="edit_ap_institucion"
                                           name="institucion"
                                           class="form-control form-control-sm">
                                </div>

                                {{-- Contacto emergencia (EDITABLE) --}}
                                <div class="col-md-6">
                                    <label class="form-label">Nombre contacto de emergencia</label>
                                    <input type="text"
                                           name="contacto_nombre"
                                           id="edit_ap_contacto_nombre"
                                           class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Celular contacto de emergencia</label>
                                    <input type="text"
                                           name="contacto_celular"
                                           id="edit_ap_contacto_celular"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /modal-body --}}

                {{-- FOOTER --}}
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
