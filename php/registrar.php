<?php 
require_once 'Modelos/ConexionDB.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/UsuariosDAO.php';
require_once 'Modelos/config.php';
require_once 'funciones.php';

$error='';

if($_SERVER['REQUEST_METHOD']=='POST'){

    //Limpiamos los datos
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $nombre=htmlentities($_POST['nombre']);
    $telefono=htmlentities($_POST['telefono']);
    $poblacion=htmlentities($_POST['poblacion']);
    $foto = '';

    //Validación 

    //Conectamos con la BD
    $conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
    $conn = $conexionDB->getConexion();
    if(empty($email)){
        $error="Tiene que insertar un email";
    }
    if(strlen($password) < 4){
        $error = "La contraseña debe tener al menos 4 caracteres";
    }
    //Compruebo que no haya un usuario registrado con el mismo email
    $usuariosDAO = new UsuariosDAO($conn);
    if($usuariosDAO->getByEmail($email) != null){
        $error = "Ya hay un usuario con ese email";
    }
    else{
        //Copiamos la foto al disco
       // Verificar si se ha proporcionado un archivo y si es una imagen
        if ($_FILES['foto']['name'] !== '') {
            if ($_FILES['foto']['type'] != 'image/jpeg' &&
                $_FILES['foto']['type'] != 'image/webp' &&
                $_FILES['foto']['type'] != 'image/png') {
                $error = "La foto no tiene el formato admitido, debe ser jpg, webp o png";
            } else {
                // Calculamos un hash para el nombre del archivo
                $foto = generarNombreArchivo($_FILES['foto']['name']);

                // Si existe un archivo con ese nombre, volvemos a calcular el hash
                while (file_exists("fotosUsuarios/$foto")) {
                    $foto = generarNombreArchivo($_FILES['foto']['name']);
                }

                if (!move_uploaded_file($_FILES['foto']['tmp_name'], "fotosUsuarios/$foto")) {
                    die("Error al copiar la foto a la carpeta fotosUsuarios");
                }
            }
        }

        

        if($error == '')    //Si no hay error
        {
            //Insertamos en la BD
            
            $usuario = new Usuario();
            $usuario->setNombre($nombre);
            $usuario->setEmail($email);
            //encriptamos el password
            $passwordCifrado = password_hash($password,PASSWORD_DEFAULT);
            $usuario->setPassword($passwordCifrado);
            $usuario->setFoto($foto);
            $usuario->setSid(sha1(rand()+time()), true);
            $usuario->setPoblacion($poblacion);
            $usuario->setTelefono($telefono);

            if($usuariosDAO->insert($usuario)){
                header("location: ../index.php");
                die();
            }else{
                $error = "No se ha podido insertar el usuario";
            }
        }
    }
    
}

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>REGISTO</title>
</head>
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
    <h1>Registro</h1>
    <?= $error ?>
    <form action="registrar.php" method="post" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>"><br>
        <input type="email" name="email" placeholder="Email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"><br>
        <input type="password" name="password" placeholder="Contraseña"><br>
        <input type="text" name="telefono" placeholder="Telefono" value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>"><br>
        <input type="text" name="poblacion" placeholder="Poblacion" value="<?= isset($_POST['poblacion']) ? htmlspecialchars($_POST['poblacion']) : '' ?>"><br>
        <input type="file" name="foto" accept="image/jpeg, image/gif, image/webp, image/png"><br>
        <input type="submit" value="registrar">
        <a href="../index.php">volver</a>
    </form>
</body>
</html>