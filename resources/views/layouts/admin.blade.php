<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Panel de Administración - CIDE SEMILLERO')</title>

  {{-- Bootstrap + Fuente --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  {{-- Tu CSS con Vite (SOLO una vez) --}}
  @vite(['resources/css/admin.css'])

  @stack('head')
</head>
<body>

  {{-- Header --}}
  @include('admin.sesiones.header')

  <div class="container">
    {{-- Sidebar --}}
    @include('admin.sesiones.sidebar')

    {{-- Contenido --}}
    <main class="main-content">
      @yield('content')
    </main>
  </div>

  {{-- 🔽 Los modales se inyectan aquí, al final del body --}}
  @stack('modals')

  {{-- JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  @vite(['resources/js/admin.js'])
  @stack('scripts')
</body>
</html>
