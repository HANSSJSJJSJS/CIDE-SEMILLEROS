// public/js/admin/usuarios.js
(function () {
  'use strict';

  // Helpers cortos
  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  // ============================================
  // VALIDACIÓN BOOTSTRAP
  // ============================================
  function attachValidation() {
    document.querySelectorAll('.needs-validation').forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }

  // ============================================
  // PASSWORD: mostrar / ocultar + generar
  // ============================================
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
        const selector = btn.getAttribute('data-toggle-pass');
        const input    = root.querySelector(selector);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.classList.toggle('active');
      });
    });

    root.querySelectorAll('[data-generate-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const selector = btn.getAttribute('data-generate-pass');
        const input    = root.querySelector(selector);
        if (!input) return;
        input.value = randomPassword();
        input.type  = 'text';
        input.focus();
      });
    });
  }

  // ============================================
  // Helpers de bloques (mostrar / ocultar + disable)
  // ============================================
  function showBlock(block) {
    if (!block) return;
    block.classList.remove('d-none');
    block.querySelectorAll('input, select, textarea').forEach(el => {
      el.disabled = false;
    });
  }

  function hideBlock(block) {
    if (!block) return;
    block.classList.add('d-none');
    block.querySelectorAll('input, select, textarea').forEach(el => {
      el.disabled = true;
      el.removeAttribute('required');
    });
  }

  // ============================================
  // MODAL CREAR USUARIO
  // ============================================
  function initModalCrear() {
    const modal = $('#modalCrearUsuario');
    if (!modal) return;

    const form         = modal.querySelector('#formCrearUsuario');
    const roleSelect   = modal.querySelector('#select-role');

    const boxLiderSemi = modal.querySelector('#box-lider-semillero');
    const boxLiderInv  = modal.querySelector('#box-lider-investigacion');
    const boxAprendiz  = modal.querySelector('#box-aprendiz');

    const boxSena      = modal.querySelector('#box-aprendiz-sena');
    const boxOtra      = modal.querySelector('#box-aprendiz-otra');

    function setRequiredForRole(role) {
      // Limpia requeridos de bloques específicos
      $$('#box-lider-semillero [name], #box-aprendiz [name]', modal)
        .forEach(i => i.removeAttribute('required'));

      if (!role) return;

      // Campos base
      ['nombre', 'apellido', 'email', 'password'].forEach(n => {
        const input = modal.querySelector(`[name="${n}"]`);
        if (input) input.setAttribute('required', 'required');
      });

      if (role === 'LIDER_SEMILLERO') {
        ['ls_tipo_documento', 'ls_documento'].forEach(n => {
          const input = modal.querySelector(`[name="${n}"]`);
          if (input) input.setAttribute('required', 'required');
        });
      }

      if (role === 'APRENDIZ') {
        ['ap_documento', 'ap_correo_institucional', 'semillero_id'].forEach(n => {
          const input = modal.querySelector(`[name="${n}"]`);
          if (input) input.setAttribute('required', 'required');
        });

        const vinc = modal.querySelector('input[name="vinculado_sena"]:checked')?.value;
        if (vinc === '1') {
          ['ap_ficha', 'ap_programa'].forEach(n => {
            const input = modal.querySelector(`[name="${n}"]`);
            if (input) input.setAttribute('required', 'required');
          });
        } else {
          const inst = modal.querySelector('[name="institucion"]');
          if (inst) inst.setAttribute('required', 'required');
        }
      }
    }

    function actualizarBloquesRol() {
      const v = roleSelect.value;

      hideBlock(boxLiderSemi);
      hideBlock(boxLiderInv);
      hideBlock(boxAprendiz);

      if (v === 'LIDER_SEMILLERO') {
        showBlock(boxLiderSemi);
      } else if (v === 'LIDER_INVESTIGACION') {
        showBlock(boxLiderInv);
      } else if (v === 'APRENDIZ') {
        showBlock(boxAprendiz);
      }

      setRequiredForRole(v);
    }

    function actualizarAprendizVinculo() {
      if (!boxSena || !boxOtra) return;

      const vinc = modal.querySelector('input[name="vinculado_sena"]:checked')?.value;

      if (vinc === '1') {
        showBlock(boxSena);
        hideBlock(boxOtra);
      } else {
        showBlock(boxOtra);
        hideBlock(boxSena);
      }

      setRequiredForRole(roleSelect.value);
    }

    // Eventos
    if (roleSelect) {
      roleSelect.addEventListener('change', actualizarBloquesRol);
    }

    modal.querySelectorAll('input[name="vinculado_sena"]').forEach(radio => {
      radio.addEventListener('change', actualizarAprendizVinculo);
    });

    // Reset al cerrar
    modal.addEventListener('hidden.bs.modal', () => {
      if (form) {
        form.reset();
        form.classList.remove('was-validated');
      }
      // Rol y bloques
      actualizarBloquesRol();
      // por defecto SENA = "sí"
      const radioSi = modal.querySelector('input[name="vinculado_sena"][value="1"]');
      if (radioSi) radioSi.checked = true;
      actualizarAprendizVinculo();
    });

    // Estado inicial
    actualizarBloquesRol();
    actualizarAprendizVinculo();

    // Passwords
    attachPasswordTools(modal);
  }

  // ============================================
  // MODAL EDITAR USUARIO
  // ============================================
  function initModalEditar() {
    const modal = $('#modalEditarUsuario');
    if (!modal) return;

    const formEditar = modal.querySelector('#formEditarUsuario');

    modal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      if (!button) return;

      const nombre      = button.getAttribute('data-nombre');
      const apellido    = button.getAttribute('data-apellido');
      const email       = button.getAttribute('data-email');
      const role        = button.getAttribute('data-role');
      const semilleroId = button.getAttribute('data-semillero-id');
      const updateUrl   = button.getAttribute('data-update-url');

      if (formEditar && updateUrl) formEditar.action = updateUrl;

      $('#edit_nombre',   modal).value = nombre   || '';
      $('#edit_apellido', modal).value = apellido || '';
      $('#edit_email',    modal).value = email    || '';
      $('#edit_role',     modal).value = role     || '';

      if (semilleroId !== null && semilleroId !== undefined) {
        const semSelect = $('#edit_semillero', modal);
        if (semSelect) semSelect.value = semilleroId;
      }

      const passInput = $('#edit_password', modal);
      if (passInput) passInput.value = '';

      formEditar && formEditar.classList.remove('was-validated');
    });

    modal.addEventListener('hidden.bs.modal', () => {
      const passInput = $('#edit_password', modal);
      if (passInput) passInput.value = '';
    });

    attachPasswordTools(modal);
  }

  // ============================================
  // INIT GLOBAL
  // ============================================
  document.addEventListener('DOMContentLoaded', () => {
    attachValidation();
    initModalCrear();
    initModalEditar();
  });

})();
