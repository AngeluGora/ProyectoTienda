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
    $descripcion =  htmlspecialchars($_POST['descripcion']);
    $precio =  htmlspecialchars($_POST['precio']);
    $foto = $_FILES['foto']['name']; 

    $partes = explode(',', $precio);

        if (count($partes) === 2) {
            // La coma está presente en el precio, dividir la parte entera y decimal
            $parte_entera = $partes[0]; // Parte entera del número
            $parte_decimal = $partes[1]; // Parte decimal del número

            $longitud_entera = strlen($parte_entera); // Longitud de la parte entera
            $longitud_decimal = strlen($parte_decimal); // Longitud de la parte decimal
        } else {
            // No se encontró una coma, considerar todo como parte entera y la parte decimal como vacía
            $parte_entera = $precio;
            $parte_decimal = '';
            $longitud_entera = strlen($parte_entera); // Longitud de la parte entera
            $longitud_decimal = 0; // Longitud de la parte decimal será cero
        }

        //Validamos los datos
        if (empty($titulo) || empty($descripcion) || empty($foto) || empty($precio)){
            $error = "Los campos obligatorios son: Titulo/Descripcion/Foto/Precio";
        } elseif ($longitud_entera > 7 || $longitud_decimal > 2) {
            // El número excede la longitud permitida
            $error = "El precio no puede ser mayor a 9999999.99€";
        }else {
        if($_FILES['foto']['type'] != 'image/jpeg' &&
            $_FILES['foto']['type'] != 'image/webp' &&
            $_FILES['foto']['type'] != 'image/png') {
    
            $error = "La foto no tiene el formato admitido, debe ser jpg, webp o png";
        } else {
            // Calculamos un hash para el nombre del archivo
            $nombreArchivo = generarNombreArchivo($_FILES['foto']['name']);
    
            // Si existe un archivo con ese nombre volvemos a calcular el hash
            while(file_exists("fotosAnuncios/$nombreArchivo")) {
                $nombreArchivo = generarNombreArchivo($_FILES['foto']['name']);
            }
            
            if(!move_uploaded_file($_FILES['foto']['tmp_name'], "fotosAnuncios/$nombreArchivo")) {
                die("Error al copiar la foto a la carpeta fotosAnuncios");
            }
        }
    
        $fotosDAO = new FotosDAO($conn);
        $f = new Foto();
        $f->setNombre($nombreArchivo);
        $f->setFotoPrincipal(true);
        $fotosDAO->actualizaFotoPrincipal($idAnuncio, $f);
    
        $response = array();
        $carpetaDestino = "fotosAnuncios/";
        $archivos = $_FILES['fileInput2'];
        $nuevasFotos = array(); // Array para almacenar las nuevas fotos
    
        foreach ($archivos['name'] as $key => $name) {
            $uploadOk = 1;
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $archivoDestino = md5(time() + rand()) . ".$extension";
    
            // Verifica si el archivo ya existe
            while (file_exists("$carpetaDestino/$archivoDestino")) {
                $archivoDestino = md5(time() + rand()) . ".$extension";
            }
    
            if ($uploadOk == 1) {
                move_uploaded_file($archivos['tmp_name'][$key], "$carpetaDestino/$archivoDestino");
    
                // Crear una instancia de Foto para cada imagen y almacenarla en el array
                $f = new Foto();
                $f->setNombre($archivoDestino);
                $f->setFotoPrincipal(false);
                $nuevasFotos[] = $f;
    
                $response['status'] = 'success';
                $response['message'] = "El archivo $name se ha subido correctamente.";
                $response['filename'] = $name;
            } else {
                $response['status'] = 'error';
                $response['message'] = "No se pudo subir el archivo $name.";
            }
        }
        
        $anuncio->setTitulo($titulo);
        $anuncio->setDescripcion($descripcion);
        $anuncio->setIdUsuario($_SESSION['id']);
        // Obtener la fecha y hora actual en formato DATETIME
        $fechaHoraActual = date("Y-m-d H:i:s");
        $anuncio->setFechaPubli($fechaHoraActual);
        $anuncio->setPrecio($precio);
        
        // Actualizar las nuevas fotos
        $fotosDAO->actualizaFotosSecundarias($idAnuncio, $nuevasFotos);
        echo json_encode($response);
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
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            tinymce.init({
                selector: '#editor'
            });
        </script>
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
            <form action="editar_anuncio.php?id=<?= $idAnuncio ?>" method="post" id="uploadForm"
                enctype="multipart/form-data">
                <input type="text" name="titulo" placeholder="Titulo" value="<?=$anuncio->getTitulo()?>"><br>
                <textarea name="descripcion" placeholder="Descripcion" id="editor"><?=$anuncio->getDescripcion()?></textarea><br>
                <input type="number" step="0.01" name="precio" placeholder="Precio" min="0.01"
                    value="<?=$anuncio->getPrecio()?>"><br>
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