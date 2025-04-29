<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Verificar que exista el ID de pago recién realizado
if (!isset($_GET['pago_id'])) {
    header('Location: index.php');
    exit;
}

$pago_id = intval($_GET['pago_id']);

// Consultar el pago
$sql = "SELECT * FROM pagos WHERE id = $pago_id AND usuario_id = " . $_SESSION['usuario']['id'];
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['mensaje'] = "Pago no encontrado.";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: index.php');
    exit;
}

$pago = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <main class="container mt-5">
        <div class="card text-center p-5 shadow">
            <h1 class="mb-4" style="color: #28a745;">¡Pago Exitoso!</h1>

            <p><strong>N° de Pago:</strong> <?php echo $pago['id']; ?></p>
            <p><strong>Total pagado:</strong> $<?php echo number_format($pago['total'], 0, ',', '.'); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?></p>

            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
            </div>
        </div>
    </main>
</body>
</html>
