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
    $stmt = $pdo->query("SELECT codigo_producto, nombre_producto, precio_producto FROM tb_productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los productos: " . $e->getMessage());
}

// Procesar la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad = $_POST['cantidad'];
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $iva = 0.16; // IVA 16%

    try {
        // Validar el producto
        $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
        $stmt->execute(['codigo_producto' => $codigo_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Obtener el ID del usuario
            $stmt = $pdo->prepare("SELECT numero_idusuario FROM tb_usuario WHERE nombre_usuario = :nombre_usuario");
            $stmt->execute(['nombre_usuario' => $nombre_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Insertar la compra
                $stmt = $pdo->prepare("INSERT INTO tb_compras (numero_idusuario, codigo_producto, cantidad) VALUES (:numero_idusuario, :codigo_producto, :cantidad)");
                $stmt->execute([
                    'numero_idusuario' => $usuario['numero_idusuario'],
                    'codigo_producto' => $codigo_producto,
                    'cantidad' => $cantidad
                ]);

                // Calcular el total
                $subtotal = $producto['precio_producto'] * $cantidad;
                $total = $subtotal * (1 + $iva);

                // Redirigir a la factura
                header("Location: factura.php?nombre_usuario={$nombre_usuario}&producto={$producto['nombre_producto']}&cantidad={$cantidad}&subtotal={$subtotal}&total={$total}");
                exit();
            }
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
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
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
            <label for="codigo_producto">Selecciona un producto:</label>
            <select id="codigo_producto" name="codigo_producto" required>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto['codigo_producto'] ?>">
                        <?= htmlspecialchars($producto['nombre_producto']) ?> - $<?= htmlspecialchars($producto['precio_producto']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" min="1" required>

            <button type="submit">Realizar Compra</button>
        </form>
    </div>
</body>
</html>
