
import './form_usuario.js';
// public/js/form_usuario.js
document.addEventListener('DOMContentLoaded', () => {
  // Helpers
  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  // Elementos del formulario
  const form         = $('#form-usuario');
  const rol          = $('#rol');
  const correoBox    = $('#box-correo');
  const correoInput  = $('#correo');
  const boxAdmin     = $('#box-admin');
  const boxLider     = $('#box-lider');
  const boxAprendiz  = $('#box-aprendiz');

  // Contenedor de acciones (botones)
  const actions = form ? form.querySelector('.d-flex.gap-2') : null;

  // Guard-rail: si no hay form, salimos
  if (!form || !rol) return;

  // === Mostrar / ocultar ===
  const show    = (el) => el && el.classList.remove('d-none');
  const hide    = (el) => el && el.classList.add('d-none');
  const hideAll = () => [correoBox, boxAdmin, boxLider, boxAprendiz].forEach(hide);

  // Ocultar acciones hasta tener rol
  const hideActions = () => actions && actions.classList.add('actions-hidden');
  const showActions = () => actions && actions.classList.remove('actions-hidden');

  function toggleSections(value) {
    if (!value) {
      hideAll();
      hideActions();
      return;
    }

    // Mostrar correo siempre que haya rol
    show(correoBox);
    if (correoInput) {
      correoInput.placeholder = (value === 'ADMINISTRADOR')
        ? 'Ej: admin@empresa.com'
        : 'Ej: nombre@misena.edu.co';
    }

    // Mostrar SOLO el bloque correspondiente
    hide(boxAdmin); hide(boxLider); hide(boxAprendiz);
    if (value === 'ADMINISTRADOR')      show(boxAdmin);
    else if (value === 'LIDER_SEMILLERO') show(boxLider);
    else if (value === 'LIDER_GENERAL')   show(boxLider);   // (usa el mismo bloque, si luego tienes uno propio cámbialo)
    else if (value === 'APRENDIZ')        show(boxAprendiz);

    // Mostrar acciones cuando hay rol
    showActions();
  }

  // Estado inicial (por si el navegador recuerda el valor)
  hideAll();
  hideActions();
  toggleSections(rol.value);
  rol.addEventListener('change', (e) => toggleSections(e.target.value));

  // === Envío por AJAX con SweetAlert ===
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn && (submitBtn.disabled = true);

    const formData = new FormData(form);

    try {
      const resp = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
          'Accept': 'application/json'
        },
        body: formData
      });

      const isJson = resp.headers.get('content-type')?.includes('application/json');
      const data   = isJson ? await resp.json() : null;

      if (resp.ok && data?.ok) {
        await Swal.fire({
          icon: 'success',
          title: '¡Registro exitoso!',
          html: `
            <p>${data.message || 'Se creó el usuario correctamente.'}</p>
            ${data.usuario?.correo ? `<p><b>Correo:</b> ${data.usuario.correo}</p>` : ''}
            ${data.usuario?.rol ? `<p><b>Rol:</b> ${data.usuario.rol}</p>` : ''}
            ${data.password ? `<p style="margin-top:8px"><b>Contraseña temporal:</b> ${data.password}</p>` : ''}
          `,
          confirmButtonText: 'Aceptar'
        });

        // Reset del formulario y UI
        form.reset();
        hideAll();
        hideActions();
        // Re-disparar el change para que la UI quede limpia
        rol.dispatchEvent(new Event('change'));
        return;
      }

      // Errores de validación (422)
      if (resp.status === 422 && data?.errors) {
        const list = Object.values(data.errors).flat().join('<br>');
        Swal.fire({
          icon: 'warning',
          title: 'Revisa los datos',
          html: list || 'Hay campos inválidos.',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      // Otros errores del servidor
      Swal.fire({
        icon: 'error',
        title: 'No se pudo guardar',
        text: data?.message || 'Ocurrió un error inesperado.',
        confirmButtonText: 'Cerrar'
      });

    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error de red',
        text: 'No pudimos conectar con el servidor. Intenta de nuevo.',
      });
    } finally {
      submitBtn && (submitBtn.disabled = false);
    }
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[action*="admin/usuarios"]');
  if (!form) return;
  // ... tu código ...
});
