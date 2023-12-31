<?php 
session_start();
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/config.php';
require_once 'funciones.php';

//Creamos la conexión utilizando la clase que hemos creado
$conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $conexionDB->getConexion();

//limpiamos los datos que vienen del usuario
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);

//Validamos el usuario
$usuariosDAO = new UsuariosDAO($conn);
if($usuario = $usuariosDAO->getByEmail($email)){
    if(password_verify($password, $usuario->getPassword()))
    {
        //email y password correctos. Inciamos sesión
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['foto'] = $usuario->getFoto();
        $_SESSION['id'] = $usuario->getId();
        
        //Creamos la cookie para que nos recuerde 1 semana
        setcookie('sid',$usuario->getSid(),time()+24*60*60,'/');
        //Redirigimos a index.php
        header('location: ../index.php');
        die();
    }
}
//email o password incorrectos, redirigir a index.php
guardarAnuncio("Email o password incorrectos");
header('location: ../index.php');