<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario creado</title>
</head>
<body style="font-family: Arial, sans-serif; color:#00304D">

    <h2>Bienvenido al Sistema CIDE</h2>

    <p>Hola <strong>{{ $user->nombre }}</strong>,</p>

    <p>
        Tu usuario ha sido creado en el sistema de gestión de semilleros del CIDE.
    </p>

    <p><strong>Datos de acceso:</strong></p>

    <ul>
        <li><strong>Correo:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña temporal:</strong> {{ $password }}</li>
    </ul>

    <p style="color:#b81e1e;">
        ⚠️ Por seguridad, deberás cambiar esta contraseña en tu primer inicio de sesión.
    </p>

    <p>
        Ingresa al sistema desde el siguiente enlace:
    </p>

    <p>
        <a href="{{ url('/login') }}">
            {{ url('/login') }}
        </a>
    </p>

    <br>

    <p>
        Atentamente,<br>
        <strong>Equipo CIDE</strong>
    </p>

</body>
</html>
