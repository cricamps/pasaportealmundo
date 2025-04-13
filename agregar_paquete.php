<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
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
  <h2 class="mb-4">➕ Nuevo Paquete Turístico</h2>

  <form action="agregar_paquete.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" class="form-control" name="titulo" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea class="form-control" name="descripcion" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha disponible</label>
      <input type="date" class="form-control" name="fecha_disponible" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Precio (CLP)</label>
      <input type="number" step="0.01" class="form-control" name="precio" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Imagen (opcional)</label>
      <input type="file" class="form-control" name="imagen">
    </div>

    <button type="submit" name="guardar" class="btn btn-success">Guardar paquete</button>
    <a href="admin.php" class="btn btn-secondary">Volver al panel</a>
  </form>
</div>
</body>
</html>

<?php
if (isset($_POST['guardar'])) {
  $titulo = $conn->real_escape_string($_POST['titulo']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);
  $fecha = $_POST['fecha_disponible'];
  $precio = floatval($_POST['precio']);
  $imagen = "";

  if (!empty($_FILES['imagen']['name'])) {
    $imagen = basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "assets/img/" . $imagen);
  }

  $sql = "INSERT INTO paquetes (titulo, descripcion, fecha_disponible, precio, imagen)
          VALUES ('$titulo', '$descripcion', '$fecha', $precio, '$imagen')";

  if ($conn->query($sql)) {
    echo "<script>alert('Paquete agregado correctamente'); location.href='admin.php';</script>";
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
