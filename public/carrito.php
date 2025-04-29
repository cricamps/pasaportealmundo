<?php
session_start();

// Manejar eliminación de paquete
if (isset($_GET['eliminar'])) {
    $indice = intval($_GET['eliminar']);
    if (isset($_SESSION['carrito'][$indice])) {
        unset($_SESSION['carrito'][$indice]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reordenamos
    }
    header('Location: carrito.php');
    exit;
}
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

    <?php if (!empty($_SESSION['carrito'])) { ?>
        <div class="row g-4">
            <?php foreach ($_SESSION['carrito'] as $indice => $paquete) { ?>
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 d-flex">
                    <div class="card flex-fill position-relative">
                        <img src="<?php echo !empty($paquete['imagen']) ? 'assets/img/' . $paquete['imagen'] : 'assets/img/default.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($paquete['destino']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="color:#003366;"><?php echo htmlspecialchars($paquete['destino']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($paquete['descripcion']); ?></p>
                            <p class="precio mt-auto">$<?php echo number_format($paquete['precio'], 0, ',', '.'); ?></p>
                        </div>
                        <a href="carrito.php?eliminar=<?php echo $indice; ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">X</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php
        $total = 0;
        foreach ($_SESSION['carrito'] as $paquete) {
            $total += $paquete['precio'];
        }
        ?>

        <div class="text-center mt-5">
            <h3 style="color:#003366;">Total a pagar: $<?php echo number_format($total, 0, ',', '.'); ?></h3>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="index.php" class="btn btn-primary">← Seguir Reservando</a>
                <a href="procesar_pago.php" class="btn btn-success">Pagar Ahora 💳</a>
            </div>
        </div>

    <?php } else { ?>
        <div class="text-center">
            <img src="assets/img/default.jpg" alt="Carrito vacío" style="width:100px; height:100px; margin-bottom: 20px;">
            <h4 style="color: #003366;">¡Tu carrito está vacío!</h4>
            <p>Es momento de encontrar tu próximo destino.</p>
            <a href="index.php" class="btn btn-primary mt-3">Ver paquetes</a>
        </div>
    <?php } ?>
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
