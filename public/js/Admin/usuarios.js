/* public/js/admin/usuarios.js */
(function () {
  'use strict';

  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));

  // -----------------------------------------
  // VALIDACIÓN BOOTSTRAP
  // -----------------------------------------
  document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });

  // -----------------------------------------
  // VARIABLES
  // -----------------------------------------
  const rol         = $('#rol');
  const correoBox   = $('#box-correo');
  const correoInput = $('#correo');
  const boxAdmin    = $('#box-admin');
  const boxLider    = $('#box-lider');
  const boxAprendiz = $('#box-aprendiz');

  const show = el => el?.classList.remove('d-none');
  const hide = el => el?.classList.add('d-none');
  const setDisabled = (c, dis) => c?.querySelectorAll('input,select,textarea').forEach(el => {
    dis ? el.setAttribute('disabled','disabled') : el.removeAttribute('disabled');
    if (dis) el.removeAttribute('required');
  });

  // -----------------------------------------
  // REQUERIDOS POR ROL
  // -----------------------------------------
  function clearRequiredAll(){
    $$('#box-admin [name], #box-lider [name], #box-aprendiz [name]')
      .forEach(i => i.removeAttribute('required'));
    correoInput?.removeAttribute('required');
  }

  function setRequiredForRole(role){
    clearRequiredAll();
    if(!role) return;

    correoInput?.setAttribute('required','required');

    if(role === 'ADMIN' || role === 'LIDER_INVESTIGACION'){
      ['nombre','apellido','password'].forEach(n =>
        boxAdmin?.querySelector(`[name="${n}"]`)?.setAttribute('required','required')
      );
    }

    if(role === 'LIDER_SEMILLERO'){
      ['nombre','apellido','ls_tipo_documento','ls_documento','password'].forEach(n =>
        boxLider?.querySelector(`[name="${n}"]`)?.setAttribute('required','required')
      );
    }

    if(role === 'APRENDIZ'){
      ['nombre','apellido','ap_ficha','ap_programa','ap_tipo_documento','ap_documento','ap_correo_institucional','password'].forEach(n =>
        boxAprendiz?.querySelector(`[name="${n}"]`)?.setAttribute('required','required')
      );
    }
  }

  // -----------------------------------------
  // MOSTRAR / OCULTAR BLOQUES
  // -----------------------------------------
  function toggle(role){
    [boxAdmin, boxLider, boxAprendiz].forEach(c => { hide(c); setDisabled(c,true); });
    hide(correoBox);

    if(!role) return;

    show(correoBox);
    correoInput.placeholder =
      (role === 'ADMIN' || role === 'LIDER_INVESTIGACION')
        ? 'lider@dominio.com'
        : 'nombre@misena.edu.co';

    if(role === 'ADMIN' || role === 'LIDER_INVESTIGACION'){
      show(boxAdmin);
      setDisabled(boxAdmin,false);
    }
    else if(role === 'LIDER_SEMILLERO'){
      show(boxLider);
      setDisabled(boxLider,false);
    }
    else if(role === 'APRENDIZ'){
      show(boxAprendiz);
      setDisabled(boxAprendiz,false);
    }

    setRequiredForRole(role);
  }

  toggle(rol?.value);
  rol?.addEventListener('change', e => toggle(e.target.value));


  // -----------------------------------------
  // MOSTRAR / OCULTAR CONTRASEÑA + GENERAR
  // -----------------------------------------
  function randPass(len=10){
    const c='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
    return Array.from(crypto.getRandomValues(new Uint32Array(len)))
      .map(n => c[n % c.length]).join('');
  }

  document.querySelectorAll('[data-toggle-pass]').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = document.querySelector(btn.dataset.togglePass);
      if(!i) return;
      i.type = i.type === 'password' ? 'text' : 'password';
      btn.classList.toggle('active');
    });
  });

  document.querySelectorAll('[data-generate-pass]').forEach(btn => {
    btn.addEventListener('click', () => {
      const i = document.querySelector(btn.dataset.generatePass);
      if(i){
        i.value = randPass();
        i.type = 'text';
        i.focus();
      }
    });
  });

// public/js/admin/usuarios.js

