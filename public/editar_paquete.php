<?php
session_start();
include '../config/db.php';

// Solo admins pueden acceder
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Verificar que venga el ID
if (!isset($_GET['id'])) {
    header('Location: admin_paquetes.php');
    exit;
}

$id = intval($_GET['id']);

// Obtener datos actuales del paquete
$sql = "SELECT * FROM paquetes WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['mensaje'] = "Paquete no encontrado.";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: admin_paquetes.php');
    exit;
}

$paquete = mysqli_fetch_assoc($result);

// Si envían el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destino = mysqli_real_escape_string($conn, trim($_POST['destino']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    $precio = floatval($_POST['precio']);

    $nuevaImagen = $paquete['imagen'] ?? 'default.jpg'; // Imagen por defecto si no se sube o no había antes

    // Procesar imagen solo si se sube una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombreImagen = uniqid('img_') . '_' . basename($_FILES['imagen']['name']);
        $carpetaImagenes = realpath(__DIR__ . '/assets/img/');
    
        if ($carpetaImagenes && is_dir($carpetaImagenes)) {
            $rutaDestino = $carpetaImagenes . '/' . $nombreImagen;
    
            $allowedTypes = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['imagen']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $nuevaImagen = $nombreImagen;
                } else {
                    $error = error_get_last();
                    $_SESSION['mensaje'] = "Error al mover el archivo al directorio. Detalles: " . (isset($error['message']) ? $error['message'] : 'No hay detalles disponibles.');
                    $_SESSION['tipo_mensaje'] = "danger";
                    header('Location: admin_paquetes.php');
                    exit;
                }
            } else {
                $_SESSION['mensaje'] = "Solo se permiten archivos JPG o PNG.";
                $_SESSION['tipo_mensaje'] = "warning";
                header('Location: editar_paquete.php?id=' . $id);
                exit;
            }
        } else {
            $_SESSION['mensaje'] = "No se encontró la carpeta de imágenes.";
            $_SESSION['tipo_mensaje'] = "danger";
            header('Location: admin_paquetes.php');
            exit;
        }
    }
    

    // Actualizar paquete
    $sql_update = "UPDATE paquetes SET
                        destino = '$destino',
                        descripcion = '$descripcion',
                        precio = $precio,
                        imagen = '$nuevaImagen'
                    WHERE id = $id";

    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['mensaje'] = "Paquete actualizado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar el paquete.";
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
    <title>Editar Paquete Turístico</title>
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
        <?php if (isset($_SESSION['mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> text-center">
                <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                <?php unset($_SESSION['mensaje']); ?>
            </div>
        <?php } ?>

        <h1 class="text-center mb-4" style="color:#003366;">Editar Paquete Turístico</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="editar_paquete.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" class="card p-4 shadow">
                    <div class="mb-3">
                        <label for="destino" class="form-label">Destino</label>
                        <input type="text" name="destino" id="destino" class="form-control" required value="<?php echo htmlspecialchars($paquete['destino']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($paquete['descripcion']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio (CLP)</label>
                        <input type="number" name="precio" id="precio" class="form-control" min="0" required value="<?php echo $paquete['precio']; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen actual:</label><br>
                        <img src="assets/img/<?php echo htmlspecialchars($paquete['imagen'] ?? 'default.jpg'); ?>" alt="Imagen actual" style="width: 200px; height: 140px; object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Cambiar imagen (opcional)</label>
                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
                        <small class="text-muted">Solo se permiten JPG o PNG (máx. 2MB).</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Actualizar Paquete</button>
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