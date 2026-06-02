<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$mensaje = "";
$tipo_mensaje = "mensaje-error";

// Eliminar un producto del carrito.
if (isset($_GET["eliminar"])) {
    $id_carrito = $_GET["eliminar"];

    $eliminar = $conexion->prepare("DELETE FROM carrito WHERE id_carrito = ? AND id_usuario = ?");
    $eliminar->bind_param("ii", $id_carrito, $id_usuario);

    if ($eliminar->execute()) {
        $mensaje = "Producto eliminado del carrito.";
        $tipo_mensaje = "mensaje-exito";
    } else {
        $mensaje = "No se pudo eliminar el producto.";
    }
}

// Confirmar una compra simulada.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmar_compra"])) {
    $consulta_total = $conexion->prepare(
        "SELECT carrito.id_producto, carrito.cantidad, productos.precio
         FROM carrito
         INNER JOIN productos ON carrito.id_producto = productos.id_producto
         WHERE carrito.id_usuario = ?"
    );
    $consulta_total->bind_param("i", $id_usuario);
    $consulta_total->execute();
    $productos_compra = $consulta_total->get_result();

    if ($productos_compra->num_rows == 0) {
        $mensaje = "Tu carrito esta vacio.";
    } else {
        $total = 0;
        $items = array();

        while ($item = $productos_compra->fetch_assoc()) {
            $subtotal = $item["precio"] * $item["cantidad"];
            $total += $subtotal;
            $items[] = $item;
        }

        $insertar_compra = $conexion->prepare("INSERT INTO compras (id_usuario, total) VALUES (?, ?)");
        $insertar_compra->bind_param("id", $id_usuario, $total);

        if ($insertar_compra->execute()) {
            $id_compra = $insertar_compra->insert_id;

            foreach ($items as $item) {
                $insertar_detalle = $conexion->prepare(
                    "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario)
                     VALUES (?, ?, ?, ?)"
                );
                $insertar_detalle->bind_param(
                    "iiid",
                    $id_compra,
                    $item["id_producto"],
                    $item["cantidad"],
                    $item["precio"]
                );
                $insertar_detalle->execute();
            }

            $vaciar_carrito = $conexion->prepare("DELETE FROM carrito WHERE id_usuario = ?");
            $vaciar_carrito->bind_param("i", $id_usuario);
            $vaciar_carrito->execute();

            $mensaje = "Compra simulada confirmada correctamente. No se realizo ningun pago real.";
            $tipo_mensaje = "mensaje-exito";
        } else {
            $mensaje = "No se pudo confirmar la compra.";
        }
    }
}

$consulta_carrito = $conexion->prepare(
    "SELECT carrito.id_carrito, carrito.cantidad, productos.id_producto, productos.nombre,
            productos.precio, productos.imagen
     FROM carrito
     INNER JOIN productos ON carrito.id_producto = productos.id_producto
     WHERE carrito.id_usuario = ?
     ORDER BY carrito.fecha_agregado DESC"
);
$consulta_carrito->bind_param("i", $id_usuario);
$consulta_carrito->execute();
$carrito = $consulta_carrito->get_result();

$total_carrito = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Marketplace Local</title>
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
                    <li><a href="publicar.php">Publicar</a></li>
                    <li><a href="perfil.php">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="seccion">
        <div class="contenedor">
            <h1 class="titulo">Carrito simulado</h1>
            <p class="texto">Revisa los productos agregados. Esta compra no realiza pagos reales.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <?php if ($carrito->num_rows > 0): ?>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $carrito->fetch_assoc()): ?>
                            <?php
                            $subtotal = $item["precio"] * $item["cantidad"];
                            $total_carrito += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <a href="detalle.php?id=<?php echo $item["id_producto"]; ?>">
                                        <?php echo htmlspecialchars($item["nombre"]); ?>
                                    </a>
                                </td>
                                <td>$<?php echo number_format($item["precio"], 2); ?></td>
                                <td><?php echo $item["cantidad"]; ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <a
                                        class="boton boton-peligro"
                                        href="carrito.php?eliminar=<?php echo $item["id_carrito"]; ?>"
                                    >
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="total-carrito">
                    <h2>Total simulado: $<?php echo number_format($total_carrito, 2); ?></h2>

                    <form action="carrito.php" method="POST">
                        <button type="submit" name="confirmar_compra">
                            Confirmar compra simulada
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <p class="texto">Tu carrito esta vacio.</p>
                <a class="boton" href="productos.php">Ver productos</a>
            <?php endif; ?>
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

