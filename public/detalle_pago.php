<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Verificar que venga el ID de pago
if (!isset($_GET['id'])) {
    header('Location: historial_pagos.php');
    exit;
}

$pago_id = intval($_GET['id']);

// Verificar que el pago le pertenezca al usuario
$sql_pago = "SELECT * FROM pagos WHERE id = $pago_id AND usuario_id = " . $_SESSION['usuario']['id'];
$result_pago = mysqli_query($conn, $sql_pago);

if (!$result_pago || mysqli_num_rows($result_pago) == 0) {
    header('Location: historial_pagos.php');
    exit;
}

$pago = mysqli_fetch_assoc($result_pago);

// Obtener los detalles de paquetes
$sql_detalles = "SELECT pd.*, p.destino 
                 FROM pago_detalles pd
                 INNER JOIN paquetes p ON pd.paquete_id = p.id
                 WHERE pd.pago_id = $pago_id";
$result_detalles = mysqli_query($conn, $sql_detalles);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg" style="background-color: #002244;">
        <div class="container">
            <a class="navbar-brand text-white" href="index.php">
                Pasaporte al Mundo
            </a>
            <div class="d-flex">
                <a href="historial_pagos.php" class="btn btn-light btn-sm me-2">Historial de Pagos</a>
                <a href="logout.php" class="btn btn-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1 class="text-center mb-4" style="color: #003366;">Detalle del Pago #<?php echo $pago['id']; ?></h1>

        <div class="mb-4 text-center">
            <p><strong>Total Pagado:</strong> $<?php echo number_format($pago['total'], 0, ',', '.'); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?></p>
        </div>

        <?php if (mysqli_num_rows($result_detalles) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Destino</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <div class="text-center mb-4">
                        <a href="factura_pago.php?id=<?php echo $pago['id']; ?>" class="btn btn-success">
                            Descargar Factura PDF
                        </a>
                    </div>

                    <tbody>
                        <?php while ($detalle = mysqli_fetch_assoc($result_detalles)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['destino']); ?></td>
                                <td>$<?php echo number_format($detalle['precio_unitario'], 0, ',', '.'); ?></td>
                                <td><?php echo $detalle['cantidad']; ?></td>
                                <td>$<?php echo number_format($detalle['precio_unitario'] * $detalle['cantidad'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">
                No se encontraron detalles para este pago.
            </div>
        <?php } ?>
    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos
                reservados.</p>
        </div>
    </footer>

</body>

</html>