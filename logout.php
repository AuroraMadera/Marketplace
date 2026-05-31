<?php
session_start();

// Vaciar las variables de sesion del usuario.
$_SESSION = array();

// Destruir la sesion actual.
session_destroy();

// Regresar al inicio.
header("Location: index.php");
exit;
?>
