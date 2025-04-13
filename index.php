<?php
include 'includes/db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pasaporte al Mundo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card:hover {
      transform: scale(1.02);
      transition: 0.2s ease-in-out;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .card-link {
      text-decoration: none;
      color: inherit;
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="display-5 fw-bold text-dark">Pasaporte al Mundo</h1>
    <p class="text-muted">Tu aventura comienza aquí 🌎</p>

    <?php if (!isset($_SESSION['usuario'])): ?>
      <a href="login.php" class="btn btn-outline-primary mt-2">Iniciar Sesión</a>
      <a href="register.php" class="btn btn-outline-success mt-2 ms-2">Registrarse</a>
    <?php else: ?>
      <div class="dropdown d-inline-block mt-2">
        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
          👤 <?php echo $_SESSION['usuario']; ?>
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="perfil.php">🧾 Mi Perfil</a></li>
          <li><a class="dropdown-item" href="cambiar_clave.php">🔐 Cambiar Contraseña</a></li>
          <?php if ($_SESSION['tipo'] === 'admin'): ?>
          <li><a class="dropdown-item" href="admin.php">⚙️ Panel de Administración</a></li>
          <?php endif; ?>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout_admin.php">🚪 Cerrar Sesión</a></li>
        </ul>
      </div>
    <?php endif; ?>
  </div>

  <div class="row justify-content-center">
    <?php
    $result = $conn->query("SELECT * FROM paquetes ORDER BY fecha_disponible ASC");
    while ($row = $result->fetch_assoc()):
      $link = "paquete.php?id=" . $row['id'];
    ?>
    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
      <a href="<?php echo $link; ?>" class="card-link">
        <div class="card h-100 shadow-sm">
          <?php if ($row['imagen']): ?>
            <img src="assets/img/<?php echo $row['imagen']; ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($row['titulo']); ?></h5>
            <p class="card-text text-muted small"><?php echo htmlspecialchars($row['descripcion']); ?></p>
            <p class="mb-1"><strong>📅</strong> <?php echo date("d/m/Y", strtotime($row['fecha_disponible'])); ?></p>
            <p><strong>💰</strong> $<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
          </div>
        </div>
      </a>
    </div>
    <?php endwhile; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
