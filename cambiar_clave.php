<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    $sql = "SELECT password FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if (!password_verify($actual, $user['password'])) {
        $error = "La contraseña actual es incorrecta.";
    } elseif ($nueva !== $confirmar) {
        $error = "Las nuevas contraseñas no coinciden.";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $conn->query("UPDATE usuarios SET password = '$hash' WHERE email = '$email'");
        $success = "Contraseña actualizada correctamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">🔐 Cambiar Contraseña</h2>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>
  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Contraseña actual</label>
      <input type="password" name="actual" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nueva contraseña</label>
      <input type="password" name="nueva" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirmar nueva contraseña</label>
      <input type="password" name="confirmar" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
