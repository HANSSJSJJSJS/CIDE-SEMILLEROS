document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('modalEditarSemillero');
    const form  = document.getElementById('formEditarSemillero');

    const editNombre = document.getElementById('editNombre');
    const editLinea  = document.getElementById('editLinea');
    const liderRO    = document.getElementById('liderNombreRO');
    const correoRO   = document.getElementById('liderCorreoRO');
    const idLiderInp = document.getElementById('editIdLider');

    const buscarInp  = document.getElementById('buscarLider');
    const listaRes   = document.getElementById('resultadosLider');

    /* Abrir modal y cargar datos */
    modal.addEventListener('show.bs.modal', async (e) => {

        const mNuevo = document.getElementById('modalNuevoSemillero');
        const instNuevo = mNuevo ? bootstrap.Modal.getInstance(mNuevo) : null;
        instNuevo?.hide();

        const id = e.relatedTarget?.dataset.id;
        if (!id) return;

        buscarInp.value = '';
        listaRes.innerHTML = '';

        const res = await fetch(`/admin/semilleros/${id}/edit`);
        const data = await res.json();

        form.action = `/admin/semilleros/${id}`;
        editNombre.value = data.nombre || '';
        editLinea.value  = data.linea_investigacion || '';
        liderRO.value    = data.lider_nombre || '—';
        correoRO.value   = data.lider_correo || '—';
        idLiderInp.value = data.id_lider_semi || '';
    });

    /* Buscador interno de líderes */
    let t;
    buscarInp.addEventListener('input', () => {
        clearTimeout(t);

        t = setTimeout(async () => {

            const q = buscarInp.value.trim();
            if (q.length < 2) { listaRes.innerHTML = ''; return; }

            const includeCurrent =
                idLiderInp.value ? `&include_current=${encodeURIComponent(idLiderInp.value)}` : '';

            const url = `/admin/semilleros/lideres-disponibles?q=${encodeURIComponent(q)}${includeCurrent}`;

            const res = await fetch(url);
            const items = await res.json();

            listaRes.innerHTML = '';
            if (!Array.isArray(items)) return;

            items.forEach(r => {
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';

                a.innerHTML = `
                    <div class="fw-semibold">${r.nombre}</div>
                    <small class="text-muted">${r.correo || '—'}</small>
                `;

                a.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    idLiderInp.value = r.id_lider_semi;
                    liderRO.value    = r.nombre;
                    correoRO.value   = r.correo || '—';
                    listaRes.innerHTML = '';
                    buscarInp.value = '';
                });

                listaRes.appendChild(a);
            });

        }, 250);
    });

    /* Evitar duplicación de backdrop */
    const cleanupBackdrop = () => {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    };

    modal.addEventListener('hidden.bs.modal', cleanupBackdrop);

    const modalNuevo = document.getElementById('modalNuevoSemillero');
    modalNuevo?.addEventListener('hidden.bs.modal', cleanupBackdrop);
});
