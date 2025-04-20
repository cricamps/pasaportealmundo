<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('perfil');
$log->pushHandler(new StreamHandler(__DIR__ . '../logs/app.log', Logger::WARNING));

$error_message = ""; // Para mensajes de error/éxito

// Obtener datos del usuario desde la base de datos
$email = $_SESSION['email'];
$sql = "SELECT * FROM usuarios WHERE email = ?";
$usuario = obtenerFila($conn, $sql, [$email], "s");

if (!$usuario) {
    $error_message = "Error al obtener los datos del usuario";
    $log->error("Error al obtener los datos del usuario: " . $conn->error);
}

if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Formato de correo electrónico inválido";
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE email = ?";
        $result = ejecutarConsulta($conn, $sql, [$nombre, $email, $_SESSION['email']], "sss");

        if ($result) {
            $_SESSION['usuario'] = $nombre;
            $_SESSION['email'] = $email;
            $error_message = "Datos actualizados correctamente";
            $log->info("Perfil de usuario actualizado: " . $_SESSION['email']);
        } else {
            $error_message = "Error al actualizar los datos";
            $log->error("Error al actualizar los datos del usuario: " . $conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">🧾 Mi Perfil</h2>
    <?php if ($error_message): ?>
        <div class="alert alert-info"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
        </div>

        <button type="submit" name="guardar" class="btn btn-primary">Guardar cambios</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>