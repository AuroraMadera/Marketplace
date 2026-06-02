<?php
session_start();
require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $telefono = trim($_POST["telefono"]);
    $password = $_POST["password"];
    $confirmar_password = $_POST["confirmar_password"];

    if (empty($nombre) || empty($correo) || empty($password) || empty($confirmar_password)) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo electronico no es valido.";
    } elseif ($password != $confirmar_password) {
        $mensaje = "Las contrasenas no coinciden.";
    } else {
        // Verificar que el correo no este registrado.
        $consulta = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "Este correo ya esta registrado.";
        } else {
            // Guardar la contrasena de forma segura.
            $password_segura = password_hash($password, PASSWORD_DEFAULT);

            $insertar = $conexion->prepare(
                "INSERT INTO usuarios (nombre, correo, password, telefono) VALUES (?, ?, ?, ?)"
            );
            $insertar->bind_param("ssss", $nombre, $correo, $password_segura, $telefono);

            if ($insertar->execute()) {
                $_SESSION["id_usuario"] = $insertar->insert_id;
                $_SESSION["nombre"] = $nombre;
                header("Location: productos.php");
                exit;
            } else {
                $mensaje = "No se pudo registrar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Marketplace Local</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="encabezado">
        <div class="contenedor barra-navegacion">
            <a class="logo" href="index.php">Marketplace Local</a>

            <nav>
                <ul class="menu">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="estadisticas.php">Estadisticas</a></li>
                    <li><a href="pruebas.php">Pruebas</a></li>
                    <li><a href="login.php">Iniciar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Crear cuenta</h1>
            <p class="texto">Registrate para publicar productos y usar el carrito simulado.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje mensaje-error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form class="formulario" action="registro.php" method="POST">
                <div class="campo">
                    <label for="nombre">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="campo">
                    <label for="correo">Correo electronico *</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="campo">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>

                <div class="campo">
                    <label for="password">Contrasena *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="campo">
                    <label for="confirmar_password">Confirmar contrasena *</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required>
                </div>

                <input type="submit" value="Registrarme">
            </form>
        </div>
    </main>

    <footer class="pie">
        <div class="contenedor">
            <p>Proyecto escolar - Marketplace local sin pagos reales</p>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>

