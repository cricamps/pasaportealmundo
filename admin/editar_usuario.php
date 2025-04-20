<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('editar_usuario');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit;
}

$id = $_GET['id'];

// Obtener los datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$usuario = obtenerFila($conn, $sql, [$id], "i");

if (!$usuario) {
    $log->warning("Intento de editar usuario inexistente. ID: " . $id);
    header("Location: usuarios.php");
    exit;
}

$error_message = "";
$success_message = "";

if (isset($_POST['editar'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);

    // Validaciones
    if (empty($nombre) || empty($email)) {
        $error_message = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Formato de correo electrónico inválido";
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
        $result = ejecutarConsulta($conn, $sql, [$nombre, $email, $id], "ssi");

        if ($result) {
            $success_message = "Usuario actualizado correctamente";
            $log->info("Usuario actualizado. ID: " . $id);
            // Actualizar la información en la sesión si es el usuario actual
            if ($_SESSION['email'] === $usuario['email']) {
                $_SESSION['usuario'] = $nombre;
                $_SESSION['email'] = $email;
            }
            // Volver a obtener los datos actualizados para mostrar en el formulario
            $usuario = obtenerFila($conn, "SELECT * FROM usuarios WHERE id = ?", [$id], "i");
        } else {
            $error_message = "Error al actualizar el usuario: " . $conn->error;
            $log->error("Error al actualizar el usuario. ID: " . $id . " Error: " . $conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>✏️ Editar Usuario</h2>
    <a href="usuarios.php" class="btn btn-secondary mb-3">← Volver</a>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
</body>
</html>