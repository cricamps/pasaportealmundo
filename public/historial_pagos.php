<?php

session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

// Traer pagos de este usuario
$sql = "SELECT * FROM pagos WHERE usuario_id = $usuario_id ORDER BY fecha_pago DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Pagos - Pasaporte al Mundo</title>
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
        <h1 class="text-center mb-4" style="color:#003366;">Historial de Pagos</h1>

        <?php
        if (mysqli_num_rows($result) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Pago</th>
                            <th>Fecha de Pago</th>
                            <th>Total Pagado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($pago = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php
                                echo $pago['id']; ?></td>
                                <td><?php
                                echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?></td>
                                <td>$<?php
                                echo number_format($pago['monto_total'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="factura_pago.php?id=<?php
                                    echo $pago['id']; ?>" class="btn btn-success btn-sm mb-1">Descargar Factura PDF</a><br>
                                    <a href="enviar_factura.php?id=<?php
                                    echo $pago['id']; ?>" class="btn btn-primary btn-sm">Enviar por Correo</a>
                                </td>

                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else { ?>
            <div class="alert alert-info text-center">
                Aún no tienes pagos registrados.
            </div>
            <?php
        } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-custom">Volver a Inicio</a>
        </div>
    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php
            echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos
                reservados.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>