<?php
session_start();
include '../config/db.php';

// Solo admins
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    if ($accion == 'promover') {
        $sql = "UPDATE usuarios SET tipo = 'admin' WHERE id = $id";
        $_SESSION['mensaje'] = "Usuario promovido a administrador exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } elseif ($accion == 'degradar') {
        $sql = "UPDATE usuarios SET tipo = 'cliente' WHERE id = $id";
        $_SESSION['mensaje'] = "Usuario degradado a cliente exitosamente.";
        $_SESSION['tipo_mensaje'] = "warning";
    }

    if (isset($sql)) {
        mysqli_query($conn, $sql);
    }
}

header('Location: admin_usuarios.php');
exit;
?>
