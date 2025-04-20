<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('admin');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

$error_message = ""; // Para mensajes de error/éxito

// Obtener la lista de paquetes
$sql = "SELECT * FROM paquetes ORDER BY fecha_disponible ASC";
$result = ejecutarConsulta($conn, $sql);

if (!$result) {
    $error_message = "Error al obtener la lista de paquetes";
    $log->error("Error al obtener la lista de paquetes: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Paquetes Turísticos</h2>
        <div>
            <a href="agregar_paquete.php" class="btn btn-success me-2">➕ Agregar Paquete</a>
            <a href="usuarios.php" class="btn btn-primary me-2">👥 Administrar Usuarios</a>
            <a href="logout_admin.php" class="btn btn-outline-danger">Cerrar sesión</a>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <table class="table table-striped table-hover">
        <thead class="table-primary">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Fecha</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($row['fecha_disponible'])); ?></td>
                    <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="editar_paquete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">✏️ Editar</a>
                        <a href="eliminar_paquete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Estás seguro de eliminar este paquete?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No hay paquetes disponibles</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>