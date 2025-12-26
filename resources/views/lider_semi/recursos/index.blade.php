{{-- resources/views/lider_semi/recursos/index.blade.php --}}
@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-4 px-4">

    {{-- TÍTULO PRINCIPAL --}}
    <div class="documentos-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Recursos para Líder de Semillero</h2>
            <p>Consulta y responde los recursos asignados a los líderes del semillero.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('lider_semi.recursos.multimedia') }}" class="btn-multimedia">
                <i class="bi bi-collection-play"></i> Multimedia
            </a>
        </div>
    </div>

    {{-- PROYECTOS DEL LÍDER --}}
    <div class="mb-5">
        <h4 class="seccion-titulo">Tus Proyectos</h4>

        @if($proyectos->isEmpty())
            <p class="text-muted">No tienes proyectos asignados aún.</p>
        @else
            <div class="row g-4">
                @foreach($proyectos as $proyecto)
                    @include('lider_semi.recursos._card_proyecto')
                @endforeach
            </div>
        @endif
    </div>

    {{-- RECURSOS DIRIGIDOS AL LÍDER --}}
    <div class="mt-4">
        <h4 class="seccion-titulo">Recursos disponibles</h4>

        @if($recursos->isEmpty())
            <p class="text-muted">No hay recursos disponibles para ti.</p>
        @else

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle tabla-semilleros">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Fecha límite</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($recursos as $r)
                        @php
                            $estadoTxt = strtolower(trim((string)($r->estado ?? '')));
                            $obsRechazo = trim((string)($r->comentarios ?? ''));
                        @endphp
                        <tr>
                            <td class="px-4">
                                <strong>{{ $r->nombre_archivo }}</strong><br>
                                <small class="text-muted">{{ $r->descripcion ?: 'Sin descripción' }}</small>
                            </td>

                            <td>{{ ucfirst($r->categoria) }}</td>

                            <td>
                                {{ $r->fecha_vencimiento ?? $r->fecha_limite ?? '—' }}
                            </td>

                            <td>
                                @if($estadoTxt === 'rechazado')
                                    <div class="estado-rechazo-inline">
                                        <span class="estado-pill-rechazado">RECHAZADA</span>
                                        @if($obsRechazo !== '')
                                            <button type="button"
                                                    class="btn-estado-observacion"
                                                    data-target="rechazo-obs-inline-{{ $r->id_recurso }}"
                                                    aria-label="Ver observación de rechazo">
                                                <i class="bi bi-chat-left-text"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($obsRechazo !== '')
                                        <div id="rechazo-obs-inline-{{ $r->id_recurso }}" class="rechazo-obs-inline d-none">
                                            <div class="rechazo-obs-box">
                                                <strong>Observación:</strong> {{ $obsRechazo }}
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($r->estado) }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="acciones-semilleros">

                                    @php
                                        $viewUrl = null;
                                        if (!empty($r->enlace_respuesta)) {
                                            $viewUrl = $r->enlace_respuesta;
                                        } elseif (!empty($r->archivo_respuesta)) {
                                            $viewUrl = asset('storage/'.$r->archivo_respuesta);
                                        } else {
                                            $coment = (string)($r->comentarios ?? '');
                                            if ($coment !== '') {
                                                if (preg_match('/Enlace respuesta:\s*(https?:\/\/\S+)/i', $coment, $m)) {
                                                    $viewUrl = $m[1];
                                                } elseif (preg_match('/Archivo respuesta:\s*([^\s]+)/i', $coment, $m)) {
                                                    $path = trim($m[1]);
                                                    if ($path !== '') {
                                                        $viewUrl = asset('storage/'.$path);
                                                    }
                                                }
                                            }

                                            if (!$viewUrl && !empty($r->respondido_en) && !empty($r->archivo) && $r->archivo !== 'sin_archivo') {
                                                $viewUrl = asset('storage/'.$r->archivo);
                                            }
                                        }
                                    @endphp

                                    @php
                                        $estadoAccion = strtolower(trim((string)($r->estado ?? '')));
                                        $puedeReResponder = in_array($estadoAccion, ['rechazado', 'vencido'], true);
                                    @endphp

                                    @if($viewUrl)
                                        <a href="{{ $viewUrl }}" class="btn btn-accion-ver" target="_blank" rel="noopener">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>

                                        @if($puedeReResponder)
                                            <button class="btn btn-accion-editar"
                                                    data-id="{{ $r->id_recurso }}"
                                                    data-nombre="{{ $r->nombre_archivo }}"
                                                    data-tipo="{{ strtolower($r->tipo_documento ?? '') }}"
                                                    onclick="abrirResponder(this)">
                                                <i class="bi bi-chat-dots"></i> Responder
                                            </button>
                                        @endif
                                    @else
                                        <button class="btn btn-accion-editar"
                                                data-id="{{ $r->id_recurso }}"
                                                data-nombre="{{ $r->nombre_archivo }}"
                                                data-tipo="{{ strtolower($r->tipo_documento ?? '') }}"
                                                onclick="abrirResponder(this)">
                                            <i class="bi bi-chat-dots"></i> Responder
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @endif
    </div>

