<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM paquetes WHERE id = $id");
if ($result->num_rows === 0) {
    echo "Paquete no encontrado.";
    exit;
}

$paquete = $result->fetch_assoc();
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
  <h2 class="mb-4">✏️ Editar Paquete</h2>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" class="form-control" name="titulo" value="<?php echo $paquete['titulo']; ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea class="form-control" name="descripcion" rows="4" required><?php echo $paquete['descripcion']; ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha disponible</label>
      <input type="date" class="form-control" name="fecha_disponible" value="<?php echo $paquete['fecha_disponible']; ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Precio</label>
      <input type="number" step="0.01" class="form-control" name="precio" value="<?php echo $paquete['precio']; ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Imagen actual</label><br>
      <?php if ($paquete['imagen']): ?>
        <img src="assets/img/<?php echo $paquete['imagen']; ?>" alt="Imagen actual" width="200"><br>
      <?php else: ?>
        <span class="text-muted">Sin imagen</span><br>
      <?php endif; ?>
      <label class="form-label mt-2">Subir nueva imagen (opcional)</label>
      <input type="file" class="form-control" name="imagen">
    </div>

    <button type="submit" name="actualizar" class="btn btn-primary">Guardar cambios</button>
    <a href="admin.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>

<?php
if (isset($_POST['actualizar'])) {
  $titulo = $conn->real_escape_string($_POST['titulo']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);
  $fecha = $_POST['fecha_disponible'];
  $precio = floatval($_POST['precio']);
  $imagen = $paquete['imagen'];

  if (!empty($_FILES['imagen']['name'])) {
    $imagen = basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "assets/img/" . $imagen);
  }

  $sql = "UPDATE paquetes SET 
            titulo = '$titulo',
            descripcion = '$descripcion',
            fecha_disponible = '$fecha',
            precio = $precio,
            imagen = '$imagen'
          WHERE id = $id";

  if ($conn->query($sql)) {
    echo "<script>alert('Paquete actualizado con éxito'); location.href='admin.php';</script>";
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
