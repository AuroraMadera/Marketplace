<?php
session_start();
require_once "conexion.php";

$total_usuarios = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()["total"];
$total_productos = $conexion->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()["total"];
$total_disponibles = $conexion->query("SELECT COUNT(*) AS total FROM productos WHERE disponible = 1")->fetch_assoc()["total"];
$total_compras = $conexion->query("SELECT COUNT(*) AS total FROM compras")->fetch_assoc()["total"];
$total_simulado = $conexion->query("SELECT IFNULL(SUM(total), 0) AS total FROM compras")->fetch_assoc()["total"];
$promedio_calificacion = $conexion->query("SELECT IFNULL(AVG(calificacion), 0) AS promedio FROM comentarios")->fetch_assoc()["promedio"];

$productos_categoria = $conexion->query(
    "SELECT categorias.nombre, COUNT(productos.id_producto) AS total
     FROM categorias
     LEFT JOIN productos ON categorias.id_categoria = productos.id_categoria
     GROUP BY categorias.id_categoria, categorias.nombre
     ORDER BY total DESC"
);

$productos_recientes = $conexion->query(
    "SELECT productos.nombre, productos.precio, productos.estado, categorias.nombre AS categoria
     FROM productos
     INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
     ORDER BY productos.fecha_publicacion DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadisticas - Marketplace Local</title>
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
            <h1 class="titulo">Panel de estadisticas</h1>
            <p class="texto">Resumen general para la demostracion del marketplace sin pagos reales.</p>

            <section class="grid-estadisticas">
                <article class="tarjeta-estadistica"><span>Usuarios</span><strong><?php echo $total_usuarios; ?></strong></article>
                <article class="tarjeta-estadistica"><span>Productos</span><strong><?php echo $total_productos; ?></strong></article>
                <article class="tarjeta-estadistica"><span>Disponibles</span><strong><?php echo $total_disponibles; ?></strong></article>
                <article class="tarjeta-estadistica"><span>Compras simuladas</span><strong><?php echo $total_compras; ?></strong></article>
                <article class="tarjeta-estadistica"><span>Total simulado</span><strong>$<?php echo number_format($total_simulado, 2); ?></strong></article>
                <article class="tarjeta-estadistica"><span>Calificacion promedio</span><strong><?php echo number_format($promedio_calificacion, 1); ?>/5</strong></article>
            </section>

            <section class="seccion">
                <h2 class="titulo">Productos por categoria</h2>
                <table class="tabla">
                    <thead>
                        <tr><th>Categoria</th><th>Total de productos</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($categoria = $productos_categoria->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria["nombre"]); ?></td>
                                <td><?php echo $categoria["total"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <section class="seccion">
                <h2 class="titulo">Productos recientes</h2>
                <table class="tabla">
                    <thead>
                        <tr><th>Producto</th><th>Categoria</th><th>Estado</th><th>Precio</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($producto = $productos_recientes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($producto["categoria"]); ?></td>
                                <td><?php echo htmlspecialchars($producto["estado"]); ?></td>
                                <td>$<?php echo number_format($producto["precio"], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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

