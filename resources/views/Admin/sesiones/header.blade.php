<header class="header">
  <div class="user-info">
    <div class="user-avatar">JC</div>
    <span style="font-weight: 600; color: var(--gray-700);">Joaquín cañon</span>
  </div>
  <h1 class="header-title">Bienvenido a CIDE SEMILLERO</h1>

  <button class="logout-btn" type="button" onclick="window.App.logout()">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
    </svg>
    Cerrar sesión
  </button>

  <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
  </form>
</header>
