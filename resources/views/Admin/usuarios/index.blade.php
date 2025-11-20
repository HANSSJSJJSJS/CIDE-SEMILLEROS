@extends('layouts.admin')

@section('module-title','Gestión de Usuarios')
@section('module-subtitle','Administra roles, estados y semilleros')

@section('content')

<div class="container-fluid mt-3 px-3">

    {{-- FILTROS + BOTONES --}}
    <form method="GET" class="card border-0 shadow-sm mb-3 glass-card">
      <div class="card-body">
        <div class="row g-3 align-items-end">

          {{-- Rol --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Rol</label>
            <select name="role" class="form-select">
              <option value="">Todos</option>
              @php 
                $roleSelected = $roleFilter ?? request('role'); 
              @endphp
              @foreach(($roles ?? []) as $value => $label)
                <option value="{{ $value }}" @selected($roleSelected === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          {{-- Semillero --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">Semillero</label>
            <select name="semillero_id" class="form-select">
              <option value="">Todos los semilleros</option>
              @foreach(($semilleros ?? collect()) as $s)
                <option value="{{ $s->id_semillero }}"
                  @selected( (old('semillero_id', $semilleroId ?? request('semillero_id'))) == $s->id_semillero )>
                  {{ $s->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Buscar --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Buscar por nombre</label>
            <input type="text" name="q" class="form-control"
                   value="{{ old('q', $q ?? request('q')) }}"
                   placeholder="Nombre, apellido o email">
          </div>

          {{-- Botones --}}
          <div class="col-12 col-md-2 col-lg-3">
<<<<<<< HEAD
            <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-nuevo-semillero" type="submit">
                <i class="bi bi-search me-1"></i> Buscar
              </button>

              <a href="{{ route('admin.usuarios.index') }}" class="btn btn-accion-ver">
                <i class="bi bi-x-lg me-1"></i> Limpiar
              </a>

              <a href="{{ route('admin.usuarios.create') }}" class="btn btn-nuevo-semillero">
                <i class="bi bi-person-plus me-1"></i> Nuevo
              </a>
=======
              <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
              <div class="btn-group-flex">
                <button class="btn btn-sena btn-eq"><i class="bi bi-search"></i> Buscar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost-blue btn-eq"><i class="bi bi-x-lg"></i> Limpiar</a>
                @php
                  $__ROLE_BTN = strtoupper(str_replace([' ', '-'], '_', auth()->user()->role ?? ''));
                  $__CAN_CREATE_USU = false;
                  if ($__ROLE_BTN === 'LIDER_GENERAL') {
                    $__CAN_CREATE_USU = true;
                  } elseif ($__ROLE_BTN === 'ADMIN') {
                    $permBtn = \DB::table('user_module_permissions')
                      ->where('user_id', auth()->id())
                      ->where('module', 'usuarios')
                      ->first();
                    $__CAN_CREATE_USU = (int)($permBtn->can_create ?? 0) === 1;
                  }
                @endphp
                @if ($__CAN_CREATE_USU)
                  <a href="{{ route('admin.usuarios.create') }}" class="btn btn-outline-green btn-eq"><i class="bi bi-person-plus"></i> Nuevo</a>
                @endif
              </div>
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
            </div>
        </div>
      </div>
    </div>
  </form>

  {{-- ==============================
       TABLA DE USUARIOS
  =============================== --}}
  <div class="card border-0 shadow-sm table-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        @php
          $__ROLE_PAGE = strtoupper(str_replace([' ', '-'], '_', auth()->user()->role ?? ''));
          $__USR_PERM_USU = null;
        @endphp
        @if ($__ROLE_PAGE === 'ADMIN')
          @php
            $__USR_PERM_USU = \DB::table('user_module_permissions')
              ->where('user_id', auth()->id())
              ->where('module', 'usuarios')
              ->first();
          @endphp
        @endif
        <table class="table table-hover table-zebra mb-0">
          <thead class="thead-blue">
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
            @if(($usuarios instanceof \Illuminate\Support\Collection ? $usuarios->count() : count($usuarios)) > 0)
            @foreach($usuarios as $u)
              @php
                $nombreCompleto = trim(($u->name ?? '').' '.($u->apellidos ?? '')) ?: ($u->nombre_completo ?? 'Usuario');
                $nPartes = preg_split('/\s+/', trim($u->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $aPartes = preg_split('/\s+/', trim($u->apellidos ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $ini1 = strtoupper(substr($nPartes[0] ?? 'U', 0, 1));
                $ini2 = strtoupper(substr($aPartes[0] ?? ($nPartes[1] ?? 'S'), 0, 1));
                $activo = $u->estado === 'Activo' || ($u->is_active ?? false);
                $roleLabel = $u->role_label
                  ?? match($u->role ?? null) {
                      'ADMIN' => 'Líder grupo de investigación CIDEINNOVA',
                      'LIDER_SEMILLERO' => 'Líder semillero',
                      'APRENDIZ' => 'Aprendiz',
                      default => ($u->role ?? '—'),
                  };
              @endphp

<<<<<<< HEAD
    {{-- TABLA DE USUARIOS --}}
    <div class="card border-0 shadow-sm glass-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 tabla-usuarios">
            <thead>
              <tr>
                <th class="py-3 px-4">Usuario</th>
                <th class="py-3">Rol</th>
                <th class="py-3">Semillero</th>
                <th class="py-3">Estado</th>
                <th class="py-3">Última actividad</th>
                <th class="py-3 text-end pe-4">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse($usuarios as $u)
                @php
                  $nombreCompleto = trim(($u->name ?? '').' '.($u->apellidos ?? ''))
                    ?: ($u->nombre_completo ?? 'Usuario');

                  $activo = $u->estado === 'Activo' || ($u->is_active ?? false);

                  $roleLabel = $u->role_label
                    ?? match($u->role ?? null) {
                        'ADMIN' => 'Líder general',
                        'LIDER_SEMILLERO' => 'Líder de semillero',
                        'APRENDIZ' => 'Aprendiz',
                        default => ($u->role ?? '—'),
                    };

                  $roleColor = match($u->role ?? '') {
                      'ADMIN' => 'bg-danger',
                      'LIDER_SEMILLERO' => 'bg-success',
                      'APRENDIZ' => 'bg-primary',
                      default => 'bg-secondary',
                  };

                  $last = $u->last_login_at ?? $u->updated_at ?? null;
                @endphp

                <tr>
                  <td class="py-3 px-4">
                    <div class="fw-semibold">{{ $nombreCompleto }}</div>
                    <small class="text-muted">{{ $u->email ?? 'Sin correo' }}</small>
                  </td>

                  <td class="py-3">
                    <span class="badge {{ $roleColor }}">{{ $roleLabel }}</span>
                  </td>

                  <td class="py-3">{{ $u->semillero_nombre ?? '—' }}</td>

                  <td class="py-3">
                    <span class="badge {{ $activo ? 'bg-success' : 'bg-secondary' }}">
                      {{ $activo ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>

                  <td class="py-3">
                    {{ $last ? \Carbon\Carbon::parse($last)->locale('es')->diffForHumans() : '—' }}
                  </td>

                  <td class="py-3 text-end pe-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                       class="btn btn-sm btn-accion-editar">
                      <i class="bi bi-pencil me-1"></i> Editar
                    </a>

                    <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Deseas eliminar este usuario?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-accion-eliminar" type="submit">
                        <i class="bi bi-trash me-1"></i> Eliminar
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    No hay usuarios registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
=======
              <tr>
                {{-- USUARIO --}}
                <td class="py-3 px-4">
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar-pill">{{ $ini1 }}{{ $ini2 }}</div>
                    <div>
                      <div class="fw-semibold">{{ $nombreCompleto }}</div>
                      <small class="text-muted">{{ $u->email ?? 'Sin correo' }}</small>
                    </div>
                  </div>
                </td>

                {{-- ROL --}}
                <td class="py-3">
                  <span class="badge-role">{{ $roleLabel }}</span>
                </td>

                {{-- SEMILLERO --}}
                <td class="py-3">{{ $u->semillero_nombre ?? '—' }}</td>

                {{-- ESTADO --}}
                <td class="py-3">
                  <span class="status-badge {{ $activo ? 'ok' : 'ko' }}">
                    {{ $activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>

                {{-- ÚLTIMA VEZ ACTIVO --}}
                <td class="py-3">
                  @php $last = $u->last_login_at ?? $u->updated_at ?? null; @endphp
                  {{ $last ? \Carbon\Carbon::parse($last)->diffForHumans() : '—' }}
                </td>

                {{-- ACCIONES --}}
                <td class="py-3 text-end pe-4">
                  <div class="action-buttons d-flex justify-content-end gap-2 flex-wrap">
                    @php $currentRole = strtoupper(str_replace([' ', '-'], '_', auth()->user()->role ?? '')); @endphp
                    @if ($currentRole === 'LIDER_GENERAL')
                      <a href="#" class="btn btn-action-blue btn-eq btn-open-edit"
                         data-id="{{ $u->id }}"
                         data-nombre="{{ $nombreCompleto }}"
                         data-email="{{ $u->email ?? '' }}"
                         data-rol="{{ $u->role ?? '' }}"
                         data-estado="{{ $u->estado ?? '' }}">
                        <i class="bi bi-pencil"></i> <span>Editar</span>
                      </a>
                      <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-action-red btn-eq">
                          <i class="bi bi-trash"></i> <span>Eliminar</span>
                        </button>
                      </form>
                    @elseif ($currentRole === 'ADMIN')
                      @php $canU = (int)($__USR_PERM_USU->can_update ?? 0) === 1; @endphp
                      @php $canD = (int)($__USR_PERM_USU->can_delete ?? 0) === 1; @endphp
                      @if ($canU)
                        <a href="#" class="btn btn-action-blue btn-eq btn-open-edit"
                           data-id="{{ $u->id }}"
                           data-nombre="{{ $nombreCompleto }}"
                           data-email="{{ $u->email ?? '' }}"
                           data-rol="{{ $u->role ?? '' }}"
                           data-estado="{{ $u->estado ?? '' }}">
                          <i class="bi bi-pencil"></i> <span>Editar</span>
                        </a>
                      @endif
                      @if ($canD)
                        <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-action-red btn-eq">
                            <i class="bi bi-trash"></i> <span>Eliminar</span>
                          </button>
                        </form>
                      @endif
                    @endif

                    {{-- Botón Permisos visible solo para LIDER_GENERAL y solo sobre usuarios ADMIN --}}
                    @php($targetRole = strtoupper(str_replace([' ', '-'], '_', $u->role ?? '')))
                    @if ($currentRole === 'LIDER_GENERAL' && $targetRole === 'ADMIN')
                      <button type="button"
                              class="btn btn-outline-secondary btn-eq"
                              data-bs-toggle="modal"
                              data-bs-target="#modalPermisos"
                              data-user-id="{{ $u->id }}"
                              data-user-name="{{ $nombreCompleto }}">
                        <i class="bi bi-shield-lock"></i> <span>Permisos</span>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
            @else
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                  <p>No hay usuarios registrados</p>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
      </div>
    </div>
  </div>

  {{-- PAGINACIÓN --}}
  @if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-3">
      {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif

  {{-- Modal: Permisos por usuario --}}
  @php($__ROLE = strtoupper(str_replace([' ', '-'], '_', auth()->user()->role ?? '')))
  @if ($__ROLE === 'LIDER_GENERAL')
  <div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Permisos por módulo — <span id="permUserName">Usuario</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="permForm">
          @csrf
          <div class="modal-body">
            <input type="hidden" id="permUserId" value="">
            <div id="permAlert" class="alert d-none" role="alert"></div>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Módulo</th>
                    <th>Crear</th>
                    <th>Actualizar</th>
                    <th>Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  @php($modules = ['usuarios'=>'Usuarios','semilleros'=>'Semilleros','recursos'=>'Recursos','reuniones-lideres'=>'Reuniones de líderes'])
                  @foreach($modules as $key=>$label)
                    <tr>
                      <td>{{ $label }}</td>
                      <td><input type="checkbox" class="form-check-input" name="modules[{{ $key }}][can_create]"></td>
                      <td><input type="checkbox" class="form-check-input" name="modules[{{ $key }}][can_update]"></td>
                      <td><input type="checkbox" class="form-check-input" name="modules[{{ $key }}][can_delete]"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <small class="text-muted">Si no marcas ningún permiso de un módulo, se eliminará su registro y quedará en solo lectura.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button id="permSubmit" type="submit" class="btn btn-primary">
              Guardar permisos
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

<<<<<<< HEAD
  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/semilleros.css') }}">

<style>
/* Glass para tarjeta de filtros y tabla */
.glass-card {
    background: rgba(255,255,255,0.45) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border-radius: 14px !important;
    border: 1px solid rgba(255,255,255,0.25) !important;
}

/* Mantenemos el fondo transparente para el cuerpo de la tabla */
.glass-card table {
    --bs-table-bg: transparent !important;
    --bs-table-striped-bg: rgba(255,255,255,0.15) !important;
    --bs-table-hover-bg: rgba(255,255,255,0.25) !important;
}
.glass-card tbody tr,
.glass-card tbody td {
    background: transparent !important;
}

/* Encabezado SOLO de esta tabla de usuarios */
.tabla-usuarios thead th {
    background-color: #0b2e4d !important;   /* tu blue-900 */
    color: #ffffff !important;
    font-size: 0.85rem;                     /* más pequeño */
    font-weight: 600;
    padding-top: 0.55rem;
    padding-bottom: 0.55rem;
    letter-spacing: 0.03em;
    border-bottom: 2px solid rgba(255,255,255,0.25) !important;
}

/* Hover un poco más notorio, manteniendo transparencia */
.tabla-usuarios.table-hover tbody tr:hover {
    background-color: rgba(11, 46, 77, 0.28) !important;
    transition: background-color 0.15s ease-in-out;
}
</style>
@endpush
=======
  @push('scripts')
  <script>
  (function(){
    const modal = document.getElementById('modalPermisos');
    const permForm = document.getElementById('permForm');
    const userIdInp = document.getElementById('permUserId');
    const userNameSpan = document.getElementById('permUserName');
    const permAlert = document.getElementById('permAlert');
    const permSubmit = document.getElementById('permSubmit');
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function setChecks(mod, data){
      const base = `modules[${mod}]`;
      const c = permForm.querySelector(`[name='${base}[can_create]']`);
      const u = permForm.querySelector(`[name='${base}[can_update]']`);
      const d = permForm.querySelector(`[name='${base}[can_delete]']`);
      if (c) c.checked = !!(data?.can_create);
      if (u) u.checked = !!(data?.can_update);
      if (d) d.checked = !!(data?.can_delete);
    }

    modal.addEventListener('show.bs.modal', async (e) => {
      const btn = e.relatedTarget;
      const userId = btn?.getAttribute('data-user-id');
      const userName = btn?.getAttribute('data-user-name') || 'Usuario';
      userIdInp.value = userId;
      userNameSpan.textContent = userName;

      // Reset
      ['usuarios','semilleros','recursos','reuniones-lideres'].forEach(m=>setChecks(m, {can_create:false,can_update:false,can_delete:false}));
      if (permAlert) { permAlert.className = 'alert d-none'; permAlert.textContent = ''; }

      try{
        // Asegurar stacking correcto
        try { document.body.appendChild(modal); } catch(_){}
        modal.style.zIndex = '2050';
        modal.querySelector('.modal-content')?.style && (modal.querySelector('.modal-content').style.pointerEvents = 'auto');
        setTimeout(()=>{
          document.querySelectorAll('.modal-backdrop').forEach((b,i)=>{ b.style.zIndex = String(2040 + i); });
        }, 0);

        const url = `/admin/usuarios/${userId}/permisos?_ts=${Date.now()}`;
        const res = await fetch(url, { headers: { 'Accept':'application/json' }, credentials: 'same-origin', cache: 'no-store' });
        const text = await res.text();
        let json = {};
        try { json = JSON.parse(text); } catch { json = {}; }
        const data = json.data || {};
        const alias = { 'reuniones': 'reuniones-lideres', 'reuniones_lideres':'reuniones-lideres' };
        Object.entries(data).forEach(([mod, perms]) => {
          const key = alias[mod] || mod;
          setChecks(key, perms);
        });
      }catch(err){ console.error('Permisos show', err); }
    });

    permForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      // Loading ON
      const prev = permSubmit.innerHTML;
      permSubmit.disabled = true; permSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando…';
      if (permAlert) { permAlert.className = 'alert d-none'; permAlert.textContent = ''; }
      const userId = userIdInp.value;
      const formData = new FormData(permForm);
      // Convertir a objeto
      const modules = {};
      ['usuarios','semilleros','recursos','reuniones-lideres'].forEach(m=>{
        modules[m] = {
          can_create: formData.get(`modules[${m}][can_create]`) ? 1 : 0,
          can_update: formData.get(`modules[${m}][can_update]`) ? 1 : 0,
          can_delete: formData.get(`modules[${m}][can_delete]`) ? 1 : 0,
        };
      });

      try{
        const url = `/admin/usuarios/${userId}/permisos`;
        console.log('POST permisos →', url, modules);
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ modules })
        });
        const text = await res.text();
        console.log('Respuesta permisos', res.status, text);
        if(!res.ok){
          if (permAlert) { permAlert.className = 'alert alert-danger'; permAlert.textContent = text || 'Error al guardar permisos.'; }
          return;
        }
        // OK
        if (permAlert) { permAlert.className = 'alert alert-success'; permAlert.textContent = 'Permisos guardados correctamente.'; }
        setTimeout(()=>{
          let inst = null;
          try { inst = bootstrap.Modal.getInstance(modal); } catch(_) {}
          if (!inst) {
            try { inst = new bootstrap.Modal(modal); } catch(e) { console.warn('No modal instance, cannot hide', e); }
          }
          try { inst?.hide(); } catch(e) { console.warn('Hide failed', e); }
        }, 400);
      }catch(err){
        console.error('Permisos update', err);
        if (permAlert) { permAlert.className = 'alert alert-danger'; permAlert.textContent = 'No se pudieron guardar los permisos.'; }
      }
      finally {
        permSubmit.disabled = false; permSubmit.innerHTML = prev;
      }
    });
  })();
  </script>
  @endpush
  @endif
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
      <form id="formEditUsuario" method="POST" action="#">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" name="id">

        <div class="modal-header">
          <h5 class="modal-title" id="modalEditUsuarioLabel">Editar usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="edit-nombre">Nombre</label>
              <input type="text" class="form-control form-underline" id="edit-nombre" name="nombre" required autocomplete="given-name">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit-apellido">Apellidos</label>
              <input type="text" class="form-control form-underline" id="edit-apellido" name="apellido" required autocomplete="family-name">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit-email">Correo</label>
              <input type="email" class="form-control form-underline" id="edit-email" name="email" required autocomplete="email">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit-rol">Rol</label>
              <select id="edit-rol" name="role" class="form-select form-underline" required autocomplete="off">
                <option value="ADMIN">Administrador</option>
                <option value="LIDER_GENERAL">Líder General</option>
                <option value="LIDER_SEMILLERO">Líder Semillero</option>
                <option value="APRENDIZ">Aprendiz</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit-estado">Estado</label>
              <select id="edit-estado" name="estado" class="form-select form-underline" autocomplete="off">
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
>>>>>>> 56c51368da107633c3e5131aee39af0989631ab3
