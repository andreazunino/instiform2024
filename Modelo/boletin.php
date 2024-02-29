<?php
    class Boletin {
        private $nota;
        private $fechaNota;
        
        function __construct($nota, $fechaNota)
        {
            $this->$nota = $nota;
            $this->$fechaNota = $fechaNota;
        }
        
        public function setNota($nota){
            $this->nota = $nota;
        }

        public function getNota(){
            return $this->nota;
        }

        public function setFechaNota($fechaNota){
            $this->fechaNota = $fechaNota;
        }

        public function getFechaNota(){
            return $this->fechaNota;
        }

    }