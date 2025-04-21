<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if (isset($_POST['nueva_contrasena'])) {
    $nueva = trim($_POST['nueva_contrasena']);
    $hash = password_hash($nueva, PASSWORD_DEFAULT);

    // Buscamos el nombre del usuario
    $query_usuario = "SELECT nombre FROM usuarios WHERE id = $id";
    $res_usuario = mysqli_query($conn, $query_usuario);

    if ($res_usuario && mysqli_num_rows($res_usuario) > 0) {
        $usuario = mysqli_fetch_assoc($res_usuario);
        $nombre = htmlspecialchars($usuario['nombre']);

        // Actualizamos la contraseña
        $sql = "UPDATE usuarios SET password = '$hash' WHERE id = $id";
        mysqli_query($conn, $sql);

        $_SESSION['mensaje'] = "La contraseña de \"$nombre\" fue actualizada exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "El usuario no fue encontrado.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    header('Location: admin_usuarios.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container mt-5">
    <h1 class="text-center mb-4" style="color:#003366;">Cambiar Contraseña de Usuario</h1>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST">
                <div class="mb-3">
                    <label for="nueva_contrasena" class="form-label">Nueva Contraseña</label>
                    <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Actualizar Contraseña</button>
                <a href="admin_usuarios.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>

