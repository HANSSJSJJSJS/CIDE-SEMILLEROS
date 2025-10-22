<section id="users" class="content-section">
  <h2 class="section-title">Gestión de Usuarios</h2>
  <p class="section-subtitle">Administra todos los usuarios del sistema</p>

  {{-- feedback --}}
  @if (session('success'))
    <div class="alert alert-success my-2">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger my-2">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Toolbar --}}
  <div class="toolbar d-flex flex-wrap gap-2 align-items-center mb-3">
    <div class="search-box position-relative flex-grow-1" style="max-width: 420px;">
      <svg class="position-absolute" style="left:10px; top:50%; transform:translateY(-50%);" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input id="userSearch" type="text" class="form-control ps-5" placeholder="Buscar usuarios por nombre o email...">
    </div>

    <select id="roleFilter" class="form-select" style="max-width: 220px;">
      <option value="">Todos los roles</option>
      <option value="ADMIN">Administrador</option>
      <option value="LIDER_GENERAL">Líder General</option>
      <option value="LIDER_SEMILLERO">Líder de Semillero</option>
      <option value="APRENDIZ">Aprendiz</option>
    </select>

    <select id="statusFilter" class="form-select" style="max-width: 180px;">
      <option value="">Todos los estados</option>
      <option value="ACTIVO">Activo</option>
      <option value="INACTIVO">Inactivo</option>
    </select>

    <button class="btn btn-primary d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Nuevo Usuario
    </button>
  </div>

  {{-- Tabla --}}
  <div class="table-container table-responsive">
    <table id="usersTable" class="table table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Usuario</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Registrado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
      @forelse($users as $user)
        @php
          $role = strtoupper($user->role ?? 'SIN ROL');
          $roleClass = match($role) {
              'ADMIN' => 'bg-danger',
              'LIDER_GENERAL' => 'bg-warning text-dark',
              'LIDER_SEMILLERO' => 'bg-primary',
              'APRENDIZ' => 'bg-success',
              default => 'bg-secondary'
          };
          $status = 'ACTIVO';
        @endphp

        <tr data-role="{{ $role }}" data-status="{{ $status }}">
          <td><strong>{{ $user->name }}</strong><br><small>{{ $user->apellidos }}</small></td>
          <td>{{ $user->email }}</td>
          <td><span class="badge {{ $roleClass }}">{{ $role }}</span></td>
          <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
          <td class="text-end">
            <div class="d-inline-flex gap-2">
              <a href="#"
                 class="btn btn-sm btn-warning btn-editar"
                 data-id="{{ $user->id }}"
                 data-edit-url="{{ route('admin.usuarios.edit', $user->id) }}"
                 data-update-url="{{ route('admin.usuarios.update', $user->id) }}">
                <i class="fa fa-edit"></i>
              </a>

              <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar usuario?')">
                  🗑️
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted">No hay usuarios.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación (si mandas LengthAwarePaginator desde el controlador) --}}
  @if(method_exists($users, 'links'))
    <div class="mt-3">{{ $users->links() }}</div>
  @endif
</section>
