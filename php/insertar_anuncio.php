    <?php 
    session_start();

    require_once 'funciones.php';
    require_once 'Modelos/ConexionDB.php';
    require_once 'Modelos/Anuncio.php';
    require_once 'Modelos/AnunciosDAO.php';
    require_once 'Modelos/Usuario.php';
    require_once 'Modelos/UsuariosDAO.php';
    require_once 'Modelos/config.php';

    //¡¡Página privada!! Esto impide que puedan ver esta página
    //si no han iniciado sesión
    if(!isset($_SESSION['email'])){
        header("location: ../index.php");
        guardarAnuncio("No puedes insertar anuncios si no estás indentificado");
        die();
    }

    $error ='';
    $foto = '';

    //Creamos la conexión utilizando la clase que hemos creado
    $conexionDB = new ConexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
    $conn = $conexionDB->getConexion();

    $usuariosDAO = new UsuariosDAO($conn);
    $usuarios = $usuariosDAO->getAll();


    if($_SERVER['REQUEST_METHOD']=='POST'){

        //Limpiamos los datos que vienen del usuario
        $titulo = htmlspecialchars($_POST['titulo']);
        $descripcion =  htmlspecialchars($_POST['descripcion']);
        $precio =  htmlspecialchars($_POST['precio']);
        
        //$idUsuario = htmlspecialchars($_POST['idUsuario']);   //Solo necesario si queremos seleccionar usuario en el desplegable

        //Validamos los datos
        if(empty($titulo) || empty($descripcion)){
            $error = "Los dos campos son obligatorios";
        }
        elseif(empty($foto)){
                $error="Tiene que insertar una foto";
            }else{
            if($_FILES['foto']['type'] != 'image/jpeg' &&
            $_FILES['foto']['type'] != 'image/webp' &&
            $_FILES['foto']['type'] != 'image/png')
            {
                $error="La foto no tiene el formato admitido, debe ser jpg, webp o png";
            }
            else{
                //Calculamos un hash para el nombre del archivo
                $foto = generarNombreArchivo($_FILES['foto']['name']);

                //Si existe un archivo con ese nombre volvemos a calcular el hash
                while(file_exists("fotosAnuncios/$foto")){
                    $foto = generarNombreArchivo($_FILES['foto']['name']);
                }
                
                if(!move_uploaded_file($_FILES['foto']['tmp_name'], "fotosAnuncios/$foto")){
                    die("Error al copiar la foto a la carpeta fotosAnuncios");
                }
            }
                $fotosDAO= new FotosDAO($coon);
                $f-> setNombre($foto);
                $f-> setFotoPrincipal("true");
                $f-> setIdAnuncio();
                $fotosDAO->insert($f);
                
                $anunciosDAO = new AnunciosDAO($conn);
                $anuncio = new Anuncio();
                $anuncio->setTitulo($titulo);
                $anuncio->setDescripcion($descripcion);
                $anuncio->setIdUsuario($_SESSION['id']);
                
                // Obtener la fecha y hora actual en formato DATETIME
                $fechaHoraActual = date("Y-m-d H:i:s");
            
                $anuncio->setFechaPubli($fechaHoraActual);
                $anuncio->setIdFoto();
                $anuncio->setPrecio($precio);
                $anunciosDAO->insert($anuncio);
                header('location: ../index.php');
                die();
            }
        }

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/estilos.css">
        <title>Inserta Anuncio</title>
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
            <form action="login.php" method="post">
            <input type="email" name="email" placeholder="email">
            <input type="password" name="password" placeholder="password">
            <input type="submit" value="login">
            </form>
            <a href="registrar.php">Registrar</a>
            <?php endif; ?>
            </div>
        </nav>
    </header>
        <?= $error ?>
        <form action="insertar_anuncio.php" method="post" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Titulo"><br>
            <textarea name="descripcion" placeholder="Descripcion"></textarea><br>
            <input type="number" step="0.01" name="precio" placeholder="Precio" min="0.01" ><br>
            <input type="file" name="foto" accept="image/jpeg, image/gif, image/webp, image/png"><br>
                <?php foreach($usuarios as $usuario): ?>
                    <option value="<?= $usuario->getId() ?>"><?= $usuario->getEmail() ?></option>
                <?php endforeach; ?>
            <input type="submit">
        </form>
    </body>
    </html>
