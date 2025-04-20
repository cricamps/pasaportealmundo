<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - Pasaporte al Mundo</title>
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
    <h1 class="text-center mb-4" style="color:#003366;">Mi Carrito de Reservas</h1>
    
    <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0) { ?>
        <div class="row g-4">
            <?php foreach ($_SESSION['carrito'] as $paquete) { ?>
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 d-flex">
                    <div class="card flex-fill">
                        <img src="<?php echo !empty($paquete['imagen']) ? 'assets/img/' . $paquete['imagen'] : 'assets/img/default.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($paquete['destino']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="color:#003366;"><?php echo htmlspecialchars($paquete['destino']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
                            <p class="precio mt-auto">$<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-custom">Seguir Reservando</a>
        </div>
    <?php } else { ?>
        <p class="text-center">Tu carrito está vacío.</p>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-custom">Ver paquetes</a>
        </div>
    <?php } ?>
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
