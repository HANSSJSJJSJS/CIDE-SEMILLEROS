<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso a la plataforma</title>
</head>
<body>
    <h2>Bienvenido/a {{ $user->nombre }}</h2>

    <p>Tu cuenta ha sido creada en la plataforma CIDE.</p>

    <p><strong>Correo:</strong> {{ $user->email }}</p>
    <p><strong>Contraseña:</strong> {{ $password }}</p>

    <p>Por seguridad, cambia tu contraseña al iniciar sesión.</p>
</body>
</html>
