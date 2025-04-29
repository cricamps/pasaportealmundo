<?php
session_start();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_paquete = intval($_GET['id']);

// Asegurar que el array de favoritos exista
if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

// Alternar favorito
if (in_array($id_paquete, $_SESSION['favoritos'])) {
    // Si ya es favorito, lo quitamos
    $_SESSION['favoritos'] = array_diff($_SESSION['favoritos'], [$id_paquete]);
} else {
    // Si no es favorito, lo agregamos
    $_SESSION['favoritos'][] = $id_paquete;
}

// Vuelve a la página anterior (donde estaba)
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
