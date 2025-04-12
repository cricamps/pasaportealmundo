<?php
$host = "localhost";
$user = "campscl1_usr_pasaporte";
$pass = "Pasaporte123@";
$db = "campscl1_pasaporte";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
