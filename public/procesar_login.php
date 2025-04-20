<?php
session_start();
include '../config/db.php'; // Ajusta si necesario

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contrasena']);
    
    if (!empty($email) && !empty($contrasena)) {
        $sql = "SELECT * FROM usuarios WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);
            
            // VALIDAR CON CONTRASEÑA ENCRIPTADA
            if (password_verify($contrasena, $usuario['password'])) {
                $_SESSION['usuario'] = $usuario;
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error_login'] = 'Contraseña incorrecta.';
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['error_login'] = 'Usuario no encontrado.';
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error_login'] = 'Por favor completa todos los campos.';
        header("Location: login.php");
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>
