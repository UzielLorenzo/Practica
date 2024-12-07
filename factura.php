<?php 
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_GET['nombre_usuario'] ?? '';
$productos_comprados = json_decode($_GET['productos'] ?? '[]', true);
$total = $_GET['total'] ?? 0;
$pdf_file = $_GET['pdf'] ?? '';

// Ajustar la fecha al horario de México
$fecha = new DateTime('now', new DateTimeZone('UTC'));
$fecha->modify('-6 hours');
$fecha_formateada = $fecha->format('Y-m-d H:i:s');

// Validar si los productos fueron enviados correctamente
if (empty($productos_comprados) || !is_array($productos_comprados)) {
    die("Error: No se encontraron productos para mostrar en la factura.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

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
        .thank-you {
            text-align: center;
            color: #d87093;
            font-size: 400%;
            font-weight: bold;
            margin-top: 20px;
            animation: bounce 2s infinite;
        }
        .pdf-link {
            text-align: center;
            margin-top: 20px;
        }
        .pdf-link a {
            text-decoration: none;
            color: #d87093;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Factura</h1>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($nombre_usuario) ?></p>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($fecha_formateada) ?></p>

        <?php if (!empty($productos_comprados) && is_array($productos_comprados)): ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_comprados as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['cantidad']) ?></td>
                    <td>$<?= htmlspecialchars(number_format($producto['subtotal'], 2)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td>$<?= htmlspecialchars(number_format($total, 2)) ?></td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
            <p>No se encontraron productos en la factura.</p>
        <?php endif; ?>

        <div class="pdf-link">
            <?php if (!empty($pdf_file)): ?>
                <p><a href="<?= htmlspecialchars($pdf_file) ?>" download>Descargar Factura en PDF</a></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="thank-you">¡Gracias por tu compra!</div>
</body>
</html>
