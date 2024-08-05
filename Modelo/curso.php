<?php

class Curso {
    private $nombre;
    private $id;
    private $cupo = 0 ; //se agrega variable cupo con get y set

    public function __construct($id, $nombre, $cupo) {
        $this->nombre = $nombre;
        $this->id = $id;
        $this->cupo = $cupo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCupo() {
        return $this->cupo;
    }

    public function setCupo($cupo) {
        $this->cupo = $cupo;
    }

}
