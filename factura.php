<?php
require 'vendor/autoload.php'; // Librería Dompdf para generar PDF

use Dompdf\Dompdf;

$usuario = $_GET['usuario'];
$producto = $_GET['producto'];
$cantidad = $_GET['cantidad'];
$subtotal = $_GET['subtotal'];
$total = $_GET['total'];

$dompdf = new Dompdf();
$html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
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
    <h1>Factura</h1>
    <p><strong>Usuario ID:</strong> $usuario</p>
    <p><strong>Producto:</strong> $producto</p>
    <p><strong>Cantidad:</strong> $cantidad</p>
    <p><strong>Subtotal:</strong> $$subtotal</p>
    <p><strong>Total (IVA incluido):</strong> $$total</p>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("factura.pdf", ["Attachment" => false]);
