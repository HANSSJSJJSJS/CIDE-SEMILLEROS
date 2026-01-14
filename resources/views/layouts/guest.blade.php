<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'CIDE')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons (OBLIGATORIO para bi-eye, etc) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS GLOBAL --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- 🔴 ESTO ES LO QUE TE FALTA 🔴 --}}
    @stack('styles')
</head>
<body class="bg-light">

    <main class="container min-vh-100 d-flex align-items-center justify-content-center">
        @yield('content')
    </main>

</body>
</html>