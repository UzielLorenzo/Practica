<?php
// Iniciar sesión para mantener el estado del usuario
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    // Si no hay sesión activa, redirigir al login
    header("Location: index.php");
    exit();
}

// Configuración de la base de datos
$host = 'practicainventario.postgres.database.azure.com';
$dbname = 'db_Inventario';
$username = 'Adminpractica';
$password = 'Alumnos1';

try {
    // Conexión a la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Si el formulario de compra es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad = $_POST['cantidad'];
    $nombre_usuario = $_SESSION['nombre_usuario']; // Usuario autenticado

    try {
        // Validar que el producto existe
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

                $total_precio = $producto['precio_producto'] * $cantidad;
                $mensaje = "Compra realizada con éxito. Producto: " . $producto['nombre_producto'] . ", Cantidad: $cantidad, Precio Total: $total_precio.";
            } else {
                $mensaje = "Usuario no encontrado.";
            }
        } else {
            $mensaje = "Producto no encontrado.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error al procesar la compra: " . $e->getMessage();
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
        }
        h1, h2 {
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
        .mensaje {
            margin-top: 20px;
            padding: 10px;
            background-color: #ffb6c1;
            color: #800000;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Compras</h1>

        <form method="POST">
            <label for="codigo_producto">Código del Producto:</label>
            <input type="number" id="codigo_producto" name="codigo_producto" required>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" min="1" required>

            <button type="submit">Realizar Compra</button>
        </form>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
