<?php 
session_start();
require_once 'php/Modelos/ConexionDB.php';
require_once 'php/Modelos/AnunciosDAO.php';
require_once 'php/Modelos/UsuariosDAO.php';
require_once 'php/Modelos/FotosDAO.php';
require_once 'php/Modelos/config.php';
require_once 'php/funciones.php';

$conexionDB = new ConexionDB(MYSQL_USER, MYSQL_PASS, MYSQL_HOST, MYSQL_DB);
$conn = $conexionDB->getConexion();
$fotoUsu='';

if (!isset($_SESSION['email']) && isset($_COOKIE['sid'])) {
    $usuariosDAO = new UsuariosDAO($conn);
    if ($usuario = $usuariosDAO->getBySid($_COOKIE['sid'])) {
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['id'] = $usuario->getId();
        $fotoUsu = $usuario->getFoto();
    }
}

$anunciosDAO = new AnunciosDAO($conn);

// Obtener la página actual desde la URL o establecerla en 1 por defecto
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Obtener todos los anuncios con paginación
$anunciosData = $anunciosDAO->getAll($pagina);

$anuncios = $anunciosData['anuncios'];
$totalPaginas = $anunciosData['totalPages'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/estilos.css">
    <title>Angelu Store</title>
    </style>
</head>
<style>
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
    <div class="contenido">
        
    <header>
        <img src="img/logo.png" alt="Logo de la web" class="logo">
        <nav class="menu">
            <a href="index.php" class="enlaceMenu">Anuncios</a>
            <a href="php/misAnuncios.php" class="enlaceMenu">Mis Anuncios</a>
            <div id="enlaceform">
            <?php if(isset($_SESSION['email'])): ?>
            <img src="php/fotosUsuarios/<?=$fotoUsu?>" class="fotoAnuncio">
            <span class="emailUsuario"><?= $_SESSION['email'] ?></span>
            <a href="php/logout.php">Cerrar sesión</a>
            <?php else: ?>
            <a href="php/inicioSesion.php" class="enlaceMenu">Iniciar Sesion</a>
            <a href="php/registrar.php" class="enlaceMenu">Registrar</a>
            <?php endif; ?>
            </div>
        </nav>
    </header>

        <main>
            <h1>Bienvenido a Angelu Store</h1>
            <h3>En nuestra tienda podrás encontrar todo tipo de artículos de segunda mano.<br> Si no lo usas, ¡STOREALO!</h3>
            
            <?php if(isset($_SESSION['email'])): ?>
                <a href="php/insertar_anuncio.php" class="nuevoAnuncio">Nuevo Anuncio</a>
            <?php endif; ?>
            <div class="contenedorAnuncios">
            <?php foreach ($anuncios as $anuncio): ?>
                <?php
                    // Obtener la foto principal para el anuncio actual
                    $fotosDAO=new FotosDAO($conn);
                    $fotoPrincipal = $fotosDAO->getFotoPrincipal($anuncio->getId());
                    $nombreFoto = $fotoPrincipal->getNombre();
                ?>
                <article class="anuncio">
                    <div class="acciones">
                        <?php if(isset($_SESSION['email']) && $_SESSION['id'] == $anuncio->getIdUsuario()): ?>
                            <span class="icono_borrar">
                                <a onclick="confirmarBorrado(<?= $anuncio->getId() ?>)">
                                    <i class="fa-solid fa-trash color_gris"></i>
                                </a>
                            </span>
                            <span class="icono_editar">
                                <a href="php/editar_anuncio.php?id=<?= $anuncio->getId() ?>">
                                    <i class="fa-solid fa-pen-to-square color_gris"></i>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                    <img src="php/fotosAnuncios/<?= $nombreFoto ?>" alt="Foto del anuncio" class="fotoAnuncio">
                    <h4 class="titulo">
                        <a href="php/ver_anuncio.php?id=<?= $anuncio->getId() ?>"><?= $anuncio->getTitulo() ?></a>
                    </h4>
                    <p class="descripcion"><?= $anuncio->getDescripcion() ?></p>
                    <p class="precio"><?= $anuncio->getPrecio() ?></p>
                </article>
            <?php endforeach; ?>

            </div>


            <!-- Navegación entre páginas -->
            <div class="pagination">
                <?php if ($totalPaginas > 1): ?>
                    <?php if ($pagina > 1): ?>
                        <a href="?pagina=<?php echo ($pagina - 1); ?>">Página anterior</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?pagina=<?php echo $i; ?>" <?php echo ($pagina === $i) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="?pagina=<?php echo ($pagina + 1); ?>">Página siguiente</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </main>

        <footer>
            &copy; 2023 Angelu Store
        </footer>
    </div>
</body>
</html>

