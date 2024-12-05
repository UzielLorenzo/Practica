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
