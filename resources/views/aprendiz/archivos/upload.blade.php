<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subir Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .dropzone{border:2px dashed #198754;border-radius:12px;padding:30px;background:#f8fff9;cursor:pointer;transition:.2s}
        .dropzone.dragover{background:#eaffef;border-color:#0a6b3c}
        .file-item{border:1px solid #eee;border-radius:10px;padding:12px;margin-bottom:10px;background:#fff}
        .file-name{font-weight:600}
        .file-size{color:#6c757d;font-size:.85rem}
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-success m-0">Subir Documentos</h2>
        <a href="{{ route('aprendiz.archivos.index') }}" class="btn btn-outline-secondary">Mis Archivos</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Selecciona un Proyecto</label>
                    <select id="proyecto" class="form-select">
                        <option value="">Selecciona un proyecto...</option>
                        @foreach(($proyectos ?? []) as $p)
                            <option value="{{ $p->id_proyecto }}" {{ (isset($proyectoSeleccionado) && (int)$proyectoSeleccionado === (int)$p->id_proyecto) ? 'selected' : '' }}>
                                {{ $p->nombre_proyecto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="text-muted small">Solo PDF. Máx 10 MB por archivo.</div>
                </div>
            </div>

            <div id="dropzone" class="dropzone text-center mb-3">
                <div class="fs-5 mb-1">Arrastra y suelta tus archivos aquí</div>
                <div class="text-muted">o haz clic para seleccionar</div>
                <input id="fileInput" type="file" accept="application/pdf" multiple hidden>
            </div>

            <div id="queue" class="mb-3"></div>

            <div class="d-flex gap-2">
                <button id="btnUpload" class="btn btn-success" disabled>Subir</button>
                <button id="btnClear" class="btn btn-outline-secondary" disabled>Limpiar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const drop = document.getElementById('dropzone');
    const input = document.getElementById('fileInput');
    const queue = document.getElementById('queue');
    const btnUpload = document.getElementById('btnUpload');
    const btnClear = document.getElementById('btnClear');
    const selProyecto = document.getElementById('proyecto');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const uploadUrl = "{{ route('aprendiz.archivos.upload.post') }}";

    let files = [];

    function fmtSize(bytes){
        const kb = bytes/1024; if(kb<1024) return kb.toFixed(1)+' KB';
        return (kb/1024).toFixed(2)+' MB';
    }

    function renderQueue(){
        queue.innerHTML = '';
        files.forEach((f, idx)=>{
            const item = document.createElement('div');
            item.className = 'file-item';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="file-name">${f.file.name}</div>
                        <div class="file-size">${fmtSize(f.file.size)}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" data-x="${idx}">Quitar</button>
                </div>
                <div class="progress mt-2" role="progressbar" aria-label="Progreso">
                    <div class="progress-bar" style="width:${f.progress||0}%">${f.progress||0}%</div>
                </div>`;
            queue.appendChild(item);
        });
        btnUpload.disabled = files.length===0 || !selProyecto.value;
        btnClear.disabled = files.length===0;
    }

    function addFiles(list){
        for(const file of list){
            if(file.type !== 'application/pdf'){ continue; }
            files.push({file, progress:0});
        }
        renderQueue();
    }

    drop.addEventListener('click', ()=> input.click());
    input.addEventListener('change', (e)=> addFiles(e.target.files));
    drop.addEventListener('dragover', (e)=>{ e.preventDefault(); drop.classList.add('dragover'); });
    drop.addEventListener('dragleave', ()=> drop.classList.remove('dragover'));
    drop.addEventListener('drop', (e)=>{ e.preventDefault(); drop.classList.remove('dragover'); addFiles(e.dataTransfer.files); });

    queue.addEventListener('click', (e)=>{
        const btn = e.target.closest('button[data-x]');
        if(btn){ files.splice(parseInt(btn.dataset.x,10),1); renderQueue(); }
    });

    selProyecto.addEventListener('change', ()=> renderQueue());

    btnClear.addEventListener('click', ()=>{ files = []; renderQueue(); });

    btnUpload.addEventListener('click', async ()=>{
        if(!selProyecto.value){
            alert('Selecciona un proyecto'); return;
        }
        for(let i=0;i<files.length;i++){
            const f = files[i];
            const fd = new FormData();
            fd.append('proyecto_id', selProyecto.value);
            // enviar uno por uno para seguimiento de progreso por archivo
            fd.append('documentos', f.file);

            await new Promise((resolve, reject)=>{
                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadUrl, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.upload.addEventListener('progress', (e)=>{
                    if(e.lengthComputable){
                        const p = Math.round((e.loaded/e.total)*100);
                        files[i].progress = p; renderQueue();
                    }
                });
                xhr.onreadystatechange = ()=>{
                    if(xhr.readyState === 4){
                        if(xhr.status>=200 && xhr.status<300){ resolve(); }
                        else { reject(new Error('Error al subir')); }
                    }
                };
                xhr.send(fd);
            }).catch(()=>{
                files[i].progress = 0; renderQueue();
            });
        }
        alert('Subidas completadas');
        files = []; renderQueue();
    });
})();
</script>
</body>
</html>
