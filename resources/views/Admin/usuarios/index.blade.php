@extends('layouts.admin')

@section('module-title','Gestión de Usuarios')
@section('module-subtitle','Administra roles, estados y semilleros')

@section('content')
<div class="container-fluid mt-3 px-3">

  {{-- ==============================
       FILTROS + BOTONES
  =============================== --}}
  <form method="GET" class="card filters-card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">

        {{-- Rol --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold">Rol</label>
          <select name="role" class="form-select">
            <option value="">Todos</option>
            @php $roleSelected = $roleFilter ?? request('role'); @endphp
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
          {{-- Botones juntos (Buscar / Limpiar / Nuevo) --}}
          <div class="col-12 col-md-2 col-lg-3">
              <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
              <div class="btn-group-flex">
                <button class="btn btn-sena btn-eq"><i class="bi bi-search"></i> Buscar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost-blue btn-eq"><i class="bi bi-x-lg"></i> Limpiar</a>
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-outline-green btn-eq"><i class="bi bi-person-plus"></i> Nuevo</a>
              </div>
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
            @forelse($usuarios as $u)
              @php
                $nombreCompleto = trim(($u->name ?? '').' '.($u->apellidos ?? '')) ?: ($u->nombre_completo ?? 'Usuario');
                $nPartes = preg_split('/\s+/', trim($u->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $aPartes = preg_split('/\s+/', trim($u->apellidos ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $ini1 = strtoupper(substr($nPartes[0] ?? 'U', 0, 1));
                $ini2 = strtoupper(substr($aPartes[0] ?? ($nPartes[1] ?? 'S'), 0, 1));
                $activo = $u->estado === 'Activo' || ($u->is_active ?? false);
                $roleLabel = $u->role_label
                  ?? match($u->role ?? null) {
                      'ADMIN' => 'Líder general',
                      'LIDER_SEMILLERO' => 'Líder semillero',
                      'APRENDIZ' => 'Aprendiz',
                      default => ($u->role ?? '—'),
                  };
              @endphp

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
                    <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                       class="btn btn-action-blue btn-eq">
                      <i class="bi bi-pencil"></i> <span>Editar</span>
                    </a>

                    <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('¿Eliminar este usuario?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-action-red btn-eq">
                        <i class="bi bi-trash"></i> <span>Eliminar</span>
                      </button>
                    </form>
                  </div>
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

  {{-- PAGINACIÓN --}}
  @if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-3">
      {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>
@endsection
