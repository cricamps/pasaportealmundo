<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];

$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario']['id'];

    // Insertar en pagos
    $sql_pago = "INSERT INTO pagos (usuario_id, total) VALUES ($usuario_id, $total)";
    if (mysqli_query($conn, $sql_pago)) {
        $pago_id = mysqli_insert_id($conn);

        // Insertar en pago_detalle
        foreach ($carrito as $item) {
            $paquete_id = intval($item['id']);
            $precio = floatval($item['precio']);
            $sql_detalle = "INSERT INTO pago_detalle (pago_id, paquete_id, cantidad, precio_unitario) VALUES ($pago_id, $paquete_id, 1, $precio)";
            mysqli_query($conn, $sql_detalle);
        }

        // Vaciar carrito
        unset($_SESSION['carrito']);

        unset($_SESSION['carrito']); // Vaciar carrito

        header('Location: confirmacion_pago.php?pago_id=' . $pago_id);
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al procesar el pago.";
        $_SESSION['tipo_mensaje'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmar Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main class="container mt-5">
        <h1 class="text-center mb-4" style="color:#003366;">Confirmar Pago</h1>

        <div class="card p-4 shadow">
            <h4>Resumen del carrito:</h4>
            <ul class="list-group mb-3">
                <?php foreach ($carrito as $item) { ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <?php echo htmlspecialchars($item['destino']); ?>
                        <span>$<?php echo number_format($item['precio'], 0, ',', '.'); ?></span>
                    </li>
                <?php } ?>
            </ul>

            <h5>Total a pagar: <strong>$<?php echo number_format($total, 0, ',', '.'); ?></strong></h5>

            <form method="post">
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Confirmar Pago</button>
                    <a href="carrito.php" class="btn btn-secondary">Volver al Carrito</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>