<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"
    <title>Panel de Administración - CIDE SEMILLERO</title>
    <link rel="stylesheet" href="../css/Style_layouts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="user-info">
            <div class="user-avatar">JC</div>
            <span style="font-weight: 600; color: var(--gray-700);">Joaquín cañon</span>
        </div>
        <h1 class="header-title">Bienvenido a CIDE SEMILLERO</h1>
        <button class="logout-btn" type="button" onclick="logout()">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar sesión
        </button>
    </header>

    <!-- Formulario oculto para cerrar sesión -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav>
                <div class="nav-item active" onclick="showSection('dashboard')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Dashboard
                </div>
                <div class="nav-item" onclick="showSection('users')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Gestión de usuarios
                </div>
                <div class="nav-item" onclick="showSection('semilleros')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Gestión de semilleros
                </div>
                <div class="nav-item" onclick="showSection('reports')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reportes
                </div>
                <div class="nav-item" onclick="showSection('activity')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Actividad del sistema
                </div>
                <div class="nav-item" onclick="showSection('settings')">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <h2 class="section-title">Panel de Administración</h2>
                <p class="section-subtitle">Vista general del sistema CIDE SEMILLERO</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value">248</div>
                                <div class="stat-label">Total Usuarios</div>
                            </div>
                            <div class="stat-icon green">
                                <svg fill="white" viewBox="0 0 24 24" width="24" height="24">
                                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-change positive">↑ 12% vs mes anterior</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value">32</div>
                                <div class="stat-label">Semilleros Activos</div>
                            </div>
                            <div class="stat-icon navy">
                                <svg fill="white" viewBox="0 0 24 24" width="24" height="24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-change positive">↑ 3 nuevos este mes</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value">1,847</div>
                                <div class="stat-label">Documentos Totales</div>
                            </div>
                            <div class="stat-icon blue">
                                <svg fill="white" viewBox="0 0 24 24" width="24" height="24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-change positive">↑ 156 esta semana</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value">18</div>
                                <div class="stat-label">Pendientes Revisión</div>
                            </div>
                            <div class="stat-icon yellow">
                                <svg fill="white" viewBox="0 0 24 24" width="24" height="24">
                                    <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-change negative">↓ Requiere atención</div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title">Actividad de Usuarios - Últimos 7 días</h3>
                    <div class="bar-chart">
                        <div class="bar" style="height: 65%;">
                            <span class="bar-value">156</span>
                            <span class="bar-label">Lun</span>
                        </div>
                        <div class="bar" style="height: 80%;">
                            <span class="bar-value">192</span>
                            <span class="bar-label">Mar</span>
                        </div>
                        <div class="bar" style="height: 55%;">
                            <span class="bar-value">132</span>
                            <span class="bar-label">Mié</span>
                        </div>
                        <div class="bar" style="height: 90%;">
                            <span class="bar-value">216</span>
                            <span class="bar-label">Jue</span>
                        </div>
                        <div class="bar" style="height: 75%;">
                            <span class="bar-value">180</span>
                            <span class="bar-label">Vie</span>
                        </div>
                        <div class="bar" style="height: 40%;">
                            <span class="bar-value">96</span>
                            <span class="bar-label">Sáb</span>
                        </div>
                        <div class="bar" style="height: 35%;">
                            <span class="bar-value">84</span>
                            <span class="bar-label">Dom</span>
                        </div>
                    </div>
                </div>
            </section>
{{-- Users Section --}}
<section id="users" class="content-section">
  <h2 class="section-title">Gestión de Usuarios</h2>
  <p class="section-subtitle">Administra todos los usuarios del sistema</p>

  {{-- Toolbar --}}
  <div class="toolbar d-flex flex-wrap gap-2 align-items-center mb-3">
    <div class="search-box position-relative flex-grow-1" style="max-width: 420px;">
      <svg class="position-absolute" style="left:10px; top:50%; transform:translateY(-50%);" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input id="userSearch" type="text" class="form-control ps-5"
             placeholder="Buscar usuarios por nombre o email...">
    </div>

    {{-- Filtro por Rol (valores EXACTOS de tu app) --}}
    <select id="roleFilter" class="form-select" style="max-width: 220px;">
      <option value="">Todos los roles</option>
      <option value="ADMIN">Administrador</option>
      <option value="LIDER_GENERAL">Líder General</option>
      <option value="LIDER_SEMILLERO">Líder de Semillero</option>
      <option value="APRENDIZ">Aprendiz</option>
    </select>

    {{-- Filtro por Estado (si aún no hay columna, queda en “ACTIVO” por defecto) --}}
    <select id="statusFilter" class="form-select" style="max-width: 180px;">
      <option value="">Todos los estados</option>
      <option value="ACTIVO">Activo</option>
      <option value="INACTIVO">Inactivo</option>
    </select>

    {{-- Abre el modal que ya tenemos --}}
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
          $status = 'ACTIVO'; // cámbialo cuando tengas la columna en BD
        @endphp

        <tr data-role="{{ $role }}" data-status="{{ $status }}">
          <td><strong>{{ $user->name }}</strong></td>
          <td>{{ $user->email }}</td>
          <td><span class="badge {{ $roleClass }}">{{ $role }}</span></td>
          <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
          <td class="text-end">
            <div class="d-inline-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary" title="Editar" disabled>✏️</button>
              <button class="btn btn-sm btn-outline-danger" title="Eliminar" disabled>🗑️</button>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No hay usuarios.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginación --}}
  @isset($users)
    @if(method_exists($users, 'links'))
      <div class="mt-3">
        {{ $users->links() }}
      </div>
    @endif
  @endisset
