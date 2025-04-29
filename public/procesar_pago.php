<?php
session_start();
include '../config/db.php';

// Verificar que el carrito no esté vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit;
}

// Calcular el monto total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'];
}

// Insertar el pago en la tabla pagos
$usuario_id = $_SESSION['usuario']['id'];
$sql_pago = "INSERT INTO pagos (usuario_id, monto_total, fecha_pago) VALUES (?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql_pago);
mysqli_stmt_bind_param($stmt, "id", $usuario_id, $total);
mysqli_stmt_execute($stmt);

// Obtener el ID del pago recién insertado
$pago_id = mysqli_insert_id($conn);

// Insertar los detalles del pago en pago_detalle
foreach ($_SESSION['carrito'] as $item) {
    $paquete_id = $item['id'];
    $cantidad = 1; // Siempre 1 por paquete en este caso
    $precio_unitario = $item['precio'];

    $sql_detalle = "INSERT INTO pago_detalle (pago_id, paquete_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_detalle = mysqli_prepare($conn, $sql_detalle);
    mysqli_stmt_bind_param($stmt_detalle, "iiid", $pago_id, $paquete_id, $cantidad, $precio_unitario);
    mysqli_stmt_execute($stmt_detalle);
}

// Vaciar el carrito
unset($_SESSION['carrito']);

// Redireccionar al comprobante
header("Location: confirmacion_pago.php?id=$pago_id");
exit;
?>
