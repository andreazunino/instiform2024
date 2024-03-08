<?php
require_once('Modelo/conexionNueva.php');
class gestionProfesor {
    private $profesores = [];

    public function __construct() {
        $this->cargarProfesoresDesdePostgrees();
    }

    public function agregarProfesor($profesor) {
        $profesorExistente = $this->buscarProfesorPorDNI($profesor->getDNI());
        
        if ($profesorExistente) {
            echo "El estudiante con el DNI {$profesor->getDNI()} ya existe en la base de datos.";
            return;
        }
        $this->profesores[] = $profesor;

        $this->guardarProfesores();
 
    }
    

    
    public function buscarProfesorPorDNI($dni) {
        if ($dni != ""){
            $conexion = Conexion::getConexion();
            $query = $conexion->prepare("SELECT * FROM profesor WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $query->execute();
    
            $profesor = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($profesor) {
                return new Profesor(
                    $profesor['nombre'],
                    $profesor['apellido'],
                    $profesor['dni'],
                    $profesor['email']
                 );
            }   else {
                return null;
            } 
        } 
    }
    

    public function cargarProfesoresDesdePostgrees(){
        $sql = "SELECT * FROM profesor";
        $profesores = Conexion::query($sql);
        
        foreach ($profesores as $profesor) {
            if (is_object($profesor)) {
                $nuevoProfesor = new Profesor(
                    $profesor->nombre,
                    $profesor->apellido,
                    $profesor->dni,
                    $profesor->email
                );
                $this->profesores[] = $nuevoProfesor;
            } else {
                echo "Los datos del profesor no están en el formato esperado.";
            }
                }
    }
    
    
    public function eliminarProfesorPorDNI($dni) {
        $conexion = Conexion::getConexion();
        try {
            $query = $conexion->prepare("DELETE FROM profesor WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $resultado = $query->execute();
    
            if ($resultado && $query->rowCount() > 0) {
                $this->cargarProfesoresDesdePostgrees();
                return true;
            }
    
        return false;
        }catch (PDOException $e) {
            echo 'Error al eliminar Profesor: No puedes eliminar un Estudiante con Inscripciones.';
        }
    }   
    
    
    
    
    public function modificarProfesorPorDNI($dni, $nuevoNombre, $nuevoApellido, $nuevoEmail) {
        $conexion = Conexion::getConexion();
        $query = $conexion->prepare("UPDATE profesor SET nombre = :nuevoNombre, apellido = :nuevoApellido, email = :nuevoEmail WHERE dni = :dni");
        $query->bindParam(':nuevoNombre', $nuevoNombre);
        $query->bindParam(':nuevoApellido', $nuevoApellido);
        $query->bindParam(':nuevoEmail', $nuevoEmail);
        $query->bindParam(':dni', $dni);
        $resultado = $query->execute();
    
        if ($resultado && $query->rowCount() > 0) {
            $this->cargarProfesoresDesdePostgrees();
            return true;
        }
    
        return false;
    }
    

    public function verDatosEInscripcionesPorDNI($dni) {
        $profesorEncontrado = $this->buscarProfesorPorDNI($dni);
    
        if ($profesorEncontrado !== null) {
            echo "Datos del profesor:\n";
            echo "Nombre: " . $profesorEncontrado->getNombre() . "\n";
            echo "Apellido: " . $profesorEncontrado->getApellido() . "\n";
            echo "DNI: " . $profesorEncontrado->getDNI() . "\n";
            echo "Email: " . $profesorEncontrado->getEmail() . "\n";
        } else {
            echo "No se encontró ningún profesor con el DNI $dni.\n";
        }
    }
    

    public function guardarProfesores() {
        $conexion = Conexion::getConexion();
    
        $query = $conexion->prepare("INSERT INTO profesor (nombre, apellido, dni, email) VALUES (:nombre, :apellido, :dni, :email) 
            ON CONFLICT (dni) DO UPDATE SET 
            nombre = EXCLUDED.nombre,
            apellido = EXCLUDED.apellido,
            email = EXCLUDED.email");
    
        foreach ($this->profesores as $profesor) {
            $nombre = $profesor->getNombre();
            $apellido = $profesor->getApellido();
            $dni = $profesor->getDNI();
            $email = $profesor->getEmail();
    
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':apellido', $apellido);
            $query->bindParam(':dni', $dni);
            $query->bindParam(':email', $email);
            $query->execute();
        }
        echo "Profesor agregado correctamente.\n";
    }    

    public function obtenerProfesoresParaInscripcion() {
        $profesoresParaInscripcion = [];
        foreach ($this->profesores as $profesor) {
            $profesoresParaInscripcion[] = $profesor;
        }
        return $profesoresParaInscripcion;
    }
    
    public function listarProfesores() {
        if (empty($this->profesores)) {
            echo "No hay Profesores disponibles.\n";
        } else {
            echo "\nLista de Profesores:\n";
            foreach ($this->profesores as $profesor) {
                echo "Nombre: " . $profesor->getNombre() . ", Apellido: " . $profesor->getApellido() . ", DNI: " . $profesor->getDNI() . "\n";
            }
            echo PHP_EOL;
        }
    }
}
