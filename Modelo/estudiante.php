<?php 

class Estudiante {
    private $nombre;
    private $apellido;
    private $dni;
    private $email;

    public function __construct($nombre, $apellido, $dni, $email){
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->dni = $dni;
        $this->email = $email;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }


    public function getApellido()
    {
        return $this->apellido;
    }


    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }


    public function getDni()
    {
        return $this->dni;
    }


    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function mostrar(){
        echo "Nombre: " . $this->getNombre() . ", Apellido: " . $this->getApellido() . ", DNI: " . $this->getDNI() . "\n";
    }    
}
