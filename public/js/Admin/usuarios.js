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
  // CONFIRMACIÓN ANTES DE ENVIAR (Eliminar, permisos, etc.)
  // Usa formularios con class="needs-confirmation"
  // ============================================
  function attachConfirmations() {
    document.querySelectorAll('form.needs-confirmation').forEach(form => {

      form.addEventListener('submit', function (e) {
        e.preventDefault(); // detener envío inmediato

        const msg         = form.dataset.message     || '¿Deseas realizar esta acción?';
        const confirmText = form.dataset.confirmText || 'Sí, continuar';
        const cancelText  = form.dataset.cancelText  || 'Cancelar';

        Swal.fire({
          title: 'Confirmación requerida',
          text: msg,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: confirmText,
          cancelButtonText: cancelText,
          reverseButtons: true,
          customClass: {
            popup: 'swal-usuarios',      // <-- en minúsculas, igual que en tu CSS
            confirmButton: 'swal-confirmar',
            cancelButton: 'swal-cancelar'
          },
          buttonsStyling: false
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit(); // ahora sí enviamos
          }
        });

      });

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

    // Mostrar / ocultar contraseña
    root.querySelectorAll('[data-toggle-pass]').forEach(btn => {
      btn.addEventListener('click', () => {
        const selector = btn.getAttribute('data-toggle-pass');
        const input    = root.querySelector(selector);
        if (!input) return;

        input.type = input.type === 'password' ? 'text' : 'password';
        btn.classList.toggle('active');
      });
    });

    // Generar contraseña
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
  // Helpers mostrar / ocultar bloques
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

    const form       = modal.querySelector('#formCrearUsuario');
    const roleSelect = modal.querySelector('#select-role');

    // Bloques por rol
    const boxLiderSemi = modal.querySelector('#box-lider-semillero');
    const boxLiderInv  = modal.querySelector('#box-lider-investigacion');
    const boxAprendiz  = modal.querySelector('#box-aprendiz');

    // Sub-bloques aprendiz
    const boxSena = modal.querySelector('#box-aprendiz-sena');
    const boxOtra = modal.querySelector('#box-aprendiz-otra');

    // -----------------------------------------
    // Manejo de requeridos según rol
    // -----------------------------------------
    function setRequiredForRole(role) {
      // Limpiar requeridos en secciones especiales
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
        ['tipo_documento', 'documento', 'semillero_id'].forEach(n => {
          const input = modal.querySelector(`[name="${n}"]`);
          if (input) input.setAttribute('required', 'required');
        });

        const vinc = modal.querySelector('input[name="vinculado_sena"]:checked')?.value;
        if (vinc === '1') {
          ['ficha', 'programa'].forEach(n => {
            const input = modal.querySelector(`[name="${n}"]`);
            if (input) input.setAttribute('required', 'required');
          });
        } else {
          const inst = modal.querySelector('[name="institucion"]');
          if (inst) inst.setAttribute('required', 'required');
        }
      }
    }

    // -----------------------------------------
    // Mostrar / ocultar bloques por rol
    // -----------------------------------------
    function actualizarBloquesRol() {
      const v = roleSelect.value;

      hideBlock(boxLiderSemi);
      hideBlock(boxLiderInv);
      hideBlock(boxAprendiz);

      if (v === 'LIDER_SEMILLERO')      showBlock(boxLiderSemi);
      else if (v === 'LIDER_INVESTIGACION') showBlock(boxLiderInv);
      else if (v === 'APRENDIZ')        showBlock(boxAprendiz);

      setRequiredForRole(v);
    }

    // -----------------------------------------
    // Mostrar / ocultar según vinculación SENA
    // -----------------------------------------
    function actualizarAprendizVinculo() {
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
    if (roleSelect) roleSelect.addEventListener('change', actualizarBloquesRol);

    modal.querySelectorAll('input[name="vinculado_sena"]').forEach(r => {
      r.addEventListener('change', actualizarAprendizVinculo);
    });

    // Reset al cerrar modal
    modal.addEventListener('hidden.bs.modal', () => {
      if (form) {
        form.reset();
        form.classList.remove('was-validated');
      }

      actualizarBloquesRol();

      const radioSi = modal.querySelector('input[name="vinculado_sena"][value="1"]');
      if (radioSi) radioSi.checked = true;

      actualizarAprendizVinculo();
    });

    // Estado inicial
    actualizarBloquesRol();
    actualizarAprendizVinculo();

    // Herramientas de password
    attachPasswordTools(modal);
  }

  // ============================================
  // INIT
  // ============================================
  document.addEventListener('DOMContentLoaded', () => {
    attachValidation();
    attachConfirmations();
    initModalCrear();
    // initModalEditar();  // si luego quieres añadir el modal de editar aquí
  });

})();

// ============================================================
// SWEETALERT2 – NOTIFICACIONES GLOBALES (flash success / error)
// ============================================================
window.swalSuccess = function (msg) {
    Swal.fire({
        icon: 'success',
        title: msg,
        timer: 2500,
        position: 'center',
        showConfirmButton: false,
        customClass: {
            popup: 'swal-Usuarios'
        }
    });
};

window.swalError = function (msg) {
    Swal.fire({
        icon: 'error',
        title: msg,
        timer: 2000,
        position: 'center',
        showConfirmButton: false,
        customClass: {
            popup: 'swal-Usuarios'
        }
    });
};
