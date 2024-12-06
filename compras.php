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

                // Calcular los totales
                $subtotal = $producto['precio_producto'] * $cantidad;
                $total = $subtotal * (1 + $iva);

                // Preparar los datos para la factura
                $productos_factura = [
                    [
                        'nombre' => $producto['nombre_producto'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $producto['precio_producto'],
                        'subtotal' => $subtotal
                    ]
                ];

                // Enviar los datos como JSON
                header("Location: factura.php?productos=" . urlencode(json_encode($productos_factura)) . "&nombre_usuario=" . urlencode($nombre_usuario) . "&total=" . $total);
                exit();
            }
        }
    } catch (PDOException $e) {
        die("Error al procesar la compra: " . $e->getMessage());
    }
}
?>
