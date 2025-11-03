(function () {
  const sidebar   = document.getElementById('admSidebar');
  const overlay   = document.getElementById('sidebarOverlay');
  const toggleBtn = document.getElementById('menuToggle');
  const body      = document.body;
  const MQ_LG     = 992; // Bootstrap lg breakpoint

  function isMobile() {
    return window.innerWidth < MQ_LG;
  }

  function openSidebar() {
    if (!isMobile()) return;
    sidebar.classList.add('show');
    overlay.classList.add('show');
    toggleBtn?.setAttribute('aria-expanded', 'true');
    body.classList.add('noscroll');
  }

  function closeSidebar() {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    toggleBtn?.setAttribute('aria-expanded', 'false');
    body.classList.remove('noscroll');
  }

  function toggleSidebar() {
    if (!isMobile()) return;
    sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
  }

  // Eventos
  toggleBtn?.addEventListener('click', toggleSidebar);
  overlay?.addEventListener('click', closeSidebar);

  // Cerrar con Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
  });

  // Al cambiar tamaño, resetea estados para evitar “menú trabado”
  window.addEventListener('resize', () => {
    if (!isMobile()) {
      // modo escritorio
      closeSidebar();
    }
  });

  // Protección: si recarga en móvil con hash, garantiza estado cerrado
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && !isMobile()) closeSidebar();
  });
})();