document.addEventListener("DOMContentLoaded", () => {

    // ============== Helpers ==============
    function attachPasswordTools(root) {
        if (!root) return;
        root.querySelectorAll("[data-toggle-pass]").forEach(btn => {
            btn.addEventListener("click", () => {
                const input = root.querySelector(btn.getAttribute("data-toggle-pass"));
                if (!input) return;
                input.type = input.type === "password" ? "text" : "password";
            });
        });

        function randomPassword(len = 10) {
            const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%";
            const arr = new Uint32Array(len);
            window.crypto.getRandomValues(arr);
            return Array.from(arr, n => chars[n % chars.length]).join("");
        }

        root.querySelectorAll("[data-generate-pass]").forEach(btn => {
            btn.addEventListener("click", () => {
                const input = root.querySelector(btn.getAttribute("data-generate-pass"));
                if (!input) return;
                input.value = randomPassword();
                input.type = "text";
                input.focus();
            });
        });
    }

    function attachValidation() {
        const forms = document.querySelectorAll(".needs-validation");
        Array.prototype.slice.call(forms).forEach(form => {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            }, false);
        });
    }

    // ============== Modal CREAR ==============
    const modalCrear = document.getElementById("modalCrearUsuario");
    if (modalCrear) {
        const roleSelect   = modalCrear.querySelector("#select-role");
        const boxAprendiz  = modalCrear.querySelector("#box-aprendiz");
        const boxLiderSemi = modalCrear.querySelector("#box-lider-semillero");
        const boxLiderInv  = modalCrear.querySelector("#box-lider-investigacion");
        const boxSena      = modalCrear.querySelector("#box-aprendiz-sena");
        const boxOtra      = modalCrear.querySelector("#box-aprendiz-otra");

        function actualizarBloquesRol() {
            const v = roleSelect.value;
            [boxAprendiz, boxLiderSemi, boxLiderInv].forEach(b => b && b.classList.add("d-none"));

            if (v === "APRENDIZ" && boxAprendiz) boxAprendiz.classList.remove("d-none");
            if (v === "LIDER_SEMILLERO" && boxLiderSemi) boxLiderSemi.classList.remove("d-none");
            if (v === "LIDER_INVESTIGACION" && boxLiderInv) boxLiderInv.classList.remove("d-none");
        }

        if (roleSelect) {
            roleSelect.addEventListener("change", actualizarBloquesRol);
        }

        // Radios SENA / otra institución
        modalCrear.querySelectorAll("input[name='radio_vinculado_sena']").forEach(radio => {
            radio.addEventListener("change", () => {
                const val = radio.value;
                if (val === "1") {
                    boxSena && boxSena.classList.remove("d-none");
                    boxOtra && boxOtra.classList.add("d-none");
                } else {
                    boxOtra && boxOtra.classList.remove("d-none");
                    boxSena && boxSena.classList.add("d-none");
                }
            });
        });

        // Reset al cerrar
        modalCrear.addEventListener("hidden.bs.modal", () => {
            const form = modalCrear.querySelector("form");
            if (form) {
                form.reset();
                form.classList.remove("was-validated");
            }
            actualizarBloquesRol();
            // por defecto SENA
            if (boxSena && boxOtra) {
                boxSena.classList.remove("d-none");
                boxOtra.classList.add("d-none");
            }
        });

        attachPasswordTools(modalCrear);
    }

    // ============== Modal EDITAR ==============
    const modalEditar = document.getElementById("modalEditarUsuario");
    if (modalEditar) {
        const formEditar = modalEditar.querySelector("#formEditarUsuario");

        modalEditar.addEventListener("show.bs.modal", event => {
            const button = event.relatedTarget;
            if (!button) return;

            const id          = button.getAttribute("data-id");
            const nombre      = button.getAttribute("data-nombre");
            const apellido    = button.getAttribute("data-apellido");
            const email       = button.getAttribute("data-email");
            const role        = button.getAttribute("data-role");
            const semilleroId = button.getAttribute("data-semillero-id");
            const updateUrl   = button.getAttribute("data-update-url");

            if (formEditar && updateUrl) {
                formEditar.action = updateUrl;
            }

            modalEditar.querySelector("#edit_nombre").value    = nombre || "";
            modalEditar.querySelector("#edit_apellido").value  = apellido || "";
            modalEditar.querySelector("#edit_email").value     = email || "";
            modalEditar.querySelector("#edit_role").value      = role || "";
            if (semilleroId !== null && semilleroId !== undefined) {
                const semSelect = modalEditar.querySelector("#edit_semillero");
                if (semSelect) semSelect.value = semilleroId;
            }

            const passInput = modalEditar.querySelector("#edit_password");
            if (passInput) passInput.value = "";
            formEditar && formEditar.classList.remove("was-validated");
        });

        modalEditar.addEventListener("hidden.bs.modal", () => {
            const passInput = modalEditar.querySelector("#edit_password");
            if (passInput) passInput.value = "";
        });

        attachPasswordTools(modalEditar);
    }

    // Validación general
    attachValidation();
});













})();
