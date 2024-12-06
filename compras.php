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

// Obtener los productos
try {
    $stmt = $pdo->query("SELECT codigo_producto, nombre_producto, precio_producto, url_imagen FROM tb_productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los productos: " . $e->getMessage());
}

// Procesar la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos_seleccionados = $_POST['productos'];
    $cantidades = $_POST['cantidades'];
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $iva = 0.16; // IVA 16%

    try {
        $total = 0;

        // Obtener el ID del usuario
        $stmt = $pdo->prepare("SELECT numero_idusuario FROM tb_usuario WHERE nombre_usuario = :nombre_usuario");
        $stmt->execute(['nombre_usuario' => $nombre_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            foreach ($productos_seleccionados as $index => $codigo_producto) {
                $cantidad = $cantidades[$index];

                // Validar el producto
                $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
                $stmt->execute(['codigo_producto' => $codigo_producto]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto) {
                    // Insertar la compra
                    $stmt = $pdo->prepare("INSERT INTO tb_compras (numero_idusuario, codigo_producto, cantidad) VALUES (:numero_idusuario, :codigo_producto, :cantidad)");
                    $stmt->execute([
                        'numero_idusuario' => $usuario['numero_idusuario'],
                        'codigo_producto' => $codigo_producto,
                        'cantidad' => $cantidad
                    ]);

                    $subtotal = $producto['precio_producto'] * $cantidad;
                    $total += $subtotal * (1 + $iva);
                }
            }

            // Redirigir a la factura
            header("Location: factura.php?nombre_usuario={$nombre_usuario}&total={$total}");
            exit();
        }
    } catch (PDOException $e) {
        die("Error al procesar la compra: " . $e->getMessage());
    }
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
        .product {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .product img {
            width: 90px; /* Incrementado un 80% */
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
        }
        .product input[type="number"] {
            width: 50px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Compras</h1>

        <form method="POST">
            <label>Selecciona tus productos:</label>
            <div id="productos-lista">
                <?php foreach ($productos as $producto): ?>
                    <div class="product">
                        <img src="<?= htmlspecialchars($producto['url_imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre_producto']) ?>">
                        <label>
                            <input type="checkbox" name="productos[]" value="<?= $producto['codigo_producto'] ?>">
                            <?= htmlspecialchars($producto['nombre_producto']) ?> - $<?= htmlspecialchars($producto['precio_producto']) ?>
                        </label>
                        <input type="number" name="cantidades[]" min="1" value="1">
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit">Realizar Compra</button>
        </form>
    </div>
</body>
</html>
