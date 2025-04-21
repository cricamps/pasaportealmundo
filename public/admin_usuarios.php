<?php
session_start();
include '../config/db.php';

// Solo permitir acceso si es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Variables para paginación
$usuarios_por_pagina = 10;

// Página actual
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}

// Si hay búsqueda
$busqueda = isset($_GET['busqueda']) ? mysqli_real_escape_string($conn, trim($_GET['busqueda'])) : '';

if (!empty($busqueda)) {
    $sql_total = "SELECT COUNT(*) AS total FROM usuarios WHERE nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%'";
    $sql = "SELECT * FROM usuarios WHERE nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%' ORDER BY id ASC LIMIT $usuarios_por_pagina OFFSET " . (($pagina_actual - 1) * $usuarios_por_pagina);
} else {
    $sql_total = "SELECT COUNT(*) AS total FROM usuarios";
    $sql = "SELECT * FROM usuarios ORDER BY id ASC LIMIT $usuarios_por_pagina OFFSET " . (($pagina_actual - 1) * $usuarios_por_pagina);
}

$result_total = mysqli_query($conn, $sql_total);
$total_usuarios = mysqli_fetch_assoc($result_total)['total'];
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);

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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://cdn-icons-png.flaticon.com/512/69/69915.png" alt="Logo">
                Pasaporte al Mundo
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Bienvenido,
                    <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                <a href="logout.php" class="btn btn-custom">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">

        <?php if (isset($_SESSION['mensaje'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show text-center"
                role="alert">
                <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
        <?php } ?>

        <h1 class="text-center mb-4" style="color:#003366;">Administrar Usuarios</h1>

        <!-- Buscador -->
        <form method="GET" action="admin_usuarios.php" class="mb-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre o email"
                        value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>

        <!-- Resultado de búsqueda -->
        <?php if (!empty($busqueda)) { ?>
            <div class="alert alert-info text-center">
                🔎 Resultados para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
                (<?php echo $total_usuarios; ?> encontrados)
            </div>
        <?php } ?>

        <!-- Tabla -->
        <?php if (mysqli_num_rows($result) > 0) { ?>
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
                                    <?php if ($usuario['email'] !== 'admin@demo.com') { ?>
                                        <?php if ($usuario['tipo'] == 'cliente') { ?>
                                            <a href="procesar_tipo_usuario.php?id=<?php echo $usuario['id']; ?>&accion=promover"
                                                class="btn btn-success btn-sm mb-1">Promover a Admin</a>
                                        <?php } elseif ($usuario['tipo'] == 'admin') { ?>
                                            <a href="procesar_tipo_usuario.php?id=<?php echo $usuario['id']; ?>&accion=degradar"
                                                class="btn btn-warning btn-sm mb-1">Degradar a Cliente</a>
                                        <?php } ?>
                                        <a href="cambiar_contrasena_usuario.php?id=<?php echo $usuario['id']; ?>"
                                            class="btn btn-primary btn-sm mt-1">Cambiar Contraseña</a>
                                        <?php if ($_SESSION['usuario']['id'] != $usuario['id']) { ?>
                                            <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>"
                                                class="btn btn-danger btn-sm mt-1"
                                                onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="text-muted">Protegido</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">
                ⚠️
                <?php echo empty($busqueda) ? "No hay usuarios registrados." : "No se encontraron resultados para \"" . htmlspecialchars($busqueda) . "\"."; ?>
            </div>
        <?php } ?>

        <!-- Paginación -->
        <?php if ($total_paginas > 1) { ?>
            <nav aria-label="Paginación">
                <ul class="pagination justify-content-center mt-4">
                    <?php if ($pagina_actual > 1) { ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="admin_usuarios.php?pagina=<?php echo $pagina_actual - 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>">Anterior</a>
                        </li>
                    <?php } ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                        <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="admin_usuarios.php?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>

                    <?php if ($pagina_actual < $total_paginas) { ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="admin_usuarios.php?pagina=<?php echo $pagina_actual + 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>">Siguiente</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        <?php } ?>

    </main>

    <footer class="mt-5 py-4" style="background-color: #002244;">
        <div class="container text-center">
            <p class="mb-0 text-white small">© <?php echo date('Y'); ?> Pasaporte al Mundo - Todos los derechos
                reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>