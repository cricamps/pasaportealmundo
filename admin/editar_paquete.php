<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

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

// Función para convertir la fecha de YYYY-MM-DD a DD-MM-YYYY
function convertirFechaParaMostrar($fecha) {
    return date('d-m-Y', strtotime($fecha));
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// Obtener los datos del paquete
$sql = "SELECT * FROM paquetes WHERE id = ?";
$paquete = obtenerFila($conn, $sql, [$id], "i");

if (!$paquete) {
    header("Location: admin.php"); // Paquete no encontrado
    log_message("warning", "Intento de editar paquete inexistente. ID: " . $id);
    exit;
}

if (isset($_POST['editar'])) {
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
        $imagen = $paquete['imagen']; // Mantener la imagen actual
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpeg', 'image/png'];
            $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
            $tamano_maximo = 2 * 1024 * 1024; // 2MB

            if (in_array($tipo_archivo, $permitidos) && $_FILES['imagen']['size'] <= $tamano_maximo) {
                // Eliminar la imagen anterior (¡SEGURIDAD!)
                if (!empty($paquete['imagen']) && file_exists("assets/img/" . $paquete['imagen'])) {
                    if (!unlink("assets/img/" . $paquete['imagen'])) {
                        log_message("error", "Error al eliminar la imagen anterior: " . $paquete['imagen']);
                        $error_message = "Error al procesar la imagen"; // Evitar información sensible al usuario
                    }
                }
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_archivo = uniqid('paquete_') . '.' . $extension;
                $ruta_destino = "assets/img/" . $nombre_archivo;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    $imagen = $nombre_archivo;
                } else {
                    $error_message = "Error al guardar la imagen en el servidor";
                    log_message("error", "Error al guardar la imagen en el servidor: " . $_FILES['imagen']['name']);
                }
            } else {
                $error_message = "Formato de imagen no válido o tamaño excedido (máximo 2MB)";
                log_message("warning", "Intento de subir imagen no válida: " . $_FILES['imagen']['name']);
            }
        }

        if (empty($error_message)) {
            $fecha_bd = convertirFechaParaBD($fecha_disponible);
            $sql = "UPDATE paquetes SET titulo = ?, descripcion = ?, fecha_disponible = ?, precio = ?, imagen = ? WHERE id = ?";
            $result = ejecutarConsulta($conn, $sql, [$titulo, $descripcion, $fecha_bd, $precio, $imagen, $id], "sssdsi");

            if ($result) {
                $success_message = "Paquete actualizado correctamente";
                log_message("info", "Paquete actualizado. ID: " . $id . " Título: " . $titulo);
                // Volver a obtener los datos actualizados para mostrar en el formulario
                $paquete = obtenerFila($conn, "SELECT * FROM paquetes WHERE id = ?", [$id], "i");
            } else {
                $error_message = "Error al actualizar el paquete";
                log_message("error", "Error al actualizar el paquete. ID: " . $id . " Error: " . $conn->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paquete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>✏️ Editar Paquete Turístico</h2>
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
            <input type="text" class="form-control" name="titulo" value="<?php echo htmlspecialchars($paquete['titulo'] ?? ''); ?>" maxlength="255" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4" maxlength="1000" required><?php echo htmlspecialchars($paquete['descripcion'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha Disponible (DD-MM-YYYY)</label>
            <input type="text" class="form-control" name="fecha_disponible" value="<?php echo $paquete['fecha_disponible'] ? convertirFechaParaMostrar($paquete['fecha_disponible']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" class="form-control" name="precio" step="0.01" value="<?php echo $paquete['precio'] ?? ''; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            <?php if (!empty($paquete['imagen'])): ?>
                <img src="assets/img/<?php echo $paquete['imagen']; ?>" alt="Imagen actual" style="max-width: 200px; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
</body>
</html>