<?php
class Foto {
    private $id;
    private $nombre;
    private $fotoPrincipal;
    private $idAnuncio;
    

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     */
    public function setNombre($nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of fotoPrincipal
     */
    public function getFotoPrincipal()
    {
        return $this->fotoPrincipal;
    }

    /**
     * Set the value of fotoPrincipal
     */
    public function setFotoPrincipal($fotoPrincipal): self
    {
        $this->fotoPrincipal = $fotoPrincipal;

        return $this;
    }

    /**
     * Get the value of idAnuncio
     */
    public function getIdAnuncio()
    {
        return $this->idAnuncio;
    }

    /**
     * Set the value of idAnuncio
     */
    public function setIdAnuncio($idAnuncio): self
    {
        $this->idAnuncio = $idAnuncio;

        return $this;
    }
}
