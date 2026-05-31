<?php
session_start();
require_once "conexion.php";

if (!isset($_GET["id"])) {
    header("Location: productos.php");
    exit;
}

$id_producto = $_GET["id"];
$mensaje = "";
$tipo_mensaje = "mensaje-error";

// Agregar producto al carrito simulado.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_carrito"])) {
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: login.php");
        exit;
    }

    $id_usuario = $_SESSION["id_usuario"];

    $consulta_carrito = $conexion->prepare(
        "SELECT id_carrito, cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?"
    );
    $consulta_carrito->bind_param("ii", $id_usuario, $id_producto);
    $consulta_carrito->execute();
    $resultado_carrito = $consulta_carrito->get_result();

    if ($resultado_carrito->num_rows > 0) {
        $producto_carrito = $resultado_carrito->fetch_assoc();
        $nueva_cantidad = $producto_carrito["cantidad"] + 1;

        $actualizar = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_carrito = ?");
        $actualizar->bind_param("ii", $nueva_cantidad, $producto_carrito["id_carrito"]);
        $actualizar->execute();
    } else {
        $insertar = $conexion->prepare(
            "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, 1)"
        );
        $insertar->bind_param("ii", $id_usuario, $id_producto);
        $insertar->execute();
    }

    $mensaje = "Producto agregado al carrito.";
    $tipo_mensaje = "mensaje-exito";
}

// Guardar comentario y calificacion.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar_comentario"])) {
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: login.php");
        exit;
    }

    $id_usuario = $_SESSION["id_usuario"];
    $comentario = trim($_POST["comentario"]);
    $calificacion = $_POST["calificacion"];

    if (empty($comentario) || empty($calificacion)) {
        $mensaje = "Debes escribir un comentario y seleccionar una calificacion.";
    } elseif ($calificacion < 1 || $calificacion > 5) {
        $mensaje = "La calificacion debe estar entre 1 y 5.";
    } else {
        $insertar_comentario = $conexion->prepare(
            "INSERT INTO comentarios (id_producto, id_usuario, comentario, calificacion)
             VALUES (?, ?, ?, ?)"
        );
        $insertar_comentario->bind_param("iisi", $id_producto, $id_usuario, $comentario, $calificacion);

        if ($insertar_comentario->execute()) {
            $mensaje = "Comentario publicado correctamente.";
            $tipo_mensaje = "mensaje-exito";
        } else {
            $mensaje = "No se pudo guardar el comentario.";
        }
    }
}

$consulta_producto = $conexion->prepare(
    "SELECT productos.*, categorias.nombre AS categoria, usuarios.nombre AS vendedor, usuarios.telefono
     FROM productos
     INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
     INNER JOIN usuarios ON productos.id_usuario = usuarios.id_usuario
     WHERE productos.id_producto = ? AND productos.disponible = 1"
);
$consulta_producto->bind_param("i", $id_producto);
$consulta_producto->execute();
$resultado_producto = $consulta_producto->get_result();

if ($resultado_producto->num_rows == 0) {
    header("Location: productos.php");
    exit;
}

$producto = $resultado_producto->fetch_assoc();

$consulta_comentarios = $conexion->prepare(
    "SELECT comentarios.*, usuarios.nombre AS usuario
     FROM comentarios
     INNER JOIN usuarios ON comentarios.id_usuario = usuarios.id_usuario
     WHERE comentarios.id_producto = ?
     ORDER BY comentarios.fecha_comentario DESC"
);
$consulta_comentarios->bind_param("i", $id_producto);
$consulta_comentarios->execute();
$comentarios = $consulta_comentarios->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto["nombre"]); ?> - Marketplace Local</title>
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

                    <?php if (isset($_SESSION["id_usuario"])): ?>
                        <li><a href="publicar.php">Publicar</a></li>
                        <li><a href="perfil.php">Mi perfil</a></li>
                        <li><a href="logout.php">Cerrar sesion</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Iniciar sesion</a></li>
                        <li><a href="registro.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <section class="formulario">
                <h1 class="titulo"><?php echo htmlspecialchars($producto["nombre"]); ?></h1>

                <?php if (!empty($producto["imagen"])): ?>
                    <img
                        src="img/productos/<?php echo htmlspecialchars($producto["imagen"]); ?>"
                        alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                    >
                <?php else: ?>
                    <img src="https://via.placeholder.com/800x500?text=Sin+imagen" alt="Sin imagen">
                <?php endif; ?>

                <p class="precio">$<?php echo number_format($producto["precio"], 2); ?></p>
                <span class="etiqueta"><?php echo htmlspecialchars($producto["categoria"]); ?></span>
                <span class="etiqueta"><?php echo htmlspecialchars($producto["estado"]); ?></span>

                <p class="texto"><?php echo nl2br(htmlspecialchars($producto["descripcion"])); ?></p>
                <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($producto["vendedor"]); ?></p>

                <?php if (!empty($producto["telefono"])): ?>
                    <p><strong>Telefono:</strong> <?php echo htmlspecialchars($producto["telefono"]); ?></p>
                <?php endif; ?>

                <form action="detalle.php?id=<?php echo $id_producto; ?>" method="POST">
                    <button type="submit" name="agregar_carrito">Agregar al carrito</button>
                </form>
            </section>

            <section class="seccion">
                <h2 class="titulo">Comentarios y calificaciones</h2>

                <?php if (isset($_SESSION["id_usuario"])): ?>
                    <form class="formulario" action="detalle.php?id=<?php echo $id_producto; ?>" method="POST">
                        <div class="campo">
                            <label for="calificacion">Calificacion</label>
                            <select id="calificacion" name="calificacion" required>
                                <option value="">Selecciona</option>
                                <option value="5">5 - Excelente</option>
                                <option value="4">4 - Bueno</option>
                                <option value="3">3 - Regular</option>
                                <option value="2">2 - Malo</option>
                                <option value="1">1 - Muy malo</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label for="comentario">Comentario</label>
                            <textarea id="comentario" name="comentario" required></textarea>
                        </div>

                        <button type="submit" name="guardar_comentario">Publicar comentario</button>
                    </form>
                <?php else: ?>
                    <p class="texto">
                        Debes <a href="login.php">iniciar sesion</a> para comentar o agregar al carrito.
                    </p>
                <?php endif; ?>

                <div class="grid-productos">
                    <?php if ($comentarios->num_rows > 0): ?>
                        <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                            <article class="tarjeta-producto">
                                <div class="contenido-producto">
                                    <h3><?php echo htmlspecialchars($comentario["usuario"]); ?></h3>
                                    <p class="precio"><?php echo $comentario["calificacion"]; ?>/5</p>
                                    <p class="texto"><?php echo nl2br(htmlspecialchars($comentario["comentario"])); ?></p>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="texto">Este producto todavia no tiene comentarios.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer class="pie">
        <div class="contenedor">
            <p>Proyecto escolar - Marketplace local sin pagos reales</p>
        </div>
    </footer>
</body>
</html>
