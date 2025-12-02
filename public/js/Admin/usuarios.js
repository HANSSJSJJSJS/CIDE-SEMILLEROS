(function () {
  'use strict';

  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));

  /* ============================================================
      VALIDACIÓN BOOTSTRAP
  ============================================================ */
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

  /* ============================================================
      MODAL VER USUARIO
  ============================================================ */
  function initModalVerUsuario() {
    const modalEl = document.getElementById('modalVerUsuario');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);

    const setField = (name, value) => {
      const el = modalEl.querySelector(`[data-field="${name}"]`);
      if (el) el.textContent = value ?? '—';
    };

    document.addEventListener('click', e => {
      const btn = e.target.closest('.btn-ver-usuario');
      if (!btn) return;

      const userId = btn.dataset.userId;
      if (!userId) return;

      modalEl.querySelectorAll('[data-field]').forEach(el => el.textContent = '—');
      modalEl.querySelectorAll(
        '#ver-bloque-admin, #ver-bloque-lider-semi, #ver-bloque-lider-inv, #ver-bloque-aprendiz'
      ).forEach(b => b.classList.add('d-none'));

      fetch(`/admin/usuarios/${userId}/detalle-ajax`)
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
          const u = data.usuario || {};
          const p = data.perfil || {};

          const roles = {
            'ADMIN'              : 'Líder general',
            'LIDER_SEMILLERO'    : 'Líder de semillero',
            'LIDER_INVESTIGACION': 'Líder de investigación',
            'APRENDIZ'           : 'Aprendiz'
          };

          $('#ver-role-label', modalEl).textContent = roles[u.role] ?? u.role ?? '—';

          setField('nombre_completo', `${u.name ?? ''} ${u.apellidos ?? ''}`.trim());
          setField('email', u.email);
          setField('tipo_documento', u.tipo_documento);
          setField('documento', u.documento);
          setField('celular', u.celular);
          setField('genero', u.genero);
          setField('tipo_rh', u.tipo_rh);
          setField('estado', u.estado || (u.is_active ? 'Activo' : 'Inactivo'));

          switch (u.role) {
            case 'ADMIN':
              $('#ver-bloque-admin', modalEl).classList.remove('d-none');
              break;

            case 'LIDER_SEMILLERO':
              $('#ver-bloque-lider-semi', modalEl).classList.remove('d-none');
              setField('ls_correo_institucional', p.correo_institucional);
              setField('ls_semillero_nombre', p.semillero_nombre);
              break;

            case 'LIDER_INVESTIGACION':
              $('#ver-bloque-lider-inv', modalEl).classList.remove('d-none');
              break;

            case 'APRENDIZ':
              $('#ver-bloque-aprendiz', modalEl).classList.remove('d-none');
              setField('ap_semillero_nombre', p.semillero_nombre);
              setField('nivel_educativo', p.nivel_educativo);
              setField('vinculado_sena', p.vinculado_sena === 1 ? 'Sí' : 'No');
              setField('ficha', p.ficha);
              setField('programa', p.programa);
              setField('institucion', p.institucion);
              setField('correo_institucional', p.correo_institucional);
              setField('contacto_nombre', p.contacto_nombre);
              setField('contacto_celular', p.contacto_celular);
              break;
          }

          modal.show();
        })
        .catch(err => swalError("No se pudieron cargar los datos del usuario."));
    });
  }

  /* ============================================================
      MODAL EDITAR USUARIO
  ============================================================ */
  function initModalEditarUsuario() {
    const modal = document.getElementById('modalEditarUsuario');
    if (!modal) return;

    const form = modal.querySelector('form');

    const inputs = {
      nombre   : modal.querySelector('input[name="name"]'),
      apellidos: modal.querySelector('input[name="apellidos"]'),
      email    : modal.querySelector('input[name="email"]'),
      role     : modal.querySelector('select[name="role"]'),
      tipoDoc  : modal.querySelector('select[name="tipo_documento"]'),
      doc      : modal.querySelector('input[name="documento"]'),
      cel      : modal.querySelector('input[name="celular"]'),
      genero   : modal.querySelector('select[name="genero"]'),
      rh       : modal.querySelector('select[name="tipo_rh"]'),
      semi     : modal.querySelector('select[name="semillero_id"]'),
    };

    document.addEventListener('click', e => {

      const btn = e.target.closest('.btn-editar-usuario');
      if (!btn) return;  // <-- ya no colisiona con editar semillero

      const id = btn.dataset.userId;
      if (!id) return;

      form.action = `/admin/usuarios/${id}`;

      Object.values(inputs).forEach(i => i && (i.value = ''));

      fetch(`/admin/usuarios/${id}/edit-ajax`)
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
          const u = data.usuario || data;

          inputs.nombre.value    = u.name ?? '';
          inputs.apellidos.value = u.apellidos ?? '';
          inputs.email.value     = u.email ?? '';
          inputs.role.value      = u.role ?? '';
          inputs.tipoDoc.value   = u.tipo_documento ?? '';
          inputs.doc.value       = u.documento ?? '';
          inputs.cel.value       = u.celular ?? '';
          inputs.genero.value    = u.genero ?? '';
          inputs.rh.value        = u.tipo_rh ?? '';
          inputs.semi.value      = u.semillero_id ?? '';

          bootstrap.Modal.getOrCreateInstance(modal).show();
        })
        .catch(() => swalError("No se pudieron cargar los datos del usuario."));
    });
  }

  /* ============================================================
      CONFIRMAR ELIMINAR
  ============================================================ */
  function initEliminarUsuario() {
    document.addEventListener('click', e => {
      const btn = e.target.closest('.btn-eliminar-usuario');
      if (!btn) return;

      const form = btn.closest('form');
      const nombre = btn.dataset.nombre ?? 'este usuario';

      Swal.fire({
        icon: "warning",
        title: "¿Eliminar usuario?",
        html: `¿Seguro que deseas eliminar <strong>${nombre}</strong>?`,
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then(r => r.isConfirmed && form.submit());
    });
  }

  /* ============================================================
      INIT
  ============================================================ */
  document.addEventListener('DOMContentLoaded', () => {
    attachValidation();
    initModalVerUsuario();
    initModalEditarUsuario();
    initEliminarUsuario();
  });

})();
