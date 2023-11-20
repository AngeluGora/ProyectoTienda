<?php 
session_start();

require_once 'funciones.php';
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Anuncio.php';
require_once 'Modelos/AnunciosDAO.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/config.php';

//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//Creamos el objeto AnunciosDAO para acceder a BBDD a través de este objeto
$anunciosDAO = new AnunciosDAO($conn);

//Obtener el anuncio
$idAnuncio = htmlspecialchars($_GET['id']);
$anuncio = $anunciosDAO->getById($idAnuncio);

//Comprobamos que anuncio pertenece al usuario conectado
if($_SESSION['id']==$anuncio->getIdUsuario()){
    $anunciosDAO->delete($idAnuncio);
}
else
{
    guardarAnuncio("No puedes borrar este Anuncio");
}

header('location: ../index.php');