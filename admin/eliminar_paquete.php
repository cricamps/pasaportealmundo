<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// Obtener los datos del paquete para el logging
$sql = "SELECT titulo FROM paquetes WHERE id = ?";
$paquete = obtenerFila($conn, $sql, [$id], "i");

if (!$paquete) {
    log_message("warning", "Intento de eliminar paquete inexistente. ID: " . $id);
    header("Location: admin.php");
    exit;
}

$titulo_paquete = $paquete['titulo'];

// Eliminar el paquete
$sql = "DELETE FROM paquetes WHERE id = ?";
$result = ejecutarConsulta($conn, $sql, [$id], "i");

if ($result) {
    log_message("info", "Paquete eliminado. ID: " . $id . " Título: " . $titulo_paquete);
    header("Location: admin.php");
    exit;
} else {
    log_message("error", "Error al eliminar el paquete. ID: " . $id . " Error: " . $conn->error);
    echo "Error al eliminar el paquete."; // Mensaje al usuario (podrías mejorarlo)
}
?>