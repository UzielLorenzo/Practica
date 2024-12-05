<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'nombre_base_datos'; // Cambia este valor por el nombre real de tu base de datos
$username = 'tu_usuario'; // Cambia este valor por tu usuario de base de datos
$password = 'tu_contraseña'; // Cambia este valor por tu contraseña de base de datos

try {
    // Conectar a la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad = $_POST['cantidad'];

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

                // Mostrar detalles de la compra
                $total_precio = $producto['precio_producto'] * $cantidad;
                echo json_encode([
                    'nombre_usuario' => $nombre_usuario,
                    'fecha_compra' => date('Y-m-d H:i:s'),
                    'producto' => $producto['nombre_producto'],
                    'precio_total' => $total_precio
                ]);
            } else {
                echo json_encode(['error' => 'Usuario no encontrado.']);
            }
        } else {
            echo json_encode(['error' => 'Producto no encontrado.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al procesar la compra: ' . $e->getMessage()]);
    }
}
?>