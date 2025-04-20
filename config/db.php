<?php
$host = "localhost";
$user = "campscl1_usr_pasaporte";
$pass = "Pasaporte123@";
$db = "campscl1_pasaporte";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Incluir el autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Crear un logger
$log = new Logger('my_app');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

// Función para ejecutar consultas preparadas de forma segura
function ejecutarConsulta($conn, $sql, $params = [], $types = "") {
    global $log; // Acceder al logger global

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $log->error("Error en la preparación de la consulta: " . $conn->error);
        return false;
    }

    if (!empty($params)) {
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            $log->error("Error en la ejecución de la consulta: " . $stmt->error);
            return false;
        }
    } else {
        if (!$stmt->execute()) {
            if (!$stmt->execute()) {
                $log->error("Error en la ejecución de la consulta: " . $stmt->error);
                return false;
            }
        }
    }

    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// Función para obtener un solo resultado
function obtenerFila($conn, $sql, $params = [], $types = "") {
    $result = ejecutarConsulta($conn, $sql, $params, $types);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Función para obtener múltiples resultados
function obtenerFilas($conn, $sql, $params = [], $types = "") {
    $result = ejecutarConsulta($conn, $sql, $params, $types);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

$conn->set_charset("utf8mb4");
?>