<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Login y Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe4e1; /* Rosa claro */
            margin: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff; /* Blanco */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #d87093; /* Rosa oscuro */
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #d87093;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c76182;
        }
        form {
            display: none;
        }
        .visible {
            display: block;
        }
        label {
            margin-top: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Login y Registro</h1>
        <div id="panel">
            <button onclick="mostrarFormulario('login')">Iniciar Sesión</button>
            <button onclick="mostrarFormulario('registro')">Registrarse</button>
        </div>

        <form id="login-form" action="login.php" method="POST">
            <h2>Login</h2>
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <form id="registro-form" action="registro.php" method="POST">
            <h2>Registro</h2>
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Registrarse</button>
        </form>
    </div>

    <script>
        function mostrarFormulario(formulario) {
            document.getElementById('login-form').classList.remove('visible');
            document.getElementById('registro-form').classList.remove('visible');
            document.getElementById('panel').style.display = 'none';

            if (formulario === 'login') {
                document.getElementById('login-form').classList.add('visible');
            } else if (formulario === 'registro') {
                document.getElementById('registro-form').classList.add('visible');
            }
        }
    </script>
</body>
</html>
