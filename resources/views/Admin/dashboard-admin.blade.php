<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - CIDE SEMILLERO</title>
    <link rel="stylesheet" href="../css/Style_layouts.css">
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
        <button class="logout-btn" onclick="logout()">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar sesión
        </button>
    </header>

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

    <div class="toolbar">
        <div class="search-box">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Buscar usuarios por nombre o email..." />
        </div>
        <select>
            <option>Todos los roles</option>
            <option>Administrador</option>
            <option>Líder</option>
            <option>Aprendiz</option>
        </select>
        <select>
            <option>Todos los estados</option>
            <option>Activo</option>
            <option>Inactivo</option>
        </select>
        <button class="btn btn-primary" onclick="openModal('addUser')">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </button>
    </div>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>Usuario</th>
                <th>Email</th>
                 <th>Rol</th> {{-- NUEVA --}}
                {{-- <th>Semillero</th>  --}} {{-- eliminado --}}
                {{-- <th>Último acceso</th> --}} {{-- eliminado --}}
                <th>Registrado</th>
                <th>Acciones</th>
            </tr>
            </thead>

            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge">
                            {{ strtoupper($user->role ?? 'SIN ROL') }}
                        </span>
                    </td>
                    <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-icon btn-edit" title="Editar">✏️</button>
                            <button class="btn btn-icon btn-delete" title="Eliminar">🗑️</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay usuarios.</td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    @isset($users)
        @if(method_exists($users, 'links'))
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        @endif
    @endisset
</section>

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
    <div id="addUser" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nuevo Usuario</h3>
                <button class="close-modal" onclick="closeModal('addUser')">×</button>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" class="form-input" placeholder="Ingrese el nombre completo">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" placeholder="usuario@sena.edu.co">
                </div>
                <div class="form-group">
                    <label class="form-label">Documento</label>
                    <input type="text" class="form-input" placeholder="Número de documento">
                </div>
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select class="form-input">
                        <option>Seleccione un rol</option>
                        <option>Administrador</option>
                        <option>Líder</option>
                        <option>Aprendiz</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-input" placeholder="Contraseña temporal">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUser')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
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
                alert('Sesión cerrada exitosamente');
                // Redirect logic here
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>