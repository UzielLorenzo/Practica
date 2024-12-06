<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

// Configuración de la base de datos
$host = 'practicainventario.postgres.database.azure.com';
$dbname = 'db_Inventario';
$username = 'Adminpractica';
$password = 'Alumnos1';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Obtener datos del usuario y la compra
$nombre_usuario = $_GET['nombre_usuario'] ?? 'Usuario desconocido';
$codigo_producto = $_GET['codigo_producto'] ?? null;
$cantidad = $_GET['cantidad'] ?? 0;
$total = $_GET['total'] ?? 0;
$fecha = date('Y-m-d H:i:s');

// Obtener el nombre del producto desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
    $stmt->execute(['codigo_producto' => $codigo_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Error: Producto no encontrado en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del producto: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe4e1;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #d87093;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #d87093;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Factura</h1>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($nombre_usuario) ?></p>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha) ?></p>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Total con IVA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($producto['nombre_producto']) ?></td>
                    <td><?= htmlspecialchars($cantidad) ?></td>
                    <td>$<?= htmlspecialchars(number_format($producto['precio_producto'], 2)) ?></td>
                    <td>$<?= htmlspecialchars(number_format($producto['precio_producto'] * $cantidad, 2)) ?></td>
                    <td>$<?= htmlspecialchars(number_format($total, 2)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
