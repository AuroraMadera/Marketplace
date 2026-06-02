<?php
session_start();
require_once "conexion.php";

$busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";
$id_categoria = isset($_GET["id_categoria"]) ? $_GET["id_categoria"] : "";
$ubicacion = isset($_GET["ubicacion"]) ? trim($_GET["ubicacion"]) : "";
$estado = isset($_GET["estado"]) ? $_GET["estado"] : "";
$precio_minimo = isset($_GET["precio_minimo"]) ? $_GET["precio_minimo"] : "";
$precio_maximo = isset($_GET["precio_maximo"]) ? $_GET["precio_maximo"] : "";
$mensaje = "";
$tipo_mensaje = "mensaje-error";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_carrito"])) {
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: login.php");
        exit;
    }

    $id_usuario = $_SESSION["id_usuario"];
    $id_producto = (int) $_POST["id_producto"];

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

$sql = "SELECT productos.*, categorias.nombre AS categoria, usuarios.nombre AS vendedor,
               calificaciones.promedio AS promedio_calificacion,
               calificaciones.total AS total_calificaciones
        FROM productos
        INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
        INNER JOIN usuarios ON productos.id_usuario = usuarios.id_usuario
        LEFT JOIN (
            SELECT id_producto, AVG(calificacion) AS promedio, COUNT(*) AS total
            FROM comentarios
            GROUP BY id_producto
        ) AS calificaciones ON productos.id_producto = calificaciones.id_producto
        WHERE productos.disponible = 1";

$parametros = array();
$tipos = "";

if (!empty($busqueda)) {
    $sql .= " AND (productos.nombre LIKE ? OR productos.descripcion LIKE ? OR categorias.nombre LIKE ? OR usuarios.nombre LIKE ?)";
    $valor_busqueda = "%" . $busqueda . "%";
    $parametros[] = $valor_busqueda;
    $parametros[] = $valor_busqueda;
    $parametros[] = $valor_busqueda;
    $parametros[] = $valor_busqueda;
    $tipos .= "ssss";
}

if (!empty($id_categoria)) {
    $sql .= " AND productos.id_categoria = ?";
    $parametros[] = $id_categoria;
    $tipos .= "i";
}

if (!empty($ubicacion)) {
    $sql .= " AND productos.ubicacion LIKE ?";
    $parametros[] = "%" . $ubicacion . "%";
    $tipos .= "s";
}

if (!empty($estado)) {
    $sql .= " AND productos.estado = ?";
    $parametros[] = $estado;
    $tipos .= "s";
}

if (!empty($precio_minimo)) {
    $sql .= " AND productos.precio >= ?";
    $parametros[] = $precio_minimo;
    $tipos .= "d";
}

if (!empty($precio_maximo)) {
    $sql .= " AND productos.precio <= ?";
    $parametros[] = $precio_maximo;
    $tipos .= "d";
}

$sql .= " ORDER BY productos.fecha_publicacion DESC";

$consulta = $conexion->prepare($sql);

if (!empty($parametros)) {
    $consulta->bind_param($tipos, ...$parametros);
}

$consulta->execute();
$productos = $consulta->get_result();

$categorias = $conexion->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Marketplace Local</title>
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
            <h1 class="titulo">Catalogo de productos</h1>
            <p class="texto">Busca productos publicados por usuarios de tu comunidad.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form class="filtros" action="productos.php" method="GET">
                <div class="campo">
                    <label for="busqueda">Buscar</label>
                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        placeholder="Nombre, descripcion o categoria"
                    >
                </div>

                <div class="campo">
                    <label for="id_categoria">Categoria</label>
                    <select id="id_categoria" name="id_categoria">
                        <option value="">Todas</option>
                        <?php while ($categoria = $categorias->fetch_assoc()): ?>
                            <option
                                value="<?php echo $categoria["id_categoria"]; ?>"
                                <?php if ($id_categoria == $categoria["id_categoria"]) echo "selected"; ?>
                            >
                                <?php echo htmlspecialchars($categoria["nombre"]); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="ubicacion">Ubicacion o zona</label>
                    <input
                        type="text"
                        id="ubicacion"
                        name="ubicacion"
                        value="<?php echo htmlspecialchars($ubicacion); ?>"
                        placeholder="Ejemplo: Centro"
                    >
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="Nuevo" <?php if ($estado == "Nuevo") echo "selected"; ?>>Nuevo</option>
                        <option value="Usado" <?php if ($estado == "Usado") echo "selected"; ?>>Usado</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="precio_minimo">Precio minimo</label>
                    <input
                        type="number"
                        id="precio_minimo"
                        name="precio_minimo"
                        min="0"
                        step="0.01"
                        value="<?php echo htmlspecialchars($precio_minimo); ?>"
                    >
                </div>

                <div class="campo">
                    <label for="precio_maximo">Precio maximo</label>
                    <input
                        type="number"
                        id="precio_maximo"
                        name="precio_maximo"
                        min="0"
                        step="0.01"
                        value="<?php echo htmlspecialchars($precio_maximo); ?>"
                    >
                </div>

                <div class="campo">
                    <label>&nbsp;</label>
                    <div class="acciones-filtros">
                        <button type="submit">Filtrar</button>
                        <a class="boton boton-secundario" href="productos.php">Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="grid-productos">
                <?php if ($productos->num_rows > 0): ?>
                    <?php while ($producto = $productos->fetch_assoc()): ?>
                        <article class="tarjeta-producto">
                            <?php if (!empty($producto["imagen"])): ?>
                                <img
                                    src="img/productos/<?php echo htmlspecialchars($producto["imagen"]); ?>"
                                    alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                                >
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=Sin+imagen" alt="Sin imagen">
                            <?php endif; ?>

                            <div class="contenido-producto">
                                <h2><?php echo htmlspecialchars($producto["nombre"]); ?></h2>
                                <p class="precio">$<?php echo number_format($producto["precio"], 2); ?></p>
                                <span class="etiqueta"><?php echo htmlspecialchars($producto["categoria"]); ?></span>
                                <span class="etiqueta"><?php echo htmlspecialchars($producto["estado"]); ?></span>
                                <span class="etiqueta"><?php echo htmlspecialchars($producto["ubicacion"]); ?></span>
                                <?php if ($producto["total_calificaciones"] > 0): ?>
                                    <div class="bloque-calificacion">
                                        <?php echo renderizar_estrellas($producto["promedio_calificacion"]); ?>
                                        <span class="texto-calificacion">
                                            <?php echo number_format($producto["promedio_calificacion"], 1); ?>/5
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="etiqueta">Sin calificaciones</span>
                                <?php endif; ?>
                                <p class="texto">
                                    Vendedor: <?php echo htmlspecialchars($producto["vendedor"]); ?>
                                </p>
                                <div class="acciones-tarjeta">
                                    <a class="boton" href="detalle.php?id=<?php echo $producto["id_producto"]; ?>">
                                        Ver detalle
                                    </a>

                                    <?php if (isset($_SESSION["id_usuario"])): ?>
                                        <form action="productos.php" method="POST">
                                            <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                                            <button type="submit" name="agregar_carrito">Agregar al carrito</button>
                                        </form>
                                    <?php else: ?>
                                        <a class="boton boton-secundario" href="login.php">Inicia sesion</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="texto">No se encontraron productos con esos filtros.</p>
                <?php endif; ?>
            </div>
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