</section>

<style>
  #users .toolbar .form-control, #users .toolbar .form-select { height: 42px; }
</style>


            <!-- Semilleros Section -->
            <section id="semilleros" class="content-section">
                <h2 class="section-title">Gestión de Semilleros</h2>
                <p class="section-subtitle">Administra todos los semilleros de investigación</p>

                <div class="toolbar">
                    <div class="search-box">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Buscar semilleros...">
                    </div>
                    <select>
                        <option>Todas las áreas</option>
                        <option>Tecnología</option>
                        <option>Ciencias</option>
                        <option>Ingeniería</option>
                    </select>
                    <button class="btn btn-primary" onclick="openModal('addSemillero')">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Semillero
                    </button>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Líder</th>
                                <th>Aprendices</th>
                                <th>Área</th>
                                <th>Estado</th>
                                <th>Fecha creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Desarrollo Web Avanzado</strong></td>
                                <td>Carlos Ramírez</td>
                                <td>24</td>
                                <td>Tecnología</td>
                                <td><span class="badge active">Activo</span></td>
                                <td>15/01/2025</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-icon btn-edit" title="Editar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button class="btn btn-icon btn-delete" title="Eliminar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Inteligencia Artificial</strong></td>
                                <td>Ana Martínez</td>
                                <td>18</td>
                                <td>Tecnología</td>
                                <td><span class="badge active">Activo</span></td>
                                <td>20/01/2025</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-icon btn-edit" title="Editar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button class="btn btn-icon btn-delete" title="Eliminar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Robótica Educativa</strong></td>
                                <td>Jorge Pérez</td>
                                <td>15</td>
                                <td>Ingeniería</td>
                                <td><span class="badge active">Activo</span></td>
                                <td>10/02/2025</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-icon btn-edit" title="Editar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button class="btn btn-icon btn-delete" title="Eliminar">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports" class="content-section">
                <h2 class="section-title">Reportes y Estadísticas</h2>
                <p class="section-subtitle">Genera y visualiza reportes del sistema</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-700);">Usuarios por Rol</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Administradores</span>
                                <strong style="color: var(--sena-navy);">5</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Líderes</span>
                                <strong style="color: var(--sena-navy);">32</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Aprendices</span>
                                <strong style="color: var(--sena-navy);">211</strong>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-700);">Documentos por Estado</h3>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Aprobados</span>
                                <strong style="color: #16A34A;">1,542</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Pendientes</span>
                                <strong style="color: var(--yellow-500);">287</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--gray-600);">Rechazados</span>
                                <strong style="color: var(--red-500);">18</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-800);">Generar Reportes</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <button class="btn btn-primary" style="justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reporte de Usuarios
                        </button>
                        <button class="btn btn-primary" style="justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reporte de Semilleros
                        </button>
                        <button class="btn btn-primary" style="justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reporte de Actividad
                        </button>
                    </div>
                </div>
            </section>

            <!-- Activity Section -->
            <section id="activity" class="content-section">
                <h2 class="section-title">Actividad del Sistema</h2>
                <p class="section-subtitle">Registro de todas las acciones realizadas en el sistema</p>

                <div class="toolbar">
                    <div class="search-box">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Buscar en el registro...">
                    </div>
                    <select>
                        <option>Todas las acciones</option>
                        <option>Creación</option>
                        <option>Edición</option>
                        <option>Eliminación</option>
                        <option>Inicio de sesión</option>
                    </select>
                    <select>
                        <option>Últimas 24 horas</option>
                        <option>Última semana</option>
                        <option>Último mes</option>
                    </select>
                </div>

                <div class="activity-log">
                    <div class="activity-item">
                        <div class="activity-icon" style="background: #DCFCE7; color: #16A34A;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Nuevo usuario registrado</div>
                            <div class="activity-description">María González creó una cuenta de administrador</div>
                            <div class="activity-time">Hace 2 horas</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon" style="background: #DBEAFE; color: #1E40AF;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Semillero actualizado</div>
                            <div class="activity-description">Carlos Ramírez modificó el semillero "Desarrollo Web"</div>
                            <div class="activity-time">Hace 3 horas</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon" style="background: #FEF3C7; color: #92400E;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Documento subido</div>
                            <div class="activity-description">Ana Martínez subió un nuevo documento al semillero "IA"</div>
                            <div class="activity-time">Hace 5 horas</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon" style="background: #FEE2E2; color: #DC2626;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Usuario eliminado</div>
                            <div class="activity-description">Se eliminó la cuenta de usuario inactivo</div>
                            <div class="activity-time">Hace 1 día</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon" style="background: #E0E7FF; color: #4338CA;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Inicio de sesión</div>
                            <div class="activity-description">Pedro López inició sesión en el sistema</div>
                            <div class="activity-time">Hace 1 día</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings" class="content-section">
                <h2 class="section-title">Configuración del Sistema</h2>
                <p class="section-subtitle">Administra la configuración general del sistema</p>

                <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 800px;">
                    <div class="stat-card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-800);">Configuración General</h3>
                        <div class="form-group">
                            <label class="form-label">Nombre del Sistema</label>
                            <input type="text" class="form-input" value="CIDE SEMILLERO">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email de Contacto</label>
                            <input type="email" class="form-input" value="contacto@sena.edu.co">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Límite de Usuarios por Semillero</label>
                            <input type="number" class="form-input" value="30">
                        </div>
                        <button class="btn btn-primary">Guardar Cambios</button>
                    </div>

                    <div class="stat-card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-800);">Seguridad</h3>
                        <div class="form-group">
                            <label class="form-label">Tiempo de Sesión (minutos)</label>
                            <input type="number" class="form-input" value="60">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Intentos de Inicio de Sesión</label>
                            <input type="number" class="form-input" value="3">
                        </div>
                        <button class="btn btn-primary">Guardar Cambios</button>
                    </div>

                    <div class="stat-card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--gray-800);">Notificaciones</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                                <span style="color: var(--gray-700);">Notificar nuevos usuarios</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                                <span style="color: var(--gray-700);">Notificar nuevos semilleros</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" checked style="width: 20px; height: 20px; cursor: pointer;">
                                <span style="color: var(--gray-700);">Notificar documentos pendientes</span>
                            </label>
                        </div>
                        <button class="btn btn-primary" style="margin-top: 1rem;">Guardar Cambios</button>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Add User -->

