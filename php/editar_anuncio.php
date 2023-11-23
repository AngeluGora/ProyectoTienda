<?php
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/Anuncio.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/config.php';
$error='';
//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Obtengo el id del anuncio que viene por GET
$idAnuncio = htmlspecialchars($_GET['id']);
//Obtengo el anuncio de la BD
$anuncioDAO = new AnunciosDAO($conn);
$anuncio = $anuncioDAO->getById($idAnuncio);

//Obtenemos los usuarios de la BD para el desplegable
$usuariosDAO = new UsuariosDAO($conn);
$usuarios = $usuariosDAO->getAll();

//Cuando se envíe el formulario actualizo el anuncio en la BD
if($_SERVER['REQUEST_METHOD']=='POST'){

    //Limpiamos los datos que vienen del usuario
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $idUsuario = htmlspecialchars($_POST['idUsuario']);

    //Validamos los datos
    if(empty($titulo) || empty($descripcion)){
        $error = "Los dos campos son obligatorios";
    }
    else{
        $anuncio->setTitulo($titulo);
        $anuncio->setDescripcion($descripcion);
        $anuncio->setIdUsuario($idUsuario);

        if($anuncioDAO->update($anuncio)){
            header('location: ../index.php');
            die();
        }
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
            <?= $error ?>
            <form action="editar_anuncio.php?id=<?= $idAnuncio ?>" method="post" id="uploadForm" enctype="multipart/form-data">
                <input type="text" name="titulo" placeholder="Titulo" value="<?=$anuncio->getTitulo()?>"><br>
                <textarea name="descripcion" placeholder="Descripcion"><?=$anuncio->getDescripcion()?></textarea><br>
                <input type="number" step="0.01" name="precio" placeholder="Precio" min="0.01" value="<?=$anuncio->getPrecio()?>"><br>
                <label for="fileInput">Selecciona la foto principal:</label>
                <input type="file" name="foto" accept="image/jpeg, image/gif, image/webp, image/png"><br>
                <label for="fileInput2">Selecciona más fotos:</label>
                <input type="file" name="fileInput2[]" id="fileInput2" multiple>
                <input type="button" value="Subir Foto" onclick="uploadFile()">
                <div id="imageContainer"></div>
                <script src="../js/upload.js"></script>
                <input type="submit">
            </form>
        </main>
        <footer>
            &copy; 2023 Angelu Store
        </footer>
    </div>
</body>
</html>