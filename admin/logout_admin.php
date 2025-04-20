<?php
session_start();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$log = new Logger('logout_admin');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::INFO));

$log->info("Cierre de sesión de administrador: " . $_SESSION['usuario']); // Log antes de destruir la sesión

session_unset();
session_destroy();
header("Location: index.php");
exit;
?>