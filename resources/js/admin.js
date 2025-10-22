// Opcional si quieres usar módulos y herramientas de Bootstrap con Vite:
// import 'bootstrap';

window.App = (function () {
  const App = {};

  // --- Navegación lateral: cambia secciones ---
  App.initSidebar = function () {
    const navItems = document.querySelectorAll('.sidebar .nav-item');
    const sections = document.querySelectorAll('.content-section');

    navItems.forEach(item => {
      item.addEventListener('click', (e) => {
        const target = item.getAttribute('data-target');
        // activar nav
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        // mostrar sección
        sections.forEach(s => s.classList.toggle('active', s.id === target));
      });
    });
  };

  // --- Logout ---
  App.logout = function () {
    if (confirm('¿Está seguro que desea cerrar sesión?')) {
      document.getElementById('logout-form')?.submit();
    }
  };

  // --- Modal Usuarios: helpers de UI ---
  function toggleRoleFields(selectRol, common, roleBlocks) {
    const role = selectRol.value || '';
    common.classList.toggle('d-none', !role);
    roleBlocks.forEach(b => b.classList.toggle('d-none', b.getAttribute('data-role') !== role));
  }
  const toUiRole = (dbRole) => (dbRole === 'LIDER GENERAL' ? 'LIDER_GENERAL' : dbRole);

  // --- Inicializa modal crear/editar usuarios ---
  App.initUsersModal = function () {
    const modalEl    = document.getElementById('modalNuevoUsuario');
    if (!modalEl) return;

    const modalLabel = modalEl.querySelector('#modalNuevoUsuarioLabel');
    const form       = document.getElementById('formNuevoUsuario');
    const selectRol  = document.getElementById('selectRol');
    const common     = document.getElementById('commonFields');
    const roleBlocks = modalEl.querySelectorAll('.role-fields');
    const submitBtn  = form.querySelector('button[type="submit"]');

    // Mostrar/ocultar bloques por rol
    selectRol.addEventListener('change', () => toggleRoleFields(selectRol, common, roleBlocks));
    toggleRoleFields(selectRol, common, roleBlocks);

    // Reset a modo crear al cerrar
    modalEl.addEventListener('hidden.bs.modal', () => {
      modalLabel.textContent = 'Registrar usuario';
      submitBtn.textContent  = 'Guardar Usuario';
      form.action            = form.dataset.storeUrl || form.action;
      form.querySelector('input[name="_method"]')?.remove();
      selectRol.removeAttribute('disabled');
      form.reset();
      toggleRoleFields(selectRol, common, roleBlocks);
    });

    // Rellenar modalidad edición
    document.querySelectorAll('.btn-editar').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const editUrl   = btn.dataset.editUrl;
        const updateUrl = btn.dataset.updateUrl;

        let payload;
        try {
          const res = await fetch(editUrl, { headers: {'X-Requested-With':'XMLHttpRequest'} });
          if (!res.ok) throw new Error('No se pudo cargar el usuario.');
          payload = await res.json();
        } catch (err) {
          alert(err.message || 'Error cargando datos para editar.');
          return;
        }

        const { usuario, perfil } = payload;

        modalLabel.textContent = 'Editar usuario';
        submitBtn.textContent  = 'Actualizar';
        form.action            = updateUrl;

        // _method=PUT
        if (!form.querySelector('input[name="_method"]')) {
          const hidden = document.createElement('input');
          hidden.type = 'hidden'; hidden.name = '_method'; hidden.value = 'PUT';
          form.appendChild(hidden);
        }

        // Campos comunes
        selectRol.value = toUiRole(usuario.role);
        selectRol.setAttribute('disabled','disabled');
        toggleRoleFields(selectRol, common, roleBlocks);

        form.querySelector('input[name="nombre"]').value   = usuario.name ?? '';
        form.querySelector('input[name="apellido"]').value = usuario.apellidos ?? '';
        form.querySelector('input[name="email"]').value    = usuario.email ?? '';
        // password: vacío (flujo aparte si quieres cambiarlo)

        // Por rol
        const role = selectRol.value;

        if (role === 'LIDER_SEMILLERO') {
          form.querySelector('select[name="ls_tipo_documento"]').value = perfil?.tipo_documento ?? '';
          form.querySelector('input[name="ls_documento"]').value       = perfil?.documento ?? '';
        }

        if (role === 'APRENDIZ') {
          form.querySelector('input[name="ap_ficha"]').value                 = perfil?.ficha ?? '';
          form.querySelector('input[name="ap_programa"]').value              = perfil?.programa ?? '';
          form.querySelector('select[name="ap_tipo_documento"]').value       = perfil?.tipo_documento ?? '';
          form.querySelector('input[name="ap_documento"]').value             = perfil?.documento ?? '';
          form.querySelector('input[name="ap_celular"]').value               = perfil?.celular ?? '';
          form.querySelector('input[name="ap_correo_institucional"]').value  = perfil?.correo_institucional ?? '';
          form.querySelector('input[name="ap_contacto_nombre"]').value       = perfil?.contacto_nombre ?? '';
          form.querySelector('input[name="ap_contacto_celular"]').value      = perfil?.contacto_celular ?? '';
          const hiddenPersonal = form.querySelector('input[name="ap_correo_personal"]');
          if (hiddenPersonal) hiddenPersonal.value = usuario.email ?? '';
        }

        // Abrir modal
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
      });
    });

    // Cuando se crea APRENDIZ: personal = login si está vacío
    form.addEventListener('submit', () => {
      if (!form.querySelector('input[name="_method"]') && selectRol.value === 'APRENDIZ') {
        const p = form.querySelector('input[name="ap_correo_personal"]');
        const e = form.querySelector('input[name="email"]');
        if (p && !p.value && e && e.value) p.value = e.value;
      }
    });
  };

  // --- Filtros de tabla usuarios ---
  App.initUsersFilters = function () {
    const $search  = document.getElementById('userSearch');
    const $role    = document.getElementById('roleFilter');
    const $status  = document.getElementById('statusFilter');
    const $rows    = Array.from(document.querySelectorAll('#usersTable tbody tr'));
    if (!$rows.length) return;

    const normalize = s => (s || '').toString().toLowerCase()
      .normalize('NFD').replace(/\p{Diacritic}/gu, '');

    let timer;
    const debounce = (fn, wait=220) => (...args) => {
      clearTimeout(timer); timer = setTimeout(() => fn(...args), wait);
    };

    function applyFilters() {
      const q  = normalize($search?.value);
      const rf = $role?.value || '';
      const sf = $status?.value || '';

      $rows.forEach(tr => {
        const name  = normalize(tr.children[0]?.innerText);
        const email = normalize(tr.children[1]?.innerText);
        const r     = tr.dataset.role || '';
        const s     = tr.dataset.status || '';

        const matchText = !q || name.includes(q) || email.includes(q);
        const matchRole = !rf || r === rf;
        const matchStat = !sf || s === sf;

        tr.style.display = (matchText && matchRole && matchStat) ? '' : 'none';
      });
    }

    $search?.addEventListener('input', debounce(applyFilters));
    $role?.addEventListener('change', applyFilters);
    $status?.addEventListener('change', applyFilters);
    applyFilters();
  };

  // --- Inicialización global ---
  document.addEventListener('DOMContentLoaded', function () {
    // Guarda la URL de store en el form para reset rápido
    const form = document.getElementById('formNuevoUsuario');
    if (form) form.dataset.storeUrl = form.action;

    App.initSidebar();
    App.initUsersModal();
    App.initUsersFilters();
  });

  return App;
})();
