{{-- Si tu layout es el del líder: cambia a layouts.lider_semi --}}
@extends('layouts.admin-lider')

@section('content')
<div class="container-fluid mt-4 px-4">

    <!-- Panel principal con header y filtros tipo pill -->
    <div class="panel-card mb-3">
        <div class="panel-header d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0 fw-bold text-navy">Gestión de Usuarios</h4>
                <small class="text-muted">Administra todos los usuarios del sistema</small>
            </div>
            <button type="button" class="btn btn-success rounded-pill"
                data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                <i class="fa fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>
        <div class="panel-body">
            <div class="row g-3 filters-bar">
                <div class="col-md-5">
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input type="text" id="filtro-nombre" class="form-control pill-input" placeholder="Buscar usuario por nombre,email o documento...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtro-rol" class="form-select pill-select">
                        <option value="">Todos los roles</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="LIDER_GENERAL">LIDER GENERAL</option>
                        <option value="LIDER_SEMILLERO">LIDER SEMILLERO</option>
                        <option value="APRENDIZ">APRENDIZ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filtro-estado" class="form-select pill-select">
                        <option value="">Todos los estados</option>
                        <option value="ACTIVO">Activo</option>
                        <option value="INACTIVO">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2"></div>
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


{{-- === MODAL NUEVO USUARIO === --}}
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header text-white" style="background: var(--sena-navy);">
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
                <select name="role" id="selectRol" class="form-select form-underline" required>
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
                      <input type="text" name="nombre" class="form-control form-underline" value="{{ old('nombre') }}" placeholder="María">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Apellidos</label>
                      <input type="text" name="apellido" class="form-control form-underline" value="{{ old('apellido') }}" placeholder="Gómez Pérez">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo (login)</label>
                      <input type="email" name="email" id="emailLogin" class="form-control form-underline" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                      <small id="emailHint" class="text-muted"></small>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contraseña</label>
                      <input type="password" name="password" class="form-control form-underline" placeholder="Mínimo 6 caracteres">
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
                      <select name="ls_tipo_documento" class="form-select form-underline">
                        <option value="">Seleccione</option>
                        <option value="CC" {{ old('ls_tipo_documento')=='CC'?'selected':'' }}>CC</option>
                        <option value="TI" {{ old('ls_tipo_documento')=='TI'?'selected':'' }}>TI</option>
                        <option value="CE" {{ old('ls_tipo_documento')=='CE'?'selected':'' }}>CE</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ls_documento" class="form-control form-underline" value="{{ old('ls_documento') }}">
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
                      <input type="text" name="ap_ficha" class="form-control form-underline" value="{{ old('ap_ficha') }}">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Programa</label>
                      <input type="text" name="ap_programa" class="form-control form-underline" value="{{ old('ap_programa') }}">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ap_tipo_documento" class="form-select form-underline">
                        <option value="">Seleccione</option>
                        <option value="CC" {{ old('ap_tipo_documento')=='CC'?'selected':'' }}>CC</option>
                        <option value="TI" {{ old('ap_tipo_documento')=='TI'?'selected':'' }}>TI</option>
                        <option value="CE" {{ old('ap_tipo_documento')=='CE'?'selected':'' }}>CE</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ap_documento" class="form-control form-underline" value="{{ old('ap_documento') }}">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Celular</label>
                      <input type="text" name="ap_celular" class="form-control form-underline" value="{{ old('ap_celular') }}">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Correo institucional</label>
                      <input type="email" name="ap_correo_institucional" class="form-control form-underline" value="{{ old('ap_correo_institucional') }}" placeholder="usuario@sena.edu.co">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Contacto emergencia (nombre)</label>
                      <input type="text" name="ap_contacto_nombre" class="form-control form-underline" value="{{ old('ap_contacto_nombre') }}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Celular de contacto</label>
                      <input type="text" name="ap_contacto_celular" class="form-control form-underline" value="{{ old('ap_contacto_celular') }}">
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
  // Submit AJAX del modal de edición
  const formEdit = document.getElementById('formEditUsuario');
  if (formEdit) {
    formEdit.addEventListener('submit', async function(e){
      e.preventDefault();
      const url = this.action;
      const formData = new FormData(this);
      const headers = {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'};
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta) headers['X-CSRF-TOKEN'] = meta.content;
      const res = await fetch(url, { method:'POST', headers, body: formData});
      if(!res.ok){ showToast('Error al guardar cambios', 'danger'); return; }
      let data = null;
      const ct = res.headers.get('content-type') || '';
      if (ct.includes('application/json')) {
        data = await res.json().catch(()=>null);
      }
      if (data && data.ok === false) { showToast('No se pudo actualizar', 'danger'); return; }

      // Actualizar fila (nombre, email, rol, estado)
      const id = formData.get('id');
      const row = document.querySelector(`button.btn-open-edit[data-id="${id}"]`)?.closest('tr');
      if (row) {
        const full = `${formData.get('nombre')||''} ${formData.get('apellido')||''}`.trim();
        row.querySelector('td:nth-child(1) .fw-semibold').textContent = full;
        row.querySelector('td:nth-child(1) small').textContent = formData.get('email');

        const roleCell = row.querySelector('td:nth-child(2)');
        if (roleCell) {
          const raw = (formData.get('role')||'').toUpperCase();
          let cls = 'role-aprendiz';
          if (raw==='ADMIN') cls='role-admin';
          else if (raw==='LIDER_GENERAL' || raw==='LIDER_SEMILLERO') cls='role-lider';
          roleCell.innerHTML = `<span class="role-badge ${cls}">${(raw||'').replace(/_/g,' ')}</span>`;
        }

        const stateCell = row.querySelector('td:nth-child(4)');
        if (stateCell) {
          const s = (formData.get('estado')||'').toLowerCase();
          if (s==='activo') stateCell.innerHTML = '<span class="badge bg-success" style="padding:6px 12px;border-radius:20px;">Activo</span>';
          else stateCell.innerHTML = '<span class="badge bg-danger" style="padding:6px 12px;border-radius:20px;">Inactivo</span>';
        }
      }

      // Cerrar modal y notificar
      bootstrap.Modal.getInstance(editModalEl)?.hide();
      showToast('Cambios guardados correctamente','success');
    });
  }

  // Toast helper minimalista
  function showToast(msg, variant='success'){
    const wrap = document.getElementById('toastWrap') || (()=>{ const d=document.createElement('div'); d.id='toastWrap'; d.style.position='fixed'; d.style.top='20px'; d.style.right='20px'; d.style.zIndex='1080'; document.body.appendChild(d); return d; })();
    const el = document.createElement('div');
    el.className = `toast align-items-center text-bg-${variant} border-0 show`;
    el.role = 'alert';
    el.style.minWidth = '260px';
    el.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    wrap.appendChild(el);
    setTimeout(()=> el.remove(), 3000);
  }
});
</script>
@endpush





    {{-- Tabla de Usuarios (modificada) --}}
    <div class="panel-card">
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
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

                                    @php
                                        $roleClass = 'role-aprendiz';
                                        $raw = strtoupper(trim((string)($roleLabel ?? '')));
                                        if ($raw === 'ADMIN') $roleClass = 'role-admin';
                                        elseif (in_array($raw, ['LIDER_GENERAL','LIDER SEMILLERO','LIDER_SEMILLERO'])) $roleClass = 'role-lider';
                                    @endphp
                                    <span class="role-badge {{ $roleClass }}">{{ $roleLabel ?? '—' }}</span>
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
                                @php
                                    $roleRaw = strtoupper(str_replace('_',' ', (string)($u->role ?? $u->rol ?? $roleLabel ?? '')));
                                    $isActiveText = ($u->estado === 'Activo' || ($u->is_active ?? false)) ? 'Activo' : 'Inactivo';
                                @endphp
                                <button type="button"
                                   class="btn btn-sm btn-outline-primary btn-open-edit"
                                   style="border-radius:20px;padding:4px 12px;"
                                   data-id="{{ $u->id ?? '' }}"
                                   data-nombre="{{ $nombre }}"
                                   data-email="{{ $u->email ?? '' }}"
                                   data-rol="{{ $roleRaw }}"
                                   data-estado="{{ $isActiveText }}">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const qInput = document.getElementById('filtro-nombre');
  const rolSel = document.getElementById('filtro-rol');
  const estSel = document.getElementById('filtro-estado');
  const tbody  = document.querySelector('table tbody');
  if(!tbody) return;

  const rows = Array.from(tbody.querySelectorAll('tr'));

  function norm(s){ return (s||'').toString().toLowerCase().trim(); }

  function apply(){
    const q   = norm(qInput?.value);
    const rol = (rolSel?.value||'').toUpperCase().replace(/_/g,' ');
    const est = norm(estSel?.value);

    let visible = 0;
    rows.forEach(tr =>{
      const userCell = tr.querySelector('td:nth-child(1)');
      const roleCell = tr.querySelector('td:nth-child(2)');
      const stateCell= tr.querySelector('td:nth-child(4)');
      if(!userCell||!roleCell||!stateCell){ tr.style.display=''; visible++; return; }

      const name  = norm(userCell.querySelector('.fw-semibold')?.textContent);
      const email = norm(userCell.querySelector('small')?.textContent);
      const roleT = (roleCell.textContent||'').replace(/\s+/g,' ').trim().toUpperCase();
      const stateT= norm(stateCell.textContent);

      const matchQ   = !q || name.includes(q) || email.includes(q);
      const matchRol = !rol || roleT.includes(rol);
      const matchEst = !est || stateT.includes(est);

      const ok = matchQ && matchRol && matchEst;
      tr.style.display = ok ? '' : 'none';
      if(ok) visible++;
    });
  }

  qInput?.addEventListener('input', apply);
  rolSel?.addEventListener('change', apply);
  estSel?.addEventListener('change', apply);

  // ===== Modal de edición =====
  const editModalEl = document.getElementById('modalEditUsuario');
  if (editModalEl) {
    const editModal = new bootstrap.Modal(editModalEl);
    document.querySelectorAll('.btn-open-edit').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.dataset.id;
        const nombreFull = btn.dataset.nombre||'';
        const parts = nombreFull.trim().split(/\s+/);
        const nombre = parts.shift() || '';
        const apellido = parts.join(' ') || '';
        const email = btn.dataset.email||'';
        const rol = (btn.dataset.rol||'').replace(/\s+/g,'_');
        const estado = btn.dataset.estado||'';

        editModalEl.querySelector('#edit-id').value = id;
        editModalEl.querySelector('#edit-nombre').value = nombre;
        const apField = editModalEl.querySelector('#edit-apellido');
        if(apField) apField.value = apellido;
        editModalEl.querySelector('#edit-email').value = email;
        editModalEl.querySelector('#edit-rol').value = rol;
        editModalEl.querySelector('#edit-estado').value = estado;

        const form = editModalEl.querySelector('#formEditUsuario');
        form.action = `{{ url('admin/usuarios') }}/${id}`;

        // Cerrar el modal de "Nuevo Usuario" si estuviera abierto para evitar doble backdrop
        const newUserEl = document.getElementById('modalNuevoUsuario');
        if (newUserEl && bootstrap.Modal.getInstance(newUserEl)) {
          bootstrap.Modal.getInstance(newUserEl).hide();
        }
        document.querySelectorAll('.modal-backdrop').forEach(b=>b.remove());

        editModal.show();
      });
    });
  }
});
</script>
@endpush

{{-- Modal Editar Usuario --}}
<div class="modal fade admin-modal" id="modalEditUsuario" tabindex="-1" aria-labelledby="modalEditUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditUsuarioLabel">Editar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditUsuario" method="POST" action="#">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" name="id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control form-underline" id="edit-nombre" name="nombre" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control form-underline" id="edit-apellido" name="apellido" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" class="form-control form-underline" id="edit-email" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol</label>
              <select id="edit-rol" name="role" class="form-select form-underline" required>
                <option value="ADMIN">Administrador</option>
                <option value="LIDER_GENERAL">Líder General</option>
                <option value="LIDER_SEMILLERO">Líder Semillero</option>
                <option value="APRENDIZ">Aprendiz</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado</label>
              <select id="edit-estado" name="estado" class="form-select form-underline">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
  </div>
