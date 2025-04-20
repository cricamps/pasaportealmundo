<?php
include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('paquete');
$log->pushHandler(new StreamHandler(__DIR__ . '../logs/app.log', Logger::WARNING));

if (!isset($_GET['id'])) {
    $log->warning("Intento de acceder a paquete sin ID.");
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM paquetes WHERE id = ?";
$paquete = obtenerFila($conn, $sql, [$id], "i");

if (!$paquete) {
    $log->error("Paquete no encontrado. ID: " . $id);
    echo "Paquete no encontrado."; // Mensaje al usuario (mejorar)
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($paquete['titulo']); ?> - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="index.php" class="btn btn-outline-secondary mb-3">← Volver</a>
    <div class="card shadow-lg">
        <?php if (!empty($paquete['imagen'])): ?>
            <img src="assets/img/<?php echo $paquete['imagen']; ?>" class="card-img-top"
                 style="height: 300px; object-fit: cover;" alt="Imagen del Paquete">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title"><?php echo htmlspecialchars($paquete['titulo']); ?></h2>
            <p class="card-text"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
            <p class="card-text">Fecha: <?php echo date("d/m/Y", strtotime($paquete['fecha_disponible'])); ?></p>
            <p class="card-text">Precio: $<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></p>
            </div>
    </div>
</div>
</body>
</html>