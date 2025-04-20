<?php
session_start();
include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('admin_login');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

$error_message = "";

if (isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE usuario = ?";
    $admin = obtenerFila($conn, $sql, [$usuario], "s");

    if ($admin) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['usuario'];
            $log->info("Inicio de sesión de administrador exitoso: " . $usuario);
            header("Location: admin.php");
            exit;
        } else {
            $error_message = "Contraseña incorrecta";
            $log->warning("Intento de inicio de sesión de administrador fallido (contraseña incorrecta): " . $usuario);
        }
    } else {
        $error_message = "Usuario no encontrado";
        $log->warning("Intento de inicio de sesión de administrador fallido (usuario no encontrado): " . $usuario);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-3">Panel de Administración</h3>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" class="form-control" name="usuario" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Ingresar</button>
    </form>
</div>
</body>
</html>