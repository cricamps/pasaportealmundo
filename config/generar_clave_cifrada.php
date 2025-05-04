<?php
require_once 'secret.php';

function cifrarClave($clave) {
    return openssl_encrypt($clave, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// Cambia 'miclave' por la contraseña real que quieras cifrar
$claveOriginal = 'miclave';
$claveCifrada = cifrarClave($claveOriginal);

echo "Clave cifrada: " . $claveCifrada;
?>

