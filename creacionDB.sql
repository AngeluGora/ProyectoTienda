-- Crear la tabla "usuarios" con id autoincremental
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(255),
    telefono VARCHAR(20),
    poblacion VARCHAR(255),
    fotoUsu VARCHAR(255),
    sid VARCHAR(200) NOT NULL
);

-- Crear la tabla "anuncios" con id autoincremental, nombre y precio not null
CREATE TABLE anuncios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    fechaHoraPublicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Nuevo campo
    foto VARCHAR(255),
    idUsuario INT,
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
);

