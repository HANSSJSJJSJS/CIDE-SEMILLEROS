// public/js/admin/semilleros.js
(function () {
  'use strict';

  const $  = (s, c = document) => (c || document).querySelector(s);
  const $$ = (s, c = document) => Array.from((c || document).querySelectorAll(s));

  // ------------------------------------------------------------------
  // Picker genérico de líder (crear / editar)
  // ------------------------------------------------------------------
  function initLiderPicker(options) {
    const {
      modal,
      selectId,
      btnLimpiarId,
      buscarInputId,
      btnBuscarId,
      resultadosId,
      hiddenId,
      nombreROId,
      correoROId
    } = options;

    const selLider   = selectId      ? $(selectId, modal)      : null;
    const btnLimpiar = btnLimpiarId  ? $(btnLimpiarId, modal)  : null;
    const txtBuscar  = buscarInputId ? $(buscarInputId, modal) : null;
    const btnBuscar  = btnBuscarId   ? $(btnBuscarId, modal)   : null;
    const divRes     = resultadosId  ? $(resultadosId, modal)  : null;
    const hidId      = hiddenId      ? $(hiddenId, modal)      : null;
    const roNombre   = nombreROId    ? $(nombreROId, modal)    : null;
    const roCorreo   = correoROId    ? $(correoROId, modal)    : null;

    const urlLideres = btnBuscar ? btnBuscar.dataset.urlLideres : null;

    function pintarLider(id, nombre, correo) {
      if (hidId)    hidId.value    = id || '';
      if (roNombre) roNombre.value = nombre || '—';
      if (roCorreo) roCorreo.value = correo || '—';
      if (selLider) selLider.value = id || '';
    }

    // cambio en select
    if (selLider) {
      selLider.addEventListener('change', () => {
        const opt = selLider.selectedOptions[0];
        if (!opt || !opt.value) {
          pintarLider('', '—', '—');
          return;
        }
        pintarLider(
          opt.value,
          opt.dataset.nombre || opt.textContent.trim(),
          opt.dataset.correo || ''
        );
      });
    }

    // botón limpiar
    if (btnLimpiar) {
      btnLimpiar.addEventListener('click', () => {
        if (selLider) selLider.value = '';
        if (txtBuscar) txtBuscar.value = '';
        if (divRes) divRes.innerHTML = '';
        pintarLider('', '—', '—');
      });
    }

    // búsqueda de líderes (solo para el modal NUEVO; en editar ya no se usa)
    async function buscarLideres() {
      if (!btnBuscar || !urlLideres || !divRes || !txtBuscar) return;

      const q = txtBuscar.value.trim();
      if (!q) {
        divRes.innerHTML =
          '<div class="list-group-item small text-muted">Escribe algo para buscar.</div>';
        return;
      }

      divRes.innerHTML =
        '<div class="list-group-item small">Buscando líderes…</div>';

      try {
        const resp = await fetch(`${urlLideres}?q=${encodeURIComponent(q)}`, {
          headers: { 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        const data  = await resp.json();
        const lista = data.items || [];

        divRes.innerHTML = '';

        if (!lista.length) {
          divRes.innerHTML =
            '<div class="list-group-item small text-muted">No se encontraron líderes disponibles.</div>';
          return;
        }

        lista.forEach(l => {
          const btn = document.createElement('button');
          btn.type      = 'button';
          btn.className = 'list-group-item list-group-item-action';
          btn.textContent = `${l.nombre} (${l.correo})`;
          btn.dataset.id     = l.id_lider_semi;
          btn.dataset.nombre = l.nombre;
          btn.dataset.correo = l.correo;

          btn.addEventListener('click', () => {
            pintarLider(
              btn.dataset.id,
              btn.dataset.nombre,
              btn.dataset.correo
            );
          });

          divRes.appendChild(btn);
        });
      } catch (err) {
        console.error(err);
        divRes.innerHTML =
          '<div class="list-group-item small text-danger">Error buscando líderes.</div>';
      }
    }

    if (btnBuscar) {
      btnBuscar.addEventListener('click', buscarLideres);
    }

    if (txtBuscar) {
      txtBuscar.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
          e.preventDefault();
          buscarLideres();
        }
      });
    }

    // estado inicial
    pintarLider('', '—', '—');
  }

  // ------------------------------------------------------------------
  // Modal NUEVO semillero
  // ------------------------------------------------------------------
  function initModalNuevoSemillero() {
    const modal = $('#modalNuevoSemillero');
    if (!modal) return;

    initLiderPicker({
      modal,
      selectId      : '#selectLiderDisponible',
      btnLimpiarId  : '#btnLimpiarLiderNuevo',
      buscarInputId : '#buscarLiderNuevo',
      btnBuscarId   : '#btnBuscarLiderNuevo',
      resultadosId  : '#resultadosLiderNuevo',
      hiddenId      : '#idLiderNuevo',
      nombreROId    : '#nuevoLiderNombreRO',
      correoROId    : '#nuevoLiderCorreoRO'
    });
  }

  // ------------------------------------------------------------------
  // Modal EDITAR semillero (solo lista desplegable)
  // ------------------------------------------------------------------
  function initModalEditarSemillero() {
    const modalEl = $('#modalEditarSemillero');
    if (!modalEl) return;

    const form          = $('#formEditarSemillero', modalEl);
    const inputNombre   = $('#editNombre', modalEl);
    const inputLinea    = $('#editLinea', modalEl);
    const liderNombreRO = $('#liderNombreRO', modalEl);
    const liderCorreoRO = $('#liderCorreoRO', modalEl);
    const hiddenIdLider = $('#editIdLider', modalEl);

    // Picker de NUEVO líder en el modal editar (solo select + quitar)
    initLiderPicker({
      modal        : modalEl,
      selectId     : '#selectLiderEditar',
      btnLimpiarId : '#btnLimpiarLiderEditar',
      buscarInputId: null,                 // YA NO hay barra de búsqueda
      btnBuscarId  : null,
      resultadosId : null,
      hiddenId     : '#editIdLider',
      nombreROId   : '#nuevoLiderNombreRO',
      correoROId   : '#nuevoLiderCorreoRO'
    });

    // Abrir modal al hacer clic en "Editar"
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-editar-semillero');
      if (!btn) return;

      const editUrl   = btn.dataset.editUrl;
      const updateUrl = btn.dataset.updateUrl;

      if (!editUrl || !updateUrl) {
        console.error('Faltan data-edit-url o data-update-url en el botón Editar.');
        return;
      }

      // reset básico
      form.reset();
      form.classList.remove('was-validated');
      if (hiddenIdLider) hiddenIdLider.value = '';
      if (liderNombreRO) liderNombreRO.value = '—';
      if (liderCorreoRO) liderCorreoRO.value = '—';

      // action correcto (PUT /admin/semilleros/{id})
      form.action = updateUrl;

      try {
        const resp = await fetch(editUrl, {
          headers: { 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        const s = await resp.json();

        if (inputNombre)   inputNombre.value   = s.nombre || '';
        if (inputLinea)    inputLinea.value    = s.linea_investigacion || '';
        if (hiddenIdLider) hiddenIdLider.value = s.id_lider_semi || '';

        if (liderNombreRO) liderNombreRO.value = s.lider_nombre || '—';
        if (liderCorreoRO) liderCorreoRO.value = s.lider_correo  || '—';

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

      } catch (err) {
        console.error(err);
        if (typeof Swal !== 'undefined') {
          Swal.fire('Error', 'No se pudieron cargar los datos del semillero.', 'error');
        } else {
          alert('No se pudieron cargar los datos del semillero.');
        }
      }
    });

    // ✅ Confirmación antes de GUARDAR cambios
    if (form && typeof Swal !== 'undefined') {
      form.addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
          icon: 'question',
          title: 'Guardar cambios',
          text: '¿Seguro que deseas guardar los cambios de este semillero?',
          showCancelButton: true,
          confirmButtonText: 'Sí, guardar',
          cancelButtonText: 'Cancelar'
        }).then(result => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    }
  }

  // ------------------------------------------------------------------
  // Confirmación ELIMINAR semillero
  // ------------------------------------------------------------------
  function initEliminarSemillero() {
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-eliminar-semillero');
      if (!btn) return;

      e.preventDefault();

      const url    = btn.dataset.url;
      const nombre = btn.dataset.nombre || 'este semillero';

      if (!url) {
        console.error('Falta data-url en btn-eliminar-semillero');
        return;
      }

      if (typeof Swal === 'undefined') {
        if (confirm(`¿Seguro que deseas eliminar "${nombre}"? Esta acción no se puede deshacer.`)) {
          // Fallback sin SweetAlert
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = url;

          const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
          const inpToken  = document.createElement('input');
          inpToken.type   = 'hidden';
          inpToken.name   = '_token';
          inpToken.value  = token;

          const inpMethod = document.createElement('input');
          inpMethod.type  = 'hidden';
          inpMethod.name  = '_method';
          inpMethod.value = 'DELETE';

          form.appendChild(inpToken);
          form.appendChild(inpMethod);
          document.body.appendChild(form);
          form.submit();
        }
        return;
      }

      Swal.fire({
        icon: 'warning',
        title: '¿Eliminar semillero?',
        html: `¿Seguro que deseas eliminar <strong>${nombre}</strong>?<br><small>Esta acción no se puede deshacer.</small>`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
      }).then(result => {
        if (!result.isConfirmed) return;

        // construimos y enviamos form DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const inpToken  = document.createElement('input');
        inpToken.type   = 'hidden';
        inpToken.name   = '_token';
        inpToken.value  = token;

        const inpMethod = document.createElement('input');
        inpMethod.type  = 'hidden';
        inpMethod.name  = '_method';
        inpMethod.value = 'DELETE';

        form.appendChild(inpToken);
        form.appendChild(inpMethod);
        document.body.appendChild(form);
        form.submit();
      });
    });
  }

  // ------------------------------------------------------------------
  // INIT GLOBAL
  // ------------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', () => {
    initModalNuevoSemillero();
    initModalEditarSemillero();
    initEliminarSemillero();
  });

})();
