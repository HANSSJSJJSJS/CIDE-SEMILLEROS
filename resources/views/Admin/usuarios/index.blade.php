@extends('layouts.admin')

@section('content')
<div class="container-fluid mt-4 px-4">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold" style="color:#2d572c;">Gestión de Usuarios</h3>
  </div>

  {{-- ==============================
       FILTROS DE BÚSQUEDA
  =============================== --}}
  <form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3">
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

        <div class="col-md-2 d-flex align-items-end">
          <div class="w-100 d-flex gap-2">
            <button class="btn btn-success w-100">
              <i class="bi bi-search"></i> Buscar
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-x"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>

  {{-- ==============================
       BOTÓN NUEVO USUARIO
  =============================== --}}
<a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary mb-3">
  <i class="fa fa-user-plus me-1"></i> Nuevo Usuario
</a>


  {{-- Aquí puedes mantener tu modal de creación sin tocarlo (no lo recorto para no romperlo) --}}
  @includeIf('admin.usuarios._modal_crear')

  {{-- ==============================
       TABLA DE USUARIOS
  =============================== --}}
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
            @php
              // === CONSTRUYE NOMBRE COMPLETO ===
              $nombreCompleto = trim(($u->name ?? '') . ' ' . ($u->apellidos ?? ''));
              if ($nombreCompleto === '') {
                  $nombreCompleto = $u->nombre_completo ?? 'Usuario';
              }

              // === INICIALES ===
              $nPartes = preg_split('/\s+/', trim($u->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
              $aPartes = preg_split('/\s+/', trim($u->apellidos ?? ''), -1, PREG_SPLIT_NO_EMPTY);
              $ini1 = strtoupper(substr($nPartes[0] ?? 'U', 0, 1));
              $ini2 = strtoupper(substr($aPartes[0] ?? ($nPartes[1] ?? 'S'), 0, 1));
            @endphp

            <tr>
              {{-- USUARIO --}}
              <td class="py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle d-flex justify-content-center align-items-center"
                       style="width:42px;height:42px;background-color:#5aa72e;color:#fff;font-weight:700;font-size:14px;">
                    {{ $ini1 }}{{ $ini2 }}
                  </div>
                  <div>
                    <div class="fw-semibold">{{ $nombreCompleto }}</div>
                    <small class="text-muted">{{ $u->email ?? 'Sin correo' }}</small>
                  </div>
                </div>
              </td>

              {{-- ROL --}}
            <td class="py-3">
                                @php
                                  // Prioriza el label que viene desde el controlador (SELECT ... AS role_label)
                                  // Si por alguna razón no vino, mapea localmente a un label bonito.
                                  $roleLabel = $u->role_label
                                      ?? match($u->role ?? null) {
                                          'ADMIN'            => 'Líder general',
                                          'LIDER_SEMILLERO'  => 'Líder semillero',
                                          'APRENDIZ'         => 'Aprendiz',
                                          default            => ($u->role ?? '—'),
                                      };
                                @endphp

                                <span class="badge"
                                      style="background-color:#e8f5e9;color:#2d572c;border:1px solid #5aa72e;padding:6px 12px;border-radius:20px;">
                                  {{ $roleLabel }}
                                </span>
              </td>


              {{-- SEMILLERO --}}
              <td class="py-3">
                {{ $u->semillero_nombre ?? '—' }}
              </td>

              {{-- ESTADO --}}
              <td class="py-3">
                @php $activo = $u->estado === 'Activo' || ($u->is_active ?? false); @endphp
                @if($activo)
                  <span class="badge bg-success" style="padding:6px 12px;border-radius:20px;">Activo</span>
                @else
                  <span class="badge bg-danger" style="padding:6px 12px;border-radius:20px;">Inactivo</span>
                @endif
              </td>

              {{-- ÚLTIMA VEZ ACTIVO --}}
              <td class="py-3">
                @php $last = $u->last_login_at ?? $u->updated_at ?? null; @endphp
                @if($last)
                  {{ \Carbon\Carbon::parse($last)->diffForHumans() }}
                @else
                  —
                @endif
              </td>

              {{-- ACCIONES --}}
              <td class="py-3 text-end pe-4">
                <a href="{{ route('admin.usuarios.edit', $u->id) }}"
                   class="btn btn-sm btn-outline-primary" style="border-radius:20px;padding:4px 12px;">
                  <i class="bi bi-pencil"></i> Editar
                </a>

                <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
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

  {{-- PAGINACIÓN --}}
  @if($usuarios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-3">
      {{ $usuarios->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>
@endsection
