<?php
require_once('Modelo/conexionNueva.php');
class gestionEstudiante {
    private $estudiantes = [];

    public function __construct() {
        $this->cargarEstudiantesDesdePostgrees();
    }

    public function agregarEstudiante($estudiante) {
        $estudianteExistente = $this->buscarEstudiantePorDNI($estudiante->getDNI());
        
        if ($estudianteExistente) {
            echo "El estudiante con el DNI {$estudiante->getDNI()} ya existe en la base de datos.";
            return;
        }
        $this->estudiantes[] = $estudiante;

        $this->guardarEstudiantes();
 
    }
    

    
    public function buscarEstudiantePorDNI($dni) {
        if ($dni != ""){
            $conexion = Conexion::getConexion();
            $query = $conexion->prepare("SELECT * FROM estudiante WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $query->execute();
    
            $estudiante = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($estudiante) {
                return new Estudiante(
                    $estudiante['nombre'],
                    $estudiante['apellido'],
                    $estudiante['dni'],
                    $estudiante['email']
                 );
            }   else {
                return null;
            } 
        } 
    }
    

    public function cargarEstudiantesDesdePostgrees(){
        $sql = "SELECT * FROM estudiante";
        $estudiantes = Conexion::query($sql);
        
         // Vaciar el arreglo de estudiantes antes de cargar los cursos nuevamente
         $this->estudiantes = [];

        foreach ($estudiantes as $estudiante) {
            if (is_object($estudiante)) {
                $nuevoEstudiante = new Estudiante(
                    $estudiante->nombre,
                    $estudiante->apellido,
                    $estudiante->dni,
                    $estudiante->email
                );
                $this->estudiantes[] = $nuevoEstudiante;
            } else {
                echo "Los datos del estudiante no están en el formato esperado.";
            }
                }
    }
    
    
    public function eliminarEstudiantePorDNI($dni) {
        $conexion = Conexion::getConexion();
        try {
            $query = $conexion->prepare("DELETE FROM estudiante WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $resultado = $query->execute();
    
            if ($resultado && $query->rowCount() > 0) {
                $this->cargarEstudiantesDesdePostgrees();
                return true;
            }
    
        return false;
        }catch (PDOException $e) {
            echo 'Error al eliminar Estudiante: No puedes eliminar un Estudiante con Inscripciones.';
        }
    }   
    
    
    
    
    public function modificarEstudiantePorDNI($dni, $nuevoNombre, $nuevoApellido, $nuevoEmail) {
        $conexion = Conexion::getConexion();
        $query = $conexion->prepare("UPDATE estudiante SET nombre = :nuevoNombre, apellido = :nuevoApellido, email = :nuevoEmail WHERE dni = :dni");
        $query->bindParam(':nuevoNombre', $nuevoNombre);
        $query->bindParam(':nuevoApellido', $nuevoApellido);
        $query->bindParam(':nuevoEmail', $nuevoEmail);
        $query->bindParam(':dni', $dni);
        $resultado = $query->execute();
    
        if ($resultado && $query->rowCount() > 0) {
            $this->cargarEstudiantesDesdePostgrees();
            return true;
        }
        
        return false;
    }
    

    public function verDatosEInscripcionesPorDNI($dni) {
        $estudianteEncontrado = $this->buscarEstudiantePorDNI($dni);
    
        if ($estudianteEncontrado !== null) {
            echo "Datos del estudiante:\n";
            echo "Nombre: " . $estudianteEncontrado->getNombre() . "\n";
            echo "Apellido: " . $estudianteEncontrado->getApellido() . "\n";
            echo "DNI: " . $estudianteEncontrado->getDNI() . "\n";
            echo "Email: " . $estudianteEncontrado->getEmail() . "\n";
        } else {
            echo "No se encontró ningún estudiante con el DNI $dni.\n";
        }
    }
    

    public function guardarEstudiantes() {
        $conexion = Conexion::getConexion();
    
        $query = $conexion->prepare("INSERT INTO estudiante (nombre, apellido, dni, email) VALUES (:nombre, :apellido, :dni, :email) 
            ON CONFLICT (dni) DO UPDATE SET 
            nombre = EXCLUDED.nombre,
            apellido = EXCLUDED.apellido,
            email = EXCLUDED.email");
    
        foreach ($this->estudiantes as $estudiante) {
            $nombre = $estudiante->getNombre();
            $apellido = $estudiante->getApellido();
            $dni = $estudiante->getDNI();
            $email = $estudiante->getEmail();
    
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':apellido', $apellido);
            $query->bindParam(':dni', $dni);
            $query->bindParam(':email', $email);
            $query->execute();
        }
        echo "Estudiante agregado correctamente.\n";
    }    

    public function obtenerEstudiantesParaInscripcion() {
        $estudiantesParaInscripcion = [];
        foreach ($this->estudiantes as $estudiante) {
            $estudiantesParaInscripcion[] = $estudiante;
        }
        return $estudiantesParaInscripcion;
    }
    
    public function listarEstudiantes() {
        if (empty($this->estudiantes)) {
            echo "No hay estudiantes disponibles.\n";
        } else {
            echo "\nLista de estudiantes:\n";
            foreach ($this->estudiantes as $estudiante) {
                echo "Nombre: " . $estudiante->getNombre() . ", Apellido: " . $estudiante->getApellido() . ", DNI: " . $estudiante->getDNI() . "\n";
            }
            echo PHP_EOL;
        }
    }
}

