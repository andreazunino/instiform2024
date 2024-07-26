<?php
require_once('Modelo/conexionNueva.php');
require_once('Modelo/estudiante.php');

class gestionEstudiante {
    private $estudiantes = [];

    public function __construct() {
        $this->cargarEstudiantesDesdePostgres();
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
        } else {
            return null;
        }
    }

    public function cargarEstudiantesDesdePostgres() {
        $conexion = Conexion::getConexion();
        $query = $conexion->query("SELECT * FROM estudiante");
        $estudiantes = $query->fetchAll(PDO::FETCH_ASSOC);

        $this->estudiantes = [];

        foreach ($estudiantes as $estudiante) {
            $nuevoEstudiante = new Estudiante(
                $estudiante['nombre'],
                $estudiante['apellido'],
                $estudiante['dni'],
                $estudiante['email']
            );
            $this->estudiantes[] = $nuevoEstudiante;
        }
    }

    public function eliminarEstudiantePorDNI($dni) {
        $conexion = Conexion::getConexion();
        try {
            $query = $conexion->prepare("DELETE FROM estudiante WHERE dni = :dni");
            $query->bindParam(':dni', $dni);
            $resultado = $query->execute();
            
            if ($resultado && $query->rowCount() > 0) {
                $this->cargarEstudiantesDesdePostgres();
                return true;
            }
    
            return false;
        } catch (PDOException $e) {
            echo 'Error al eliminar Estudiante: ' . $e->getMessage();
            return false;
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
            $this->cargarEstudiantesDesdePostgres();
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
        return $this->estudiantes;
    }

    public function listarEstudiantes() {
        if (empty($this->estudiantes)) {
            echo "No hay estudiantes disponibles.\n";
        } else {
            echo "\nLista de estudiantes:\n";
            foreach ($this->estudiantes as $estudiante) {
                $estudiante->mostrar();
            }
            echo PHP_EOL;
        }
    }

    public function obtenerEstudiantesInscritosEnCurso($idCurso) {
        $conexion = Conexion::getConexion();
        $query = $conexion->prepare("SELECT dni_estudiante FROM inscripcion WHERE id_curso = :idCurso");
        $query->bindParam(':idCurso', $idCurso);
        $query->execute();
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

        $estudiantes = [];
        foreach ($resultados as $resultado) {
            $estudiante = $this->buscarEstudiantePorDNI($resultado['dni_estudiante']);
            if ($estudiante) {
                $estudiantes[] = $estudiante;
            }
        }
        return $estudiantes;
    }
}
