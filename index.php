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
<body class="pagina-inicio">
    <header class="encabezado">
        <div class="contenedor barra-navegacion">
            <a class="logo" href="index.php">Marketplace Local</a>

            <nav>
                <ul class="menu">
                    <li><a class="activo" href="index.php">Inicio</a></li>
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="estadisticas.php">Estadisticas</a></li>
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

    <main class="inicio-main">
        <section class="inicio-hero">
            <div class="hero-contenido">
                <p class="hero-etiqueta">Marketplace escolar sin pagos reales</p>
                <h1>Compra y vende productos de manera facil</h1>
                <p>
                    Plataforma para publicar productos, buscar articulos, filtrar resultados,
                    agregar al carrito y contactar vendedores de tu comunidad.
                </p>

                <div class="hero-acciones">
                    <a class="boton-hero principal" href="productos.php">Explorar productos <span>›</span></a>
                    <?php if (isset($_SESSION["id_usuario"])): ?>
                        <a class="boton-hero secundario" href="publicar.php">Publicar producto <span class="mini-plus"></span></a>
                    <?php else: ?>
                        <a class="boton-hero secundario" href="registro.php">Crear cuenta <span class="mini-plus"></span></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero-ilustracion" aria-hidden="true">
                <div class="circulo-fondo"></div>
                <div class="tienda-linea">
                    <div class="toldo"></div>
                    <div class="ventanas"><span></span><span></span><span></span></div>
                    <div class="lineas"><span></span><span></span><span></span></div>
                </div>
                <div class="carrito-linea"></div>
                <div class="burbuja corazon"></div>
                <div class="burbuja lupa"></div>
            </div>
        </section>

        <section class="inicio-funciones">
            <article class="inicio-card"><span class="card-icono card-plus"></span><h2>Publica productos</h2><p>Sube articulos con nombre, precio, imagen y descripcion.</p></article>
            <article class="inicio-card"><span class="card-icono card-search"></span><h2>Busca facilmente</h2><p>Encuentra productos por palabra clave, descripcion o vendedor.</p></article>
            <article class="inicio-card"><span class="card-icono card-filter"></span><h2>Filtra resultados</h2><p>Ordena por categoria, precio, estado y disponibilidad.</p></article>
            <article class="inicio-card"><span class="card-icono card-cart"></span><h2>Carrito simulado</h2><p>Guarda productos antes de confirmar una compra de prueba.</p></article>
            <article class="inicio-card"><span class="card-icono card-chat"></span><h2>Contacta al vendedor</h2><p>Consulta el detalle del producto y los datos del vendedor.</p></article>
        </section>

        <section class="inicio-aviso">
            <span class="aviso-icono"></span>
            <p>Marketplace Local facilita la compra y venta dentro de tu comunidad sin solicitar pagos reales.</p>
        </section>
    </main>

    <footer class="pie">
        <div class="contenedor"><p>Proyecto escolar - Marketplace local sin pagos reales</p></div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
