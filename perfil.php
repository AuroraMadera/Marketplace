<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$consulta_usuario = $conexion->prepare(
    "SELECT nombre, correo, telefono, fecha_registro FROM usuarios WHERE id_usuario = ?"
);
$consulta_usuario->bind_param("i", $id_usuario);
$consulta_usuario->execute();
$usuario = $consulta_usuario->get_result()->fetch_assoc();

$consulta_productos = $conexion->prepare(
    "SELECT productos.*, categorias.nombre AS categoria
     FROM productos
     INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
     WHERE productos.id_usuario = ?
     ORDER BY productos.fecha_publicacion DESC"
);
$consulta_productos->bind_param("i", $id_usuario);
$consulta_productos->execute();
$productos = $consulta_productos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - Marketplace Local</title>
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
                    <li><a href="publicar.php">Publicar</a></li>
                    <li><a href="logout.php">Cerrar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Mi perfil</h1>

            <section class="formulario">
                <h2>Datos de usuario</h2>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario["nombre"]); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($usuario["correo"]); ?></p>

                <?php if (!empty($usuario["telefono"])): ?>
                    <p><strong>Telefono:</strong> <?php echo htmlspecialchars($usuario["telefono"]); ?></p>
                <?php endif; ?>

                <p><strong>Fecha de registro:</strong> <?php echo $usuario["fecha_registro"]; ?></p>
            </section>

            <section class="seccion">
                <h2 class="titulo">Mis productos publicados</h2>
                <a class="boton" href="publicar.php">Publicar nuevo producto</a>

                <?php if ($productos->num_rows > 0): ?>
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoria</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Disponible</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $productos->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <a href="detalle.php?id=<?php echo $producto["id_producto"]; ?>">
                                            <?php echo htmlspecialchars($producto["nombre"]); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto["categoria"]); ?></td>
                                    <td>$<?php echo number_format($producto["precio"], 2); ?></td>
                                    <td><?php echo htmlspecialchars($producto["estado"]); ?></td>
                                    <td>
                                        <?php echo $producto["disponible"] == 1 ? "Si" : "No"; ?>
                                    </td>
                                    <td>
                                        <a
                                            class="boton boton-secundario"
                                            href="editar_producto.php?id=<?php echo $producto["id_producto"]; ?>"
                                        >
                                            Editar
                                        </a>

                                        <a
                                            class="boton boton-peligro"
                                            href="eliminar_producto.php?id=<?php echo $producto["id_producto"]; ?>"
                                        >
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="texto">Todavia no has publicado productos.</p>
                <?php endif; ?>
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

