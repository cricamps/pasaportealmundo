<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM paquetes WHERE id = $id";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die('Paquete no encontrado.');
}
$paquete = mysqli_fetch_assoc($result);
$imagen = !empty($paquete['imagen']) ? 'assets/img/' . $paquete['imagen'] : 'assets/img/default.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Paquete</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <?php if (isset($_SESSION['usuario'])) { ?>
                <span class="text-white me-3">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                <a href="perfil.php" class="btn btn-light btn-sm me-2">Mi Perfil</a>
                <a href="carrito.php" class="btn btn-light btn-sm me-2">Ver Carrito</a>
                <a href="historial_pagos.php" class="btn btn-light btn-sm me-2">Mis Pagos</a>
                <?php if ($_SESSION['usuario']['tipo'] === 'admin') { ?>
                    <a href="admin_paquetes.php" class="btn btn-light btn-sm me-2">Administrar Paquetes</a>
                    <a href="admin_usuarios.php" class="btn btn-light btn-sm me-2">Administrar Usuarios</a>
                <?php } ?>
                <a href="logout.php" class="btn btn-light btn-sm">Cerrar Sesión</a>
            <?php } else { ?>
                <a href="login.php" class="btn btn-light btn-sm me-2">Iniciar Sesión</a>
                <a href="registro.php" class="btn btn-light btn-sm">Registrarse</a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- Contenido -->
<main class="container mt-5">
    <div class="card mx-auto" style="max-width: 800px;">
        <img src="<?php echo $imagen; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($paquete['destino']); ?>">
        <div class="card-body text-center">
            <h2 class="card-title mb-3"><?php echo htmlspecialchars($paquete['destino']); ?></h2>
            <p class="card-text"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
            <h4 class="text-primary mt-3">$<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></h4>
            <a href="reservar_paquete.php?id=<?php echo $paquete['id']; ?>" class="btn btn-primary mt-3">Reservar Paquete</a>
        </div>
    </div>

    <!-- Botón Volver -->
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-light btn-sm">← Volver al Inicio</a>
    </div>
</main>

<!-- Footer -->
<footer class="mt-5 py-4" style="background-color: #002244;">
    <div class="container text-center">
        <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
