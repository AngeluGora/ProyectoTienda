<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Inicia Sesion</title>
</head>
<body>
    <header>
        <img src="../img/logo.png" alt="Logo de la web" class="logo">
        <nav class="menu">
            <a href="index.php" class="enlaceMenu">Anuncios</a>
            <a href="misAnuncios.php" class="enlaceMenu">Mis Anuncios</a>
            <div id="enlaceform">
            <?php if(isset($_SESSION['email'])): ?>
            <img src="fotosUsuarios/<?= $_SESSION['foto']?>" class="fotoUsuario">
            <span class="emailUsuario"><?= $_SESSION['email'] ?></span>
            <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
            <a href="inicioSesion.php">Iniciar Sesion</a>
            <a href="registrar.php">Registrar</a>
            <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
        <div class="form-background">
        <h1>Iniciar sesión</h1>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="email"><br>
            <input type="password" name="password" placeholder="password"><br><br>
            <input type="submit" value="login">
        </form>
        </div>
        <div class="login">
        <p>¿Necesitas una cuenta?<a href="registrar.php">Registrarse</a></p>
        </div>
        </div>
    </main>
    <footer>
        &copy; 2023 Angelu Store
    </footer>
</body>
</html>