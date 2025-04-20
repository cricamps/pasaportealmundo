<?php
session_start();
include '../config/db.php'; // Ajusta si la conexión está en otra carpeta

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM paquetes WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $paquete = mysqli_fetch_assoc($result);
    } else {
        echo "Paquete no encontrado.";
        exit;
    }
} else {
    echo "ID de paquete no especificado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Paquete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="https://cdn-icons-png.flaticon.com/512/69/69915.png" alt="Logo">
            Pasaporte al Mundo
        </a>
    </div>
</nav>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <img src="<?php echo !empty($paquete['imagen']) ? 'assets/img/' . $paquete['imagen'] : 'assets/img/default.jpg'; ?>" class="card-img-top mb-4" alt="<?php echo htmlspecialchars($paquete['destino']); ?>">
                <div class="card-body text-center">
                    <h2 class="card-title" style="color:#003366;"><?php echo htmlspecialchars($paquete['destino']); ?></h2>
                    <p class="card-text my-3"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
                    <h4 class="precio mb-4">$<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></h4>
                    <div class="d-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-custom">Volver al inicio</a>
                    <a href="reservar_paquete.php?id=<?php echo $paquete['id']; ?>" class="btn btn-primary">Reservar Paquete</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="mt-5 py-4" style="background-color: #002244;">
    <div class="container text-center">
        <p class="mb-0 text-white small">© 2025 Pasaporte al Mundo - Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
