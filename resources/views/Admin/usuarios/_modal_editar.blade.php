{{-- MODAL EDITAR USUARIO --}}
@php
    /** @var \App\Models\User $usuario */
@endphp

<div class="modal fade" id="modalEditarUsuario{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="POST"
                  action="{{ route('admin.usuarios.update', $usuario->id) }}"
                  class="needs-validation"
                  novalidate
                  autocomplete="off">

                @csrf
                @method('PUT')

                <div class="modal-body" style="max-height: calc(100vh - 260px); overflow-y:auto;">

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">
                                Datos básicos del usuario
                            </h6>

                            <div class="row g-3">
                                {{-- Tipo de documento --}}
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                                    <select name="tipo_documento" class="form-select" required>
                                        <option value="">Seleccione…</option>
                                        <option value="CC"  @selected($usuario->tipo_documento === 'CC')>Cédula de ciudadanía</option>
                                        <option value="TI"  @selected($usuario->tipo_documento === 'TI')>Tarjeta de identidad</option>
                                        <option value="CE"  @selected($usuario->tipo_documento === 'CE')>Cédula de extranjería</option>
                                        <option value="PASAPORTE" @selected($usuario->tipo_documento === 'PASAPORTE')>Pasaporte</option>
                                        <option value="PERMISO ESPECIAL" @selected($usuario->tipo_documento === 'PERMISO ESPECIAL')>Permiso especial</option>
                                        <option value="REGISTRO CIVIL" @selected($usuario->tipo_documento === 'REGISTRO CIVIL')>Registro civil</option>
                                    </select>
                                </div>

                                {{-- Documento --}}
                                <div class="col-md-4">
                                    <label class="form-label">Número de documento <span class="text-danger">*</span></label>
                                    <input type="text" name="documento" value="{{ $usuario->documento }}" class="form-control" required>
                                </div>

                                {{-- Celular --}}
                                <div class="col-md-4">
                                    <label class="form-label">Celular</label>
                                    <input type="text" name="celular" value="{{ $usuario->celular }}" class="form-control">
                                </div>

                                {{-- Nombres --}}
                                <div class="col-md-4">
                                    <label class="form-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" value="{{ $usuario->nombre }}" class="form-control" required>
                                </div>

                                {{-- Apellidos --}}
                                <div class="col-md-4">
                                    <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellido" value="{{ $usuario->apellidos }}" class="form-control" required>
                                </div>

                                {{-- Género --}}
                                <div class="col-md-4">
                                    <label class="form-label">Género</label>
                                    <select name="genero" class="form-select">
                                        <option value="">Seleccionar…</option>
                                        <option value="HOMBRE" @selected($usuario->genero === 'HOMBRE')>Hombre</option>
                                        <option value="MUJER" @selected($usuario->genero === 'MUJER')>Mujer</option>
                                        <option value="NO DEFINIDO" @selected($usuario->genero === 'NO DEFINIDO')>No definido</option>
                                    </select>
                                </div>

                                {{-- Tipo de RH --}}
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de RH</label>
                                    <select name="tipo_rh" class="form-select">
                                        <option value="">Seleccionar…</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $rh)
                                            <option value="{{ $rh }}" @selected($usuario->tipo_rh === $rh)>{{ $rh }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Correo de acceso --}}
                                <div class="col-md-4">
                                    <label class="form-label">Correo de acceso <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ $usuario->email }}" class="form-control" required>
                                </div>

                                {{-- Contraseña (opcional) --}}
                                <div class="col-md-5">
                                    <label class="form-label">Contraseña (dejar vacío para no cambiar)</label>
                                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Mínimo 6 caracteres">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($usuario->role === 'LIDER_SEMILLERO')
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Datos del líder de semillero</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Correo institucional</label>
                                        <input type="email" name="ls_correo_institucional" class="form-control" value="{{ old('ls_correo_institucional') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Semillero que lidera</label>
                                        <select name="ls_semillero_id" class="form-select">
                                            <option value="">Seleccionar…</option>
                                            @foreach($semilleros ?? [] as $s)
                                                <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($usuario->role === 'APRENDIZ')
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Perfil de aprendiz</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Semillero</label>
                                        <select name="semillero_id" class="form-select">
                                            <option value="">Seleccionar…</option>
                                            @foreach($semilleros ?? [] as $s)
                                                <option value="{{ $s->id_semillero }}">{{ $s->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Correo institucional</label>
                                        <input type="email" name="correo_institucional" class="form-control" value="{{ old('correo_institucional') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Vinculado al SENA</label>
                                        <select name="vinculado_sena" class="form-select">
                                            <option value="">Seleccionar…</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nivel educativo</label>
                                        <select name="nivel_educativo" class="form-select">
                                            <option value="">Seleccionar…</option>
                                            <option value="ARTICULACION_MEDIA_10_11">Articulación media 10/11</option>
                                            <option value="TECNOACADEMIA_7_9">Tecnoacademia 7/9</option>
                                            <option value="TECNOLOGO">Tecnólogo</option>
                                            <option value="PROFESIONAL">Profesional</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ficha</label>
                                        <input type="text" name="ficha" class="form-control" value="{{ old('ficha') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Programa</label>
                                        <input type="text" name="programa" class="form-control" value="{{ old('programa') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Institución</label>
                                        <input type="text" name="institucion" class="form-control" value="{{ old('institucion') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contacto de emergencia</label>
                                        <input type="text" name="contacto_nombre" class="form-control" value="{{ old('contacto_nombre') }}" placeholder="Nombre">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Celular contacto</label>
                                        <input type="text" name="contacto_celular" class="form-control" value="{{ old('contacto_celular') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
