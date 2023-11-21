<?php
require_once 'Anuncio.php';
class AnunciosDAO{
    private mysqli $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtiene un anuncio de la BD en función del id pasado
     * @return Anuncio Devuelve el objeto Anuncio o null si no lo encuentra
     */
    public function getById($id):Anuncio|null {
        if(!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE id = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Anuncio, sino null
        if($result->num_rows == 1){
            $anuncio = $result->fetch_object(Anuncio::class);
            return $anuncio;
        }
        else{
            return null;
        }
    }

    /**
     * Obtiene todos los anuncios de la tabla anuncios
     * @return array Devuelve un array de objetos Anuncio
     */
    public function getAll($page = 1, $perPage = 5): array {
        // Consulta para contar la cantidad total de anuncios
        $totalAnuncios = $this->conn->query("SELECT COUNT(*) as total FROM anuncios")->fetch_assoc()['total'];
    
        // Calcular el número total de páginas
        $totalPages = ceil($totalAnuncios / $perPage);
    
        // Validar la página actual para evitar valores inválidos
        if ($page < 1 || $page > $totalPages) {
            $page = 1; // Página predeterminada si es inválida
        }
    
        // Calcular el offset para la paginación
        $offset = ($page - 1) * $perPage;
    
        // Consulta para obtener los anuncios para la página actual
        $stmt = $this->conn->prepare("SELECT * FROM anuncios ORDER BY fechaPublicacion DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $perPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $array_anuncios = [];
        while ($anuncio = $result->fetch_object(Anuncio::class)) {
            $array_anuncios[] = $anuncio;
        }
    
        return [
            'anuncios' => $array_anuncios,
            'totalPages' => $totalPages
        ];
    }
    
    
    
    
    

    /**
     * Borra el anuncio de la tabla anuncios del id pasado por parámetro
     * @return true si ha borrado el anuncio y false si no lo ha borrado (por que no existia)
     */
    function delete($id):bool{

        if(!$stmt = $this->conn->prepare("DELETE FROM anuncios WHERE id = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Comprobamos si ha borrado algún registro o no
        if($stmt->affected_rows==1){
            return true;
        }
        else{
            return false;
        }
        
    }

    /**
     * Inserta en la base de datos el anuncio que recibe como parámetro
     * @return idAnuncio Devuelve el id autonumérico que se le ha asignado al anuncio o false en caso de error
     */
    function insert(Anuncio $anuncio): int|bool {
        if (!$stmt = $this->conn->prepare("INSERT INTO anuncios (titulo, descripcion, precio, fechaPublicacion, idUsuario, idFoto) VALUES (?, ?, ?, ?, ?, ?)")) {
            die("Error al preparar la consulta insert: " . $this->conn->error);
        }
        
        $titulo = $anuncio->getTitulo();
        $descripcion = $anuncio->getDescripcion();
        $precio = $anuncio->getPrecio();
        $fechaPubli = $anuncio->getFechaPubli(); // Asegúrate de que aquí esté llamando al método correcto de la clase Anuncio
        $idFoto = $anuncio->getIdFoto();
        $idUsuario = $anuncio->getIdUsuario();
        
        $stmt->bind_param('ssdsii', $titulo, $descripcion, $precio, $fechaPubli, $idUsuario, $idFoto);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return false;
        }
    }
    
    /**
     * 
     */
    function update($anuncio){
        if(!$stmt = $this->conn->prepare("UPDATE anuncios SET titulo=?, descripcion=?, precio=?, fechaPublicacion=?, idUsuario=?, idFoto=? WHERE id=?")){
            die("Error al preparar la consulta update: " . $this->conn->error );
        }
        $id = $anuncio->getId();
        $titulo = $anuncio->getTitulo();
        $descripcion = $anuncio->getDescripcion();
        $precio = $anuncio->getPrecio();
        $fechaPubli = $anuncio->getFechaPubli();
        $idFoto = $anuncio->getIdFoto();
        $idUsuario = $anuncio->getIdUsuario();
        $stmt->bind_param('ssdsiii', $titulo, $descripcion, $precio, $fechaPubli, $idUsuario, $idFoto, $id);
        return $stmt->execute();
    }
    
}