@extends('layouts.lider_semi')

@section('content')
<div class="container mt-3">
    <div class="modal-header px-0 border-0">
        <h5 class="fw-bold mb-0" style="color:#2d572c;">{{ $proyecto->nombre }}</h5>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <h6 class="fw-bold mb-1">Editar Aprendices Asignados</h6>
            <small class="text-muted">Agregue o elimine aprendices del proyecto</small>

            <div class="row mt-3 g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label">Nombre del Aprendiz</label>
                    <input id="ap-nombre" type="text" class="form-control" placeholder="Ej: Juan Pérez">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input id="ap-email" type="email" class="form-control" placeholder="Ej: juan.perez@sena.edu.co">
                </div>
                <div class="col-12">
                    <button id="btn-crear-agregar" class="btn w-100" style="background-color:#5aa72e;color:#fff;">+ Agregar Aprendiz</button>
                </div>
            </div>

            <div class="mt-3">
                <input id="buscador" type="text" class="form-control" placeholder="Buscar aprendices existentes por nombre o correo">
                <div id="resultados" class="list-group mt-1" style="max-height:200px;overflow:auto;display:none;"></div>
            </div>

            <div class="mt-4">
                <h6 class="fw-bold mb-2">Aprendices Actuales</h6>
                <div id="lista-asignados" class="vstack gap-2">
                    @foreach($proyecto->aprendices as $ap)
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

            <form id="form-sync" class="d-none" method="POST" action="{{ route('lider_semi.proyectos.aprendices.update', $proyecto->id_proyecto) }}">
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
    const proyectoId = {{ (int)$proyecto->id_proyecto }};
    const token = document.querySelector('input[name="_token"]').value || document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const buscador = document.getElementById('buscador');
    const resultados = document.getElementById('resultados');
    const lista = document.getElementById('lista-asignados');
    const btnCrearAgregar = document.getElementById('btn-crear-agregar');
    const inpNombre = document.getElementById('ap-nombre');
    const inpEmail = document.getElementById('ap-email');
    const btnGuardar = document.getElementById('btn-guardar');

    let typingTimer;

    function renderResultado(item){
        const a = document.createElement('a');
        a.href = '#';
        a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
        a.dataset.id = item.id_aprendiz;
        a.innerHTML = `<span><strong>${item.nombre_completo}</strong><br><small class=\"text-muted\">${item.correo_institucional ?? ''}</small></span><button class=\"btn btn-sm btn-success\">Agregar</button>`;
        a.addEventListener('click', function(e){
            e.preventDefault();
            attachAprendiz(item.id_aprendiz, item.nombre_completo, item.correo_institucional);
        });
        return a;
    }

    function attachAprendiz(id, nombre, correo){
        console.log('Intentando asignar aprendiz:', {id, nombre, correo, proyectoId});
        fetch(`{{ route('lider_semi.proyectos.aprendices.attach', ['proyecto' => '__ID__']) }}`.replace('__ID__', proyectoId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ aprendiz_id: id })
        })
        .then(r=>{
            console.log('Respuesta del servidor:', r.status, r.statusText);
            if(!r.ok) {
                return r.text().then(text => {
                    console.error('Error del servidor:', text);
                    alert('Error al asignar aprendiz: ' + r.statusText);
                    throw new Error(text);
                });
            }
            return r.json();
        })
        .then(data=>{
            console.log('Datos recibidos:', data);
            if(data && data.ok){
                const div = document.createElement('div');
                div.className = 'border rounded p-2 d-flex justify-content-between align-items-center';
                div.dataset.id = id;
                div.innerHTML = `<div><div class=\"fw-semibold\">${nombre}</div><small class=\"text-muted\">${correo ?? ''}</small></div><button class=\"btn btn-sm btn-danger btn-eliminar\">Eliminar</button>`;
                lista.appendChild(div);
                resultados.style.display = 'none';
                resultados.innerHTML = '';
                alert('Aprendiz asignado exitosamente');
            } else {
                alert('No se pudo asignar el aprendiz');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
        });
    }

    function detachAprendiz(id, nodo){
        fetch(`{{ route('lider_semi.proyectos.aprendices.detach', ['proyecto' => '__PID__', 'aprendiz' => '__AID__']) }}`
            .replace('__PID__', proyectoId).replace('__AID__', id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': token }
        }).then(r=>r.json()).then(data=>{
            if(data && data.ok){
                nodo.remove();
            }
        });
    }

    function crearYAdjuntar(){
        const nombre = inpNombre.value.trim();
        const correo = inpEmail.value.trim();
        if(!nombre) {
            alert('Por favor ingrese el nombre del aprendiz');
            return;
        }
        console.log('Creando y asignando aprendiz:', {nombre, correo, proyectoId});
        fetch(`{{ route('lider_semi.proyectos.aprendices.create', ['proyecto' => '__ID__']) }}`.replace('__ID__', proyectoId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ nombre_completo: nombre, correo_institucional: correo || null })
        })
        .then(r=>{
            console.log('Respuesta del servidor:', r.status, r.statusText);
            if(!r.ok) {
                return r.text().then(text => {
                    console.error('Error del servidor:', text);
                    alert('Error al crear aprendiz: ' + r.statusText);
                    throw new Error(text);
                });
            }
            return r.json();
        })
        .then(data=>{
            console.log('Datos recibidos:', data);
            if(data && data.ok){
                const ap = data.aprendiz;
                const div = document.createElement('div');
                div.className = 'border rounded p-2 d-flex justify-content-between align-items-center';
                div.dataset.id = ap.id_aprendiz;
                div.innerHTML = `<div><div class=\"fw-semibold\">${ap.nombre_completo}</div><small class=\"text-muted\">${ap.correo_institucional ?? ''}</small></div><button class=\"btn btn-sm btn-danger btn-eliminar\">Eliminar</button>`;
                lista.appendChild(div);
                inpNombre.value='';
                inpEmail.value='';
                alert('Aprendiz creado y asignado exitosamente');
            } else {
                alert('No se pudo crear el aprendiz');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
        });
    }

    buscador.addEventListener('keyup', function(){
        clearTimeout(typingTimer);
        const txt = buscador.value.trim();
        if(txt.length < 2){ resultados.style.display='none'; resultados.innerHTML=''; return; }
        typingTimer = setTimeout(function(){
            fetch(`{{ route('lider_semi.proyectos.aprendices.search', ['proyecto' => '__ID__']) }}`.replace('__ID__', proyectoId) + `?q=${encodeURIComponent(txt)}`)
                .then(r=>r.json()).then(items=>{
                    resultados.innerHTML = '';
                    if(items && items.length){
                        items.forEach(it=> resultados.appendChild(renderResultado(it)) );
                        resultados.style.display='block';
                    } else {
                        resultados.style.display='none';
                    }
                });
        }, 250);
    });

    resultados.addEventListener('click', function(e){ if(e.target.closest('a')) e.preventDefault(); });

    lista.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-eliminar');
        if(!btn) return;
        const cont = btn.closest('[data-id]');
        const id = cont.dataset.id;
        detachAprendiz(id, cont);
    });

    btnCrearAgregar.addEventListener('click', function(e){ e.preventDefault(); crearYAdjuntar(); });

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
