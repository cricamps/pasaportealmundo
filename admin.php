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
  <title>Panel de Administración</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Paquetes Turísticos</h2>
    <div>
      <a href="agregar_paquete.php" class="btn btn-success me-2">➕ Agregar Paquete</a>
      <a href="usuarios.php" class="btn btn-primary me-2">👥 Administrar Usuarios</a>  <a href="logout_admin.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
  </div>

  <table class="table table-striped table-hover">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Fecha</th>
        <th>Precio</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $result = $conn->query("SELECT * FROM paquetes ORDER BY fecha_disponible ASC");
      while ($row = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
        <td><?php echo date("d/m/Y", strtotime($row['fecha_disponible'])); ?></td>
        <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
        <td>
          <a href="editar_paquete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
          <a href="eliminar_paquete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este paquete?')">🗑️ Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>