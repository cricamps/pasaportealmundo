<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('agregar_paquete');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

$error_message = "";
$success_message = "";

// Función para validar la fecha (formato DD-MM-YYYY)
function validarFecha($fecha) {
    $partes = explode('-', $fecha);
    if (count($partes) == 3 && checkdate($partes[1], $partes[0], $partes[2])) {
        return true;
    }
    return false;
}

// Función para convertir la fecha de DD-MM-YYYY a YYYY-MM-DD
function convertirFechaParaBD($fecha) {
    $partes = explode('-', $fecha);
    return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
}

if (isset($_POST['agregar'])) {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_disponible = trim($_POST['fecha_disponible']);
    $precio = trim($_POST['precio']);

    // Validaciones
    if (empty($titulo) || empty($descripcion) || empty($fecha_disponible) || empty($precio)) {
        $error_message = "Todos los campos son obligatorios";
    } elseif (strlen($titulo) > 255) {
        $error_message = "El título es demasiado largo";
    } elseif (strlen($descripcion) > 1000) {
        $error_message = "La descripción es demasiado larga";
    } elseif (!validarFecha($fecha_disponible)) {
        $error_message = "Formato de fecha inválido (DD-MM-YYYY)";
    } elseif (!is_numeric($precio) || $precio <= 0) {
        $error_message = "El precio debe ser un número positivo";
    } else {
        // Manejo de la imagen (¡MUCHO MÁS SEGURO!)
        $imagen = "";
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpeg', 'image/png'];
            $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
            $tamano_maximo = 2 * 1024 * 1024; // 2MB

            if (in_array($tipo_archivo, $permitidos) && $_FILES['imagen']['size'] <= $tamano_maximo) {
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_archivo = uniqid('paquete_') . '.' . $extension; // Prefijo y extensión
                $ruta_destino = "assets/img/" . $nombre_archivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    $imagen = $nombre_archivo;
                } else {
                    $error_message = "Error al guardar la imagen en el servidor";
                    $log->error("Error al guardar la imagen: " . $_FILES['imagen']['name']);
                }
            } else {
                $error_message = "Formato de imagen no válido o tamaño excedido (máximo 2MB)";
                $log->warning("Intento de subir imagen no válida: " . $_FILES['imagen']['name']);
            }
        }

        if (empty($error_message)) {
            $fecha_bd = convertirFechaParaBD($fecha_disponible);
            $sql = "INSERT INTO paquetes (titulo, descripcion, fecha_disponible, precio, imagen) VALUES (?, ?, ?, ?, ?)";
            $result = ejecutarConsulta($conn, $sql, [$titulo, $descripcion, $fecha_bd, $precio, $imagen], "sssds");

            if ($result) {
                $success_message = "Paquete agregado correctamente";
                $log->info("Paquete agregado: " . $titulo);
            } else {
                $error_message = "Error al agregar el paquete";
                $log->error("Error al agregar el paquete: " . $conn->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Paquete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>➕ Agregar Paquete Turístico</h2>
    <a href="admin.php" class="btn btn-secondary mb-3">← Volver</a>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" value="<?php echo htmlspecialchars($titulo ?? ''); ?>" maxlength="255" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4" maxlength="1000" required><?php echo htmlspecialchars($descripcion ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha Disponible (DD-MM-YYYY)</label>
            <input type="text" class="form-control" name="fecha_disponible" value="<?php echo $fecha_disponible ?? ''; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" class="form-control" name="precio" step="0.01" value="<?php echo $precio ?? ''; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
        </div>
        <button type="submit" name="agregar" class="btn btn-primary">Agregar Paquete</button>
    </form>
</div>
</body>
</html>