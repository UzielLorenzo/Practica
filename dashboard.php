<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
} 

// Obtener el nombre del usuario
$nombre_usuario = $_SESSION['nombre_usuario'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe4e1; /* Rosa claro */
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff; /* Blanco */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #d87093; /* Rosa oscuro */
        }
        a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #d87093;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background-color: #c76182;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?= htmlspecialchars($nombre_usuario) ?>!</h1>
        <p>Selecciona una opción para continuar:</p>
        <a href="compras.php">Ir al Sistema de Compras</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</body>
</html>
