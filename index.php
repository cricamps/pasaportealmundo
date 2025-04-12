<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pasaporte al Mundo - Inicio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .card-title {
      font-weight: bold;
    }
    .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h1 class="text-center mb-5 display-5 fw-semibold text-primary">🌍 Explora nuestros Paquetes Turísticos</h1>
    <div class="row justify-content-center">
      <?php
        $sql = "SELECT * FROM paquetes ORDER BY fecha_disponible ASC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
      ?>
        <div class="col-md-4 col-lg-3 mb-4">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($row['imagen'])): ?>
            <img src="assets/img/<?php echo $row['imagen']; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Paquete">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo $row['titulo']; ?></h5>
              <p class="card-text small text-muted mb-2"><?php echo $row['descripcion']; ?></p>
              <ul class="list-unstyled mb-3">
                <li><strong>📅 Fecha:</strong> <?php echo $row['fecha_disponible']; ?></li>
                <li><strong>💲 Precio:</strong> $<?php echo number_format($row['precio'], 0, ',', '.'); ?></li>
              </ul>
              <a href="#" class="btn btn-outline-primary mt-auto">Ver más</a>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <p class="text-center">No hay paquetes disponibles en este momento.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

