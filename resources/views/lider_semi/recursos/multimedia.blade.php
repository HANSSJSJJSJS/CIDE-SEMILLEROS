@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/recursos.css') }}">
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid px-4 mt-4">

    <div class="documentos-header mb-4">
        <h2>Multimedia</h2>
        <p class="text-muted">Archivos compartidos por el administrador</p>
    </div>

    <div class="row g-4" id="contenedorMultimedia"></div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch("{{ route('lider_semi.recursos.multimedia.list') }}")
        .then(res => res.json())
        .then(data => {
            const cont = document.getElementById('contenedorMultimedia');

            if (!data.length) {
                cont.innerHTML = `<p class="text-muted">No hay archivos disponibles.</p>`;
                return;
            }

            data.forEach(r => {
                const url = `/storage/${r.archivo}`;
                cont.innerHTML += `
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h6>${r.nombre_archivo}</h6>
                                <p class="text-muted">${r.categoria}</p>
                                <a href="${url}" class="btn btn-sm btn-primary" download>
                                    <i class="bi bi-download"></i> Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(() => {
            document.getElementById('contenedorMultimedia')
                .innerHTML = `<p class="text-danger">Error cargando multimedia</p>`;
        });
});
</script>
@endpush
