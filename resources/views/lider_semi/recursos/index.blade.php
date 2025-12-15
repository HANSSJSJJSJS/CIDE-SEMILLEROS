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
                        <tr>
                            <td class="px-4">
                                <strong>{{ $r->nombre_archivo }}</strong><br>
                                <small class="text-muted">{{ $r->descripcion ?: 'Sin descripción' }}</small>
                            </td>

                            <td>{{ ucfirst($r->categoria) }}</td>

                            <td>
                                {{ $r->fecha_vencimiento ? $r->fecha_vencimiento : '—' }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($r->estado) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="acciones-semilleros">

                                    {{-- DESCARGAR --}}
                                    <a href="{{ asset('storage/'.$r->archivo) }}" 
                                       class="btn btn-accion-ver" 
                                       download>
                                        <i class="bi bi-download"></i> Descargar
                                    </a>

                                    {{-- RESPONDER --}}
                                    <button class="btn btn-accion-editar"
                                            data-id="{{ $r->id }}"
                                            data-nombre="{{ $r->nombre_archivo }}"
                                            onclick="abrirResponder(this)">
                                        <i class="bi bi-chat-dots"></i> Responder
                                    </button>

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
@push('scripts')
<script>

window.abrirResponder = function (btn) {
    const id = btn.dataset.id;
    const nombre = btn.dataset.nombre;

    document.getElementById("modalRecursoTitulo").textContent = nombre;
    document.getElementById("responder_id_recurso").value = id;

    let modal = new bootstrap.Modal(document.getElementById("modalResponderRecurso"));
    modal.show();
};

window.enviarRespuesta = function () {

    let form = document.getElementById("formResponderRecurso");
    let formData = new FormData(form);

    fetch("{{ route('lider_semi.recursos.responder') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire("¡Listo!", "Respuesta enviada correctamente.", "success")
                .then(() => location.reload());
        } else {
            Swal.fire("Error", "No se pudo enviar la respuesta.", "error");
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire("Error", "Hubo un problema al enviar la respuesta.", "error");
    });
};

</script>
@endpush

@endpush
