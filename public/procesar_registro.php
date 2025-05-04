<?php
session_start();
include '../config/db.php';
require_once '../config/configuracion_smtp.php'; // Asegúrate de que contiene la función enviarCorreoBienvenida()

if (isset($_POST['nombre'], $_POST['email'], $_POST['password'])) {
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si ya existe el email
    $sql_check = "SELECT id FROM usuarios WHERE email = '$email'";
    $result_check = mysqli_query($conn, $sql_check);

    if ($result_check && mysqli_num_rows($result_check) > 0) {
        $_SESSION['mensaje_error'] = "El correo ya está registrado.";
        header('Location: registro.php');
        exit;
    }

    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, email, password, tipo) VALUES ('$nombre', '$email', '$password_hash', 'cliente')";
    if (mysqli_query($conn, $sql)) {
        // Intentar enviar correo de bienvenida
        $envio = enviarCorreoBienvenida($email, $nombre);
        if ($envio === true) {
            $_SESSION['mensaje'] = "Registro exitoso. Revisa tu correo para más información.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Registro exitoso, pero no se pudo enviar el correo: $envio";
            $_SESSION['tipo_mensaje'] = "warning";
        }
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['mensaje_error'] = "Error al registrar. Intenta de nuevo.";
        header('Location: registro.php');
        exit;
    }
} else {
    $_SESSION['mensaje_error'] = "Debes completar todos los campos.";
    header('Location: registro.php');
    exit;
}