<!-- BOTÓN -->
<button type="button" class="btn btn-primary mb-3"
        data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
  <i class="fa fa-user-plus me-1"></i> Nuevo Usuario
</button>

<!-- MODAL -->
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

            {{-- 1) Rol (único visible al inicio) --}}
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <label class="form-label fw-semibold mb-1">Rol</label>
                <select name="role" id="selectRol" class="form-select" required>
                  <option value="">Seleccione un rol...</option>
                  <option value="ADMIN">Administrador</option>
                  <option value="LIDER_GENERAL">Líder General</option>
                  <option value="LIDER_SEMILLERO">Líder de Semillero</option>
                  <option value="APRENDIZ">Aprendiz</option>
                </select>
              </div>
            </div>

            {{-- 2) Campos comunes (aparecen después de elegir rol) --}}
            <div id="commonFields" class="d-none">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Nombre</label>
                      <input type="text" name="nombre" class="form-control" placeholder="María">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Apellidos</label>
                      <input type="text" name="apellido" class="form-control" placeholder="Gómez Pérez">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Correo (login)</label>
                      <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
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

            {{-- 3) Campos por rol (solo lo necesario, sin textos largos) --}}

            {{-- Líder General: sin campos extra (institucional = login) --}}
            <div class="role-fields d-none" data-role="LIDER_GENERAL"></div>

            {{-- Líder Semillero: documento --}}
            <div class="role-fields d-none" data-role="LIDER_SEMILLERO">
              <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ls_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ls_documento" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Aprendiz: institucional requerido --}}
            <div class="role-fields d-none" data-role="APRENDIZ">
              <div class="card border-0 shadow-sm mb-1">
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Ficha</label>
                      <input type="text" name="ap_ficha" class="form-control">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Programa</label>
                      <input type="text" name="ap_programa" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Tipo documento</label>
                      <select name="ap_tipo_documento" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="CC">CC</option>
                        <option value="TI">TI</option>
                        <option value="CE">CE</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Número documento</label>
                      <input type="text" name="ap_documento" class="form-control">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Celular )</label>
                      <input type="text" name="ap_celular" class="form-control">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label">Correo institucional</label>
                      <input type="email" name="ap_correo_institucional" class="form-control" placeholder="usuario@sena.edu.co">
                    </div>

                                        <div class="col-md-6">
                    <label class="form-label">Contacto emergencia (nombre)</label>
                    <input type="text" name="ap_contacto_nombre" class="form-control">
                    </div>
                    <div class="col-md-6">
                    <label class="form-label">Celular de contacto</label>
                    <input type="text" name="ap_contacto_celular" class="form-control">
                    </div>


                    {{-- Personal = login (oculto) --}}
                    <input type="hidden" name="ap_correo_personal">
                  </div>
                </div>
              </div>
            </div>

            {{-- Admin: sin extras --}}
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



    <!-- Modal for Add Semillero -->
    <div id="addSemillero" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nuevo Semillero</h3>
                <button class="close-modal" onclick="closeModal('addSemillero')">×</button>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Nombre del Semillero</label>
                    <input type="text" class="form-input" placeholder="Ingrese el nombre del semillero">
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-input" rows="3" placeholder="Descripción del semillero"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Líder Asignado</label>
                    <select class="form-input">
                        <option>Seleccione un líder</option>
                        <option>Carlos Ramírez</option>
                        <option>Ana Martínez</option>
                        <option>Jorge Pérez</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Área</label>
                    <select class="form-input">
                        <option>Seleccione un área</option>
                        <option>Tecnología</option>
                        <option>Ciencias</option>
                        <option>Ingeniería</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addSemillero')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Semillero</button>
                </div>
            </form>
        </div>
    </div>



    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Add active class to clicked nav item
            event.target.closest('.nav-item').classList.add('active');
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function logout() {
            if (confirm('¿Está seguro que desea cerrar sesión?')) {
                document.getElementById('logout-form').submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>

<script>
function openModal() {
  const el = document.getElementById('modalNuevoUsuario');
  if (!el) {
    console.error('No se encontró #modalNuevoUsuario en el DOM');
    return;
  }
  // Requiere bootstrap.bundle ya cargado
  const modal = bootstrap.Modal.getOrCreateInstance(el);
  modal.show();
}
</script>



{{-- Estética mínima --}}
<style>
  #modalNuevoUsuario .card-body { padding: 1rem 1rem; }
  #modalNuevoUsuario .form-label { font-weight: 600; }
</style>

{{-- Lógica UI minimalista --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const selectRol = document.getElementById('selectRol');
  const common    = document.getElementById('commonFields');
  const sections  = document.querySelectorAll('.role-fields');
  const emailHint = document.getElementById('emailHint');

  // inputs base
  const nombre   = document.querySelector('[name="nombre"]');
  const apellido = document.querySelector('[name="apellido"]');
  const email    = document.querySelector('[name="email"]');
  const pass     = document.querySelector('[name="password"]');

  // líder semillero
  const ls_tipo  = document.querySelector('[name="ls_tipo_documento"]');
  const ls_doc   = document.querySelector('[name="ls_documento"]');

  // aprendiz
  const ap_ficha    = document.querySelector('[name="ap_ficha"]');
  const ap_programa = document.querySelector('[name="ap_programa"]');
  const ap_tipo     = document.querySelector('[name="ap_tipo_documento"]');
  const ap_doc      = document.querySelector('[name="ap_documento"]');
  const ap_ci       = document.querySelector('[name="ap_correo_institucional"]');
  const ap_cp       = document.querySelector('[name="ap_correo_personal"]');

  const setReq = (el, on) => { if(!el) return; on ? el.setAttribute('required','required') : el.removeAttribute('required'); };

  function updateEmailHint(role) {
    const map = {
      'LIDER_GENERAL': 'El correo institucional será el correo de login.',
      'LIDER_SEMILLERO': 'El correo institucional será el correo de login.',
      'APRENDIZ': 'El correo personal será el correo de login.',
      'ADMIN': ''
    };
    emailHint.textContent = map[role] || '';
  }

  function toggle() {
    const rol = selectRol.value;

    // comunes sólo si hay rol
    common.classList.toggle('d-none', !rol);

    // reset required
    [nombre,apellido,email,pass,ls_tipo,ls_doc,ap_ficha,ap_programa,ap_tipo,ap_doc,ap_ci].forEach(el => setReq(el,false));

    if (rol) { // base requeridos
      setReq(nombre,true); setReq(apellido,true); setReq(email,true); setReq(pass,true);
    }

    // mostrar sección del rol
    sections.forEach(s => s.classList.toggle('d-none', s.dataset.role !== rol));

    // requeridos por rol
    if (rol === 'LIDER_SEMILLERO') {
      setReq(ls_tipo,true); setReq(ls_doc,true);
    }
    if (rol === 'APRENDIZ') {
      setReq(ap_ficha,true); setReq(ap_programa,true); setReq(ap_tipo,true); setReq(ap_doc,true); setReq(ap_ci,true);
    }

    updateEmailHint(rol);
  }

  selectRol.addEventListener('change', toggle);
  toggle();

  // personal = login para aprendices (antes de enviar)
  document.getElementById('formNuevoUsuario').addEventListener('submit', () => {
    if (selectRol.value === 'APRENDIZ' && ap_cp) ap_cp.value = email.value || '';
  });
});
</script>



<!--  filtros-->

<script>
document.addEventListener('DOMContentLoaded', () => {
  const $search  = document.getElementById('userSearch');
  const $role    = document.getElementById('roleFilter');
  const $status  = document.getElementById('statusFilter');
  const $rows    = Array.from(document.querySelectorAll('#usersTable tbody tr'));

  const normalize = s => (s || '').toString().toLowerCase()
                        .normalize('NFD').replace(/\p{Diacritic}/gu, '');

  let timer;
  const debounce = (fn, wait=220) => (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), wait);
  };

  function applyFilters() {
    const q  = normalize($search.value);
    const rf = $role.value;             // ADMIN / LIDER_GENERAL / ...
    const sf = $status.value;           // ACTIVO / INACTIVO

    let visible = 0;

    $rows.forEach(tr => {
      const name  = normalize(tr.children[0]?.innerText);
      const email = normalize(tr.children[1]?.innerText);
      const r     = tr.dataset.role || '';
      const s     = tr.dataset.status || '';

      const matchText = !q || name.includes(q) || email.includes(q);
      const matchRole = !rf || r === rf;
      const matchStat = !sf || s === sf;

      const show = matchText && matchRole && matchStat;
      tr.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    // Si quieres, aquí puedes mostrar/ocultar un renglón "sin resultados"
  }

  $search.addEventListener('input', debounce(applyFilters));
  $role.addEventListener('change', applyFilters);
  $status.addEventListener('change', applyFilters);

  applyFilters();
});
</script>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

</body>
</html>
