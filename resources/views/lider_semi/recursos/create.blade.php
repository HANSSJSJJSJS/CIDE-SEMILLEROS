@extends('layouts.lider_semi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lider_semi/recursos.css') }}">
@endpush

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="documentos-header mb-4">
        <h2>Subir Nuevo Recurso</h2>
        <p>Agrega documentos para tu semillero</p>
    </div>

    {{-- FORMULARIO --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('lider_semi.recursos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <label class="form-label-evidencia">Título del archivo</label>
                <input type="text" name="nombre_archivo" class="form-control-evidencia" placeholder="Ej: Documento de logística">

                <label class="form-label-evidencia">Archivo</label>
                <input type="file" name="archivo" required class="form-control-evidencia">

                <label class="form-label-evidencia">Categoría</label>
                <select name="categoria" class="form-select-evidencia" required>
                    <option value="plantillas">Plantillas</option>
                    <option value="manuales">Manuales</option>
                    <option value="otros">Otros</option>
                </select>

                <label class="form-label-evidencia">Descripción</label>
                <textarea name="descripcion" class="form-control-evidencia form-textarea-evidencia"
                          placeholder="Describe brevemente el recurso..."></textarea>

                <div class="modal-botones mt-4">
                    <a href="{{ route('lider_semi.recursos.index') }}" class="btn-cancelar-modal">Cancelar</a>
                    <button type="submit" class="btn-guardar-modal">Guardar Recurso</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
