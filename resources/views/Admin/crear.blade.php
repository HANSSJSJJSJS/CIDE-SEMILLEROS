{{-- resources/views/Admin/crear.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Registro de Usuarios · SENA</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tu CSS (ajusta la versión para romper caché cuando cambies estilos) -->
  <link href="{{ asset('css/estilos_sena.css') }}?v=5" rel="stylesheet">

</head>
<body class="sena-theme">


  <div class="container my-4">
    <div class="card card-sena w-100">
      <div class="card-body p-4 p-md-5">

        <h2 class="mb-4 text-white fw-bold" style="background:#00733B;border-radius:10px;padding:12px 16px;">
          Registro de usuarios · SENA
        </h2>

        {{-- Mensajes --}}
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- FORMULARIO --}}
        <form method="POST" action="{{ route('usuarios.store') }}" id="form-usuario" novalidate>
          @csrf

          {{-- ===== 1. Selección de rol ===== --}}
          <div class="row g-3">
            <div class="col-md-6">
              <label for="rol" class="form-label fw-semibold">Rol del usuario</label>
              <select id="rol" name="rol" class="form-select" required>
                <option value="" disabled selected>Seleccione un rol</option>
                <option value="ADMINISTRADOR">Administrador</option>
                <option value="LIDER_SEMILLERO">Líder de semillero</option>
                <option value="APRENDIZ">Aprendiz</option>
              </select>
            </div>
          </div>

          {{-- ===== 2. Correo ===== --}}
          <div id="box-correo" class="row g-3 mt-3 d-none">
            <div class="col-md-6">
              <label for="correo" class="form-label fw-semibold">Correo del usuario</label>
              <input type="email" id="correo" name="correo" class="form-control" required>
            </div>
          </div>

          {{-- ===== ADMINISTRADOR ===== --}}
          <div id="box-admin" class="mt-4 d-none">
            <div class="p-3 border rounded-3 ">
              <h5 class="fw-bold text-success mb-3">Datos de Administrador</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="admin_nombre">Nombre</label>
                  <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Nombre del administrador">
                </div>
              </div>
            </div>
          </div>

          {{-- ===== LÍDER DE SEMILLERO ===== --}}
          <div id="box-lider" class="mt-4 d-none">
            <div class="p-3 border rounded-3 ">
              <h5 class="fw-bold text-success mb-3">Datos de Líder de Semillero</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" id="lider_nombre" name="lider_nombre" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apellido</label>
                  <input type="text" id="lider_apellido" name="lider_apellido" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tipo de documento</label>
                  <select id="lider_tipo_doc" name="lider_tipo_doc" class="form-select">
                    <option value="" disabled selected>Seleccione</option>
                    <option value="CC">CC</option>
                    <option value="CE">CE</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Documento</label>
                  <input type="text" id="lider_documento" name="lider_documento" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Contacto de emergencia (nombre)</label>
                  <input type="text" id="lider_contacto_emerg" name="lider_contacto_emerg" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Celular del contacto</label>
                  <input type="tel" id="lider_cel_contacto" name="lider_cel_contacto" class="form-control">
                </div>
            
              </div> <!-- /row -->
            </div>   <!-- /p-3 border -->
          </div>     <!-- /box-lider -->

          {{-- ===== APRENDIZ ===== --}}
          <div id="box-aprendiz" class="mt-4 d-none">
            <div class="p-3 border rounded-3 ">
              <h5 class="fw-bold text-success mb-3">Datos de Aprendiz</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" id="aprendiz_nombre" name="aprendiz_nombre" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apellido</label>
                  <input type="text" id="aprendiz_apellido" name="aprendiz_apellido" class="form-control">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Ficha</label>
                  <input type="text" id="aprendiz_ficha" name="aprendiz_ficha" class="form-control">
                </div>
                <div class="col-md-8">
                  <label class="form-label">Programa</label>
                  <input type="text" id="aprendiz_programa" name="aprendiz_programa" class="form-control">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Tipo de documento</label>
                  <select id="aprendiz_tipo_doc" name="aprendiz_tipo_doc" class="form-select">
                    <option value="" disabled selected>Seleccione</option>
                    <option value="TI">TI</option>
                    <option value="CC">CC</option>
                    <option value="CE">CE</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Documento</label>
                  <input type="text" id="aprendiz_documento" name="aprendiz_documento" class="form-control">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Celular</label>
                  <input type="tel" id="aprendiz_celular" name="aprendiz_celular" class="form-control">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Correo institucional</label>
                  <input type="email" id="aprendiz_correo_institucional" name="aprendiz_correo_institucional" class="form-control" placeholder="ej: nombre@misena.edu.co">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contacto de emergencia</label>
                  <input type="text" id="aprendiz_contacto_emerg" name="aprendiz_contacto_emerg" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Celular del contacto</label>
                  <input type="tel" id="aprendiz_cel_contacto" name="aprendiz_cel_contacto" class="form-control">
                </div>
              </div> <!-- /row -->
            </div>   <!-- /p-3 border -->
          </div>     <!-- /box-aprendiz -->

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success px-4">Guardar</button>
            <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary px-4">Volver</a>
          </div>
        </form>

      </div>
    </div>
  </div> <!-- /container -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script dinámico -->
  <script>
  (function () {
    const rol = document.getElementById('rol');
    const correoBox = document.getElementById('box-correo');
    const correoInput = document.getElementById('correo');
    const boxAdmin = document.getElementById('box-admin');
    const boxLider = document.getElementById('box-lider');
    const boxAprendiz = document.getElementById('box-aprendiz');

    const show = el => el.classList.remove('d-none');
    const hide = el => el.classList.add('d-none');

    function toggleSections(value) {
      if (!value) { // sin rol seleccionado
        hide(correoBox); hide(boxAdmin); hide(boxLider); hide(boxAprendiz);
        return;
      }

      show(correoBox);
      correoInput.placeholder = (value === 'ADMINISTRADOR')
        ? 'Ej: admin@empresa.com'
        : 'Ej: nombre@misena.edu.co';

      hide(boxAdmin); hide(boxLider); hide(boxAprendiz);
      if (value === 'ADMINISTRADOR') show(boxAdmin);
      if (value === 'LIDER_SEMILLERO') show(boxLider);
      if (value === 'APRENDIZ') show(boxAprendiz);
    }

    // Inicializa al cargar (por si el navegador recuerda el select)
    toggleSections(rol.value);
    rol.addEventListener('change', e => toggleSections(e.target.value));
  })();
  </script>
