<?php
session_start();
include '../config/db.php';

// Solo admins
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id != $_SESSION['usuario']['id']) {
        $sql = "DELETE FROM usuarios WHERE id = $id";
        mysqli_query($conn, $sql);
        $_SESSION['mensaje'] = "Usuario eliminado exitosamente.";
        $_SESSION['tipo_mensaje'] = "danger";
    } else {
        $_SESSION['mensaje'] = "No puedes eliminar tu propio usuario.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
}

header('Location: admin_usuarios.php');
exit;
?>
