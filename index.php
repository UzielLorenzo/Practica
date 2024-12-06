<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Login y Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Sistema de Login y Registro</h1>

    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <h2>Registro</h2>
    <form action="registro.php" method="POST">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
