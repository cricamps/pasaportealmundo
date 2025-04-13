<?php
include 'includes/db.php';
session_start();

if (isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrarse</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-container {
      max-width: 450px;
      margin: 60px auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="bg-light">
  <div class="container">
    <div class="form-container">
      <h3 class="mb-4 text-center">📝 Crear cuenta</h3>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nombre completo</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="registrar" class="btn btn-success w-100">Registrarse</button>
        <div class="mt-3 text-center">
          ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
          <br><a href="index.php" class="btn btn-link mt-2">← Volver al inicio</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
