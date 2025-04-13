<?php
session_start();
include 'includes/db.php';

if (isset($_SESSION['admin'])) {
  header("Location: admin.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3 class="mb-3">Panel de Administración</h3>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Usuario</label>
      <input type="text" class="form-control" name="usuario" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" class="form-control" name="password" required>
    </div>
    <button type="submit" name="login" class="btn btn-primary">Ingresar</button>
  </form>
</div>
</body>
</html>

<?php
if (isset($_POST['login'])) {
  $usuario = $conn->real_escape_string($_POST['usuario']);
  $password = $_POST['password'];

  $query = $conn->query("SELECT * FROM admin WHERE usuario = '$usuario'");
  if ($query->num_rows == 1) {
    $admin = $query->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = $admin['usuario'];
      header("Location: admin.php");
      exit;
    } else {
      echo "<script>alert('Contraseña incorrecta');</script>";
    }
  } else {
    echo "<script>alert('Usuario no encontrado');</script>";
  }
}
?>
