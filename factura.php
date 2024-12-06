<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

// Decodificar los datos enviados como JSON
$productos = json_decode($_GET['productos'], true);
$nombre_usuario = $_GET['nombre_usuario'] ?? 'Usuario desconocido';
$total = $_GET['total'] ?? 0;
$fecha = date('Y-m-d H:i:s');
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= htmlspecialchars($producto['cantidad']) ?></td>
                        <td>$<?= htmlspecialchars(number_format($producto['precio_unitario'], 2)) ?></td>
                        <td>$<?= htmlspecialchars(number_format($producto['subtotal'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total con IVA:</strong> $<?= htmlspecialchars(number_format($total, 2)) ?></p>
    </div>
</body>
</html>
