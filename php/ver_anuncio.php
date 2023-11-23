<?php
session_start();
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Anuncio.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/FotosDAO.php';
require_once 'Modelos/config.php';

//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Creamos el objeto AnunciosDAO para acceder a BBDD a través de este objeto
$anunciosDAO = new AnunciosDAO($conn);

//Obtener el anuncio
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anunciosDAO->getById($idAnuncio);
$fotosDAO=new FotosDAO($conn);
$fotoPrincipal = $fotosDAO->getFotoPrincipal($anuncio->getId());
$nombreFotoP = $fotoPrincipal->getNombre();

$fotosNoPrincipales = $fotosDAO->getFotosNoPrincipales($anuncio->getId());

if (!isset($_SESSION['email']) && isset($_COOKIE['sid'])) {
    $usuariosDAO = new UsuariosDAO($conn);
    if ($usuario = $usuariosDAO->getBySid($_COOKIE['sid'])) {
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['id'] = $usuario->getId();
        $_SESSION['foto']= $usuario->getFoto();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncio</title>
    <link rel="stylesheet" href="../css/estilos.css">
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
    <div class="contenido">
        <header>
            <img src="../img/logo.png" alt="Logo de la web" class="logo">
            <nav class="menu">
                <a href="../index.php" class="enlaceMenu">Anuncios</a>
                <a href="misAnuncios.php" class="enlaceMenu">Mis Anuncios</a>
                <div id="enlaceform">
                    <?php if(isset($_SESSION['email'])): ?>
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
            <article class="anuncio" id="anuncioUnico">
                <?php if( $anuncio!= null): ?>
                <img src="fotosAnuncios/<?= $nombreFotoP?>" class="fotoAnuncio">
                <h4 class="titulo"><?= $anuncio->getTitulo() ?></h4>
                <p class="descripcion"><?= $anuncio->getDescripcion() ?></p>
                <p class="precio"><?= $anuncio->getPrecio() ?></p>
                <?php foreach ($fotosNoPrincipales as $foto): ?>
                    <img src="fotosAnuncios/<?= $foto->getNombre() ?>" class="fotoNoPAnuncio">
                <?php endforeach; ?>
                <div class="acciones">
                        <?php if (isset($_SESSION['email']) && $_SESSION['id'] == $anuncio->getIdUsuario()): ?>
                            <button><a onclick="confirmarBorrado(<?= $anuncio->getId() ?>)">Borrar</a></button>
                            <button><a href="editar_anuncio.php?id=<?= $anuncio->getId() ?>">Modificar</a></button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                <strong>Mensaje con id <?= $id ?> no encontrado</strong>
                <?php endif; ?>
                <br><br><br>
                <a href="../index.php">Volver al listado de mensajes</a>
            </article>
        </main>

        <footer>
            &copy; 2023 Angelu Store
        </footer>
    </div>
</body>

</html>