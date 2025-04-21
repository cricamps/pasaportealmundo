<?php
session_start();
include '../config/db.php';

// Solo permitir acceso si es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Validar ID
if (!isset($_GET['id'])) {
    header('Location: admin_paquetes.php');
    exit;
}

$id = intval($_GET['id']);

// Buscar el paquete para conocer la imagen
$sql = "SELECT imagen FROM paquetes WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    header('Location: admin_paquetes.php');
    exit;
}

$paquete = mysqli_fetch_assoc($result);

// Eliminar el paquete de la base de datos
$sql_delete = "DELETE FROM paquetes WHERE id = $id";

if (mysqli_query($conn, $sql_delete)) {
    // Eliminar imagen solo si no es la default
    if ($paquete['imagen'] !== 'default.jpg') {
        $ruta_imagen = '../assets/img/' . $paquete['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen); // Borrar el archivo de imagen
        }
    }
    $_SESSION['mensaje'] = "Paquete eliminado exitosamente.";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "Error al eliminar el paquete.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// Volver a administración
header('Location: admin_paquetes.php');
exit;
?>