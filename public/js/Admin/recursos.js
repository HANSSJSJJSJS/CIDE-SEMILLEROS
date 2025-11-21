/* public/js/admin/recursos.js */

(() => {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    const ENDPOINTS = {
        listar:  window.REC_LISTAR_URL,
        store:   window.REC_STORE_URL,
        destroy: (id) => window.REC_DELETE_URL.replace(':id', id)
    };

    const recContainer = document.getElementById('recContainer');
    const recSearch    = document.getElementById('recSearch');
    const recFilter    = document.getElementById('recFilter');
    const recForm      = document.getElementById('recForm');         // puede ser null
    const modalEl      = document.getElementById('recUploadModal');  // puede ser null

    function iconFromMime(mime) {
        if (!mime) return '📎';
        if (mime.includes('pdf')) return '📘';
        if (mime.includes('word') || mime.includes('officedocument.word')) return '📄';
        if (mime.includes('excel') || mime.includes('spreadsheet')) return '📊';
        if (mime.includes('image')) return '🖼️';
        return '📎';
    }

    function prettySize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        const kb = bytes / 1024;
        if (kb < 1024) return kb.toFixed(1) + ' KB';
        const mb = kb / 1024;
        return mb.toFixed(1) + ' MB';
    }

    function escapeHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    const cleanupBackdrop = () => {
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    };

    // =======================
    //      LISTAR RECURSOS
    // =======================
    async function loadResources() {
        const q = recSearch ? recSearch.value.trim() : '';
        const categoria = recFilter ? recFilter.value : '';

        const url = new URL(ENDPOINTS.listar, window.location.origin);
        if (q) url.searchParams.set('q', q);
        if (categoria) url.searchParams.set('categoria', categoria);

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        renderGrouped(json.data || []);
    }

    function renderGrouped(items) {
        const cats = { plantillas: [], manuales: [], otros: [] };
        items.forEach(it => {
            if (!cats[it.categoria]) cats[it.categoria] = [];
            cats[it.categoria].push(it);
        });

        recContainer.innerHTML = '';
        renderSection('Plantillas del sistema', cats.plantillas);
        renderSection('Manuales y guías', cats.manuales);
        renderSection('Otros documentos', cats.otros);
    }

    function renderSection(title, arr) {
        const section = document.createElement('section');
        section.className = 'rec-section';
        section.innerHTML = `<h4>${title}</h4>`;

        if (!arr || arr.length === 0) {
            section.innerHTML += `<div class="rec-empty">No hay recursos en esta sección.</div>`;
            recContainer.appendChild(section);
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'rec-grid';

        arr.forEach(r => {
            const card = document.createElement('div');
            card.className = 'rec-card';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge-cat">${r.categoria}</span>
                    <small class="text-muted">${prettySize(r.size)}</small>
                </div>
                <h5>${iconFromMime(r.mime)} ${escapeHtml(r.titulo)}</h5>
                <p>${escapeHtml(r.descripcion || '')}</p>
                <div class="rec-actions">
                    <a class="btn btn-sm btn-outline-primary" href="${r.url}" target="_blank" rel="noopener">Ver</a>
                    <a class="btn btn-sm btn-primary" href="${r.download}">Descargar</a>

                    ${
                        window.REC_CAN_DELETE
                            ? `<button class="btn btn-sm btn-outline-danger" data-id="${r.id}">Eliminar</button>`
                            : ``
                    }
                </div>
            `;
            grid.appendChild(card);

            if (window.REC_CAN_DELETE) {
                const btnDel = card.querySelector('button[data-id]');
                if (btnDel) {
                    btnDel.addEventListener('click', async (e) => {
                        const id = e.currentTarget.getAttribute('data-id');
                        if (!confirm('¿Eliminar este recurso?')) return;

                        await fetch(ENDPOINTS.destroy(id), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                        });

                        await loadResources();
                    });
                }
            }
        });

        section.appendChild(grid);
        recContainer.appendChild(section);
    }

    // =======================
    //    BUSCAR / FILTRAR
    // =======================
    let searchDebounce;
    if (recSearch) {
        recSearch.addEventListener('input', () => {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(loadResources, 300);
        });
    }

    if (recFilter) {
        recFilter.addEventListener('change', loadResources);
    }

    // =======================
    //   SUBIR ARCHIVOS (solo si hay formulario)
    // =======================
    if (recForm && modalEl) {
        const btnSubmit = recForm.querySelector('button[type="submit"]');
        const btnText   = btnSubmit ? btnSubmit.innerHTML : '';

        function setLoading(on) {
            if (!btnSubmit) return;
            btnSubmit.disabled = on;
            btnSubmit.innerHTML = on
                ? '<span class="spinner-border spinner-border-sm me-2"></span> Subiendo...'
                : btnText;
        }

        recForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!window.REC_CAN_CREATE) {
                alert("No tienes permiso para subir recursos.");
                return;
            }

            const fd = new FormData(recForm);
            setLoading(true);

            try {
                const res = await fetch(ENDPOINTS.store, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: fd
                });

                if (!res.ok) {
                    const txt = await res.text();
                    try {
                        const j = JSON.parse(txt);
                        alert(Object.values(j.errors || { error: [j.message || 'Error al subir'] }).flat().join('\n'));
                    } catch {
                        alert('Error al subir el recurso');
                    }
                    return;
                }

                bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                modalEl.addEventListener('hidden.bs.modal', () => {
                    recForm.reset();
                    setLoading(false);
                    cleanupBackdrop();
                    loadResources();
                }, { once: true });

            } catch (err) {
                alert('Ocurrió un error.');
            } finally {
                if (!document.body.classList.contains('modal-open')) {
                    setLoading(false);
                    cleanupBackdrop();
                }
            }
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            setLoading(false);
            cleanupBackdrop();
        });
    }

    // =======================
    //   INICIO
    // =======================
    if (recContainer) {
        loadResources();
    }
})();
