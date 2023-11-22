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
        if (!$stmt = $this->conn->prepare("SELECT * FROM fotos WHERE idAnuncio = ? AND fotoPrincipal = 1")) {
            echo "Error en la SQL: " . $this->conn->error;
            return null;
        } else {
            // Asignar el idAnuncio como parámetro y ejecutar la consulta
            $stmt->bind_param("i", $idAnuncio);
            $stmt->execute();
    
            // Obtener el resultado y procesar los datos de la foto principal
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $foto = new Foto();
                $foto->setId($row['id']);
                $foto->setNombre($row['nombre']);
                $foto->setFotoPrincipal($row['fotoPrincipal']);
                $foto->setIdAnuncio($row['idAnuncio']);
                
                return $foto;
            } else {
                echo "No se encontró ninguna foto principal para el idAnuncio proporcionado.";
                return null;
            }
    
            // Cerrar la consulta
            $stmt->close();
        }
    }

    function borrarFotoAnuncio($idAnun):  int|bool {
        if (!$stmt = $this->conn->prepare("DELETE FROM fotos WHERE idAnuncio = ?")) {
            die("Error al preparar la consulta delete: " . $this->conn->error);
        }
        
        $stmt->bind_param('i', $idAnun);
        
        if ($stmt->execute()) {
            return $stmt->affected_rows; // Retorna el número de filas afectadas
        } else {
            return false;
        }
    }

    function borrarFoto($id): bool {
        if (!$stmt = $this->conn->prepare("DELETE FROM fotos WHERE idAnuncio = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
        }
    
        //Asociar las variables a las interrogaciones (parámetros)
        $stmt->bind_param('i', $id);
    
        // Ejecutamos la SQL
        $stmt->execute();
    
        // Comprobamos si ha borrado algún registro o no
        if ($stmt->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
    function insert(Foto $foto): int|bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO fotos (nombre, fotoPrincipal) VALUES (?, ?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }
        
        $nombre =$foto->getNombre();
        $fotoPrincipal = $foto->getFotoPrincipal();
        
        $stmt->bind_param('si', $nombre, $fotoPrincipal);
        
        if ($stmt->execute()) {
            $idFotoGenerada=$stmt->insert_id;
            return $idFotoGenerada;
        } else {
            return false;
        }
    }

    function modifyInsert($id,$idAnun): int|bool{
        if(!$stmt=$this->conn->prepare("UPDATE fotos set idAnuncio=? where id=?")){
            die("Error al preparar la consulta modifyInsert: ".$this->conn->error);
        }
        $idFoto=$id;
        $idAnuncio=$idAnun;
        $stmt->bind_param('ii', $idAnun,$idFoto);
        return $stmt->execute();
    }

}