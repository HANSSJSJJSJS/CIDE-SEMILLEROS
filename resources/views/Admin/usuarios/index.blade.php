{{-- Si tu layout es el del líder: cambia a layouts.lider_semi --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4 px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold" style="color:#2d572c;">Gestión de Usuarios</h3>

    
       
    </div>

    <!-- (Opcional) Filtros de Búsqueda: los dejo como los tenías -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de Documento</label>
                    <select id="filtro-tipo-doc" class="form-select">
                        <option value="">Todos</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de Documento</label>
                    <input type="text" id="filtro-documento" class="form-control" placeholder="Ej: 1023456789">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" id="filtro-nombre" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="btn-limpiar-filtros" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

{{-- === ERRORES DE VALIDACIÓN (si los hay) === --}}
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- === BOTÓN: abre modal === --}}
<button type="button" class="btn btn-primary mb-3"
        data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
  <i class="fa fa-user-plus me-1"></i> Nuevo Usuario
</button>

{{-- === MODAL NUEVO USUARIO === --}}
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold" id="modalNuevoUsuarioLabel">Registrar usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formNuevoUsuario" method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        <div class="modal-body bg-light">
          <div class="container-fluid">

            {{-- 1) Rol --}}
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <label class="form-label fw-semibold mb-1">Rol</label>
                <select name="role" id="selectRol" class="form-select" required>
                  <option value="">Seleccione un rol...</option>
                  <option value="ADMIN" {{ old('role')=='ADMIN'?'selected':'' }}>Administrador</option>
                  <option value="LIDER_GENERAL" {{ old('role')=='LIDER_GENERAL'?'selected':'' }}>Líder General</option>
                  <option value="LIDER_SEMILLERO" {{ old('role')=='LIDER_SEMILLERO'?'selected':'' }}>Líder de Semillero</option>
                  <option value="APRENDIZ" {{ old('role')=='APRENDIZ'?'selected':'' }}>Aprendiz</option>
                </select>
              </div>
            </div>

            {{-- 2) Campos comunes --}}
            <div id="commonFields" class="d-none">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Nombre</label>
                      <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="María">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Apellidos</label>
                      <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" placeholder="Gómez Pérez">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo (login)</label>
                      <input type="email" name="email" id="emailLogin" class="form-control" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                      <small id="emailHint" class="text-muted"></small>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contraseña</label>
                      <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- 3) Campos por rol --}}

            {{-- LIDER_GENERAL: sin extras --}}
            <div class="role-fields d-none" data-role="LIDER_GENERAL"></div>

            {{-- LIDER_SEMILLERO --}}
            <div class="role-fields d-none" data-role="LIDER_SEMILLERO">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ls_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC" {{ old('ls_tipo_documento')=='CC'?'selected':'' }}>CC</option>
                        <option value="TI" {{ old('ls_tipo_documento')=='TI'?'selected':'' }}>TI</option>
                        <option value="CE" {{ old('ls_tipo_documento')=='CE'?'selected':'' }}>CE</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ls_documento" class="form-control" value="{{ old('ls_documento') }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- APRENDIZ --}}
            <div class="role-fields d-none" data-role="APRENDIZ">
              <div class="card border-0 shadow-sm mb-1">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Ficha</label>
                      <input type="text" name="ap_ficha" class="form-control" value="{{ old('ap_ficha') }}">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Programa</label>
                      <input type="text" name="ap_programa" class="form-control" value="{{ old('ap_programa') }}">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ap_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC" {{ old('ap_tipo_documento')=='CC'?'selected':'' }}>CC</option>
                        <option value="TI" {{ old('ap_tipo_documento')=='TI'?'selected':'' }}>TI</option>
                        <option value="CE" {{ old('ap_tipo_documento')=='CE'?'selected':'' }}>CE</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ap_documento" class="form-control" value="{{ old('ap_documento') }}">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Celular</label>
                      <input type="text" name="ap_celular" class="form-control" value="{{ old('ap_celular') }}">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Correo institucional</label>
                      <input type="email" name="ap_correo_institucional" class="form-control" value="{{ old('ap_correo_institucional') }}" placeholder="usuario@sena.edu.co">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contacto emergencia (nombre)</label>
                      <input type="text" name="ap_contacto_nombre" class="form-control" value="{{ old('ap_contacto_nombre') }}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Celular de contacto</label>
                      <input type="text" name="ap_contacto_celular" class="form-control" value="{{ old('ap_contacto_celular') }}">
                    </div>

                    {{-- Personal = login (oculto) --}}
                    <input type="hidden" name="ap_correo_personal" id="ap_correo_personal" value="{{ old('ap_correo_personal') }}">
                  </div>
                </div>
              </div>
            </div>

            {{-- ADMIN: sin extras --}}
            <div class="role-fields d-none" data-role="ADMIN"></div>

          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectRol   = document.getElementById('selectRol');
  const common      = document.getElementById('commonFields');
  const roleBlocks  = document.querySelectorAll('.role-fields');
  const emailLogin  = document.getElementById('emailLogin');
  const emailHint   = document.getElementById('emailHint');
  const correoPers  = document.getElementById('ap_correo_personal');

  function showByRole(role){
    if (role) common.classList.remove('d-none'); else common.classList.add('d-none');

    roleBlocks.forEach(b => {
      b.classList.toggle('d-none', b.dataset.role !== role);
      b.querySelectorAll('select, input').forEach(el=>{
        el.required = false;
        if (role === 'LIDER_SEMILLERO' && ['ls_tipo_documento','ls_documento'].includes(el.name)) el.required = true;
        if (role === 'APRENDIZ' && ['ap_ficha','ap_programa','ap_documento','ap_correo_institucional'].includes(el.name)) el.required = true;
      });
    });

    if (emailHint) {
      if (role === 'APRENDIZ') emailHint.textContent = 'El correo personal será el mismo que uses para login.';
      else if (role === 'LIDER_GENERAL') emailHint.textContent = 'El correo institucional será el login.';
      else emailHint.textContent = '';
    }
  }

  function syncAprendizPersonal(){
    if (selectRol?.value === 'APRENDIZ' && correoPers && emailLogin) {
      correoPers.value = emailLogin.value || '';
    }
  }

  selectRol?.addEventListener('change', ()=>{ showByRole(selectRol.value); syncAprendizPersonal(); });
  emailLogin?.addEventListener('input', syncAprendizPersonal);

  // ===== Variables Blade como JSON seguro (sin errores de sintaxis en editor) =====
  const hadErrors = JSON.parse(`{!! json_encode($errors->any()) !!}`);
  const oldRole   = JSON.parse(`{!! json_encode(old('role')) !!}`);

  if (hadErrors) {
    const modal = new bootstrap.Modal(document.getElementById('modalNuevoUsuario'));
    modal.show();
    showByRole(oldRole || '');
  } else {
    showByRole(selectRol?.value || '');
  }
});
</script>
@endpush





    {{-- Tabla de Usuarios (modificada) --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color:#2d572c;color:#fff;">
                        <tr>
                            <th class="py-3 px-4">USUARIO</th>
                            <th class="py-3">ROL</th>
                            <th class="py-3">SEMILLERO</th>
                            <th class="py-3">ESTADO</th>
                            <th class="py-3">ÚLTIMA VEZ ACTIVO</th>
                            <th class="py-3 text-end pe-4">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                        <tr>
                            {{-- USUARIO (avatar + nombre + email) --}}
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $nombre = $u->name ?? ($u->nombre_completo ?? 'Usuario');
                                        $partes = preg_split('/\s+/', trim($nombre));
                                        $ini1 = strtoupper(substr($partes[0] ?? 'U', 0, 1));
                                        $ini2 = strtoupper(substr($partes[1] ?? 'S', 0, 1));
                                    @endphp
                                    <div class="rounded-circle d-flex justify-content-center align-items-center"
                                         style="width:42px;height:42px;background-color:#5aa72e;color:#fff;font-weight:700;font-size:14px;">
                                        {{ $ini1 }}{{ $ini2 }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $nombre }}</div>
                                        <small class="text-muted">{{ $u->email ?? 'Sin correo' }}</small>
                                    </div>
                                </div>
                            </td>

                                {{-- ROL --}}
                                <td class="py-3">
                                    @php
                                        // 1) Si tu tabla users tiene la columna 'role' (lo que usas en store)
                                        $roleLabel = $u->role ?? null;

                                        // 2) Si por alguna razón tu columna se llama 'rol'
                                        if (!$roleLabel && isset($u->rol)) {
                                            $roleLabel = $u->rol;
                                        }

                                        // 3) Si usas Spatie (getRoleNames())
                                        if (!$roleLabel && method_exists($u, 'getRoleNames')) {
                                            $roleLabel = $u->getRoleNames()->implode(', ');
                                        }

                                        // Normalizar guiones bajos/espacios (opcional)
                                        if ($roleLabel) {
                                            $roleLabel = str_replace('_', ' ', $roleLabel);
                                        }
                                    @endphp

                                    <span class="badge"
                                        style="background-color:#e8f5e9;color:#2d572c;border:1px solid #5aa72e;padding:6px 12px;border-radius:20px;">
                                        {{ $roleLabel ?? '—' }}
                                    </span>
                                </td>

                            {{-- SEMILLERO --}}
                            <td class="py-3">
                                {{ $u->semillero->nombre ?? $u->semillero_nombre ?? '—' }}
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @php
                                    $activo = $u->estado === 'Activo' || ($u->is_active ?? false);
                                @endphp
                                @if($activo)
                                    <span class="badge bg-success" style="padding:6px 12px;border-radius:20px;">Activo</span>
                                @else
                                    <span class="badge bg-danger" style="padding:6px 12px;border-radius:20px;">Inactivo</span>
                                @endif
                            </td>

                            {{-- ÚLTIMA VEZ ACTIVO --}}
                            <td class="py-3">
                                @php
                                    $last = $u->last_login_at ?? $u->updated_at ?? null;
                                @endphp
                                @if($last)
                                    {{ \Carbon\Carbon::parse($last)->diffForHumans() }}
                                @else
                                    —
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="py-3 text-end pe-4">
                                <a href="{{ route('admin.usuarios.edit', $u->id ?? $u) }}"
                                   class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:4px 12px;">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>

                                <form action="{{ route('admin.usuarios.destroy', $u->id ?? $u) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" style="border-radius:20px;padding:4px 12px;">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <p>No hay usuarios registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación si existiera --}}
    <div class="mt-3">
   {{-- Paginación Bootstrap 5 --}}
@if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
  <div class="mt-3">
    {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
@endif
    </div>
</div>
@endsection