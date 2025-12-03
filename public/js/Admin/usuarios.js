(function () {
  'use strict';

  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  // ----------------------------------------------------
  // Helpers generales
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

        const icon = btn.querySelector('i');

        if (input.type === 'password') {
          input.type = 'text';
          if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
          }
        } else {
          input.type = 'password';
          if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
          }
        }
      });
    });

    root.querySelectorAll('[data-generate-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const sel   = btn.getAttribute('data-generate-pass');
        const input = root.querySelector(sel);
        if (!input) return;

        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@$%*?';
        let pass = '';
        for (let i = 0; i < 10; i++) {
          pass += chars[Math.floor(Math.random() * chars.length)];
        }
        input.value = pass;
        input.dispatchEvent(new Event('input'));
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

    // Solo letras y espacios
    ['nombre', 'apellido', 'contacto_nombre'].forEach(name => {
      $$(`input[name="${name}"]`, modal).forEach(inp => {
        inp.addEventListener('input', () => {
          inp.value = inp.value.replace(/[^A-Za-zÁÉÍÓÚÑáéíóúñ\s]/g, '');
        });
      });
    });
  }

  // ----------------------------------------------------
  // Modal CREAR USUARIO (wizard)
  // ----------------------------------------------------
  function initModalCrear() {
    const modal = document.getElementById('modalCrearUsuario');
    if (!modal) return;

    const form         = modal.querySelector('#formCrearUsuario');
    const selectRole   = modal.querySelector('#select-role');
    const rolLabel     = modal.querySelector('#rol-actual-label');

    const areaGeneral  = modal.querySelector('#area-general');
    const areaAprendiz = modal.querySelector('#area-aprendiz');

    const userSteps    = modal.querySelectorAll('.user-step');
    const userNav      = modal.querySelector('#user-steps-nav');
    const btnUserPrev  = modal.querySelector('#btn-user-prev');
    const btnUserNext  = modal.querySelector('#btn-user-next');
    const userStepLabel= modal.querySelector('#user-step-label');

    const aprSteps     = modal.querySelectorAll('.apr-step');
    const aprNav       = modal.querySelector('#apr-steps-nav');
    const btnAprPrev   = modal.querySelector('#btn-apr-prev');
    const btnAprNext   = modal.querySelector('#btn-apr-next');
    const aprStepLabel = modal.querySelector('#apr-step-label');

    const btnSave      = modal.querySelector('#btn-save-user');

    // Campos que dependen del rol
    const lsCorreo     = modal.querySelector('input[name="ls_correo_institucional"]');
    const lsSemillero  = modal.querySelector('select[name="ls_semillero_id"]');
    const aprCorreo    = modal.querySelector('input[name="correo_institucional"]');
    const aprSemillero = modal.querySelector('select[name="semillero_id"]');

    // Campos SENA (Aprendiz)
    const radioSena = modal.querySelectorAll('input[name="vinculado_sena"]');
    const boxSena   = modal.querySelector('#apr-sena');
    const boxNoSena = modal.querySelector('#apr-no-sena');

    let currentUserStep = 1;
    let maxUserSteps    = 1;
    let currentAprStep  = 1;
    const maxAprSteps   = aprSteps.length || 1;

    function setStepVisibility(steps, current) {
      steps.forEach(step => {
        const n = parseInt(step.dataset.step, 10);
        step.classList.toggle('d-none', n !== current);
      });
    }

    function validateStep(steps, current) {
      const step = Array.from(steps).find(s => parseInt(s.dataset.step, 10) === current);
      if (!step) return true;

      const inputs = step.querySelectorAll('input, select, textarea');
      let firstInvalid = null;

      inputs.forEach(el => {
        el.classList.remove('is-invalid');
        if (!el.checkValidity()) {
          if (!firstInvalid) firstInvalid = el;
          el.classList.add('is-invalid');
        }
      });

      if (firstInvalid) {
        if (typeof firstInvalid.reportValidity === 'function') {
          firstInvalid.reportValidity();
        }
        firstInvalid.focus();
        return false;
      }

      return true;
    }

    function updateRolLabel(role) {
      if (!rolLabel) return;
      if (!role) {
        rolLabel.style.display = 'none';
        rolLabel.textContent = '';
        return;
      }
      const map = {
        'ADMIN'              : 'Líder general',
        'LIDER_SEMILLERO'    : 'Líder de semillero',
        'LIDER_INVESTIGACION': 'Líder de investigación',
        'APRENDIZ'           : 'Aprendiz'
      };
      rolLabel.textContent   = map[role] || role;
      rolLabel.style.display = 'inline-block';
    }

    function updateRequiredByRole(role) {
      [lsCorreo, lsSemillero, aprCorreo, aprSemillero].forEach(el => {
        if (!el) return;
        el.required = false;
      });

      if (role === 'LIDER_SEMILLERO') {
        if (lsCorreo) lsCorreo.required = true;
      } else if (role === 'APRENDIZ') {
        if (aprCorreo)    aprCorreo.required    = true;
        if (aprSemillero) aprSemillero.required = true;
      }
    }

    function updateSenaBlocks() {
      const val  = Array.from(radioSena).find(r => r.checked)?.value || '1';
      const sena = (val === '1');
      setBlockEnabled(boxSena, sena);
      setBlockEnabled(boxNoSena, !sena);
    }

    function updateSaveState() {
      if (!btnSave) return;
      const role = selectRole.value;

      if (!role) {
        btnSave.disabled = true;
        return;
      }

      if (role === 'APRENDIZ') {
        btnSave.disabled = (currentAprStep !== maxAprSteps);
      } else {
        btnSave.disabled = (currentUserStep !== maxUserSteps);
      }
    }

    function configureByRole() {
      const role = selectRole.value || '';

      updateRolLabel(role);
      updateRequiredByRole(role);

      currentUserStep = 1;
      currentAprStep  = 1;

      areaAprendiz.classList.add('d-none');
      userNav.classList.add('d-none');
      aprNav.classList.add('d-none');

      setBlockEnabled(boxSena, false);
      setBlockEnabled(boxNoSena, false);

      if (!role) {
        areaGeneral.classList.add('d-none');
        updateSaveState();
        return;
      }

      areaGeneral.classList.remove('d-none');

      if (role === 'APRENDIZ') {
        areaAprendiz.classList.remove('d-none');
        aprNav.classList.remove('d-none');

        setStepVisibility(aprSteps, currentAprStep);
        if (aprStepLabel) {
          aprStepLabel.textContent = `Paso ${currentAprStep} de ${maxAprSteps}`;
        }

        updateSenaBlocks();
      } else {
        maxUserSteps = (role === 'LIDER_SEMILLERO') ? userSteps.length : 1;

        userSteps.forEach(step => {
          const n = parseInt(step.dataset.step, 10);
          if (n === 2 && role !== 'LIDER_SEMILLERO') {
            step.classList.add('d-none');
          }
        });

        setStepVisibility(userSteps, currentUserStep);

        if (maxUserSteps > 1) {
          userNav.classList.remove('d-none');
          if (userStepLabel) {
            userStepLabel.textContent = `Paso ${currentUserStep} de ${maxUserSteps}`;
          }
        } else {
          userNav.classList.add('d-none');
        }
      }

      updateSaveState();
    }

    if (btnUserNext) {
      btnUserNext.addEventListener('click', () => {
        if (!validateStep(userSteps, currentUserStep)) return;

        if (currentUserStep < maxUserSteps) {
          currentUserStep++;
          setStepVisibility(userSteps, currentUserStep);
          if (userStepLabel) {
            userStepLabel.textContent = `Paso ${currentUserStep} de ${maxUserSteps}`;
          }
          updateSaveState();
        }
      });
    }

    if (btnUserPrev) {
      btnUserPrev.addEventListener('click', () => {
        if (currentUserStep > 1) {
          currentUserStep--;
          setStepVisibility(userSteps, currentUserStep);
          if (userStepLabel) {
            userStepLabel.textContent = `Paso ${currentUserStep} de ${maxUserSteps}`;
          }
          updateSaveState();
        }
      });
    }

    if (btnAprNext) {
      btnAprNext.addEventListener('click', () => {
        if (!validateStep(aprSteps, currentAprStep)) return;

        if (currentAprStep < maxAprSteps) {
          currentAprStep++;
          setStepVisibility(aprSteps, currentAprStep);
          if (aprStepLabel) {
            aprStepLabel.textContent = `Paso ${currentAprStep} de ${maxAprSteps}`;
          }
          updateSaveState();
        }
      });
    }

    if (btnAprPrev) {
      btnAprPrev.addEventListener('click', () => {
        if (currentAprStep > 1) {
          currentAprStep--;
          setStepVisibility(aprSteps, currentAprStep);
          if (aprStepLabel) {
            aprStepLabel.textContent = `Paso ${currentAprStep} de ${maxAprSteps}`;
          }
          updateSaveState();
        }
      });
    }

    radioSena.forEach(r => r.addEventListener('change', updateSenaBlocks));

    if (selectRole) {
      selectRole.addEventListener('change', configureByRole);
    }

    modal.addEventListener('hidden.bs.modal', () => {
      form.reset();
      currentUserStep = 1;
      currentAprStep  = 1;
      configureByRole();
    });

    attachPasswordTools(modal);
    attachInputFilters(modal);

    configureByRole();
  }

  // ----------------------------------------------------
  // Modal VER USUARIO (btn "Ver datos")
  // ----------------------------------------------------
  function initModalVerUsuario() {
    const modalEl = document.getElementById('modalVerUsuario');
    if (!modalEl) return;         // si no existe, no hacemos nada

    let bsModal = null;           // instancia de Bootstrap Modal (lazy)

    function setField(name, value) {
      const el = modalEl.querySelector('[data-field="' + name + '"]');
      if (el) el.textContent = (value ?? '—');
    }

    document.addEventListener('click', e => {
      const btn = e.target.closest('.btn-ver-usuario');
      if (!btn) return;

      const userId = btn.dataset.userId;
      if (!userId) return;

      // Limpiar campos y bloques
      modalEl.querySelectorAll('[data-field]').forEach(el => el.textContent = '—');
      modalEl.querySelectorAll(
        '#ver-bloque-admin,#ver-bloque-lider-semi,#ver-bloque-lider-inv,#ver-bloque-aprendiz'
      ).forEach(b => b.classList.add('d-none'));

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
            'ADMIN'              : 'Líder general',
            'LIDER_SEMILLERO'    : 'Líder de semillero',
            'LIDER_INVESTIGACION': 'Líder de investigación',
            'APRENDIZ'           : 'Aprendiz'
          }[u.role] || u.role || '—';

          const badge = modalEl.querySelector('#ver-role-label');
          if (badge) badge.textContent = roleLabel;

          const nombreCompleto = `${u.name ?? ''} ${u.apellidos ?? ''}`.trim() || 'Usuario';

          setField('nombre_completo',    nombreCompleto);
          setField('email',              u.email);
          setField('tipo_documento',     u.tipo_documento);
          setField('documento',          u.documento);
          setField('celular',            u.celular);
          setField('genero',             u.genero);
          setField('tipo_rh',            u.tipo_rh);
          setField('estado',             u.estado || (u.is_active ? 'Activo' : 'Inactivo'));

          switch (u.role) {
            case 'ADMIN':
              modalEl.querySelector('#ver-bloque-admin')?.classList.remove('d-none');
              break;

            case 'LIDER_SEMILLERO':
              modalEl.querySelector('#ver-bloque-lider-semi')?.classList.remove('d-none');
              setField('ls_correo_institucional', p.correo_institucional);
              setField('ls_semillero_nombre',     p.semillero_nombre);
              break;

            case 'LIDER_INVESTIGACION':
              modalEl.querySelector('#ver-bloque-lider-inv')?.classList.remove('d-none');
              break;

            case 'APRENDIZ':
              modalEl.querySelector('#ver-bloque-aprendiz')?.classList.remove('d-none');
              setField('ap_semillero_nombre',  p.semillero_nombre);
              setField('nivel_educativo',      p.nivel_educativo);
              setField('vinculado_sena',
                p.vinculado_sena === 1 ? 'Sí' : (p.vinculado_sena === 0 ? 'No' : '—')
              );
              setField('ficha',                p.ficha);
              setField('programa',             p.programa);
              setField('institucion',          p.institucion);
              setField('correo_institucional', p.correo_institucional);
              setField('contacto_nombre',      p.contacto_nombre);
              setField('contacto_celular',     p.contacto_celular);
              break;
          }

          if (!bsModal) {
            bsModal = new bootstrap.Modal(modalEl);
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
  // Confirmación EDITAR usuario
  // ----------------------------------------------------
  function initConfirmEditarUsuario() {
    const form = document.getElementById('formEditarUsuario');
    if (!form) return;   // si no existe el modal, no hacemos nada

    form.addEventListener('submit', function (e) {
      e.preventDefault();   // detenemos el submit normal

      // Intentamos armar el nombre del usuario para mostrarlo en el mensaje
      const nombreInput    = form.querySelector('#edit_nombre')      || form.querySelector('[name="nombre"]');
      const apellidosInput = form.querySelector('#edit_apellidos')   || form.querySelector('[name="apellidos"]');

      const nombreCompleto = [
        nombreInput?.value    || '',
        apellidosInput?.value || ''
      ].join(' ').trim() || 'este usuario';

      Swal.fire({
        icon: 'question',
        title: 'Guardar cambios',
        html: `¿Seguro que deseas guardar los cambios de <strong>${nombreCompleto}</strong>?`,
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        customClass: {
          confirmButton: 'custom-confirm swal2-confirm',
          cancelButton: 'custom-cancel swal2-cancel'
        }
      }).then(result => {
        if (result.isConfirmed) {
          form.submit();    // ahora sí enviamos el formulario
        }
      });
    });
  }

    // ----------------------------------------------------
  // Confirmación eliminar usuario
  // ----------------------------------------------------
  function initConfirmEliminarUsuario() {
    document.addEventListener('submit', function (e) {
      const form = e.target.closest('.form-eliminar-usuario');
      if (!form) return;

      e.preventDefault(); // detenemos el submit normal

      const nombre = form.dataset.userName || 'este usuario';

      Swal.fire({
        icon: 'warning',
        title: '¿Eliminar usuario?',
        html: `¿Seguro que deseas eliminar a <strong>${nombre}</strong>?<br><small>Esta acción no se puede deshacer.</small>`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
          confirmButton: 'custom-danger swal2-confirm',
          cancelButton: 'custom-cancel swal2-cancel'
        }
      }).then(result => {
        if (result.isConfirmed) {
          form.submit(); // ahora sí enviamos el form de verdad
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
    initModalEditarUsuario();
    initConfirmEliminarUsuario();
    initConfirmEditarUsuario();
  });

  // ----------------------------------------------------
  // Modal EDITAR USUARIO
  // ----------------------------------------------------
  function initModalEditarUsuario() {
    const modalEl = document.getElementById('modalEditarUsuario');
    if (!modalEl) return;

    const form = modalEl.querySelector('#formEditarUsuario');
    const rolLabel = modalEl.querySelector('#editar-rol-label');

    // Campos básicos
    const fTipoDoc   = modalEl.querySelector('#edit_tipo_documento');
    const fDoc       = modalEl.querySelector('#edit_documento');
    const fNombre    = modalEl.querySelector('#edit_nombre');
    const fApellidos = modalEl.querySelector('#edit_apellidos');
    const fGenero    = modalEl.querySelector('#edit_genero');
    const fRh        = modalEl.querySelector('#edit_tipo_rh');
    const fCelular   = modalEl.querySelector('#edit_celular');
    const fEmail     = modalEl.querySelector('#edit_email');

    // Bloques por rol
    const bloqueLider    = modalEl.querySelector('#editar-bloque-lider');
    const bloqueAprendiz = modalEl.querySelector('#editar-bloque-aprendiz');

    // Campos Líder semillero
    const fLsCorreo    = modalEl.querySelector('#edit_ls_correo_institucional');
    const fLsSemillero = modalEl.querySelector('#edit_ls_semillero_nombre');

    // Campos Aprendiz
    const fApSemillero   = modalEl.querySelector('#edit_ap_semillero_nombre');
    const fApCorreoInst  = modalEl.querySelector('#edit_ap_correo_institucional');
    const fApNivel       = modalEl.querySelector('#edit_ap_nivel_educativo');
    const fApFicha       = modalEl.querySelector('#edit_ap_ficha');
    const fApPrograma    = modalEl.querySelector('#edit_ap_programa');
    const fApInstitucion = modalEl.querySelector('#edit_ap_institucion');
    const fApContNombre  = modalEl.querySelector('#edit_ap_contacto_nombre');
    const fApContCel     = modalEl.querySelector('#edit_ap_contacto_celular');

    attachInputFilters(modalEl);

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-editar-usuario');
      if (!btn) return;

      const userId = btn.dataset.userId;

      form.reset();
      form.classList.remove('was-validated');
      bloqueLider.classList.add('d-none');
      bloqueAprendiz.classList.add('d-none');
      form.action = `/admin/usuarios/${userId}`;

      try {
        const resp = await fetch(`/admin/usuarios/${userId}/detalle-ajax`);
        const data = await resp.json();

        const u = data.usuario;
        const p = data.perfil;

        const roleMap = {
          'ADMIN': 'Líder general',
          'LIDER_SEMILLERO': 'Líder de semillero',
          'LIDER_INVESTIGACION': 'Líder de investigación',
          'APRENDIZ': 'Aprendiz'
        };

        rolLabel.textContent = roleMap[u.role];

        fTipoDoc.value = u.tipo_documento;
        fDoc.value = u.documento;
        fNombre.value = u.nombre;
        fApellidos.value = u.apellidos;
        fGenero.value = u.genero;
        fRh.value = u.tipo_rh;
        fCelular.value = u.celular;
        fEmail.value = u.email;

        if (u.role === 'LIDER_SEMILLERO') {
          bloqueLider.classList.remove('d-none');
          fLsCorreo.value = p.correo_institucional || '';
          fLsSemillero.value = p.semillero_nombre || '—';
        }

        if (u.role === 'APRENDIZ') {
          bloqueAprendiz.classList.remove('d-none');
          fApSemillero.value = p.semillero_nombre || '—';
          fApCorreoInst.value = p.correo_institucional || '';
          fApNivel.value = p.nivel_educativo || '';
          fApFicha.value = p.ficha || '';
          fApPrograma.value = p.programa || '';
          fApInstitucion.value = p.institucion || '';
          fApContNombre.value = p.contacto_nombre || '';
          fApContCel.value = p.contacto_celular || '';
        }

        bootstrap.Modal.getOrCreateInstance(modalEl).show();

      } catch (err) {
        swalError('No se pudieron cargar los datos');
      }
    });
  }

})();
