<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: perfil.php");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$id_producto = $_GET["id"];
$mensaje = "";

$consulta_producto = $conexion->prepare(
    "SELECT id_producto, nombre, imagen FROM productos WHERE id_producto = ? AND id_usuario = ?"
);
$consulta_producto->bind_param("ii", $id_producto, $id_usuario);
$consulta_producto->execute();
$resultado_producto = $consulta_producto->get_result();

if ($resultado_producto->num_rows == 0) {
    header("Location: perfil.php");
    exit;
}

$producto = $resultado_producto->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["confirmar_eliminacion"])) {
        $eliminar = $conexion->prepare("DELETE FROM productos WHERE id_producto = ? AND id_usuario = ?");
        $eliminar->bind_param("ii", $id_producto, $id_usuario);

        if ($eliminar->execute()) {
            header("Location: perfil.php");
            exit;
        } else {
            $mensaje = "No se pudo eliminar el producto.";
        }
    }

    if (isset($_POST["cancelar"])) {
        header("Location: perfil.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar producto - Marketplace Local</title>
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
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="perfil.php">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Eliminar producto</h1>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje mensaje-error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <section class="formulario">
                <p class="texto">
                    Estas seguro de que quieres eliminar el siguiente producto?
                </p>

                <h2><?php echo htmlspecialchars($producto["nombre"]); ?></h2>

                <?php if (!empty($producto["imagen"])): ?>
                    <img
                        src="img/productos/<?php echo htmlspecialchars($producto["imagen"]); ?>"
                        alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                    >
                <?php endif; ?>

                <form action="eliminar_producto.php?id=<?php echo $id_producto; ?>" method="POST">
                    <button class="boton-peligro" type="submit" name="confirmar_eliminacion">
                        Si, eliminar producto
                    </button>

                    <button class="boton-secundario" type="submit" name="cancelar">
                        Cancelar
                    </button>
                </form>
            </section>
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


