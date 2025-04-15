<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

// Obtener datos del usuario desde la base de datos
$email = $conn->real_escape_string($_SESSION['email']); // Escapar la variable de sesión
$result = $conn->query("SELECT * FROM usuarios WHERE email = '$email'");
$usuario = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">🧾 Mi Perfil</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <button type="submit" name="guardar" class="btn btn-primary">Guardar cambios</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>

<?php
if (isset($_POST['guardar'])) {
    $nuevoNombre = $conn->real_escape_string($_POST['nombre']);
    $nuevoEmail = $conn->real_escape_string($_POST['email']);

    $sql = "UPDATE usuarios SET nombre='$nuevoNombre', email='$nuevoEmail' WHERE email='$email'";
    if ($conn->query($sql)) {
        $_SESSION['usuario'] = $nuevoNombre;
        $_SESSION['email'] = $nuevoEmail;
        echo "<script>alert('Datos actualizados correctamente'); location.href='perfil.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>