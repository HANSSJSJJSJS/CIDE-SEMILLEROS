@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Proyectos Asignados</h1>

    @if($proyectos->isEmpty())
        <p>No tienes proyectos asignados.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre del Proyecto</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $proyecto)
                    <tr>
                        <td>{{ $proyecto->nombre }}</td>
                        <td>{{ $proyecto->descripcion }}</td>
                        <td>
                            <a href="{{ route('aprendiz.proyectos.show', $proyecto->id) }}" class="btn btn-info">Ver Detalles</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
