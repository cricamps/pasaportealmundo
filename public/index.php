<?php
session_start();
include '../config/db.php';

// Asegurar que exista el carrito y los favoritos
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

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
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['usuario'])) { ?>
                <span class="text-white me-3">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
                </span>
                <a href="perfil.php" class="btn btn-custom me-2">Mi Perfil</a>
                <a href="carrito.php" class="btn btn-custom me-2">Ver Carrito</a>
                <a href="historial_pagos.php" class="btn btn-light btn-sm me-2">Mis Pagos</a>

                <?php if ($_SESSION['usuario']['tipo'] == 'admin') { ?>
                    <a href="admin_paquetes.php" class="btn btn-custom me-2">Administrar Paquetes</a>
                    <a href="admin_usuarios.php" class="btn btn-custom me-2">Administrar Usuarios</a>
                <?php } ?>

                <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
            <?php } else { ?>
                <a href="login.php" class="btn btn-custom me-2">Iniciar Sesión</a>
                <a href="registro.php" class="btn btn-custom">Registrarse</a>
            <?php } ?>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['usuario'])) { ?>
    <div id="mensajeBienvenida" class="alert alert-success text-center mt-4" role="alert">
        ¡Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!
    </div>
<?php } ?>

<!-- Contenido principal -->
<main class="container mt-5">
    <h1 class="text-center mb-4" style="color:#003366;">Paquetes Turísticos Disponibles</h1>
    <div class="row g-4">
        <?php
        $delay = 0.1;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imagen = !empty($row['imagen']) ? 'assets/img/' . $row['imagen'] : 'assets/img/default.jpg';
                $isFavorito = in_array($row['id'], $_SESSION['favoritos']);
        ?>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 d-flex">
                <div class="card animacion-cards position-relative" style="animation-delay: <?php echo $delay; ?>s;">
                    <a href="toggle_favorito.php?id=<?php echo $row['id']; ?>" class="position-absolute top-0 start-0 m-2">
                        <?php if ($isFavorito) { ?>
                            <span style="color: gold; font-size: 24px;">★</span>
                        <?php } else { ?>
                            <span style="color: lightgray; font-size: 24px;">☆</span>
                        <?php } ?>
                    </a>

                    <a href="detalle_paquete.php?id=<?php echo $row['id']; ?>" class="text-decoration-none flex-grow-1" style="color: inherit;">
                        <img src="<?php echo $imagen; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['destino']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="color:#003366;"><?php echo htmlspecialchars($row['destino']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                            <p class="precio mt-auto">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
                        </div>
                    </a>
                </div>
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
        <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Hacemos que el mensaje de bienvenida desaparezca después de 3 segundos
setTimeout(function () {
    var mensaje = document.getElementById('mensajeBienvenida');
    if (mensaje) {
        mensaje.style.transition = "opacity 1s ease";
        mensaje.style.opacity = 0;
        setTimeout(function () {
            mensaje.remove();
        }, 1000);
    }
}, 3000);
</script>
</body>
</html>
