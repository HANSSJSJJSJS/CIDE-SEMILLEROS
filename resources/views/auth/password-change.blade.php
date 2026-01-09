@php
    $layout = match(auth()->user()->role) {
        'ADMIN', 'LIDER_INVESTIGACION' => 'layouts.admin',
        'LIDER_SEMILLERO'              => 'layouts.lider',
        'APRENDIZ'                     => 'layouts.aprendiz',
        default                        => 'layouts.app',
    };
@endphp

@extends($layout)

@section('content')
<div class="container mt-5" style="max-width: 500px">

    <h4 class="mb-3 text-center">Cambiar contraseña</h4>

    <p class="text-muted small text-center">
        Por seguridad, debes cambiar tu contraseña antes de continuar.
    </p>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                required
            >
        </div>

        <div class="alert alert-info small">
            <strong>Requisitos:</strong>
            <ul class="mb-0">
                <li>Mínimo 10 caracteres</li>
                <li>Al menos una letra mayúscula</li>
                <li>Al menos un número</li>
            </ul>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Guardar nueva contraseña
        </button>
    </form>

</div>
@endsection
