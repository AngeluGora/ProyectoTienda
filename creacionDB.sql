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

CREATE TABLE fotos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(250) NOT NULL,
    fotoPrincipal BOOLEAN
);

CREATE TABLE anuncios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    fechaPublicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    idUsuario INT,
    FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
);

ALTER TABLE fotos
ADD COLUMN idAnuncio INT,
ADD FOREIGN KEY (idAnuncio) REFERENCES anuncios(id);



