<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);

// Buscar la imagen asociada (para borrarla si existe)
$consulta = $conn->query("SELECT imagen FROM paquetes WHERE id = $id");
if ($consulta->num_rows === 0) {
    echo "Paquete no encontrado.";
    exit;
}

$paquete = $consulta->fetch_assoc();
$imagen = $paquete['imagen'];

// Eliminar imagen del servidor si existe
if (!empty($imagen) && file_exists("assets/img/$imagen")) {
    unlink("assets/img/$imagen");
}

// Eliminar el paquete de la base de datos
$conn->query("DELETE FROM paquetes WHERE id = $id");

echo "<script>alert('Paquete eliminado correctamente'); window.location.href='admin.php';</script>";
?>
