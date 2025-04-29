<?php
session_start();
include '../config/db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Verificar que haya algo en el carrito
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    $_SESSION['mensaje'] = "Tu carrito está vacío.";
    $_SESSION['tipo_mensaje'] = "warning";
    header('Location: carrito.php');
    exit;
}

// Calcular el total
$total = 0;
$detalles = [];
foreach ($_SESSION['carrito'] as $paquete) {
    $total += $paquete['precio'];
    $detalles[] = $paquete['destino'] . ' ($' . number_format($paquete['precio'], 0, ',', '.') . ')';
}

// Guardar en la base de datos
$usuario_id = $_SESSION['usuario']['id'];
$detalles_texto = implode(", ", $detalles);

$sql = "INSERT INTO pagos (usuario_id, monto_total, detalles, fecha_pago) VALUES (?, ?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ids", $usuario_id, $total, $detalles_texto);
$exito = mysqli_stmt_execute($stmt);

if ($exito) {
    $ultimo_pago_id = mysqli_insert_id($conn);
    // Copiamos carrito para mostrar comprobante antes de vaciarlo
    $carrito_realizado = $_SESSION['carrito'];
    unset($_SESSION['carrito']); // Limpiar carrito SOLO DESPUÉS de guardar
} else {
    $_SESSION['mensaje'] = "Error al procesar el pago.";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: carrito.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <main class="container mt-5">
        <h1 class="text-center mb-4" style="color:#003366;">Comprobante de Pago</h1>

        <div class="card p-4 shadow">
            <h4 class="text-success text-center">Pago realizado con éxito</h4>
            <p><strong>ID de pago:</strong> #<?php echo $ultimo_pago_id; ?></p>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i'); ?></p>
            <p><strong>Total pagado:</strong> $<?php echo number_format($total, 0, ',', '.'); ?></p>
            <p><strong>Paquetes reservados:</strong></p>
            <ul>
                <?php foreach ($carrito_realizado as $paquete) { ?>
                    <li><?php echo htmlspecialchars($paquete['destino']); ?> - $<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></li>
                <?php } ?>
            </ul>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Volver al inicio</a>
                <a href="historial_pagos.php" class="btn btn-secondary ms-2">Ver Historial de Pagos</a>
            </div>
        </div>
    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
