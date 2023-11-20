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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?= $error ?>
    <form action="editar_anuncio.php?id=<?= $idAnuncio ?>" method="post">
        <input type="text" name="titulo" placeholder="Titulo" value="<?=$anuncio->getTitulo()?>"><br>
        <textarea name="descripcion" placeholder="Descripcion"><?=$anuncio->getDescripcion()?></textarea><br>
        <select name="idUsuario">
            <?php foreach($usuarios as $usuario): ?>
                <?php if($usuario->getId() == $anuncio->getIdUsuario()):?>
                    <option value="<?= $usuario->getId() ?>" selected><?= $usuario->getEmail() ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select><br>
        <input type="submit">
    </form>
</body>
</html>