@extends('layouts.admin')

{{-- =========================================================
     ESTILOS: Marca de agua + Transparencia tabla + Encabezado
   ========================================================= --}}
@push('styles')
<style>
/* ===== Marca de agua SOLO para esta vista ===== */
.wm-page{
  position: relative;
  z-index: 0;
  background: transparent !important;
}
.wm-page::before{
  content: "";
  position: absolute;
  inset: 0;
  background: url("{{ asset('images/bg-semillero.png') }}") no-repeat center 180px / 900px auto;
  opacity: .12;               /* intensidad del logo */
  pointer-events: none;
  z-index: -1;                /* detrás de todo */
}

/* ===== Transparencia real en filtros y tabla ===== */
.card.filters-card,
.card.table-card,
.card.table-card > .card-body,
.card.table-card .table-responsive {
  background-color: rgba(255,255,255,0.50) !important;
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255,255,255,0.2) !important;
  border-radius: 14px;
}

/* ===== Quitar fondos sólidos ===== */
.card.table-card .table,
.card.table-card .table thead,
.card.table-card .table tbody,
.card.table-card .table tr,
.card.table-card .table th,
.card.table-card .table td {
  background-color: transparent !important;
}

/* Variables Bootstrap para que no repinten */
.card.table-card .table {
  --bs-table-bg: transparent !important;
  --bs-table-striped-bg: rgba(11,46,77,.03) !important;
  --bs-table-hover-bg: rgba(11,46,77,.06) !important;
  --bs-table-color: inherit !important;
}

/* ===== Encabezado más compacto y centrado ===== */
.card.table-card .thead-blue th {
  background-color: rgba(5,53,94,0.85) !important; /* azul con alpha */
  color: #fff !important;
  text-align: center !important;
  font-weight: 600 !important;
  font-size: .90rem !important;
  letter-spacing: .3px;
  padding-top: .65rem !important;
  padding-bottom: .65rem !important;
  border: 0 !important;
}

/* Zebra y hover suaves */
.card.table-card .table-zebra tbody tr:nth-child(odd){
  background-color: rgba(11,46,77,0.03) !important;
}
.card.table-card .table-zebra tbody tr:hover{
  background-color: rgba(11,46,77,0.06) !important;
}

/* Quitar fondo “fantasma” */
.card.table-card .bg-white,
.card.table-card .bg-light,
.card.table-card [class*="bg-"]{
  background-color: transparent !important;
}
</style>
@endpush


@section('module-title','Gestión de Usuarios')
@section('module-subtitle','Administra roles, estados y semilleros')

@section('content')
<div class="wm-page"> {{-- Marca de agua activa --}}

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

          {{-- Botones Buscar / Limpiar / Nuevo --}}
          <div class="col-12 col-md-2 col-lg-3">
            <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
            <div class="btn-group-flex">
              <button class="btn btn-sena btn-eq">
                <i class="bi bi-search"></i> Buscar
              </button>
              <a href="{{ route('admin.usuarios.index') }}" class="btn btn-ghost-blue btn-eq">
                <i class="bi bi-x-lg"></i> Limpiar
              </a>
              <a href="{{ route('admin.usuarios.create') }}" class="btn btn-outline-green btn-eq">
                <i class="bi bi-person-plus"></i> Nuevo
              </a>
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

  </div>{{-- /.container-fluid --}}
</div>{{-- /.wm-page --}}
@endsection
