<?php 
/**
 * Genera un hash aleatorio para un nombre de arhivo manteniendo la extensión original
 */
function generarNombreArchivo(string $nombreOriginal):string {
    $nuevoNombre = md5(time()+rand());
    $partes = explode('.',$nombreOriginal);
    $extension = $partes[count($partes)-1];
    return $nuevoNombre.'.'.$extension;
}

function guardarAnuncio($anuncio){
    $_SESSION['error']=$anuncio;
}

function imprimirAnuncio(){
    if(isset($_SESSION['error'])){
        echo '<div class="error" id="mensajeError">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    } 
}
