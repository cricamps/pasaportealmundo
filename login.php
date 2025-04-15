<?php
session_unset();
session_start();
include 'includes/db.php';

if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$error_message = ""; // Inicializar la variable de error

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string(trim($_POST['email']));  // Escapar el email
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE email = '$email'"; // Consulta segura
    $result = $conn->query($query);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tipo'] = $user['tipo'];

            if ($user['tipo'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error_message = "Contraseña incorrecta";
        }
    } else {
        $error_message = "Correo no registrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 400px;
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
        <h3 class="mb-4 text-center">🔐 Iniciar Sesión</h3>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Ingresar</button>
            <div class="mt-3 text-center">
                ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
                <br><a href="index.php" class="btn btn-link mt-2">← Volver al inicio</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>