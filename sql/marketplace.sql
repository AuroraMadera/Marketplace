CREATE DATABASE IF NOT EXISTS marketplace_local
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE marketplace_local;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL
);

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    ubicacion VARCHAR(120) NOT NULL,
    estado ENUM('Nuevo', 'Usado') NOT NULL,
    imagen VARCHAR(255),
    disponible TINYINT(1) DEFAULT 1,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON DELETE RESTRICT
);

CREATE TABLE carrito (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

CREATE TABLE compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);

CREATE TABLE detalle_compras (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra)
        ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE
);

CREATE TABLE comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    id_usuario INT NOT NULL,
    comentario TEXT NOT NULL,
    calificacion INT NOT NULL,
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    CHECK (calificacion BETWEEN 1 AND 5)
);

INSERT INTO categorias (nombre) VALUES
('Electronica'),
('Ropa'),
('Hogar'),
('Libros'),
('Deportes'),
('Juguetes'),
('Otros');

-- Datos simulados para demostracion del marketplace.
-- Password de usuarios de prueba: 123456
INSERT INTO usuarios (nombre, correo, password, telefono) VALUES
('Ana Lopez', 'ana.demo@marketplace.local', '$2y$10$NMGC8V8qQlMB34vlZFxMgOztJ95kVRQQyX.zNBA2j/F7h3ZJ7Ev.y', '9991112233'),
('Carlos Perez', 'carlos.demo@marketplace.local', '$2y$10$NMGC8V8qQlMB34vlZFxMgOztJ95kVRQQyX.zNBA2j/F7h3ZJ7Ev.y', '9992223344'),
('Maria Gomez', 'maria.demo@marketplace.local', '$2y$10$NMGC8V8qQlMB34vlZFxMgOztJ95kVRQQyX.zNBA2j/F7h3ZJ7Ev.y', '9993334455');

INSERT INTO productos (id_usuario, id_categoria, nombre, descripcion, precio, ubicacion, estado, imagen, disponible) VALUES
(1, 1, 'Celular Samsung usado', 'Celular en buen estado, ideal para uso escolar y redes sociales.', 2800.00, 'Centro', 'Usado', '', 1),
(2, 2, 'Chamarra azul', 'Chamarra casual talla mediana, poco uso.', 350.00, 'Norte', 'Usado', '', 1),
(3, 4, 'Libro de programacion', 'Libro basico para aprender logica y fundamentos de programacion.', 180.00, 'Sur', 'Usado', '', 1),
(1, 3, 'Lampara de escritorio', 'Lampara LED para estudio con luz blanca.', 220.00, 'Poniente', 'Nuevo', '', 1),
(2, 5, 'Balon de futbol', 'Balon numero 5 para entrenamiento.', 160.00, 'Oriente', 'Nuevo', '', 1),
(3, 7, 'Mochila escolar', 'Mochila resistente con varios compartimentos.', 420.00, 'Centro', 'Usado', '', 1);

INSERT INTO comentarios (id_producto, id_usuario, comentario, calificacion) VALUES
(1, 2, 'Producto publicado con buena descripcion y precio justo.', 5),
(1, 3, 'El vendedor responde rapido.', 4),
(2, 1, 'La prenda se ve en buen estado.', 4),
(3, 1, 'Util para clases de programacion.', 5),
(4, 2, 'Buena opcion para estudiar en casa.', 5),
(5, 3, 'Precio accesible para estudiantes.', 4),
(6, 1, 'La mochila tiene buena capacidad.', 4);
