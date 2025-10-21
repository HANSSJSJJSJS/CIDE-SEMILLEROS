@extends('layouts.lider_semi')

@section('content')
<div class="container mt-3">
    <div class="modal-header px-0 border-0">
        <h5 class="fw-bold mb-0" style="color:#2d572c;">{{ $semillero->nombre }}</h5>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <h6 class="fw-bold mb-1">Editar Aprendices Asignados</h6>
            <small class="text-muted">Agregue o elimine aprendices existentes del proyecto</small>

            <div class="mt-3">
                <label class="form-label fw-semibold">Buscar Aprendices Existentes</label>
                <div class="row g-2">
                    <div class="col-md-5">
                        <label class="form-label">Tipo de Documento</label>
                        <select id="tipo-doc" class="form-select">
                            <option value="">Todos</option>
                            <option value="CC">CC</option>
                            <option value="TI">TI</option>
                            <option value="CE">CE</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Número de Documento</label>
                        <input id="buscador" type="text" class="form-control" placeholder="Ej: 1023456789">
                    </div>
                </div>
                <div class="border rounded mt-2" style="max-height:260px; overflow:auto;">
                    <div id="resultados" class="list-group list-group-flush" style="display:none;"></div>
                    <div id="no-result" class="p-3 text-muted" style="display:none;">Sin resultados</div>
                </div>
            </div>

            <div class="mt-4">
                <h6 class="fw-bold mb-2">Aprendices Actuales</h6>
                <div id="lista-asignados" class="vstack gap-2">
                    @foreach($semillero->aprendices as $ap)
                        <div class="border rounded p-2 d-flex justify-content-between align-items-center" data-id="{{ $ap->id_aprendiz }}">
                            <div>
                                <div class="fw-semibold">{{ $ap->nombre_completo }}</div>
                                <small class="text-muted">{{ $ap->correo_institucional }}</small>
                            </div>
                            <button class="btn btn-sm btn-danger btn-eliminar">Eliminar</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <form id="form-sync" class="d-none" method="POST" action="{{ route('lider_semi.semilleros.aprendices.update', $semillero->id_semillero) }}">
                @csrf
                @method('PUT')
            </form>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('lider_semi.semilleros') }}" class="btn btn-light">Cancelar</a>
            <button id="btn-guardar" class="btn btn-success">Guardar Cambios</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const semilleroId = {{ (int)$semillero->id_semillero }};
    const token = "{{ csrf_token() }}";
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
    const buscador = document.getElementById('buscador');
    const tipoDoc = document.getElementById('tipo-doc');
    const resultados = document.getElementById('resultados');
    const noResult = document.getElementById('no-result');
    const lista = document.getElementById('lista-asignados');
    const btnGuardar = document.getElementById('btn-guardar');

    let typingTimer;

    function renderResultado(item){
        const row = document.createElement('label');
        row.className = 'list-group-item d-flex align-items-start gap-2';
        row.dataset.id = item.id_aprendiz;
        const cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'form-check-input mt-1';
        cb.addEventListener('change', function(){
            if (cb.checked) {
                attachAprendiz(item.id_aprendiz, item.nombre_completo, item.programa);
            } else {
                // Si se desmarca, eliminar si estuviera en la lista y desasignar
                const nodo = lista.querySelector(`[data-id="${item.id_aprendiz}"]`);
                if (nodo) detachAprendiz(item.id_aprendiz, nodo);
            }
        });
        const info = document.createElement('div');
        info.innerHTML = `<div class="fw-semibold">${item.nombre_completo}</div><small class="text-muted">${item.programa ?? 'Sin programa'}</small>`;
        row.appendChild(cb);
        row.appendChild(info);
        return row;
    }

    function attachAprendiz(id, nombre, programa){
        apiFetch(`{{ route('lider_semi.semilleros.aprendices.attach', ['semillero' => '__ID__']) }}`.replace('__ID__', semilleroId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ aprendiz_id: id })
        }).then(async r=>{ let data; try{ data = await r.json(); }catch(e){ throw new Error('Respuesta no JSON ('+r.status+')'); } return {ok:r.ok,status:r.status,data}; })
          .then(({ok,status,data})=>{
            if(ok && data && data.ok){
                if (!lista.querySelector(`[data-id="${id}"]`)){
                    const div = document.createElement('div');
                    div.className = 'border rounded p-2 d-flex justify-content-between align-items-center';
                    div.dataset.id = id;
                    const prog = data.aprendiz && data.aprendiz.programa ? data.aprendiz.programa : (programa ?? 'Sin programa');
                    div.innerHTML = `<div><div class=\"fw-semibold\">${nombre}</div><small class=\"text-muted\">${prog}</small></div><button class=\"btn btn-sm btn-danger btn-eliminar\">Eliminar</button>`;
                    lista.appendChild(div);
                    notify('Aprendiz asignado correctamente','success');
                }
            } else { notify('Error al asignar ('+status+')','danger'); }
        }).catch(()=> notify('No se pudo asignar (red/JSON)','danger'));
    }

    function detachAprendiz(id, nodo){
        apiFetch(`{{ route('lider_semi.semilleros.aprendices.detach', ['semillero' => '__SID__', 'aprendiz' => '__AID__']) }}`
            .replace('__SID__', semilleroId).replace('__AID__', id), {
            method: 'DELETE'
        }).then(async r=>{ let data; try{ data = await r.json(); }catch(e){ throw new Error('Respuesta no JSON ('+r.status+')'); } return {ok:r.ok,status:r.status,data}; })
          .then(({ok,status,data})=>{
            if(ok && data && data.ok){
                nodo.remove();
                // Desmarcar en resultados si visible
                const row = resultados.querySelector(`[data-id="${id}"] input[type="checkbox"]`);
                if (row) row.checked = false;
                notify('Aprendiz eliminado correctamente','success');
            } else { notify('Error al eliminar ('+status+')','danger'); }
        }).catch(()=> notify('No se pudo eliminar (red/JSON)','danger'));
    }

    function doSearch(){
        clearTimeout(typingTimer);
        const tipo = (tipoDoc && tipoDoc.value.trim()) || '';
        const num = (buscador && buscador.value.trim()) || '';
        
        // Construir URL con parámetros separados
        let searchUrl = `{{ route('lider_semi.semilleros.aprendices.search', ['semillero' => '__ID__']) }}`.replace('__ID__', semilleroId);
        const params = new URLSearchParams();
        if (tipo) params.append('tipo', tipo);
        if (num) params.append('num', num);
        if (params.toString()) searchUrl += '?' + params.toString();
        
        typingTimer = setTimeout(function(){
            apiFetch(searchUrl)
                .then(r=>r.json()).then(items=>{
                    resultados.innerHTML = '';
                    if(items && items.length){
                        items.forEach(it=> resultados.appendChild(renderResultado(it)) );
                        resultados.style.display='block';
                        noResult.style.display='none';
                    } else {
                        resultados.style.display='none';
                        noResult.style.display='block';
                    }
                });
        }, 250);
    }
    buscador.addEventListener('keyup', doSearch);
    if (tipoDoc) tipoDoc.addEventListener('change', doSearch);
    // Cargar aprendices iniciales al cargar la página
    doSearch();

    lista.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-eliminar');
        if(!btn) return;
        const cont = btn.closest('[data-id]');
        const id = cont.dataset.id;
        detachAprendiz(id, cont);
    });

    btnGuardar.addEventListener('click', function(){
        const form = document.getElementById('form-sync');
        form.innerHTML = `@csrf @method('PUT')`;
        Array.from(lista.querySelectorAll('[data-id]')).forEach(n=>{
            const i = document.createElement('input');
            i.type = 'hidden';
            i.name = 'aprendices_ids[]';
            i.value = n.dataset.id;
            form.appendChild(i);
        });
        form.submit();
    });
});
</script>
@endsection
