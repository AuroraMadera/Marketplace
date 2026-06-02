<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas - Marketplace Local</title>
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
            <h1 class="titulo">Pruebas completas del flujo</h1>
            <p class="texto">Checklist de validacion usado para la entrega final del marketplace.</p>

            <section class="formulario">
                <h2>T-13. Pruebas completas del flujo</h2>
                <ul class="lista-check">
                    <li>Registro de usuario probado.</li>
                    <li>Inicio y cierre de sesion probado.</li>
                    <li>Publicacion de producto con imagen probada.</li>
                    <li>Catalogo, busqueda avanzada y filtros probados.</li>
                    <li>Detalle de producto, comentarios y calificaciones probados.</li>
                    <li>Carrito simulado, eliminacion y compra simulada probados.</li>
                    <li>Perfil, edicion y eliminacion de productos propios probados.</li>
                    <li>Panel de estadisticas probado.</li>
                </ul>
            </section>

            <section class="formulario">
                <h2>T-14. Correcciones finales y entrega</h2>
                <ul class="lista-check">
                    <li>Se corrigio la subida de imagenes creando automaticamente la carpeta de destino.</li>
                    <li>Se ajustaron imagenes cuadradas y tarjetas compactas del catalogo.</li>
                    <li>Se agregaron datos simulados para demostracion.</li>
                    <li>Se agregaron calificaciones de ejemplo.</li>
                    <li>Se agrego panel de estadisticas.</li>
                    <li>Se integro JavaScript basico para confirmaciones y mensajes.</li>
                    <li>Se mantiene la aclaracion de que no hay pagos reales.</li>
                </ul>
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

