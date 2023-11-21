<?php 
session_start();
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/config.php';
require_once 'funciones.php';


//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Si existe la cookie y no ha iniciado sesión, le iniciamos sesión de forma automática
if( !isset($_SESSION['email']) && isset($_COOKIE['sid'])){
    //Nos conectamos para obtener el id y la foto del usuario
    $usuariosDAO = new UsuariosDAO($conn);
    //$usuario = $usuariosDAO->getByEmail($_COOKIE['email']);
    if($usuario = $usuariosDAO->getBySid($_COOKIE['sid'])){
        //Inicio sesión
        $_SESSION['email']=$usuario->getEmail();
        $_SESSION['id']=$usuario->getId();
        $_SESSION['foto']=$usuario->getFoto();
    }
}

//Creamos el objeto AnunciosDAO para acceder a BBDD a través de este objeto
$anunciosDAO = new AnunciosDAO($conn);
$anuncios = $anunciosDAO->getAll();


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Mis Anuncios</title>
    </style>
</head>
    <script>
    function confirmarBorrado(id) {
        if (confirm('¿Estás seguro de que quieres borrar este anuncio?')) {
            window.location.href = `borrar_anuncio.php?id=${id}`;
        } else {
            // No hacer nada o mostrar un mensaje de cancelación
        }
    }
</script>
<body>
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
        <h1>Bienvenido a "Mis Anuncios"</h1>
        <h3>Aqui puedes crear, ver, editar y modificar tus anuncios.</3>
        <?php if (function_exists('imprimirAnuncio')): ?>
            <?php imprimirAnuncio(); ?>
            <?php foreach ($anuncios as $anuncio): ?>
                <?php if(isset($_SESSION['email']) && $_SESSION['id'] == $anuncio->getIdUsuario()): ?>
                    <article class="anuncio">
                    <h4 class="titulo"><a href="ver_anuncio.php?id=<?=$anuncio->getId()?>"><?= $anuncio->getTitulo() ?></a></h4>
                    <img src="fotoAnuncios/<?=$anuncio->getFoto()?>" alt="Foto del anuncio">
                    <p class="descripcion"><?= $anuncio->getDescripcion() ?></p>
                    <p class="precio"><?= $anuncio->getPrecio()?></p>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if(isset($_SESSION['email'])): ?>
                <a href="insertar_anuncio.php" class="nuevoAnuncio">Nuevo Anuncio</a>
            <?php else :?>
                <p>Tienes que iniciar sesion para ver tus anuncios</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No hay anuncios disponibles.</p>
        <?php endif; ?>

    </main>
    <script>
    setTimeout(function(){document.getElementById('anuncioError').style.display='none'},5000);
    </script>
    <footer>
        &copy; 2023 Angelu Store
    </footer>
</body>
</html>
