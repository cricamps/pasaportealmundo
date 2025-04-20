<?php
session_start();
var_dump($_SESSION['usuario']); 
include '../config/db.php';

// Solo permitir acceso si es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="https://cdn-icons-png.flaticon.com/512/69/69915.png" alt="Logo">
            Pasaporte al Mundo
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
            <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<main class="container mt-5">
<?php if (isset($_SESSION['mensaje'])) { ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show text-center" role="alert">
        <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
<?php } ?>
    <h1 class="text-center mb-4" style="color:#003366;">Administrar Usuarios</h1>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo $usuario['tipo']; ?></td>
                        <td>
                            <?php if ($usuario['tipo'] == 'cliente') { ?>
                                <a href="procesar_tipo_usuario.php?id=<?php echo $usuario['id']; ?>&accion=promover" class="btn btn-success btn-sm mb-1">Promover a Admin</a>
                            <?php } elseif ($usuario['tipo'] == 'admin') { ?>
                                <a href="procesar_tipo_usuario.php?id=<?php echo $usuario['id']; ?>&accion=degradar" class="btn btn-warning btn-sm mb-1">Degradar a Cliente</a>
                            <?php } ?>
                            <?php if ($_SESSION['usuario']['id'] != $usuario['id']) { // Evita eliminarse a sí mismo ?>
                                <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
