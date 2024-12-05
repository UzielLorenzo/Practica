# Practica
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Sistema de Compras</h1>
    <form action="compras.php" method="POST">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required><br><br>

        <label for="codigo_producto">Código del Producto:</label>
        <input type="number" id="codigo_producto" name="codigo_producto" required><br><br>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" min="1" required><br><br>

        <button type="submit">Realizar Compra</button>
    </form>

    <h2>Compras Realizadas</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre de Usuario</th>
                <th>Fecha de Compra</th>
                <th>Producto</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody id="compra-detalle">
            <!-- Aquí se insertarán las compras realizadas -->
        </tbody>
    </table>
</body>
</html>
Código PHP (compras.php)
php
Copiar código
<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'nombre_base_datos'; // Cambia esto por el nombre de tu base de datos
$username = 'tu_usuario'; // Cambia esto por tu usuario de la base de datos
$password = 'tu_contraseña'; // Cambia esto por tu contraseña de la base de datos

try {
    // Conexión a la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad = $_POST['cantidad'];

    try {
        // Obtener el precio del producto
        $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
        $stmt->execute(['codigo_producto' => $codigo_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Insertar la compra en la base de datos
            $stmt = $pdo->prepare(
                "INSERT INTO tb_compras (numero_idusuario, codigo_producto, cantidad) VALUES (
                    (SELECT numero_idusuario FROM tb_usuario WHERE nombre_usuario = :nombre_usuario LIMIT 1),
                    :codigo_producto, :cantidad
                )"
            );
            $stmt->execute([
                'nombre_usuario' => $nombre_usuario,
                'codigo_producto' => $codigo_producto,
                'cantidad' => $cantidad
            ]);

            // Mostrar datos en la tabla visible
            echo "<tr>";
            echo "<td>" . htmlspecialchars($nombre_usuario) . "</td>";
            echo "<td>" . htmlspecialchars(date('Y-m-d H:i:s')) . "</td>";
            echo "<td>" . htmlspecialchars($producto['nombre_producto']) . "</td>";
            echo "<td>" . htmlspecialchars($producto['precio_producto'] * $cantidad) . "</td>";
            echo "</tr>";
        } else {
            echo "Producto no encontrado.";
        }
    } catch (PDOException $e) {
        die("Error al realizar la compra: " . $e->getMessage());
    }
}
?>
