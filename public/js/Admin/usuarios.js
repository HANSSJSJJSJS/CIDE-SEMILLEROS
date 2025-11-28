(function () {
  'use strict';

  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));

  // ----------------------------------------------------
  // Helpers
  // ----------------------------------------------------
  function setBlockEnabled(block, enabled) {
    if (!block) return;
    block.classList.toggle('d-none', !enabled);
    block.querySelectorAll('input,select,textarea').forEach(el => {
      el.disabled = !enabled;
      if (!enabled) el.removeAttribute('required');
    });
  }

  function randomPassword(len = 10) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
    const arr   = new Uint32Array(len);
    window.crypto.getRandomValues(arr);
    return Array.from(arr, n => chars[n % chars.length]).join('');
  }

  function attachPasswordTools(root) {
    if (!root) return;

    root.querySelectorAll('[data-toggle-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const sel   = btn.getAttribute('data-toggle-pass');
        const input = root.querySelector(sel);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
      });
    });

    root.querySelectorAll('[data-generate-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const sel   = btn.getAttribute('data-generate-pass');
        const input = root.querySelector(sel);
        if (!input) return;
        input.value = randomPassword();
        input.type  = 'text';
        input.focus();
      });
    });
  }

  function attachValidation() {
    document.querySelectorAll('.needs-validation').forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    });
  }

  // ----------------------------------------------------
  // WIZARD GENERAL (ADMIN / LÍDERES)
  // ----------------------------------------------------
  function initGeneralWizard(modal, btnSave) {
    const nav      = $('#user-steps-nav', modal);
    const steps    = $$('.user-step', modal);
    const boxLider = $('#box-lider-semillero', modal);

    const label   = $('#user-step-label', modal);
    const btnPrev = $('#btn-user-prev', modal);
    const btnNext = $('#btn-user-next', modal);

    let current = 1;
    let maxStep = 2;

    function showStep(n) {
      current = Math.min(Math.max(n, 1), maxStep);

      steps.forEach(s => {
        const step = parseInt(s.dataset.step, 10);
        s.classList.toggle('d-none', step !== current);
      });

      if (label) label.textContent = `Paso ${current} de ${maxStep}`;
      if (btnPrev) btnPrev.disabled = (current === 1);

      if (btnNext) {
        if (current === maxStep) {
          btnNext.classList.add('d-none');
        } else {
          btnNext.classList.remove('d-none');
        }
      }

      if (btnSave) btnSave.disabled = (current !== maxStep);
    }

    function configureForRole(role) {
      // Sin rol o rol Aprendiz: solo mostramos step 1 sin nav
      if (!role || role === 'APRENDIZ') {
        if (nav) nav.classList.add('d-none');

        steps.forEach(s => {
          const step = parseInt(s.dataset.step, 10);
          s.classList.toggle('d-none', step !== 1);
        });

        setBlockEnabled(boxLider, false);
        if (btnSave) btnSave.disabled = true;
        return;
      }

      // Otros roles: activamos wizard
      maxStep = (role === 'LIDER_SEMILLERO') ? 3 : 2;
      setBlockEnabled(boxLider, role === 'LIDER_SEMILLERO');

      if (nav) nav.classList.remove('d-none');
      showStep(1);
    }

    btnPrev && btnPrev.addEventListener('click', () => showStep(current - 1));
    btnNext && btnNext.addEventListener('click', () => showStep(current + 1));

    return { configureForRole, showStep };
  }

  // ----------------------------------------------------
  // WIZARD APRENDIZ (8 pasos)
  // ----------------------------------------------------
  function initAprendizWizard(modal, btnSave) {
    const areaApr   = $('#area-aprendiz', modal);
    const navApr    = $('#apr-steps-nav', modal);
    const steps     = $$('.apr-step', modal);
    const label     = $('#apr-step-label', modal);
    const btnPrev   = $('#btn-apr-prev', modal);
    const btnNext   = $('#btn-apr-next', modal);
    const radioSena = $$('input[name="vinculado_sena"]', modal);
    const boxSena   = $('#apr-sena', modal);
    const boxNoSena = $('#apr-no-sena', modal);

    const TOTAL = 8;
    let current = 1;

    function updateSenaBlocks() {
      const val  = (radioSena.find(r => r.checked)?.value) || '1';
      const sena = (val === '1');
      setBlockEnabled(boxSena, sena);
      setBlockEnabled(boxNoSena, !sena);
    }

    function showAprStep(n) {
      current = Math.min(Math.max(n, 1), TOTAL);

      steps.forEach(s => {
        const step = parseInt(s.dataset.step, 10);
        s.classList.toggle('d-none', step !== current);
      });

      if (label) label.textContent = `Paso ${current} de ${TOTAL}`;
      if (btnPrev) btnPrev.disabled = (current === 1);

      if (btnNext) {
        if (current === TOTAL) {
          btnNext.classList.add('d-none');
        } else {
          btnNext.classList.remove('d-none');
        }
      }

      if (btnSave) btnSave.disabled = (current !== TOTAL);
    }

    btnPrev && btnPrev.addEventListener('click', () => showAprStep(current - 1));
    btnNext && btnNext.addEventListener('click', () => showAprStep(current + 1));
    radioSena.forEach(r => r.addEventListener('change', updateSenaBlocks));

    function enableAprendiz(enabled) {
      setBlockEnabled(areaApr, enabled);
      setBlockEnabled(navApr, enabled);

      if (!enabled) {
        if (btnSave) btnSave.disabled = true;
        return;
      }

      current = 1;
      showAprStep(current);
      updateSenaBlocks();
    }

    return { enableAprendiz, showAprStep };
  }

  // ----------------------------------------------------
  // INPUT FILTERS
  // ----------------------------------------------------
  function attachInputFilters(modal) {
    if (!modal) return;

    // Solo números
    ['documento', 'celular', 'contacto_celular', 'ficha'].forEach(name => {
      $$(`input[name="${name}"]`, modal).forEach(inp => {
        inp.addEventListener('input', () => {
          inp.value = inp.value.replace(/[^0-9]/g, '');
        });
      });
    });

    // Nombres sin caracteres especiales
    ['nombre', 'apellido', 'contacto_nombre'].forEach(name => {
      $$(`input[name="${name}"]`, modal).forEach(inp => {
        inp.addEventListener('input', () => {
          inp.value = inp.value.replace(/[^A-Za-zÁÉÍÓÚÑáéíóúñ\\s]/g, '');
        });
      });
    });
  }

  // ----------------------------------------------------
  // MODAL CREAR USUARIO
  // ----------------------------------------------------
  function initModalCrear() {
    const modal      = $('#modalCrearUsuario');
    if (!modal) return;

    const form       = $('#formCrearUsuario', modal);
    const roleSelect = $('#select-role', modal);
    const areaGen    = $('#area-general', modal);
    const areaApr    = $('#area-aprendiz', modal);
    const labelRol   = $('#rol-actual-label', modal);
    const btnSave    = $('#btn-save-user', modal);

    const wizGeneral = initGeneralWizard(modal, btnSave);
    const wizApr     = initAprendizWizard(modal, btnSave);

    attachPasswordTools(modal);
    attachInputFilters(modal);

    function resetForm() {
      if (!form) return;
      form.reset();
      form.classList.remove('was-validated');
      if (labelRol) {
        labelRol.style.display = 'none';
        labelRol.textContent = '';
      }
      if (btnSave) btnSave.disabled = true;
    }

    function configureRole(role) {
      const fields = form.querySelectorAll('input,select,textarea');

      // Siempre mostramos datos básicos (Rol, correo, nombre...)
      setBlockEnabled(areaGen, true);

      if (!role) {
        // Sin rol: deshabilitamos todo menos rol y token
        fields.forEach(el => {
          if (el.name === 'role' || el.name === '_token' || el.type === 'hidden') {
            el.disabled = false;
          } else {
            el.disabled = true;
          }
        });

        wizGeneral.configureForRole(null);
        wizApr.enableAprendiz(false);
        setBlockEnabled(areaApr, false);

        if (btnSave) btnSave.disabled = true;
        if (labelRol) {
          labelRol.style.display = 'none';
          labelRol.textContent = '';
        }
        return;
      }

      // Hay rol → habilitamos campos
      fields.forEach(el => {
        if (el.type !== 'hidden') el.disabled = false;
      });

      // Badge rol
      if (labelRol) {
        let texto = '';
        if      (role === 'ADMIN')               texto = 'Líder general';
        else if (role === 'LIDER_SEMILLERO')     texto = 'Líder de semillero';
        else if (role === 'LIDER_INVESTIGACION') texto = 'Líder de investigación';
        else if (role === 'APRENDIZ')            texto = 'Aprendiz';
        labelRol.textContent = texto;
        labelRol.style.display = 'inline-block';
      }

      if (role === 'APRENDIZ') {
        // General sin wizard (solo step 1 visible) + wizard de aprendiz
        wizGeneral.configureForRole(null);
        setBlockEnabled(areaApr, true);
        wizApr.enableAprendiz(true);
        return;
      }

      // Otros roles: wizard general, sin área aprendiz
      wizApr.enableAprendiz(false);
      setBlockEnabled(areaApr, false);
      wizGeneral.configureForRole(role);
    }

    // Cambio de rol
    roleSelect && roleSelect.addEventListener('change', () => {
      const newRole = roleSelect.value || '';

      // Limpiar todo al cambiar rol (menos el propio rol y token)
      const rolValue = roleSelect.value;
      form.querySelectorAll('input,select,textarea').forEach(el => {
        if (el.name === 'role' || el.name === '_token' || el.type === 'hidden') return;
        if (el.type === 'radio' || el.type === 'checkbox') {
          el.checked = el.defaultChecked;
        } else {
          el.value = '';
        }
      });
      roleSelect.value = rolValue;

      configureRole(newRole);
    });

    // Reset al cerrar modal
    modal.addEventListener('hidden.bs.modal', () => {
      resetForm();
      configureRole('');
    });

    // Estado inicial
    configureRole('');
  }

  // ----------------------------------------------------
  // INIT GLOBAL
  // ----------------------------------------------------
  document.addEventListener('DOMContentLoaded', () => {
    attachValidation();
    initModalCrear();
  });

})();
