<?php
Class TipoCliente{
     private int $id;
     private string $tipo;
     private string $notas;

     public function __construct(int $id, string $tipo, string $notas)
     {
          $this->id = $id;
          $this->tipo = $tipo;
          $this->notas = $notas;
     }

     public function getId(): int
     {
          return $this->id;
     }

     public function getTipo(): string
     {
          return $this->tipo;
     }

     public function getNotas(): string
     {
          return $this->notas;
     }
}
?>