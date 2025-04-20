<?php
session_start();
include '../config/db.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM paquetes WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $paquete = mysqli_fetch_assoc($result);
        
        // Inicializamos el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Agregamos el paquete al carrito
        $_SESSION['carrito'][] = $paquete;
        
        // Redireccionamos al carrito
        header("Location: carrito.php");
        exit;
    } else {
        echo "Paquete no encontrado.";
        exit;
    }
} else {
    echo "ID de paquete no especificado.";
    exit;
}
?>
