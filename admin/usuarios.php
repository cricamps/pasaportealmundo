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

$log = new Logger('usuarios');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

define('USERS_PER_PAGE', 10);

$error_message = "";

// Determinar la página actual
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page);

// Calcular el offset
$offset = ($page - 1) * USERS_PER_PAGE;

// Construir la consulta SQL
$sql = "SELECT * FROM usuarios";
$whereClauses = [];
$types = "";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . trim($_GET['search']) . "%";
    $whereClauses[] = "(nombre LIKE ? OR email LIKE ?)";
    $types .= "ss";
    $params[] = $search;
    $params[] = $search;

    $log->info("Búsqueda de usuarios: " . $_GET['search']);
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$sql .= " ORDER BY id ASC LIMIT " . USERS_PER_PAGE . " OFFSET " . $offset;

// Consulta para contar el total de usuarios (para la paginación)
$countSql = "SELECT COUNT(*) as total FROM usuarios";
if (!empty($whereClauses)) {
    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
}

$usuarios = obtenerFilas($conn, $sql, $params, $types);
$total_result = ejecutarConsulta($conn, $countSql, $params, $types);

if (!$total_result) {
    $error_message = "Error al obtener la lista de usuarios";
    $log->error("Error al obtener la lista de usuarios: " . $conn->error);
} else {
    $total_row = $total_result->fetch_assoc();
    $total_users = $total_row['total'];
    $total_pages = ceil($total_users / USERS_PER_PAGE);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">👥 Usuarios Registrados</h2>
    <a href="admin.php" class="btn btn-secondary mb-3">← Volver al panel</a>

    <div class="mb-3">
        <form method="GET" class="form-inline">
            <input type="text" class="form-control mr-2" name="search" placeholder="Buscar usuarios"
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if (isset($_GET['search'])): ?>
                <a href="usuarios.php" class="btn btn-secondary ml-2">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Tipo de Usuario</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($usuarios): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
          <span class="badge bg-<?php echo $u['tipo'] === 'admin' ? 'success' : 'secondary'; ?>">
            <?php echo strtoupper($u['tipo']); ?>
          </span>
                    </td>
                    <td>
                        <?php if ($u['email'] !== $_SESSION['email']): ?>
                            <?php if ($u['tipo'] === 'cliente'): ?>
                                <a href="cambiar_tipo.php?id=<?php echo $u['id']; ?>&tipo=admin"
                                class="btn btn-sm btn-outline-success">Dar permisos admin</a>
                            <?php else: ?>
                                <a href="cambiar_tipo.php?id=<?php echo $u['id']; ?>&tipo=cliente"
                                class="btn btn-sm btn-outline-secondary">Quitar permisos admin</a>
                            <?php endif; ?>
                            <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary ml-2">Editar</a>  <?php else: ?>
                            <i>(Tú)</i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No hay usuarios registrados</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <nav aria-label="Paginación de usuarios">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                <a class="page-link" href="usuarios.php?page=<?php echo $page - 1; ?><?php if (isset($_GET['search'])) {
                    echo '&search=' . htmlspecialchars($_GET['search']);
                } ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="usuarios.php?page=<?php echo $i; ?><?php if (isset($_GET['search'])) {
                        echo '&search=' . htmlspecialchars($_GET['search']);
                    } ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php if ($page == $total_pages) echo 'disabled'; ?>">
                <a class="page-link" href="usuarios.php?page=<?php echo $page + 1; ?><?php if (isset($_GET['search'])) {
                    echo '&search=' . htmlspecialchars($_GET['search']);
                } ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
</div>
</body>
</html>