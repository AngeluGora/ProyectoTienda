<?php

require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Anuncio.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/config.php';

//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Creamos el objeto AnunciosDAO para acceder a BBDD a través de este objeto
$anunciosDAO = new AnunciosDAO($conn);

//Obtener el anuncio
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anunciosDAO->getById($idAnuncio);
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncio</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="contenido">
    <header>
        <img src="../img/logo.png" alt="Logo de la web" class="logo">
        <nav class="menu">
            <a href="../index.php" class="enlaceMenu">Anuncios</a>
            <a href="misAnuncios.php" class="enlaceMenu">Mis Anuncios</a>
            <div id="enlaceform">
            <?php if(isset($_SESSION['email'])): ?>
            <img src="fotosUsuarios/<?= $_SESSION['foto']?>" class="fotoUsuario">
            <span class="emailUsuario"><?= $_SESSION['email'] ?></span>
            <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
            <a href="inicioSesion.php" class="enlaceMenu">Iniciar Sesion</a>
            <a href="registrar.php" class="enlaceMenu">Registrar</a>
            <?php endif; ?>
            </div>
        </nav>
    </header>

        <main>
        <div class="anuncio" id="anuncioUnico">
            <?php if( $anuncio!= null): ?>
            <div><img src="fotosAnuncios/<?= $anuncio->getFoto()?>"></div>
            <div class="titulo"><?= $anuncio->getTitulo() ?> </div>
            <div class="descripcion"><?= $anuncio->getDescripcion() ?> </div>
            <div class="fecha"><?= $anuncio->getFechaPubli() ?> </div>
            <?php else: ?>
            <strong>Mensaje con id <?= $id ?> no encontrado</strong>
            <?php endif; ?>
            <br><br><br>
            <a href="../index.php">Volver al listado de mensajes</a>
        </div>
        </main>

        <footer>
            &copy; 2023 Angelu Store
        </footer>
    </div>
</body>
</html>