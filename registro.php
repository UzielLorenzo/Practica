<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO tb_usuario (nombre_usuario, contrasena, fecha_registro) VALUES (:nombre_usuario, :contrasena, CURRENT_TIMESTAMP)");
        $stmt->execute([
            'nombre_usuario' => $nombre_usuario,
            'contrasena' => $contrasena,
        ]);

        echo "Usuario registrado con éxito. <a href='index.php'>Inicia sesión aquí</a>.";
    } catch (PDOException $e) {
        die("Error al registrar al usuario: " . $e->getMessage());
    }
}
?>
