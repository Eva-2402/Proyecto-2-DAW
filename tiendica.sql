-- Borrar si existe para evitar conflictos en la prueba
DROP DATABASE IF EXISTS tiendica;
CREATE DATABASE tiendica;
USE tiendica;

-- 1. Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario'
);

SELECT * FROM usuarios;
-- buscar al admin --
SELECT id_usuario, nombre, email
FROM usuarios
WHERE nombre = 'admin';
-- asignarle rol --
UPDATE usuarios
SET rol = 'admin'
WHERE id_usuario = 2;

-- 2. Categorías
CREATE TABLE tipos_productos (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

INSERT INTO tipos_productos (id_tipo, nombre) VALUES 
(1, 'Camas'), (2, 'Comederos'), (3, 'Higiene'), (4, 'Juguetes'), (5, 'Accesorios');


-- 3. Productos (con relación a categoría)
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo INT,
    nombre VARCHAR(150) NOT NULL,
    imagen VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_tipo) REFERENCES tipos_productos(id_tipo)
);
SELECT * FROM productos;
-- 4. Ventas 
CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
SELECT * FROM ventas;
-- 5. Detalle de Ventas
CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
SELECT * FROM detalle_ventas;

-- ==========================
-- DATOS DE PRUEBA
-- ==========================
INSERT INTO productos (nombre, imagen, precio) VALUES
('Arnés para perro verde', 'images/arnes.png', 12.86),
('Arnés para perro azul', 'images/correita.png', 12.86),
('Arnés para perro rojo', 'images/correa.png', 12.86),

('Bandana roja', 'images/bandana.png', 5.24),
('Bandana azul', 'images/bandanita.png', 5.24),
('Bandana verde', 'images/la-bandana.png', 5.24),

('Collar azul', 'images/collarcito.png', 9.35),
('Collar verde', 'images/el-collar.png', 9.35),
('Collar rojo', 'images/collar.png', 9.35),

('Cama redondita blandita', 'images/cama.png', 32.55),
('Cama rectangular', 'images/camita.png', 22.72),
('Cama cute', 'images/cama-gato.png', 14.91),

('Comedero rojo', 'images/cuenco-1.png', 11.11),
('Comedero azul', 'images/cuenco-2.png', 11.11),
('Comedero verde', 'images/cuenco-3.png', 11.11),

('Kit de cepillado dental', 'images/cepillado-dientes.png', 16.15),
('Cepillo de pelo anti-nudos', 'images/cepillo.png', 22.24),

('Arenero cerrado para gato', 'images/arenero.png', 36.36),

('Kit de champú y acondicionador', 'images/champu.png', 26.26),

('Rascador para gato', 'images/rascador.png', 56.26),

('Piscina para animales', 'images/piscina.png', 78.16),

('Juguete para perros', 'images/juguete.png', 7.38),
('Juguete para gatos', 'images/juguete-gatos.png', 4.22);