</body>
</html>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script dinámico -->
  <script>
  (function () {
    const rol = document.getElementById('rol');
    const correoBox = document.getElementById('box-correo');
    const correoInput = document.getElementById('correo');
    const boxAdmin = document.getElementById('box-admin');
    const boxLider = document.getElementById('box-lider');
    const boxAprendiz = document.getElementById('box-aprendiz');

    function show(el) { el.classList.remove('d-none'); }
    function hide(el) { el.classList.add('d-none'); }

    function toggleSections(value) {
      // Si no hay rol seleccionado, ocultar todo
      if (!value) {
        hide(correoBox); hide(boxAdmin); hide(boxLider); hide(boxAprendiz);
        return;
      }

      // Mostrar correo siempre que haya un rol
      show(correoBox);
      correoInput.placeholder = (value === 'ADMINISTRADOR')
        ? 'Ej: admin@empresa.com'
        : 'Ej: nombre@misena.edu.co';

      // Ocultar todos y mostrar el correspondiente
      hide(boxAdmin); hide(boxLider); hide(boxAprendiz);
      if (value === 'ADMINISTRADOR') show(boxAdmin);
      if (value === 'LIDER_SEMILLERO') show(boxLider);
      if (value === 'APRENDIZ') show(boxAprendiz);
    }

    // Inicializa estado al cargar (por si el navegador mantiene el valor del select)
    toggleSections(rol.value);
    rol.addEventListener('change', e => toggleSections(e.target.value));
  })();
  </script>
</body>
</html>
