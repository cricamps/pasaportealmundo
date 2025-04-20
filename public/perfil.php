<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
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
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
            </span>
            <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<main class="container mt-5">
    <h1 class="text-center mb-4" style="color:#003366;">Mi Perfil</h1>
    
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title text-center" style="color:#003366;"><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h5>
            <p class="text-center mb-3"><?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
            <p class="text-center"><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario']['tipo']); ?></p>
            <div class="d-grid mt-4">
                <a href="index.php" class="btn btn-custom">Volver al Inicio</a>
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
