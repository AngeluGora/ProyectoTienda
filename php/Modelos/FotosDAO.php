<?php
require_once 'Foto.php';
class FotosDAO{
    private mysqli $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * @return Foto principal del anuncio que se le pasa por parametro
     */
    public function getFotoPrincipal($idAnuncio):Foto|null {
        $idFoto='';
        if(!$stmt = $this->conn->prepare("SELECT idFoto FROM anuncios WHERE id=?"))
        {
            echo "Error en la SQL1: " . $this->conn->error;
        }else{
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();
        $idFoto = $result;
        $stmt->close();
        }
        if (!$stmt = $this->conn->prepare("SELECT * FROM fotos WHERE id = ? AND fotoPrincipal = ?")) {
            echo "Error en la SQL2: " . $this->conn->error;
        } else {
            // Asigna valores a los parámetros y ejecuta la consulta
            $fotoPrincipal = true; // O el valor correspondiente
            $stmt->bind_param("ii", $idFoto, $fotoPrincipal); // Aquí "ii" indica dos valores enteros, ajusta según los tipos
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
        
                // Procesa los resultados
                while ($row = $result->fetch_assoc()) {
                    // Accede a los datos de cada fila
                }
            } else {
                echo "Error al ejecutar la consulta: " . $stmt->error;
            }
        
            // Cierra la consulta
            $stmt->close();
        }
        
    }

}