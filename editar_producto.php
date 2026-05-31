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
$tipo_mensaje = "mensaje-error";

$consulta_producto = $conexion->prepare(
    "SELECT * FROM productos WHERE id_producto = ? AND id_usuario = ?"
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
    $id_categoria = $_POST["id_categoria"];
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = $_POST["precio"];
    $estado = $_POST["estado"];
    $disponible = isset($_POST["disponible"]) ? 1 : 0;
    $nombre_imagen = $producto["imagen"];

    if (empty($id_categoria) || empty($nombre) || empty($descripcion) || empty($precio) || empty($estado)) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
    } elseif ($precio <= 0) {
        $mensaje = "El precio debe ser mayor que cero.";
    } else {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $carpeta_destino = "img/productos/";
            $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $extensiones_permitidas = array("jpg", "jpeg", "png", "gif");

            if (in_array($extension, $extensiones_permitidas)) {
                $nombre_imagen = time() . "_" . basename($_FILES["imagen"]["name"]);
                $ruta_imagen = $carpeta_destino . $nombre_imagen;

                if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
                    $mensaje = "No se pudo subir la nueva imagen.";
                }
            } else {
                $mensaje = "La imagen debe ser JPG, JPEG, PNG o GIF.";
            }
        }

        if (empty($mensaje)) {
            $actualizar = $conexion->prepare(
                "UPDATE productos
                 SET id_categoria = ?, nombre = ?, descripcion = ?, precio = ?, estado = ?, imagen = ?, disponible = ?
                 WHERE id_producto = ? AND id_usuario = ?"
            );
            $actualizar->bind_param(
                "issdssiii",
                $id_categoria,
                $nombre,
                $descripcion,
                $precio,
                $estado,
                $nombre_imagen,
                $disponible,
                $id_producto,
                $id_usuario
            );

            if ($actualizar->execute()) {
                $mensaje = "Producto actualizado correctamente.";
                $tipo_mensaje = "mensaje-exito";

                $consulta_producto->execute();
                $producto = $consulta_producto->get_result()->fetch_assoc();
            } else {
                $mensaje = "No se pudo actualizar el producto.";
            }
        }
    }
}

$categorias = $conexion->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto - Marketplace Local</title>
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
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="perfil.php">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Editar producto</h1>
            <p class="texto">Modifica la informacion de tu producto publicado.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form class="formulario" action="editar_producto.php?id=<?php echo $id_producto; ?>" method="POST" enctype="multipart/form-data">
                <div class="campo">
                    <label for="nombre">Nombre del producto</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="descripcion">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto["descripcion"]); ?></textarea>
                </div>

                <div class="campo">
                    <label for="id_categoria">Categoria</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <?php while ($categoria = $categorias->fetch_assoc()): ?>
                            <option
                                value="<?php echo $categoria["id_categoria"]; ?>"
                                <?php if ($producto["id_categoria"] == $categoria["id_categoria"]) echo "selected"; ?>
                            >
                                <?php echo htmlspecialchars($categoria["nombre"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="precio">Precio</label>
                    <input
                        type="number"
                        id="precio"
                        name="precio"
                        min="1"
                        step="0.01"
                        value="<?php echo htmlspecialchars($producto["precio"]); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="Nuevo" <?php if ($producto["estado"] == "Nuevo") echo "selected"; ?>>Nuevo</option>
                        <option value="Usado" <?php if ($producto["estado"] == "Usado") echo "selected"; ?>>Usado</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="imagen">Nueva imagen</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <?php if (!empty($producto["imagen"])): ?>
                    <p class="texto">Imagen actual: <?php echo htmlspecialchars($producto["imagen"]); ?></p>
                <?php endif; ?>

                <div class="campo">
                    <label>
                        <input
                            type="checkbox"
                            name="disponible"
                            <?php if ($producto["disponible"] == 1) echo "checked"; ?>
                        >
                        Producto disponible
                    </label>
                </div>

                <input type="submit" value="Guardar cambios">
                <a class="boton boton-secundario" href="perfil.php">Volver al perfil</a>
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
