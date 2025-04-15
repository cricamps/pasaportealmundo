<?php
include 'includes/db.php';
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$error_message = "";  // Inicializar mensaje de error

if (isset($_POST['registrar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Formato de correo electrónico inválido";
    } elseif (strlen($password) < 6) { // Validar longitud mínima de la contraseña (ejemplo)
        $error_message = "La contraseña debe tener al menos 6 caracteres";
    } else {
        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar en la base de datos
        $query = "INSERT INTO usuarios (nombre, email, password, tipo) VALUES ('$nombre', '$email', '$hashed_password', 'cliente')";
        if ($conn->query($query)) {
            header("Location: login.php"); // Redirigir al login tras el registro exitoso
            exit;
        } else {
            $error_message = "Error al registrar el usuario: " . $conn->error;
        }
    }
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
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
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