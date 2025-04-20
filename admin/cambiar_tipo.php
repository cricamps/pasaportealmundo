<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$id = intval($_GET['id']);
$tipo = ($_GET['tipo'] === 'admin') ? 'admin' : 'cliente';

// Evitar que un admin se desadministre a sí mismo
if ($_SESSION['email'] === $conn->query("SELECT email FROM usuarios WHERE id = $id")->fetch_assoc()['email']) {
    echo "<script>alert('No puedes cambiar tu propio rol.'); window.location.href='usuarios.php';</script>";
    exit;
}

$conn->query("UPDATE usuarios SET tipo = '$tipo' WHERE id = $id");
header("Location: usuarios.php");
exit;
