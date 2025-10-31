<?php
Class Cliente {
    private int $id;
    private string $nombre;
    private string $email;
    private string $telefono;
    private string $direccion;
    private string $etiqueta;
    private string $imagen;
    private string $nombreFisicoImagen;
    private int $tipoId;
    private string $comentarios;

    public function __construct(int $id, string $nombre, string $email, string $telefono, string $direccion, string $etiqueta, string $imagen, string $nombreFisicoImagen, int $tipoId, string $comentarios)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->etiqueta = $etiqueta;
        $this->imagen = $imagen;
        $this->nombreFisicoImagen = $nombreFisicoImagen;
        $this->tipoId = $tipoId;
        $this->comentarios = $comentarios;
    }

     
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function getEtiqueta(): string
    {
        return $this->etiqueta;
    }

    public function getImagen(): string
    {
        return $this->imagen;
    }

    public function getNombreFisicoImagen(): string
    {
        return $this->nombreFisicoImagen;
    }

    public function getTipoId(): int
    {
        return $this->tipoId;
    }

    public function getComentarios(): string
    {
        return $this->comentarios;
    } 

}


?>