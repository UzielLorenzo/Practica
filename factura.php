<?php
require __DIR__ . '/vendor/autoload.php'; // Cargar la librería mPDF

session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre_usuario'];
$fecha = date('Y-m-d H:i:s');
$productos = json_decode($_POST['productos'], true);
$total_sin_iva = $_POST['total_sin_iva'];
$total_con_iva = $_POST['total_con_iva'];

use Mpdf\Mpdf;

$mpdf = new Mpdf();

$html = "
<h1>Factura de Compra</h1>
<p><strong>Usuario:</strong> {$nombre_usuario}</p>
<p><strong>Fecha:</strong> {$fecha}</p>
<table border='1' style='width: 100%; border-collapse: collapse;'>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
";

foreach ($productos as $producto) {
    $html .= "
        <tr>
            <td>{$producto['nombre']}</td>
            <td>{$producto['cantidad']}</td>
            <td>\${$producto['precio_unitario']}</td>
            <td>\${$producto['subtotal']}</td>
        </tr>
    ";
}

$html .= "
    </tbody>
</table>
<p><strong>Total (sin IVA):</strong> \${$total_sin_iva}</p>
<p><strong>Total (con IVA):</strong> \${$total_con_iva}</p>
";

$mpdf->WriteHTML($html);
$mpdf->Output('factura.pdf', 'D'); // Descarga directa del PDF
?>
