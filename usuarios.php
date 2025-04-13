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
  <title>Administrar Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">👥 Usuarios Registrados</h2>
  <a href="admin.php" class="btn btn-secondary mb-3">← Volver al panel</a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Tipo de Usuario</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id ASC");
      while ($u = $usuarios->fetch_assoc()):
      ?>
      <tr>
        <td><?php echo $u['id']; ?></td>
        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td>
          <span class="badge bg-<?php echo $u['tipo'] === 'admin' ? 'success' : 'secondary'; ?>">
            <?php echo strtoupper($u['tipo']); ?>
          </span>
        </td>
        <td>
          <?php if ($u['email'] !== $_SESSION['email']): ?>
            <?php if ($u['tipo'] === 'cliente'): ?>
              <a href="cambiar_tipo.php?id=<?php echo $u['id']; ?>&tipo=admin" class="btn btn-sm btn-outline-success">Dar permisos admin</a>
            <?php else: ?>
              <a href="cambiar_tipo.php?id=<?php echo $u['id']; ?>&tipo=cliente" class="btn btn-sm btn-outline-secondary">Quitar permisos admin</a>
            <?php endif; ?>
          <?php else: ?>
            <span class="text-muted">Tú</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
