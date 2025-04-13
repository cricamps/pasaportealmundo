<?php
include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM paquetes WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Paquete no encontrado.";
    exit;
}

$paquete = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $paquete['titulo']; ?> - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="index.php" class="btn btn-outline-secondary mb-3">← Volver</a>
    <div class="card shadow-lg">
        <?php if (!empty($paquete['imagen'])): ?>
        <img src="assets/img/<?php echo $paquete['imagen']; ?>" class="card-img-top" style="height: 300px; object-fit: cover;" alt="Imagen del Paquete">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title"><?php echo $paquete['titulo']; ?></h2>
            <p class="card-text"><?php echo $paquete['descripcion']; ?></p>
            <p><strong>Fecha disponible:</strong> <?php echo date("d/m/Y", strtotime($paquete['fecha_disponible'])); ?></p>
            <p><strong>Precio:</strong> $<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></p>
        </div>
    </div>
</div>
</body>
</html>
