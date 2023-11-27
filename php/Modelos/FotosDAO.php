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

    public function getFotosNoPrincipales($idAnuncio): array {
        $fotos = [];
    
        if (!$stmt = $this->conn->prepare("SELECT * FROM fotos WHERE idAnuncio = ? AND fotoPrincipal = 0")) {
            echo "Error en la SQL: " . $this->conn->error;
            return [];
        } else {
            // Asignar el idAnuncio como parámetro y ejecutar la consulta
            $stmt->bind_param("i", $idAnuncio);
            $stmt->execute();
            
            // Obtener el resultado y procesar los datos de las fotos no principales
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $foto = new Foto();
                $foto->setId($row['id']);
                $foto->setNombre($row['nombre']);
                $foto->setFotoPrincipal($row['fotoPrincipal']);
                $foto->setIdAnuncio($row['idAnuncio']);
    
                $fotos[] = $foto;
            }
    
            // Cerrar la consulta
            $stmt->close();
        }
    
        return $fotos;
    }
    
    function borrarFotoYAnuncio($fotoId = null, $anuncioId = null): bool {
        if ($fotoId !== null && $anuncioId !== null) {
            // Obtener la información de la foto
            if (!$stmt = $this->conn->prepare("SELECT nombre FROM fotos WHERE id = ? AND idAnuncio = ?")) {
                die("Error al preparar la consulta select: " . $this->conn->error);
            }
            $stmt->bind_param('ii', $fotoId, $anuncioId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Verificar si se encontró la foto asociada al anuncio
            if ($row = $result->fetch_assoc()) {
                $fotoNombre = $row['nombre'];
    
                // Eliminar la foto de la base de datos
                if (!$stmt = $this->conn->prepare("DELETE FROM fotos WHERE id = ? AND idAnuncio = ?")) {
                    die("Error al preparar la consulta delete: " . $this->conn->error);
                }
                $stmt->bind_param('ii', $fotoId, $anuncioId);
                $stmt->execute();
    
                // Eliminar el archivo físico de la carpeta
                $rutaArchivo = "../fotosAnuncios/$fotoNombre";
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                
                return true;
            } else {
                echo "No se encontró ninguna foto para el idAnuncio proporcionado.";
                return false;
            }
        } else {
            // Si no se proporcionan ambas IDs, borrar todas las fotos relacionadas con un anuncio
            if (!$stmt = $this->conn->prepare("SELECT nombre FROM fotos WHERE idAnuncio = ?")) {
                die("Error al preparar la consulta select: " . $this->conn->error);
            }
            $stmt->bind_param('i', $anuncioId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $fotoNombre = $row['nombre'];
    
                // Eliminar cada foto de la carpeta
                $rutaArchivo = "../fotosAnuncios/$fotoNombre";
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }
    
            // Eliminar todas las fotos de la base de datos
            if (!$stmt = $this->conn->prepare("DELETE FROM fotos WHERE idAnuncio = ?")) {
                die("Error al preparar la consulta delete: " . $this->conn->error);
            }
            $stmt->bind_param('i', $anuncioId);
            $stmt->execute();
    
            return true;
        }
    }
    
    
    

    function insert(Foto $foto): int|bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO fotos (nombre, fotoPrincipal) VALUES (?, ?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }
        
        $nombre = $foto->getNombre();
        $fotoPrincipal = $foto->getFotoPrincipal();
    
        $stmt->bind_param('si', $nombre, $fotoPrincipal);
    
        if ($stmt->execute()) {
            $idFotoGenerada = $stmt->insert_id;
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


    function actualizaFotoPrincipal($anuncioId, $nuevaFotoPrincipal) {
        // Eliminar la foto principal anterior del anuncio
        $this->eliminarFotoPrincipalDeAnuncio($anuncioId);
    
        // Insertar la nueva foto principal
        $idFotoGenerada = $this->insert($nuevaFotoPrincipal);
        $this->modifyInsert($idFotoGenerada, $anuncioId);
    }
    
    function actualizaFotosSecundarias($anuncioId, $nuevasFotos) {
        // Eliminar fotos secundarias anteriores del anuncio
        $this->eliminarFotosSecundariasDeAnuncio($anuncioId);
    
        // Insertar las nuevas fotos secundarias
        foreach ($nuevasFotos as $foto) {
            $idFotoGenerada = $this->insert($foto);
            $this->modifyInsert($idFotoGenerada, $anuncioId);
        }
    }
    
    function eliminarFotoPrincipalDeAnuncio($anuncioId) {
        $query = "DELETE FROM fotos WHERE idAnuncio = ? AND fotoPrincipal = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $anuncioId);
        $stmt->execute();
    }
    
    function eliminarFotosSecundariasDeAnuncio($anuncioId) {
        $query = "DELETE FROM fotos WHERE idAnuncio = ? AND fotoPrincipal = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $anuncioId);
        $stmt->execute();
    }
    

}