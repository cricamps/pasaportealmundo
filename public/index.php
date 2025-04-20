<?php
session_start();
include '../config/db.php'; // Tu conexión a la base de datos

// Traemos los paquetes de la base de datos
$sql = "SELECT * FROM paquetes ORDER BY destino ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link href="assets/css/styles.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasaporte al Mundo</title>
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
        <div>
            <?php if (isset($_SESSION['usuario'])) { ?>
                <a href="perfil.php" class="btn btn-custom me-2">Mi Perfil</a>
                <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
            <?php } else { ?>
                <a href="login.php" class="btn btn-navbar me-2">Iniciar Sesión</a>
                <a href="registro.php" class="btn btn-navbar">Registrarse</a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- Contenido principal -->
<main class="container mt-5">
    <h1 class="text-center mb-4" style="color:#003366;">Paquetes Turísticos Disponibles</h1>
    <div class="row g-4">
        <?php
        $delay = 0.1;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imagen = !empty($row['imagen']) ? 'assets/img/' . $row['imagen'] : 'assets/img/default.jpg';
        ?>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 d-flex">
                <a href="detalle_paquete.php?id=<?php echo $row['id']; ?>" class="text-decoration-none flex-grow-1">
                 <div class="card animacion-cards" style="animation-delay: <?php echo $delay; ?>s;">
                    <img src="<?php echo $imagen; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['destino']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title" style="color:#003366;"><?php echo htmlspecialchars($row['destino']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <p class="precio mt-auto">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
            </div>
        </div>
    </a>
</div>
        <?php
                $delay += 0.1;
            }
        } else {
            echo '<p class="text-center">No hay paquetes disponibles por el momento.</p>';
        }
        ?>
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
