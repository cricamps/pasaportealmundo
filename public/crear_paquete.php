<?php
session_start();
include '../config/db.php';

// Solo admins pueden acceder
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Si envían el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destino = mysqli_real_escape_string($conn, trim($_POST['destino']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    $precio = floatval($_POST['precio']);

    $nombreImagen = 'default.jpg'; // Imagen por defecto

    // Procesar imagen solo si se sube una
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombreImagen = uniqid('img_') . '_' . basename($_FILES['imagen']['name']);
        $carpetaImagenes = realpath(__DIR__ . '/assets/img/');

        if ($carpetaImagenes && is_dir($carpetaImagenes)) {
            $rutaDestino = $carpetaImagenes . '/' . $nombreImagen;

            $allowedTypes = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['imagen']['type'], $allowedTypes)) {
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $nombreImagen = 'default.jpg'; // Si falla, usamos la default
                }
            } else {
                $nombreImagen = 'default.jpg'; // Si tipo inválido, usamos default
            }
        }
    }

    // Insertar paquete en la base de datos
    $sql_insert = "INSERT INTO paquetes (destino, descripcion, precio, imagen) 
                   VALUES ('$destino', '$descripcion', $precio, '$nombreImagen')";

    if (mysqli_query($conn, $sql_insert)) {
        $_SESSION['mensaje'] = "Paquete creado exitosamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al crear el paquete.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    header('Location: admin_paquetes.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Paquete</title>
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
                <span class="text-white me-3">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1 class="text-center mb-4" style="color:#003366;">Crear Nuevo Paquete</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="crear_paquete.php" method="post" enctype="multipart/form-data" class="card p-4 shadow">
                    <div class="mb-3">
                        <label for="destino" class="form-label">Destino</label>
                        <input type="text" name="destino" id="destino" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio (CLP)</label>
                        <input type="number" name="precio" id="precio" class="form-control" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Paquete (opcional)</label>
                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/jpeg,image/png">
                        <small class="text-muted">Formatos permitidos: JPG, PNG (máx 2MB)</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Crear Paquete</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="admin_paquetes.php" class="btn btn-custom btn-sm">Volver a Administración</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
