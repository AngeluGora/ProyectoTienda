<?php 
session_start();

require_once 'funciones.php';
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Anuncio.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/config.php';
require_once 'Modelos/Foto.php';
require_once 'Modelos/FotosDAO.php';

//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Creamos el objeto AnunciosDAO/FotosDAO para acceder a BBDD a través de este objeto
$anunciosDAO = new AnunciosDAO($conn);
$fotosDAO= new FotosDAO($conn);
//Obtener el anuncio
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anunciosDAO->getById($idAnuncio);

//Comprobamos que anuncio pertenece al usuario conectado
if ($_SESSION['id'] === $anuncio->getIdUsuario()) {
    // Obtener el ID del anuncio
    $idAnuncio = $anuncio->getId();

    // Eliminar las fotos relacionadas con el anuncio
    $fotosDAO->borrarFotoYAnuncio(null, $idAnuncio);

    // Eliminar el anuncio
    if ($anunciosDAO->delete($idAnuncio)) {
        // Anuncio eliminado con éxito
        // Redirige o muestra un mensaje de éxito
    } else {
        // Error al eliminar el anuncio
        guardarAnuncio("Error al borrar el Anuncio");
        // Puedes redirigir o mostrar un mensaje de error
    }
} else {
    guardarAnuncio("No puedes borrar este Anuncio");
    // Puedes redirigir o mostrar un mensaje informando que no tiene permisos para borrar este anuncio
}

header('location: ../index.php');