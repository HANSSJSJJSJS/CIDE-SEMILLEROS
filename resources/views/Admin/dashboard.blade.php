@extends('layouts.admin')

@section('title', 'Dashboard | Admin')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="alert alert-success mb-3">
        ¡Listo! Estás viendo el Dashboard de Admin con el diseño del Líder.
      </div>
    </div>
  </div>

  {{-- Aquí vas pegando el contenido real de tu dashboard --}}
  <div class="row g-3">
    <div class="col-md-3">
      <div class="card"><div class="card-body">
        <h6 class="text-muted mb-1">Usuarios</h6>
        <h3 class="mb-0">—</h3>
      </div></div>
    </div>
  </div>
</div>
@endsection
