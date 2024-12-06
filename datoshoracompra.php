<?php
session_start();

// Verificar si el usuario ha iniciado sesión
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
    // Conectar a la base de datos
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

try {
    // Consultar todas las compras realizadas
    $stmt = $pdo->query("
        SELECT c.fecha_compra, u.nombre_usuario, p.nombre_producto, c.cantidad
        FROM tb_compras c
        INNER JOIN tb_usuario u ON c.numero_idusuario = u.numero_idusuario
        INNER JOIN tb_productos p ON c.codigo_producto = p.codigo_producto
        ORDER BY c.fecha_compra DESC
    ");
    $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos de las compras: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #d87093;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #d87093;
            color: white;
        }
        .back {
            display: block;
            margin: 20px 0;
            padding: 10px;
            text-align: center;
            background-color: #d87093;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back:hover {
            background-color: #c76182;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Historial de Compras</h1>

        <?php if (!empty($compras)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Compra</th>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td><?= htmlspecialchars($compra['fecha_compra']) ?></td>
                            <td><?= htmlspecialchars($compra['nombre_usuario']) ?></td>
                            <td><?= htmlspecialchars($compra['nombre_producto']) ?></td>
                            <td><?= htmlspecialchars($compra['cantidad']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron compras realizadas.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="back">Regresar al Dashboard</a>
    </div>
</body>
</html>
