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

})();
