<?php
session_start();
include '../config/db.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario;
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['mensaje_error'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['mensaje_error'] = "El correo electrónico no está registrado.";
    }
}

header('Location: login.php');
exit;
?>
