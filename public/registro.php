<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Pasaporte al Mundo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="https://cdn-icons-png.flaticon.com/512/69/69915.png" alt="Logo">
            Pasaporte al Mundo
        </a>
    </div>
</nav>

<?php if (isset($_SESSION['mensaje_error'])) { ?>
    <div class="alert alert-danger text-center">
        <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
    </div>
<?php } ?>

<main class="main-formulario">
    <div class="card-formulario">
        <h2 class="text-center mb-4" style="color: #003366;">Crear Cuenta</h2>
        <form action="procesar_registro.php" method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>
            <div class="text-center mt-3">
                <small>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></small>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-custom btn-sm">Volver al inicio</a>
        </div>
    </div>
</main>

<footer class="mt-5 py-4" style="background-color: #002244;">
    <div class="container text-center">
        <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
