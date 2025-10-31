@extends('layouts.lider_semi')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/mis-proyectos.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="container mt-4">
    <div class="page-header">
        <h4 class="fw-bold mb-4">Mis Proyectos</h4>
    </div>
    <p class="text-muted">Gestiona y supervisa todos tus semilleros activos</p>

    <div class="row">
        @foreach($semilleros as $semillero)
        <div class="col-md-4 mb-4">
            <div class="card card-project">
                <div class="card-header text-white fw-bold">
                    {{ $semillero->nombre }}
                </div>
                <div class="card-body">
                    <span class="badge badge-status">{{ $semillero->estado }}</span>
                    <small id="apr-count-{{ $loop->index }}" class="text-muted float-end">{{ (int)($semillero->aprendices ?? 0) }} aprendices</small>
                    <p class="mt-3 text-secondary">{{ $semillero->descripcion }}</p>

                    <div class="mt-3">
                        <small>Progreso</small>
                        <div class="progress">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $semillero->progreso }}%;"
                                aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted float-end mt-1">{{ $semillero->progreso }}%</small>
                    </div>

                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-details w-100" data-bs-toggle="modal" data-bs-target="#detalleSemillero{{ $loop->index }}"><i class="bi bi-eye me-1"></i>Ver Detalles</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detalleSemillero{{ $loop->index }}" tabindex="-1" aria-labelledby="detalleSemilleroLabel{{ $loop->index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header brand-header">
                        <h5 class="modal-title fw-bold" id="detalleSemilleroLabel{{ $loop->index }}">{{ $semillero->nombre }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Información General</h6>
                            <div class="d-flex align-items-center gap-3">
                                <div><span class="fw-semibold">Nombre:</span> {{ $semillero->nombre }}</div>
                                <div><span class="fw-semibold">Estado:</span> <span class="badge bg-primary-subtle text-primary fw-semibold border">{{ $semillero->estado }}</span></div>
                            </div>
                        </div>

                        <hr class="my-3"/>

                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Descripción</h6>
                            <p class="mb-0 text-secondary">{{ $semillero->descripcion ?: 'Sin descripción' }}</p>
                        </div>

                        <hr class="my-3"/>

                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Progreso del Proyecto</h6>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-success" style="width: {{ $semillero->progreso }}%;">{{ $semillero->progreso }}%</div>
                            </div>
                        </div>

                        <hr class="my-3"/>

                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Información Adicional</h6>
                            <div class="row g-2">
                                <div class="col-md-4"><span class="fw-semibold">Líder:</span> {{ Auth::user()->name }}</div>
                                <div class="col-md-4"><span class="fw-semibold">Fecha de Inicio:</span> {{ isset($semillero->fecha_inicio) && $semillero->fecha_inicio ? \Carbon\Carbon::parse($semillero->fecha_inicio)->translatedFormat('d \de F, Y') : 'N/D' }}</div>
                                <div class="col-md-4"><span class="fw-semibold">Aprendices Asignados:</span> {{ (int)($semillero->aprendices ?? 0) }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="fw-bold mb-2">Aprendices Asignados</h6>
                            <div class="row g-3" id="aprendicesList{{ $loop->index }}">
                                @php
                                    $items = isset($semillero->aprendices_items) && is_iterable($semillero->aprendices_items) ? $semillero->aprendices_items : [];
                                @endphp
                                @forelse($items as $ap)
                                    <div class="col-md-6" data-isan servid="{{ $ap['id_aprendiz'] ?? '' }}">
                                        <div class="border rounded p-3 d-flex align-items-center gap-3">
                                            <div class="avatar-initials">
                                                {{ strtoupper(substr($ap['nombre'] ?? 'A',0,1)) }}{{ strtoupper(substr(explode(' ', $ap['nombre'] ?? 'M')[1] ?? 'M',0,1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $ap['nombre'] ?? 'Aprendiz' }}</div>
                                                <small class="text-muted">{{ $ap['programa'] ?? 'Sin programa' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12"><small class="text-muted">Sin aprendices asignados.</small></div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        @php
                            // Priorizar id_proyecto si existe (vista de proyectos). Fallback a ->id si viene así.
                            $idProj = $semillero->id_proyecto ?? ($semillero->id ?? null);
                            // Si no hay proyecto, usar semillero (fallback a ->id si es listado de semilleros)
                            $idSem = !$idProj ? ($semillero->id_semillero ?? ($semillero->id ?? null)) : null;
                        @endphp
                        @if(!empty($idSem) || !empty($idProj))
                            <button type="button" id="open-edit-{{ $loop->index }}" class="btn btn-brand" data-refproj="{{ (int)($idProj ?? 0) }}" data-refsem="{{ (int)($idSem ?? 0) }}"><i class="bi bi-people me-1"></i>Editar Aprendices</button>
                        @else
                            <button type="button" class="btn btn-brand" disabled><i class="bi bi-people me-1"></i>Editar Aprendices</button>
                        @endif
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal flotante para editar aprendices (por semillero o proyecto) --}}
        <div class="modal fade" id="editApr{{ $loop->index }}" tabindex="-1" aria-labelledby="editAprLabel{{ $loop->index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header brand-header">
                        <h5 class="modal-title fw-bold" id="editAprLabel{{ $loop->index }}">{{ $semillero->nombre }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <small class="text-muted">Agregue o Elimine Aprendices del Proyecto</small>
                        <div class="row mt-2 g-2"></div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Buscar Aprendices Existentes</label>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label">Tipo de Documento</label>
                                    <select id="tipo-doc-{{ $loop->index }}" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="CC">CC</option>
                                        <option value="TI">TI</option>
                                        <option value="CE">CE</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label">Número de Documento</label>
                                    <input id="buscador-{{ $loop->index }}" type="text" class="form-control" placeholder="Ej: 1023456789">
                                </div>
                            </div>
                            <div class="border rounded mt-2" style="max-height:260px; overflow:auto;">
                                <div id="resultados-{{ $loop->index }}" class="list-group list-group-flush" style="display:none;"></div>
                                <div id="no-result-{{ $loop->index }}" class="p-3 text-muted" style="display:none;">Sin resultados</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Aprendices Actuales</h6>
                            <div id="lista-asignados-{{ $loop->index }}" class="vstack gap-2">
                                @php
                                    $items = isset($semillero->aprendices_items) && is_iterable($semillero->aprendices_items) ? $semillero->aprendices_items : [];
                                @endphp
                                @foreach($items as $ap)
                                    <div class="border rounded p-2 d-flex justify-content-between align-items-center" data-id="{{ $ap['id_aprendiz'] ?? '' }}">
                                        <div>
                                            <div class="fw-semibold">{{ $ap['nombre'] ?? '' }}</div>
                                            <small class="text-muted">{{ $ap['programa'] ?? 'Sin programa' }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-danger btn-eliminar"><i class="bi bi-trash me-1"></i>Eliminar</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <form id="form-sync-{{ $loop->index }}" class="d-none" method="POST" action="#">
                            @csrf
                            @method('PUT')
                        </form>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>Cancelar</button>
                        <button id="btn-guardar-{{ $loop->index }}" class="btn btn-brand"><i class="bi bi-check2-circle me-1"></i>Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const idx = {{ $loop->index }};
            const isSem = {{ !empty($idSem) ? 'true' : 'false' }};
            const refId = {{ !empty($idSem) ? (int)$idSem : (int)($idProj ?? 0) }};
            const token = "{{ csrf_token() }}";
            console.log('[INIT] Modal #' + idx + ' | isSem:', isSem, '| refId:', refId, '| idProj:', {{ (int)($idProj ?? 0) }}, '| idSem:', {{ (int)($idSem ?? 0) }});
            function getCookie(name){
                const m = document.cookie.match(new RegExp('(^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g,'\\$1') + '=([^;]*)'));
                return m ? decodeURIComponent(m[2]) : null;
            }

            function apiFetch(url, opts={}){
                const baseHeaders = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };
                if (token) baseHeaders['X-CSRF-TOKEN'] = token;
                const xsrf = getCookie('XSRF-TOKEN');
                if (xsrf) baseHeaders['X-XSRF-TOKEN'] = xsrf;
                const headers = Object.assign({}, baseHeaders, (opts.headers||{}));
                // Si mandamos JSON y es POST/PUT/DELETE, incluir _token también en body
                if (opts.body && typeof opts.body === 'string' && /application\/json/i.test(headers['Content-Type']||'')){
                    try {
                        const parsed = JSON.parse(opts.body);
                        if (!parsed._token) parsed._token = token;
                        opts.body = JSON.stringify(parsed);
                    } catch (_) {}
                }
                return fetch(url, Object.assign({
                    credentials: 'same-origin',
                    headers,
                }, opts));
            }

            function notify(message, type = 'success'){
                const div = document.createElement('div');
                div.className = `alert alert-${type}`;
                div.textContent = message;
                Object.assign(div.style, {
                    position:'fixed', left:'50%', transform:'translateX(-50%)', bottom:'24px', zIndex:1080,
                    minWidth:'280px', boxShadow:'0 6px 20px rgba(0,0,0,.15)'
                });
                document.body.appendChild(div);
                setTimeout(()=>{ div.classList.add('show'); }, 10);
                setTimeout(()=>{ div.remove(); }, 2200);
            }

            const q = (sel) => document.getElementById(sel + '-' + idx);
            // Abrir modal de edición sin anidar modales: cerrar detalle y luego abrir edición
            (function bindOpenEdit(){
                const btn = document.getElementById('open-edit-' + idx);
                if (!btn) return;
                btn.addEventListener('click', function(){
                    // Override dinámico de contexto por si Blade no trajo IDs esperados
                    const dp = parseInt(btn.dataset.refproj||'0',10) || 0;
                    const ds = parseInt(btn.dataset.refsem||'0',10) || 0;
                    if (dp > 0){ window['isSem'+idx] = false; window['refId'+idx] = dp; }
                    else if (ds > 0){ window['isSem'+idx] = true; window['refId'+idx] = ds; }

                    const detEl = document.getElementById('detalleSemillero' + idx);
                    const editEl = document.getElementById('editApr' + idx);
                    if (!detEl || !editEl) return;
                    const det = bootstrap.Modal.getInstance(detEl) || new bootstrap.Modal(detEl);
                    const edit = bootstrap.Modal.getInstance(editEl) || new bootstrap.Modal(editEl);
                    detEl.addEventListener('hidden.bs.modal', function onHidden(){
                        edit.show();
                    }, { once: true });
                    det.hide();
                });
            })();
            const buscador = q('buscador');
            const tipoDoc = q('tipo-doc');
            const resultados = q('resultados');
            const noResult = q('no-result');
            const lista = q('lista-asignados');
            const detailsList = document.getElementById('aprendicesList' + idx);
            const countEl = document.getElementById('apr-count-' + idx);
            const btnCrearAgregar = null;
            const inpNombre = null;
            const inpEmail = null;
            const btnGuardar = q('btn-guardar');
            const formSync = q('form-sync');

            function route(name, params){
                // Plantillas básicas generadas por Blade
                const r = {
                    sem: {
                        search: `{{ route('lider_semi.semilleros.aprendices.search', ['semillero' => '__ID__']) }}`,
                        attach: `{{ route('lider_semi.semilleros.aprendices.attach', ['semillero' => '__ID__']) }}`,
                        detach: `{{ route('lider_semi.semilleros.aprendices.detach', ['semillero' => '__SID__', 'aprendiz' => '__AID__']) }}`,
                        update: `{{ route('lider_semi.semilleros.aprendices.update', ['semillero' => '__ID__']) }}`,
                        create: `{{ route('lider_semi.semilleros.aprendices.create', ['semillero' => '__ID__']) }}`,
                    },
                    proj: {
                        search: `{{ route('lider_semi.proyectos.aprendices.search', ['proyecto' => '__ID__']) }}`,
                        attach: `{{ route('lider_semi.proyectos.aprendices.attach', ['proyecto' => '__ID__']) }}`,
                        detach: `{{ route('lider_semi.proyectos.aprendices.detach', ['proyecto' => '__PID__', 'aprendiz' => '__AID__']) }}`,
                        update: `{{ route('lider_semi.proyectos.aprendices.update', ['proyecto' => '__ID__']) }}`,
                        create: `{{ route('lider_semi.proyectos.aprendices.create', ['proyecto' => '__ID__']) }}`,
                    }
                }[(window['isSem'+idx] ?? isSem) ? 'sem' : 'proj'];
                const REF = (window['refId'+idx] ?? refId);
                let url = r[name] || '#';
                url = url.replace('__ID__', REF).replace('__SID__', REF).replace('__PID__', REF);
                if (params && params.aprendiz) url = url.replace('__AID__', params.aprendiz);
                return url;
            }

            let typingTimer;
            function renderResultado(item){
                const row = document.createElement('label');
                row.className = 'list-group-item d-flex align-items-start gap-2';
                row.dataset.id = item.id_aprendiz;
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input mt-1';
                // Marcar si ya está asignado
                if (lista && lista.querySelector(`[data-id="${item.id_aprendiz}"]`)) cb.checked = true;
                cb.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); });
                row.addEventListener('click', function(e){
                    e.preventDefault(); e.stopPropagation();
                    cb.checked = !cb.checked;
                    if (cb.checked) {
                        attachAprendiz(item.id_aprendiz, item.nombre_completo, item.programa);
                    } else {
                        const nodo = lista.querySelector(`[data-id="${item.id_aprendiz}"]`);
                        if (nodo) detachAprendiz(item.id_aprendiz, nodo);
                    }
                });
                cb.addEventListener('change', function(e){ e.preventDefault(); e.stopPropagation();
                    if (cb.checked) {
                        attachAprendiz(item.id_aprendiz, item.nombre_completo, item.programa);
                    } else {
                        const nodo = lista.querySelector(`[data-id="${item.id_aprendiz}"]`);
                        if (nodo) detachAprendiz(item.id_aprendiz, nodo);
                    }
                });
                const info = document.createElement('div');
                info.innerHTML = `<div class=\"fw-semibold\">${item.nombre_completo}</div><small class=\"text-muted\">${item.programa ?? 'Sin programa'}</small>`;
                row.appendChild(cb);
                row.appendChild(info);
                return row;
            }
            function attachAprendiz(id, nombre, programa){
                apiFetch(route('attach'), { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({aprendiz_id:id}) })
                    .then(async r=>{ let data; try{ data = await r.json(); }catch(e){ throw new Error('Respuesta no JSON ('+r.status+')'); } return {ok:r.ok,status:r.status,data}; })
                    .then(({ok,status,data})=>{ if(ok && data && data.ok){
                        const div = document.createElement('div');
                        div.className = 'border rounded p-2 d-flex justify-content-between align-items-center';
                        div.dataset.id = id;
                        const prog = data.aprendiz && data.aprendiz.programa ? data.aprendiz.programa : (programa ?? 'Sin programa');
                        div.innerHTML = `<div><div class=\"fw-semibold\">${nombre}</div><small class=\"text-muted\">${prog}</small></div><button class=\"btn btn-sm btn-danger btn-eliminar\">Eliminar</button>`;
                        lista.appendChild(div); resultados.style.display='none'; resultados.innerHTML='';
                        // Actualizar "Ver Detalles": agregar tarjeta y contador
                        if (detailsList){
                            const col = document.createElement('div');
                            col.className = 'col-md-6';
                            col.dataset.id = id;
                            const ini1 = (nombre||'A').trim().charAt(0).toUpperCase();
                            const seg = (nombre||'').trim().split(' ');
                            const ini2 = (seg.length>1 ? seg[1].charAt(0) : 'M').toUpperCase();
                            const prog = data.aprendiz && data.aprendiz.programa ? data.aprendiz.programa : (programa ?? 'Sin programa');
                            col.innerHTML = `<div class=\"border rounded p-3 d-flex align-items-center gap-3\"><div class=\"rounded-circle d-flex justify-content-center align-items-center\" style=\"width:48px;height:48px;background-color:#5aa72e;color:#fff;font-weight:700;\">${ini1}${ini2}</div><div><div class=\"fw-semibold\">${nombre}</div><small class=\"text-muted\">${prog}</small></div></div>`;
                            detailsList.appendChild(col);
                        }
                        if (countEl){
                            const m = (countEl.textContent||'0').match(/\d+/); let n = m?parseInt(m[0],10):0; n++; countEl.textContent = n + ' aprendices';
                        }
                        notify('Aprendiz Asignado Correctamente','success');
                    } else { notify('Error Al Asignar ('+status+')','danger'); }})
                    .catch(()=> notify('No Se Pudo Asignar (red/JSON)','danger'));
            }
            function detachAprendiz(id, nodo){
                apiFetch(route('detach', {aprendiz:id}), { method:'DELETE' })
                    .then(async r=>{ let data; try{ data = await r.json(); }catch(e){ throw new Error('Respuesta no JSON ('+r.status+')'); } return {ok:r.ok,status:r.status,data}; })
                    .then(({ok,status,data})=>{ if(ok && data && data.ok){
                        nodo.remove();
                        // Desmarcar en resultados si visible
                        const rowCb = resultados && resultados.querySelector(`[data-id="${id}"] input[type="checkbox"]`);
                        if (rowCb) rowCb.checked = false;
                        // Quitar también en "Ver Detalles" y decrementar contador
                        if (detailsList){
                            const card = detailsList.querySelector(`[data-id="${id}"]`);
                            if (card) card.remove();
                        }
                        if (countEl){
                            const m = (countEl.textContent||'0').match(/\d+/); let n = m?parseInt(m[0],10):0; n = Math.max(0, n-1); countEl.textContent = n + ' aprendices';
                        }
                    } else { notify('Error Al Eliminar ('+status+')','danger'); }})
                    .catch(()=> notify('No se Pudo Eliminar (red/JSON)','danger'));
            }
            function crearYAdjuntar(){
                const nombre = (inpNombre && inpNombre.value.trim()) || ''; const correo = (inpEmail && inpEmail.value.trim()) || '';
                if(!nombre) return;
                apiFetch(route('create'), { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ nombre_completo:nombre, correo_institucional: correo || null })})
                    .then(r=>r.json()).then(data=>{ if(data && data.ok){ const ap=data.aprendiz; const div=document.createElement('div'); div.className='border rounded p-2 d-flex justify-content-between align-items-center'; div.dataset.id=ap.id_aprendiz; div.innerHTML = `<div><div class=\"fw-semibold\">${ap.nombre_completo}</div><small class=\"text-muted\">${ap.correo_institucional ?? ''}</small></div><button class=\"btn btn-sm btn-danger btn-eliminar\">Eliminar</button>`; lista.appendChild(div); if(inpNombre) inpNombre.value=''; if(inpEmail) inpEmail.value=''; }});
            }

            function doSearch(){
                clearTimeout(typingTimer);
                const tipo = (tipoDoc && tipoDoc.value.trim()) || '';
                const num = (buscador && buscador.value.trim()) || '';
                console.log('[SEARCH] Tipo:', tipo, '| Num:', num);

                typingTimer = setTimeout(function(){
                    let url = route('search') + `?tipo=${encodeURIComponent(tipo)}&num=${encodeURIComponent(num)}`;
                    console.log('[SEARCH] URL:', url);
                    apiFetch(url)
                        .then(r => {
                            console.log('[SEARCH] Response status:', r.status);
                            return r.json();
                        })
                        .then(items=>{
                        console.log('[SEARCH] Items recibidos:', items.length, items);
                        resultados.innerHTML='';
                        if(items && items.length){
                            items.forEach(it=> resultados.appendChild(renderResultado(it)));
                            resultados.style.display='block';
                            if(noResult) noResult.style.display='none';
                        } else {
                            resultados.style.display='none';
                            if(noResult) noResult.style.display='block';
                        }
                    })
                    .catch(err => {
                        console.error('[SEARCH] Error:', err);
                        resultados.style.display='none';
                        if(noResult) {
                            noResult.textContent = 'Error al buscar: ' + err.message;
                            noResult.style.display='block';
                        }
                    });
                }, 250);
            }
            if (buscador) buscador.addEventListener('keyup', doSearch);
            if (tipoDoc) tipoDoc.addEventListener('change', doSearch);
            // Cargar aprendices iniciales al abrir el modal
            const modalEl = document.getElementById('editApr' + idx);
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function(){
                    doSearch();
                });
            }
            if (lista) lista.addEventListener('click', function(e){ const btn = e.target.closest('.btn-eliminar'); if(!btn) return; const cont = btn.closest('[data-id]'); const id = cont.dataset.id; if(id) detachAprendiz(id, cont); });
            if (btnCrearAgregar) btnCrearAgregar.addEventListener('click', function(e){ e.preventDefault(); crearYAdjuntar(); });
            if (btnGuardar) btnGuardar.addEventListener('click', function(e){
                e.preventDefault();
                const ids = Array.from(lista.querySelectorAll('[data-id]')).map(n=> parseInt(n.dataset.id,10)).filter(Boolean);
                apiFetch(route('update'), { method:'PUT', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ aprendices_ids: ids }) })
                    .then(async r=>{ let data; try{ data = await r.json(); }catch(_){ data=null; } return {ok:r.ok,status:r.status,data}; })
                    .then(({ok,status,data})=>{
                        if(ok && data && data.ok){
                            notify('Cambios guardados','success');
                            const editEl = document.getElementById('editApr' + idx);
                            const edit = editEl ? (bootstrap.Modal.getInstance(editEl) || new bootstrap.Modal(editEl)) : null;
                            if (edit) edit.hide();
                        } else {
                            notify('Error al guardar ('+status+')','danger');
                        }
                    })
                    .catch(()=> notify('No se pudo guardar (red/JSON)','danger'));
            });
        });
        </script>
        @endforeach
    </div>
</div>
@endsection
