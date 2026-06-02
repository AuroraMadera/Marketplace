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

$resultado_columna = $conexion->query("SHOW COLUMNS FROM productos LIKE 'ubicacion'");

if ($resultado_columna && $resultado_columna->num_rows === 0) {
    $conexion->query("ALTER TABLE productos ADD COLUMN ubicacion VARCHAR(120) NOT NULL DEFAULT 'Centro' AFTER precio");
}

if (!function_exists("renderizar_estrellas")) {
    function renderizar_estrellas($calificacion)
    {
        $calificacion_redondeada = (int) round((float) $calificacion);
        $calificacion_redondeada = max(0, min(5, $calificacion_redondeada));
        $salida = '<span class="estrellas" aria-label="Calificacion de ' . number_format((float) $calificacion, 1) . ' de 5">';

        for ($i = 1; $i <= 5; $i++) {
            $clase = $i <= $calificacion_redondeada ? "estrella activa" : "estrella";
            $salida .= '<span class="' . $clase . '">&#9733;</span>';
        }

        $salida .= "</span>";
        return $salida;
    }
}
?>

