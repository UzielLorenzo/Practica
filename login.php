<?php
session_start(); // Mantener la sesión activa

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
    $contrasena = $_POST['contrasena'];

    try {
        // Validar usuario y contraseña
        $stmt = $pdo->prepare("SELECT contrasena FROM tb_usuario WHERE nombre_usuario = :nombre_usuario");
        $stmt->execute(['nombre_usuario' => $nombre_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['nombre_usuario'] = $nombre_usuario; // Guardar usuario en sesión
            header("Location: dashboard.php"); // Redirigir al dashboard
            exit();
        } else {
            echo "Usuario o contraseña no válidos. <a href='index.php'>Inténtalo de nuevo</a>.";
        }
    } catch (PDOException $e) {
        die("Error al validar al usuario: " . $e->getMessage());
    }
}
?>
