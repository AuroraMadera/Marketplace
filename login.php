<?php
session_start();
require_once "conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    if (empty($correo) || empty($password)) {
        $mensaje = "Debes escribir tu correo y contrasena.";
    } else {
        $consulta = $conexion->prepare("SELECT id_usuario, nombre, password FROM usuarios WHERE correo = ?");
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($password, $usuario["password"])) {
                $_SESSION["id_usuario"] = $usuario["id_usuario"];
                $_SESSION["nombre"] = $usuario["nombre"];

                header("Location: productos.php");
                exit;
            } else {
                $mensaje = "La contrasena es incorrecta.";
            }
        } else {
            $mensaje = "No existe una cuenta con ese correo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion - Marketplace Local</title>
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
                    <li><a href="registro.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Iniciar sesion</h1>
            <p class="texto">Entra con tu cuenta para publicar productos y usar el carrito.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje mensaje-error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form class="formulario" action="login.php" method="POST">
                <div class="campo">
                    <label for="correo">Correo electronico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="campo">
                    <label for="password">Contrasena</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <input type="submit" value="Entrar">
            </form>
        </div>
    </main>

    <footer class="pie">
        <div class="contenedor">
            <p>Proyecto escolar - Marketplace local sin pagos reales</p>
        </div>
    </footer>
</body>
</html>