</div>

{{-- MODAL --}}
@include('lider_semi.recursos.modal-responder-recurso')

@endsection


{{-- ======================== SCRIPTS LIMPIOS ======================== --}}
@push('scripts')
<script>

window.abrirResponder = function (btn) {
    const id = btn.dataset.id;
    const nombre = btn.dataset.nombre;
    const tipo = (btn.dataset.tipo || '').toLowerCase().trim();

    window.__respCurrentRow = btn.closest('tr') || null;

    const tituloEl = document.getElementById("resp_titulo");
    const idEl = document.getElementById("resp_id_recurso");
    const respEl = document.getElementById("resp_respuesta");
    const fileEl = document.getElementById("resp_archivo");
    const fileLabelEl = document.getElementById("resp_archivo_label");
    const dropzoneEl = document.getElementById("resp_dropzone");
    const dropzoneTipoEl = document.getElementById("resp_dropzone_tipo");
    const fileNameEl = document.getElementById("resp_file_name");
    const linkEl = document.getElementById("resp_enlace");
    const linkLabelEl = document.getElementById("resp_enlace_label");
    const tipoLabelEl = document.getElementById("resp_tipo_label");
    const tipoHintEl = document.getElementById("resp_tipo_hint");

    if (tituloEl) tituloEl.textContent = nombre;
    if (idEl) idEl.value = id;
    if (respEl) respEl.value = '';
    if (fileNameEl) fileNameEl.textContent = 'Sin archivos seleccionados';

    const defaultAccept = 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,image/*,video/*,text/plain,text/csv,application/zip,application/x-rar-compressed';

    let label = 'Archivo';
    let accept = defaultAccept;
    let isLink = false;

    if (['pdf'].includes(tipo)) { label = 'Archivo PDF (.pdf)'; accept = '.pdf,application/pdf'; }
    else if (['doc','docx','word','documento'].includes(tipo)) { label = 'Documento Word (.doc, .docx)'; accept = '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'; }
    else if (['ppt','pptx','presentacion','presentación'].includes(tipo)) { label = 'Presentación PowerPoint (.ppt, .pptx)'; accept = '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation'; }
    else if (['img','imagen','image'].includes(tipo)) { label = 'Imagen (JPG, PNG, GIF)'; accept = 'image/*'; }
    else if (['video','mp4','avi','mov','mkv'].includes(tipo)) { label = 'Video (MP4, AVI, MOV, MKV)'; accept = 'video/*'; }
    else if (['enlace','link','url'].includes(tipo)) { label = 'Enlace (URL)'; isLink = true; }
    else if (tipo) { label = tipo.toUpperCase(); }

    if (tipoLabelEl) tipoLabelEl.textContent = label;
    if (tipoHintEl) tipoHintEl.style.display = 'none';
    if (dropzoneTipoEl) dropzoneTipoEl.textContent = label;

    if (fileEl) {
        fileEl.value = '';
        fileEl.accept = accept;
        fileEl.required = !isLink;
        fileEl.style.display = 'none';
    }

    if (fileLabelEl) {
        fileLabelEl.style.display = isLink ? 'none' : '';
    }

    if (dropzoneEl) {
        dropzoneEl.style.display = isLink ? 'none' : '';
    }

    if (linkEl) {
        linkEl.value = '';
        linkEl.required = isLink;
        linkEl.style.display = isLink ? '' : 'none';
    }

    if (linkLabelEl) {
        linkLabelEl.style.display = isLink ? '' : 'none';
    }

    let modal = new bootstrap.Modal(document.getElementById("modalResponderRecurso"));
    modal.show();

    if (!window.__respDropzoneInit) {
        window.__respDropzoneInit = true;
        const dz = document.getElementById('resp_dropzone');
        const input = document.getElementById('resp_archivo');
        const nameEl = document.getElementById('resp_file_name');

        const setName = () => {
            if (!nameEl) return;
            if (input && input.files && input.files.length > 0) {
                nameEl.textContent = input.files[0].name;
            } else {
                nameEl.textContent = 'Sin archivos seleccionados';
            }
        };

        if (input) {
            input.addEventListener('change', setName);
        }

        if (dz && input) {
            dz.addEventListener('click', () => input.click());
            dz.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    input.click();
                }
            });

            dz.addEventListener('dragover', (e) => {
                e.preventDefault();
                dz.classList.add('is-dragover');
            });
            dz.addEventListener('dragleave', () => dz.classList.remove('is-dragover'));
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.classList.remove('is-dragover');
                const file = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files[0] : null;
                if (!file) return;
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                setName();
            });
        }
    }
};

window.enviarRespuesta = function () {

    let form = document.getElementById("formResponderRecurso");
    let formData = new FormData(form);

    const idEl = document.getElementById("resp_id_recurso");
    const fileEl = document.getElementById("resp_archivo");
    const linkEl = document.getElementById("resp_enlace");

    if (!idEl || !idEl.value) {
        Swal.fire("Error", "No se pudo identificar el recurso.", "error");
        return;
    }

    if (fileEl && fileEl.required && (!fileEl.files || fileEl.files.length === 0)) {
        Swal.fire("Falta archivo", "Selecciona el archivo solicitado antes de enviar.", "warning");
        return;
    }

    if (linkEl && linkEl.required && !String(linkEl.value || '').trim()) {
        Swal.fire("Falta enlace", "Ingresa el enlace solicitado antes de enviar.", "warning");
        return;
    }

    fetch("{{ route('lider_semi.recursos.responder') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: formData
    })
    .then(async (res) => {
        const data = await res.json().catch(() => null);
        if (!res.ok) {
            throw data || { message: 'No se pudo procesar la solicitud.' };
        }
        return data;
    })
    .then(data => {
        if (data && data.success) {
            Swal.fire("¡Listo!", data.message || "Respuesta enviada correctamente.", "success")
                .then(() => {
                    try {
                        const modalEl = document.getElementById('modalResponderRecurso');
                        const instance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                        if (instance) instance.hide();
                    } catch (e) {}

                    const viewUrl = data.view_url;
                    const row = window.__respCurrentRow;
                    if (viewUrl && row) {
                        const actions = row.querySelector('.acciones-semilleros');
                        if (actions) {
                            actions.innerHTML = `
                                <a href="${viewUrl}" class="btn btn-accion-ver" target="_blank" rel="noopener">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            `;
                        }
                    }
                    window.__respCurrentRow = null;
                });
            return;
        }
        Swal.fire("Error", (data && data.message) ? data.message : "No se pudo enviar la respuesta.", "error");
    })
    .catch(err => {
        console.error(err);
        let msg = (err && err.message) ? err.message : "Hubo un problema al enviar la respuesta.";
        if (err && err.errors && typeof err.errors === 'object') {
            const firstKey = Object.keys(err.errors)[0];
            if (firstKey && Array.isArray(err.errors[firstKey]) && err.errors[firstKey][0]) {
                msg = err.errors[firstKey][0];
            }
        }
        Swal.fire("Error", msg, "error");
    });
};

document.addEventListener('click', function (e) {
    const btn = e.target && e.target.closest ? e.target.closest('.btn-estado-observacion') : null;
    if (!btn) return;

    const targetId = btn.getAttribute('data-target');
    if (!targetId) return;

    const target = document.getElementById(targetId);
    if (!target) return;

    const willShow = target.classList.contains('d-none');

    document.querySelectorAll('.rechazo-obs-inline').forEach((el) => {
        el.classList.add('d-none');
    });

    if (willShow) {
        target.classList.remove('d-none');
    }
});

</script>
@endpush
