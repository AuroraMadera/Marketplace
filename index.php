<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace Local</title>
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

    <main>
        <section class="seccion">
            <div class="contenedor">
                <h1 class="titulo">Compra y vende productos en tu comunidad</h1>
                <p class="texto">
                    Este marketplace escolar permite publicar productos, consultar articulos,
                    simular un carrito de compra y contactar vendedores. No se manejan pagos reales.
                </p>

                <a class="boton" href="productos.php">Ver productos</a>

                <?php if (isset($_SESSION["id_usuario"])): ?>
                    <a class="boton boton-secundario" href="publicar.php">Publicar producto</a>
                <?php else: ?>
                    <a class="boton boton-secundario" href="registro.php">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="seccion">
            <div class="contenedor">
                <h2 class="titulo">Funciones principales</h2>

                <div class="grid-productos">
                    <article class="tarjeta-producto">
                        <div class="contenido-producto">
                            <h3>Publica productos</h3>
                            <p class="texto">
                                Los usuarios registrados pueden subir productos con imagen,
                                categoria, precio, ubicacion y estado.
                            </p>
                        </div>
                    </article>

                    <article class="tarjeta-producto">
                        <div class="contenido-producto">
                            <h3>Busca y filtra</h3>
                            <p class="texto">
                                El catalogo permite encontrar productos por nombre, categoria,
                                ubicacion, precio y estado.
                            </p>
                        </div>
                    </article>

                    <article class="tarjeta-producto">
                        <div class="contenido-producto">
                            <h3>Carrito simulado</h3>
                            <p class="texto">
                                Agrega productos al carrito, revisa el total y confirma una compra
                                de prueba sin pagos reales.
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <footer class="pie">
        <div class="contenedor">
            <p>Proyecto escolar - Marketplace local sin pagos reales</p>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
