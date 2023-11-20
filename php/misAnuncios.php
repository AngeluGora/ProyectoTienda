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
    <title>Mis Anuncios</title>
    </style>
</head>
<style>
    .anuncio{
        margin: 30px auto;
        padding:5px;
        border:1px solid black;
        width: 80%;
        position: relative;
    }
    .nuevoAnuncio{
        margin: 30px auto;
        padding:5px;
        border:1px solid black;
        width: 80%;
        background-color: #00f;        
        color:white;
        display: block;
        text-align: center;
        text-decoration: none;
    }
    .titulo{
        font-size: 2em;
    }
    .texto{
        font-size: 1.5em;
    }
    .icono_borrar{
        top: 5px;
        right: 5px;
        position: absolute;
    }
    .icono_editar{
        top: 5px;
        right: 25px;
        position: absolute;
    }
    .color_gris:hover{
        color:black;
    }
    .color_gris{
        color:#aaa;
    }

    .error{
        color:red;
        display: block;
        padding: 5px;
        margin: auto;
        width: 80%;
        border: 1px solid red;
        text-align: center;
        margin-top: 20px;

    }
    .fotoUsuario{
        height: 50px;;
    }
    header{
        margin: 0px auto;
        padding:5px;
        border:1px solid black;
        width: 80%;
        position: relative;
        height: 140px;
    }
    .tituloPagina{
        text-align: center;
    }
    </style>
    <script>
    function confirmarBorrado(id) {
        if (confirm('¿Estás seguro de que quieres borrar este anuncio?')) {
            window.location.href = `php/borrar_anuncio.php?id=${id}`;
        } else {
            // No hacer nada o mostrar un mensaje de cancelación
        }
    }
</script>
<body>
    <header>
        <img src="logo.png" alt="Logo de la web" style="max-width: 100%;">

        <nav class="menu">
            <a href="../index.php">Anuncios</a>
            <a href="#">Mis Anuncios</a>
        <?php if(isset($_SESSION['email'])): ?>
            <img src="fotosUsuarios/<?= $_SESSION['foto']?>" class="fotoUsuario">
            <span class="emailUsuario"><?= $_SESSION['email'] ?></span>
            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="email">
            <input type="password" name="password" placeholder="password">
            <input type="submit" value="login">
        </form>
        <a href="registrar.php">Registrar</a>
    <?php endif; ?>
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
        <h4 class="titulo"><a href="php/ver_anuncio.php?id=<?=$anuncio->getId()?>"><?= $anuncio->getTitulo() ?></a></h4>
        <img src="<?=$anuncio->getFoto()?>" alt="Foto del anuncio">
        <p class="descripcion"><?= $anuncio->getDescripcion() ?></p>
        <p class="precio"><?= $anuncio->getPrecio()?></p>
        </article>
        <?php endif; ?>
        
    
    <?php endforeach; ?>
    <?php if(isset($_SESSION['email'])): ?>
        <a href="php/insertar_anuncio.php" class="nuevoAnuncio">Nuevo Anuncio</a>
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
