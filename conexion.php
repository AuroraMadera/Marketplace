<?php
// Datos de conexion para XAMPP.
// En XAMPP normalmente el usuario es "root" y la contrasena esta vacia.
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "marketplace_local";

// Crear la conexion con MySQL.
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar si hubo error al conectar.
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// Usar caracteres UTF-8 para guardar correctamente acentos y caracteres especiales.
$conexion->set_charset("utf8mb4");
?>
