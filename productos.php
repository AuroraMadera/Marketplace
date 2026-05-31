<?php
session_start();
require_once "conexion.php";

$busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";
$id_categoria = isset($_GET["id_categoria"]) ? $_GET["id_categoria"] : "";
$estado = isset($_GET["estado"]) ? $_GET["estado"] : "";
$precio_minimo = isset($_GET["precio_minimo"]) ? $_GET["precio_minimo"] : "";
$precio_maximo = isset($_GET["precio_maximo"]) ? $_GET["precio_maximo"] : "";

$sql = "SELECT productos.*, categorias.nombre AS categoria, usuarios.nombre AS vendedor
        FROM productos
        INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
        INNER JOIN usuarios ON productos.id_usuario = usuarios.id_usuario
        WHERE productos.disponible = 1";

$parametros = array();
$tipos = "";

if (!empty($busqueda)) {
    $sql .= " AND productos.nombre LIKE ?";
    $parametros[] = "%" . $busqueda . "%";
    $tipos .= "s";
}

if (!empty($id_categoria)) {
    $sql .= " AND productos.id_categoria = ?";
    $parametros[] = $id_categoria;
    $tipos .= "i";
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

            <form class="filtros" action="productos.php" method="GET">
                <div class="campo">
                    <label for="busqueda">Buscar</label>
                    <input
                        type="text"
                        id="busqueda"
                        name="busqueda"
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        placeholder="Nombre del producto"
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
                    <button type="submit">Filtrar</button>
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
                                <p class="texto">
                                    Vendedor: <?php echo htmlspecialchars($producto["vendedor"]); ?>
                                </p>
                                <a class="boton" href="detalle.php?id=<?php echo $producto["id_producto"]; ?>">
                                    Ver detalle
                                </a>
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
</body>
</html>
