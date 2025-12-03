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
  function initGeneralWizard(modal) {
    const nav      = $('#user-steps-nav', modal);
    const steps    = $$('.user-step', modal);
    const boxLider = $('#box-lider-semillero', modal);

    const label   = $('#user-step-label', modal);
    const btnPrev = $('#btn-user-prev', modal);
    const btnNext = $('#btn-user-next', modal);

    let current = 1;
    let maxStep = 1;

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
    }

    function configureForRole(role) {
      // Sin rol o Aprendiz → solo paso 1, sin nav, sin bloque líder
      if (!role || role === 'APRENDIZ') {
        if (nav) nav.classList.add('d-none');

        steps.forEach(s => {
          const step = parseInt(s.dataset.step, 10);
          s.classList.toggle('d-none', step !== 1);
        });

        setBlockEnabled(boxLider, false);

        if (label) label.textContent = '';
        if (btnPrev) btnPrev.disabled = true;
        if (btnNext) btnNext.classList.add('d-none');
        return;
      }

      // LÍDER DE SEMILLERO → 2 pasos (1: básicos, 2: datos líder)
      if (role === 'LIDER_SEMILLERO') {
        maxStep = 2;
        setBlockEnabled(boxLider, true);

        if (nav) nav.classList.remove('d-none');
        showStep(1);
        return;
      }

      // ADMIN y LÍDER INVESTIGACIÓN → solo un paso
      setBlockEnabled(boxLider, false);

      if (nav) nav.classList.add('d-none');

      steps.forEach(s => {
        const step = parseInt(s.dataset.step, 10);
        s.classList.toggle('d-none', step !== 1);
      });

      if (label) label.textContent = 'Paso 1 de 1';
      if (btnPrev) btnPrev.disabled = true;
      if (btnNext) btnNext.classList.add('d-none');
    }

    btnPrev && btnPrev.addEventListener('click', () => showStep(current - 1));
    btnNext && btnNext.addEventListener('click', () => showStep(current + 1));

    return { configureForRole, showStep };
  }

  // ----------------------------------------------------
  // WIZARD APRENDIZ
  // ----------------------------------------------------
  function initAprendizWizard(modal) {
    const areaApr   = $('#area-aprendiz', modal);
    const navApr    = $('#apr-steps-nav', modal);
    const steps     = $$('.apr-step', modal);
    const label     = $('#apr-step-label', modal);
    const btnPrev   = $('#btn-apr-prev', modal);
    const btnNext   = $('#btn-apr-next', modal);
    const radioSena = $$('input[name="vinculado_sena"]', modal);
    const boxSena   = $('#apr-sena', modal);
    const boxNoSena = $('#apr-no-sena', modal);

    const TOTAL = steps.length || 1;
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
    }

    btnPrev && btnPrev.addEventListener('click', () => showAprStep(current - 1));
    btnNext && btnNext.addEventListener('click', () => showAprStep(current + 1));
    radioSena.forEach(r => r.addEventListener('change', updateSenaBlocks));

    function enableAprendiz(enabled) {
      setBlockEnabled(areaApr, enabled);
      setBlockEnabled(navApr, enabled);

      if (!enabled) return;

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

    ['documento', 'celular', 'contacto_celular', 'ficha'].forEach(name => {
      $$(`input[name="${name}"]`, modal).forEach(inp => {
        inp.addEventListener('input', () => {
          inp.value = inp.value.replace(/[^0-9]/g, '');
        });
      });
    });

    ['nombre', 'apellido', 'contacto_nombre'].forEach(name => {
      $$(`input[name="${name}"]`, modal).forEach(inp => {
        inp.addEventListener('input', () => {
          inp.value = inp.value.replace(/[^A-Za-zÁÉÍÓÚÑáéíóúñ\s]/g, '');
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

    const wizGeneral = initGeneralWizard(modal);
    const wizApr     = initAprendizWizard(modal);

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

      setBlockEnabled(areaGen, true);

      if (!role) {
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

      fields.forEach(el => {
        if (el.type !== 'hidden') el.disabled = false;
      });
      if (btnSave) btnSave.disabled = false;

      if (labelRol) {
        let texto = '';
        if      (role === 'ADMIN')               texto = 'Líder general';
        else if (role === 'LIDER_SEMILLERO')     texto = 'Líder de semillero';
        else if (role === 'LIDER_INVESTIGACION') texto = 'Líder de investigación';
        else if (role === 'LIDER_GENERAL')       texto = 'Líder general';
        else if (role === 'APRENDIZ')            texto = 'Aprendiz';

        labelRol.textContent   = texto;
        labelRol.style.display = 'inline-block';
      }

      if (role === 'APRENDIZ') {
        wizGeneral.configureForRole(null);
        setBlockEnabled(areaApr, true);
        wizApr.enableAprendiz(true);
        return;
      }

      wizApr.enableAprendiz(false);
      setBlockEnabled(areaApr, false);
      wizGeneral.configureForRole(role);
    }

    roleSelect && roleSelect.addEventListener('change', () => {
      const newRole = roleSelect.value || '';

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

    modal.addEventListener('hidden.bs.modal', () => {
      resetForm();
      configureRole('');
    });

    configureRole('');
  }

  // ----------------------------------------------------
  // MODAL VER USUARIO
  // ----------------------------------------------------
  function initModalVerUsuario() {
    const modalEl = document.getElementById('modalVerUsuario');
    if (!modalEl) return;

    const bsModal = new bootstrap.Modal(modalEl);

    function setField(name, value) {
      const el = modalEl.querySelector('[data-field="' + name + '"]');
      if (el) el.textContent = (value ?? '—');
    }

    document.addEventListener('click', e => {
      const btn = e.target.closest('.btn-ver-usuario');
      if (!btn) return;

      const userId = btn.dataset.userId;
      if (!userId) return;

      // Limpiar
      modalEl.querySelectorAll('[data-field]').forEach(el => el.textContent = '—');
      modalEl.querySelectorAll('#ver-bloque-admin,#ver-bloque-lider-semi,#ver-bloque-lider-inv,#ver-bloque-aprendiz')
        .forEach(b => b.classList.add('d-none'));

      fetch(`/admin/usuarios/${userId}/detalle-ajax`)
        .then(r => {
          if (!r.ok) throw new Error('HTTP ' + r.status);
          return r.json();
        })
        .then(data => {
          if (data.ok === false) {
            throw new Error(data.message || 'Error en la respuesta');
          }

          const u = data.usuario || {};
          const p = data.perfil  || {};

          const roleLabel = {
            'ADMIN':               'Líder general',
            'LIDER_SEMILLERO':     'Líder de semillero',
            'LIDER_INVESTIGACION': 'Líder de investigación',
            'APRENDIZ':            'Aprendiz'
          }[u.role] || u.role || '—';

          const badge = modalEl.querySelector('#ver-role-label');
          if (badge) badge.textContent = roleLabel;

          const nombreCompleto = `${u.name ?? ''} ${u.apellidos ?? ''}`.trim() || 'Usuario';

          setField('nombre_completo', nombreCompleto);
          setField('email', u.email);
          setField('tipo_documento', u.tipo_documento);
          setField('documento', u.documento);
          setField('celular', u.celular);
          setField('genero', u.genero);
          setField('tipo_rh', u.tipo_rh);
          setField('estado', u.estado || (u.is_active ? 'Activo' : 'Inactivo'));

          switch (u.role) {
            case 'ADMIN':
              modalEl.querySelector('#ver-bloque-admin')?.classList.remove('d-none');
              break;

            case 'LIDER_SEMILLERO':
              modalEl.querySelector('#ver-bloque-lider-semi')?.classList.remove('d-none');
              setField('ls_correo_institucional', p.correo_institucional);
              setField('ls_semillero_nombre', p.semillero_nombre);
              break;

            case 'LIDER_INVESTIGACION':
              modalEl.querySelector('#ver-bloque-lider-inv')?.classList.remove('d-none');
              break;

            case 'APRENDIZ':
              modalEl.querySelector('#ver-bloque-aprendiz')?.classList.remove('d-none');
              setField('ap_semillero_nombre', p.semillero_nombre);
              setField('nivel_educativo', p.nivel_educativo);
              setField('vinculado_sena', p.vinculado_sena === 1 ? 'Sí' : (p.vinculado_sena === 0 ? 'No' : '—'));
              setField('ficha', p.ficha);
              setField('programa', p.programa);
              setField('institucion', p.institucion);
              setField('correo_institucional', p.correo_institucional);
              setField('contacto_nombre', p.contacto_nombre);
              setField('contacto_celular', p.contacto_celular);
              break;
          }

          bsModal.show();
        })
        .catch(err => {
          console.error(err);
          if (typeof swalError === 'function') {
            swalError('No se pudieron cargar los datos del usuario.');
          } else {
            alert('No se pudieron cargar los datos del usuario.');
          }
        });
    });
  }

  // ----------------------------------------------------
  // INIT GLOBAL
  // ----------------------------------------------------
  document.addEventListener('DOMContentLoaded', () => {
    attachValidation();
    initModalCrear();
    initModalVerUsuario();
  });

})();