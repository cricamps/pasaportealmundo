<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id != $_SESSION['usuario']['id']) {
        // Primero obtenemos el nombre del usuario
        $query_usuario = "SELECT nombre FROM usuarios WHERE id = $id";
        $res_usuario = mysqli_query($conn, $query_usuario);

        if ($res_usuario && mysqli_num_rows($res_usuario) > 0) {
            $usuario = mysqli_fetch_assoc($res_usuario);
            $nombre = htmlspecialchars($usuario['nombre']);

            // Ahora eliminamos
            $sql = "DELETE FROM usuarios WHERE id = $id";
            mysqli_query($conn, $sql);

            $_SESSION['mensaje'] = "Usuario \"$nombre\" eliminado exitosamente.";
            $_SESSION['tipo_mensaje'] = "danger";
        } else {
            $_SESSION['mensaje'] = "El usuario no fue encontrado.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } else {
        $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
}

header('Location: admin_usuarios.php');
exit;
?>
