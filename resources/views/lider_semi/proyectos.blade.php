@extends('layouts.lider_semi')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lider_semi/mis-proyectos.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="container mt-4 projects-wallpaper">
    <div class="page-header">
        <h4 class="fw-bold mb-1">Mis Proyectos</h4>
        <p class="page-subtitle text-muted mb-0">Gestiona y supervisa todos tus semilleros activos</p>
    </div>

    <div class="row g-4">
        @foreach($semilleros as $semillero)
        @php
            $estado = strtoupper(trim($semillero->estado ?? ''));
            $estadoClass = match($estado){
                'EN EJECUCIÓN', 'EN EJECUCION' => 'status-ejecucion',
                'EN FORMACIÓN', 'EN FORMACION' => 'status-formacion',
                'FINALIZADO' => 'status-finalizado',
                default => 'status-default'
            };
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card card-project">
                <div class="card-header text-white fw-bold d-flex align-items-center justify-content-between">
                    <span class="truncate-1">{{ $semillero->nombre }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-status {{ $estadoClass }}">{{ $semillero->estado }}</span>
                        <small id="apr-count-{{ $loop->index }}" class="text-white-50">{{ (int)($semillero->aprendices ?? 0) }} aprendices</small>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mt-1 text-secondary mb-2">{{ $semillero->descripcion }}</p>

                    <div class="mt-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="fw-semibold text-muted">Progreso</small>
                            <small class="text-muted">{{ $semillero->progreso }}%</small>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $semillero->progreso }}%;"
                                aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center btn-container">
                        <button type="button" class="btn btn-details w-100" data-bs-toggle="modal" data-bs-target="#detalleSemillero{{ $loop->index }}"><i class="bi bi-eye me-1"></i>Ver Detalles</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade detail-modal" id="detalleSemillero{{ $loop->index }}" tabindex="-1" aria-labelledby="detalleSemilleroLabel{{ $loop->index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header brand-header detail-modal-header">
                        <h5 class="modal-title fw-bold" id="detalleSemilleroLabel{{ $loop->index }}">{{ $semillero->nombre }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body detail-modal-body">
                        <div class="detail-section-card">
                            <div class="detail-section-head">
                                <h6 class="detail-section-title">Información General</h6>
                            </div>
                            <div class="detail-info-row">
                                <div class="detail-info-item"><span class="detail-info-label">Nombre:</span> {{ $semillero->nombre }}</div>
                                <div class="detail-info-item"><span class="detail-info-label">Estado:</span> <span class="detail-status-pill">{{ $semillero->estado }}</span></div>
                            </div>
                        </div>

                        <div class="detail-section-card">
                            <div class="detail-section-head">
                                <h6 class="detail-section-title">Descripción</h6>
                            </div>
                            <p class="detail-text">{{ $semillero->descripcion ?: 'Sin descripción' }}</p>
                        </div>

                        <div class="detail-section-card">
                            <div class="detail-section-head">
                                <h6 class="detail-section-title">Progreso del Proyecto</h6>
                            </div>
                            <div class="progress detail-progress" role="progressbar" aria-valuenow="{{ $semillero->progreso }}" aria-valuemin="0" aria-valuemax="100">
                                <div id="modalProgress-{{ $loop->index }}" class="progress-bar detail-progress-bar" style="width: {{ $semillero->progreso }}%;">{{ $semillero->progreso }}%</div>
                            </div>
                        </div>

                        <div class="detail-section-card">
                            <div class="detail-section-head">
                                <h6 class="detail-section-title">Información Adicional</h6>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4"><span class="detail-info-label">Líder:</span> {{ Auth::user()->name }}</div>
                                <div class="col-md-4"><span class="detail-info-label">Fecha de Inicio:</span> {{ isset($semillero->fecha_inicio) && $semillero->fecha_inicio ? \Carbon\Carbon::parse($semillero->fecha_inicio)->translatedFormat('d \de F, Y') : 'N/D' }}</div>
                                <div class="col-md-4"><span class="detail-info-label">Aprendices Asignados:</span> {{ (int)($semillero->aprendices ?? 0) }}</div>
                            </div>
                        </div>

                        <div class="detail-section-card">
                            <div class="detail-section-head">
                                <h6 class="detail-section-title">Aprendices Asignados</h6>
                            </div>
                            <div class="detail-apr-list" id="aprendicesList{{ $loop->index }}">
                                @php
                                    $items = isset($semillero->aprendices_items) && is_iterable($semillero->aprendices_items) ? $semillero->aprendices_items : [];
                                @endphp
                                @forelse($items as $ap)
                                    @php
                                        $nombreAp = trim((string)($ap['nombres'] ?? '') . ' ' . (string)($ap['apellidos'] ?? '')) ?: ($ap['nombre'] ?? 'Aprendiz');
                                        $ini1 = strtoupper(mb_substr((string)$nombreAp, 0, 1));
                                        $parts = preg_split('/\s+/', trim((string)$nombreAp));
                                        $ini2 = strtoupper(mb_substr((string)($parts[1] ?? ''), 0, 1));
                                        $ini = trim($ini1 . $ini2);
                                        $sub = (string)($ap['programa'] ?? '');
                                        if (trim($sub) === '') {
                                            $sub = trim((string)($ap['tipo_documento'] ?? '') . ' ' . (string)($ap['documento'] ?? ''));
                                        }
                                        if (trim($sub) === '') {
                                            $sub = 'Sin programa';
                                        }
                                    @endphp
                                    <div class="detail-apr-item" data-id="{{ $ap['id_aprendiz'] ?? '' }}">
                                        <div class="detail-apr-avatar">{{ $ini ?: 'AP' }}</div>
                                        <div class="detail-apr-meta">
                                            <div class="detail-apr-name">{{ $nombreAp }}</div>
                                            <div class="detail-apr-sub">{{ $sub }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="detail-empty"><small class="text-muted">Sin aprendices asignados.</small></div>
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
        <div class="modal fade edit-apr-modal" id="editApr{{ $loop->index }}" tabindex="-1" aria-labelledby="editAprLabel{{ $loop->index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header brand-header">
                        <h5 class="modal-title fw-bold" id="editAprLabel{{ $loop->index }}">{{ $semillero->nombre }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <small class="text-muted">Agregue o Elimine Aprendices del Proyecto</small>
                        <div class="row mt-2 g-2"></div>

                        <div class="mt-3 search-filter-card">
                            <div class="search-filter-head">
                                <div class="search-filter-title">
                                    <i class="bi bi-search"></i>
                                    <span>Buscar Aprendices Existentes</span>
                                </div>
                            </div>
                            <div class="row g-2 search-filter-controls">
                                <div class="col-md-5">
                                    <label class="form-label">Tipo de Documento</label>
                                    <select id="tipo-doc-{{ $loop->index }}" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="CC">Cédula de ciudadanía</option>
                                        <option value="TI">Tarjeta de identidad</option>
                                        <option value="CE">Cédula de extranjería</option>
                                        <option value="PAS">Pasaporte</option>
                                        <option value="PEP">Permiso especial</option>
                                        <option value="RC">Registro civil</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label">Número de Documento</label>
                                    <input id="buscador-{{ $loop->index }}" type="text" class="form-control" placeholder="Ej: 1023456789">
                                </div>
                            </div>
                            <div class="search-results-pane mt-3">
                                <div id="resultados-{{ $loop->index }}" class="search-results" style="display:none;"></div>
                                <div id="no-result-{{ $loop->index }}" class="search-no-result" style="display:none;">Sin resultados</div>
                            </div>
                        </div>

                        <div class="mt-4 apr-current-section">
                            <div class="apr-current-head">
                                <div class="apr-current-title">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Aprendices Actuales en el Proyecto</span>
                                </div>
                            </div>
                            <div id="lista-asignados-{{ $loop->index }}" class="apr-current-list">
                                @php
                                    $items = isset($semillero->aprendices_items) && is_iterable($semillero->aprendices_items) ? $semillero->aprendices_items : [];
                                @endphp
                                @foreach($items as $ap)
                                    @php
                                        $displayName = trim((string)($ap['nombres'] ?? '') . ' ' . (string)($ap['apellidos'] ?? '')) ?: ($ap['nombre'] ?? 'Aprendiz');
                                        $badgeText = (string)($ap['programa'] ?? '');
                                        if (trim($badgeText) === '') {
                                            $badgeText = trim((string)($ap['tipo_documento'] ?? '') . ' ' . (string)($ap['documento'] ?? ''));
                                        }
                                        if (trim($badgeText) === '') {
                                            $badgeText = 'Sin programa';
                                        }
                                        $ini = strtoupper(mb_substr((string)$displayName, 0, 1));
                                    @endphp
                                    <div class="apr-current-item" data-id="{{ $ap['id_aprendiz'] ?? '' }}">
                                        <div class="apr-current-left">
                                            <div class="apr-current-avatar">{{ $ini }}</div>
                                            <div class="apr-current-meta">
                                                <div class="apr-current-name">{{ $displayName }}</div>
                                                <span class="apr-current-badge">{{ $badgeText }}</span>
                                            </div>
                                        </div>
                                        <button type="button" class="apr-current-remove btn-eliminar" aria-label="Eliminar">
                                            <svg viewBox="0 0 448 512" class="svgIcon" aria-hidden="true"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <form id="form-sync-{{ $loop->index }}" class="d-none" method="POST" action="javascript:void(0)" onsubmit="return false;">
                            @csrf
                        </form>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>Cancelar</button>
                        <button type="button" id="btn-guardar-{{ $loop->index }}" class="btn btn-brand"><i class="bi bi-check2-circle me-1"></i>Guardar Cambios</button>
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
                    // Evitar foco dentro de un contenedor que pronto será aria-hidden
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }
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
            const btnCrearAgregar = q('btn-crear');
            const inpNombre = q('nombres');
            const inpApellidos = q('apellidos');
            const inpEmail = q('correo');
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
                row.className = 'search-item';
                const aid = (item && item.id_aprendiz) ? parseInt(item.id_aprendiz,10) : null;
                const uid = (item && item.user_id) ? parseInt(item.user_id,10) : null;
                row.dataset.id = aid ? String(aid) : (uid ? ('U-' + uid) : '');
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input search-check';
                // Marcar si ya está asignado
                if (lista && lista.querySelector(`[data-id="${item.id_aprendiz}"]`)) cb.checked = true;
                cb.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); });
                const displayName = `${String(item.nombres||'').trim()} ${String(item.apellidos||'').trim()}`.trim() || 'Aprendiz';
                const tipoRaw = String(item.tipo_documento||'').trim().toUpperCase();
                const docRaw = String(item.documento||'').trim();
                const tipoLabelMap = { CC:'CC', TI:'TI', CE:'CE', PAS:'PASAPORTE', PEP:'PEP', RC:'RC' };
                const tipoLabel = (tipoLabelMap[tipoRaw] || tipoRaw);
                const displayDoc = `${tipoRaw} ${docRaw}`.trim();

                const ini1 = (displayName || 'A').trim().charAt(0).toUpperCase();
                const seg = (displayName || '').trim().split(' ');
                const ini2 = (seg.length > 1 ? seg[1].charAt(0) : 'M').toUpperCase();

                const avatar = document.createElement('div');
                avatar.className = 'search-avatar';
                avatar.textContent = `${ini1}${ini2}`;

                const meta = document.createElement('div');
                meta.className = 'search-meta';
                const nameEl = document.createElement('div');
                nameEl.className = 'search-name';
                nameEl.textContent = displayName;

                const subEl = document.createElement('div');
                subEl.className = 'search-sub';
                if (tipoLabel || docRaw) {
                    const pill = document.createElement('span');
                    pill.className = 'search-pill';
                    pill.textContent = tipoLabel || 'DOC';
                    const num = document.createElement('span');
                    num.className = 'search-docnum';
                    num.textContent = docRaw || '';
                    subEl.appendChild(pill);
                    if (docRaw) subEl.appendChild(num);
                } else {
                    subEl.textContent = (item.programa ?? 'Sin programa');
                }

                meta.appendChild(nameEl);
                meta.appendChild(subEl);

                if (cb.checked) row.classList.add('is-checked');
                row.addEventListener('click', function(e){
                    e.preventDefault(); e.stopPropagation();
                    cb.checked = !cb.checked;
                    row.classList.toggle('is-checked', cb.checked);
                    if (cb.checked) {
                        attachAprendiz({aprendiz_id: aid, user_id: uid}, displayName, item.programa, displayDoc);
                    } else {
                        const nodo = lista.querySelector(`[data-id="${item.id_aprendiz}"]`);
                        if (nodo) detachAprendiz(item.id_aprendiz, nodo);
                    }
                });
                cb.addEventListener('change', function(e){ e.preventDefault(); e.stopPropagation();
                    row.classList.toggle('is-checked', cb.checked);
                    if (cb.checked) {
                        attachAprendiz({aprendiz_id: aid, user_id: uid}, displayName, item.programa, displayDoc);
                    } else {
                        const nodo = lista.querySelector(`[data-id="${item.id_aprendiz}"]`);
                        if (nodo) detachAprendiz(item.id_aprendiz, nodo);
                    }
                });
                row.appendChild(cb);
                row.appendChild(avatar);
                row.appendChild(meta);
                return row;
            }
            function attachAprendiz(arg, nombre, programa, docLabel){
                const payload = (arg && typeof arg === 'object') ? arg : {aprendiz_id: arg};
                apiFetch(route('attach'), { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload) })
                    .then(async r=>{ let data; try{ data = await r.json(); }catch(e){ throw new Error('Respuesta no JSON ('+r.status+')'); } return {ok:r.ok,status:r.status,data}; })
                    .then(({ok,status,data})=>{ if(ok && data && data.ok){
                        const div = document.createElement('div');
                        const newId = data.aprendiz && data.aprendiz.id_aprendiz ? data.aprendiz.id_aprendiz : (payload.aprendiz_id || '');
                        div.dataset.id = newId;

                        const doc = (data.aprendiz && (String(data.aprendiz.tipo_documento||'').trim() || String(data.aprendiz.documento||'').trim()))
                            ? `${String(data.aprendiz.tipo_documento||'').trim()} ${String(data.aprendiz.documento||'').trim()}`.trim()
                            : (docLabel || '');
                        const badgeText = (data.aprendiz && data.aprendiz.programa ? data.aprendiz.programa : (programa ?? '')) || doc || 'Sin programa';
                        const ini = (nombre||'A').trim().charAt(0).toUpperCase();

                        div.className = 'apr-current-item';
                        div.innerHTML = `
                            <div class="apr-current-left">
                                <div class="apr-current-avatar">${ini}</div>
                                <div class="apr-current-meta">
                                    <div class="apr-current-name">${nombre}</div>
                                    <span class="apr-current-badge">${badgeText}</span>
                                </div>
                            </div>
                            <button type="button" class="apr-current-remove btn-eliminar" aria-label="Eliminar">
                                <svg viewBox="0 0 448 512" class="svgIcon" aria-hidden="true"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path></svg>
                            </button>
                        `;
                        lista.appendChild(div);
                        resultados.style.display='none';
                        resultados.innerHTML='';

                        // Actualizar "Ver Detalles": agregar tarjeta y contador
                        if (detailsList){
                            const item = document.createElement('div');
                            item.className = 'detail-apr-item';
                            item.dataset.id = newId;
                            const ini1 = (nombre||'A').trim().charAt(0).toUpperCase();
                            const seg = (nombre||'').trim().split(' ');
                            const ini2 = (seg.length>1 ? seg[1].charAt(0) : '').toUpperCase();
                            const iniPair = (ini1 + ini2).trim() || 'AP';
                            const sub2 = (data.aprendiz && data.aprendiz.programa ? data.aprendiz.programa : (programa ?? '')) || doc || 'Sin programa';
                            item.innerHTML = `<div class="detail-apr-avatar">${iniPair}</div><div class="detail-apr-meta"><div class="detail-apr-name">${nombre}</div><div class="detail-apr-sub">${sub2}</div></div>`;
                            detailsList.appendChild(item);
                            const empty = detailsList.querySelector('.detail-empty');
                            if (empty) empty.remove();
                        }
                        if (countEl){
                            const m = (countEl.textContent||'0').match(/\d+/);
                            let n = m ? parseInt(m[0],10) : 0;
                            n++;
                            countEl.textContent = n + ' aprendices';
                        }
                        notify('Aprendiz Asignado Correctamente','success');
                    } else {
                        notify('Error Al Asignar ('+status+')','danger');
                    }})
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
                                const m = (countEl.textContent||'0').match(/\d+/);
                                let n = m ? parseInt(m[0],10) : 0;
                                n = Math.max(0, n-1);
                                countEl.textContent = n + ' aprendices';
                            }
                        } else {
                            notify('Error Al Eliminar ('+status+')','danger');
                        }
                    })
                    .catch(()=> notify('No se Pudo Eliminar (red/JSON)','danger'));
            }
            function crearYAdjuntar(){
                const nombres = (inpNombre && inpNombre.value.trim()) || '';
                const apellidos = (inpApellidos && inpApellidos.value.trim()) || '';
                const correo = (inpEmail && inpEmail.value.trim()) || '';

                if(!nombres || !apellidos) {
                    notify('Debe ingresar nombres y apellidos', 'danger');
                    return;
                }

                const nombreCompleto = nombres + ' ' + apellidos;

                apiFetch(route('create'), {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json' },
                    body: JSON.stringify({ nombres: nombres, apellidos: apellidos, correo_institucional: correo || null })
                })
                    .then(r=>r.json())
                    .then(data=>{
                        if(data && data.ok){
                            const ap = data.aprendiz;
                            const div = document.createElement('div');
                            div.dataset.id = ap.id_aprendiz;
                            const ini = (nombreCompleto||'A').trim().charAt(0).toUpperCase();
                            div.className = 'apr-current-item';
                            div.innerHTML = `
                                <div class="apr-current-left">
                                    <div class="apr-current-avatar">${ini}</div>
                                    <div class="apr-current-meta">
                                        <div class="apr-current-name">${nombreCompleto}</div>
                                        <span class="apr-current-badge">Sin programa</span>
                                    </div>
                                </div>
                                <button type="button" class="apr-current-remove btn-eliminar" aria-label="Eliminar">
                                    <svg viewBox="0 0 448 512" class="svgIcon" aria-hidden="true"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path></svg>
                                </button>
                            `;
                            lista.appendChild(div);
                            if(inpNombre) inpNombre.value='';
                            if(inpApellidos) inpApellidos.value='';
                            if(inpEmail) inpEmail.value='';
                            notify('Aprendiz creado y agregado correctamente', 'success');
                        } else {
                            notify('Error al crear aprendiz', 'danger');
                        }
                    })
                    .catch(err => {
                        console.error('Error creando aprendiz:', err);
                        notify('Error al crear aprendiz', 'danger');
                    });
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

            const detailEl = document.getElementById('detalleSemillero' + idx);
            if (detailEl) {
                detailEl.addEventListener('shown.bs.modal', function(){
                    const progressBar = document.getElementById('modalProgress-' + idx);
                    if (!progressBar) return;
                    const targetWidth = (progressBar.dataset && progressBar.dataset.targetWidth) ? progressBar.dataset.targetWidth : (progressBar.style.width || '0%');
                    if (progressBar.dataset) progressBar.dataset.targetWidth = targetWidth;
                    progressBar.style.width = '0%';
                    void progressBar.offsetWidth;
                    setTimeout(() => {
                        progressBar.style.width = targetWidth;
                    }, 120);
                });
            }
            // Cargar aprendices iniciales al abrir el modal
            const modalEl = document.getElementById('editApr' + idx);
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function(){
                    doSearch();
                });
                // Evitar aria-hidden con elemento enfocado dentro del modal en cierre
                const blurInside = () => {
                    const insideFocused = modalEl.contains(document.activeElement) ? document.activeElement : null;
                    if (insideFocused && typeof insideFocused.blur === 'function') insideFocused.blur();
                };
                modalEl.addEventListener('hide.bs.modal', blurInside);
                modalEl.addEventListener('hidden.bs.modal', blurInside);
            }
            if (lista) lista.addEventListener('click', function(e){ const btn = e.target.closest('.btn-eliminar'); if(!btn) return; const cont = btn.closest('[data-id]'); const id = cont.dataset.id; if(id) detachAprendiz(id, cont); });
            if (btnCrearAgregar) btnCrearAgregar.addEventListener('click', function(e){ e.preventDefault(); crearYAdjuntar(); });
            if (btnGuardar) btnGuardar.addEventListener('click', function(e){
                e.preventDefault();
                const ids = Array.from(lista.querySelectorAll('[data-id]')).map(n=> parseInt(n.dataset.id,10)).filter(Boolean);
                const REF = (window['refId'+idx] ?? refId);
                const isSemDyn = (window['isSem'+idx] ?? isSem);
                const refNum = parseInt(REF, 10);
                if (!Number.isInteger(refNum) || refNum <= 0) {
                    console.error('[Guardar Aprendices] REF inválido:', REF, '| idx:', idx, '| isSem:', isSemDyn);
                    notify('No se pudo guardar: ID inválido (recarga la página).', 'danger');
                    return;
                }
                const updateUrlNamed = route('update');
                const updateUrlFallback = isSemDyn
                    ? `/lider_semi/semilleros/${refNum}/aprendices`
                    : `/lider_semi/proyectos/${refNum}/aprendices`;
                const updateUrl = (updateUrlNamed && updateUrlNamed !== '#' && /\/aprendices(\/|$)/.test(updateUrlNamed))
                    ? updateUrlNamed
                    : updateUrlFallback;

                if (!/\/aprendices(\/|$)/.test(updateUrl)) {
                    console.error('[Guardar Aprendices] URL inválida:', updateUrl, '| named:', updateUrlNamed, '| fallback:', updateUrlFallback);
                    notify('No se pudo guardar: URL inválida (recarga la página).', 'danger');
                    return;
                }
                console.log('[Guardar Aprendices] PUT', updateUrl, { idsCount: ids.length, idx, isSem: isSemDyn, refId: refNum });
                apiFetch(updateUrl, { method:'PUT', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify({ aprendices_ids: ids }) })
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
