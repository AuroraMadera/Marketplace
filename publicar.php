<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$tipo_mensaje = "mensaje-error";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION["id_usuario"];
    $id_categoria = $_POST["id_categoria"];
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = $_POST["precio"];
    $estado = $_POST["estado"];
    $nombre_imagen = "";

    if (empty($id_categoria) || empty($nombre) || empty($descripcion) || empty($precio) || empty($estado)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif ($precio <= 0) {
        $mensaje = "El precio debe ser mayor que cero.";
    } else {
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $carpeta_destino = __DIR__ . "/img/productos/";
            $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
            $extensiones_permitidas = array("jpg", "jpeg", "png", "gif", "webp");

            if (!is_dir($carpeta_destino)) {
                mkdir($carpeta_destino, 0777, true);
            }

            if (in_array($extension, $extensiones_permitidas)) {
                $nombre_imagen = time() . "_producto." . $extension;
                $ruta_imagen = $carpeta_destino . $nombre_imagen;

                if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
                    $mensaje = "No se pudo subir la imagen.";
                }
            } else {
                $mensaje = "La imagen debe ser JPG, JPEG, PNG, GIF o WEBP.";
            }
        }

        if (empty($mensaje)) {
            $insertar = $conexion->prepare(
                "INSERT INTO productos (id_usuario, id_categoria, nombre, descripcion, precio, estado, imagen)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $insertar->bind_param(
                "iissdss",
                $id_usuario,
                $id_categoria,
                $nombre,
                $descripcion,
                $precio,
                $estado,
                $nombre_imagen
            );

            if ($insertar->execute()) {
                $mensaje = "Producto publicado correctamente.";
                $tipo_mensaje = "mensaje-exito";
            } else {
                $mensaje = "No se pudo guardar el producto.";
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
    <title>Publicar producto - Marketplace Local</title>
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
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="perfil.php">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Publicar producto</h1>
            <p class="texto">Completa los datos del producto que quieres vender localmente.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form class="formulario" action="publicar.php" method="POST" enctype="multipart/form-data">
                <div class="campo">
                    <label for="nombre">Nombre del producto</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="campo">
                    <label for="descripcion">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>

                <div class="campo">
                    <label for="id_categoria">Categoria</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">Selecciona una categoria</option>
                        <?php while ($categoria = $categorias->fetch_assoc()): ?>
                            <option value="<?php echo $categoria["id_categoria"]; ?>">
                                <?php echo htmlspecialchars($categoria["nombre"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="precio">Precio</label>
                    <input type="number" id="precio" name="precio" min="1" step="0.01" required>
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="">Selecciona el estado</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="Usado">Usado</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="imagen">Imagen del producto</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <input type="submit" value="Publicar producto">
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


