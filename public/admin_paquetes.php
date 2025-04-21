<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

$sql = "SELECT * FROM paquetes ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Paquetes Turísticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://cdn-icons-png.flaticon.com/512/69/69915.png" alt="Logo">
                Pasaporte al Mundo
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Bienvenido,
                    <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1 class="text-center mb-4" style="color:#003366;">Administrar Paquetes Turísticos</h1>

        <div class="text-end mb-3">
            <a href="crear_paquete.php" class="btn btn-success">+ Crear Nuevo Paquete</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Destino</th>
                            <th>Precio</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['destino']); ?></td>
                                <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    $imagen = !empty($row['imagen']) ? htmlspecialchars($row['imagen']) : 'default.jpg';
                                    ?>
                                    <img src="assets/img/<?php echo $imagen; ?>" alt="Imagen"
                                        style="width: 100px; height: 70px; object-fit: cover;">
                                </td>
                                <td>
                                    <a href="editar_paquete.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-primary btn-sm">Editar</a>
                                    <a href="eliminar_paquete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que deseas eliminar este paquete?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">
                ⚠️ No hay paquetes registrados.
            </div>
        <?php } ?>

    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos
                reservados.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>