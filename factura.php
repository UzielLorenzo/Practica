<?php
require __DIR__ . '/vendor/autoload.php'; // Cargar la librería mPDF

session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

// Configuración de la base de datos
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

// Datos del usuario y de la compra
$nombre_usuario = $_GET['nombre_usuario'] ?? 'Usuario desconocido';
$codigo_producto = $_GET['codigo_producto'] ?? null;
$cantidad = $_GET['cantidad'] ?? 0;
$fecha = date('Y-m-d H:i:s');

// Obtener información del usuario desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT numero_idusuario FROM tb_usuario WHERE nombre_usuario = :nombre_usuario");
    $stmt->execute(['nombre_usuario' => $nombre_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Error: Usuario no encontrado en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del usuario: " . $e->getMessage());
}

// Obtener datos del producto desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT nombre_producto, precio_producto FROM tb_productos WHERE codigo_producto = :codigo_producto");
    $stmt->execute(['codigo_producto' => $codigo_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Error: Producto no encontrado en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del producto: " . $e->getMessage());
}

// Calcular el subtotal y total con IVA
$precio_unitario = $producto['precio_producto'];
$subtotal = $precio_unitario * $cantidad;
$iva = $subtotal * 0.16; // 16% IVA
$total = $subtotal + $iva;

use Mpdf\Mpdf;

$mpdf = new Mpdf();

$html = "
<h1>Factura de Compra</h1>
<p><strong>Usuario:</strong> {$nombre_usuario}</p>
<p><strong>ID Usuario:</strong> {$usuario['numero_idusuario']}</p>
<p><strong>Fecha:</strong> {$fecha}</p>
<table border='1' style='width: 100%; border-collapse: collapse;'>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
            <th>IVA</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{$producto['nombre_producto']}</td>
            <td>{$cantidad}</td>
            <td>$" . number_format($precio_unitario, 2) . "</td>
            <td>$" . number_format($subtotal, 2) . "</td>
            <td>$" . number_format($iva, 2) . "</td>
            <td>$" . number_format($total, 2) . "</td>
        </tr>
    </tbody>
</table>
";

$mpdf->WriteHTML($html);
$mpdf->Output('factura.pdf', 'D'); // Descarga directa del PDF
?>
