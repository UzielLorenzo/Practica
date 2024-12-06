<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

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

$factura = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos_seleccionados = $_POST['productos'];
    $productos_cantidad = $_POST['cantidad'];
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $total_sin_iva = 0;

    $factura = "Factura de Compra\n";
    $factura .= "Usuario: " . htmlspecialchars($nombre_usuario) . "\n";
    $factura .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
    $factura .= "---------------------------------------------\n";
    $factura .= "Producto\tCantidad\tPrecio Unitario\tSubtotal\n";

    foreach ($productos_seleccionados as $index => $codigo_producto) {
        $cantidad = $productos_cantidad[$index];

        // Obtener información del producto
        $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
        $stmt->execute(['codigo_producto' => $codigo_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $subtotal = $producto['precio_producto'] * $cantidad;
            $total_sin_iva += $subtotal;

            $factura .= $producto['nombre_producto'] . "\t" .
                        $cantidad . "\t" .
                        "$" . number_format($producto['precio_producto'], 2) . "\t" .
                        "$" . number_format($subtotal, 2) . "\n";
        }
    }

    $iva = $total_sin_iva * 0.16;
    $total_con_iva = $total_sin_iva + $iva;

    $factura .= "---------------------------------------------\n";
    $factura .= "Total sin IVA: $" . number_format($total_sin_iva, 2) . "\n";
    $factura .= "IVA (16%): $" . number_format($iva, 2) . "\n";
    $factura .= "Total con IVA: $" . number_format($total_con_iva, 2) . "\n";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffe4e1; /* Rosa claro */
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff; /* Blanco */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #d87093; /* Rosa oscuro */
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .factura {
            margin-top: 20px;
            padding: 10px;
            background-color: #ffb6c1;
            color: #800000;
            white-space: pre-wrap;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Compras</h1>

        <form method="POST">
            <label for="productos">Selecciona los productos:</label>
            <div id="productos">
                <?php
                $stmt = $pdo->query("SELECT * FROM tb_productos");
                while ($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div>';
                    echo '<input type="checkbox" name="productos[]" value="' . $producto['codigo_producto'] . '">';
                    echo htmlspecialchars($producto['nombre_producto']) . ' - $' . number_format($producto['precio_producto'], 2);
                    echo '<br>Cantidad: <input type="number" name="cantidad[]" min="1" value="1">';
                    echo '</div><br>';
                }
                ?>
            </div>
            <button type="submit">Realizar Compra</button>
        </form>

        <?php if ($factura): ?>
            <div class="factura">
                <?= htmlspecialchars($factura) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
