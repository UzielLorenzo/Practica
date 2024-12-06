<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_GET['nombre_usuario'] ?? '';
$producto = $_GET['producto'] ?? '';
$cantidad = $_GET['cantidad'] ?? 0;
$subtotal = $_GET['subtotal'] ?? 0;
$total = $_GET['total'] ?? 0;

// Ajustar la fecha al horario de México restando 6 horas
$fecha = new DateTime('now', new DateTimeZone('UTC')); // Hora actual en UTC
$fecha->modify('-6 hours'); // Restar 6 horas
$fecha_formateada = $fecha->format('Y-m-d H:i:s'); // Formatear la fecha
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
        <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha_formateada) ?></p>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($producto) ?></td>
                    <td><?= htmlspecialchars($cantidad) ?></td>
                    <td>$<?= htmlspecialchars(number_format($subtotal, 2)) ?></td>
                    <td>$<?= htmlspecialchars(number_format($total, 2)) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
