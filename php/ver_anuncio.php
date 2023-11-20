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
    <style>
    .ver_anuncio{
        margin: 30px auto;
        padding:5px;
        border:1px solid black;
        width: 80%;
        min-height: 400px;
    }
    .titulo{
        font-size: 2em;
    }
    .descripcion{
        font-size: 1.5em;
    }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="ver_anuncio">
    <?php if( $anuncio!= null): ?>
        <div class="titulo"><?= $anuncio->getTitulo() ?> </div>
        <div class="descripcion"><?= $anuncio->getDescripcion() ?> </div>
        <div class="fecha"><?= $anuncio->getFechaPubli() ?> </div>
    <?php else: ?>
        <strong>Mensaje con id <?= $id ?> no encontrado</strong>
    <?php endif; ?>
    <br><br><br>
    <a href="../index.php">Volver al listado de mensajes</a>
</div>
</body>
</html>