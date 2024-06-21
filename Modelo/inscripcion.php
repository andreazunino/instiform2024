<?php
require_once('Modelo/gestionCurso.php');
require_once('Modelo/gestionEstudiante.php');

class Inscripcion {
    private $inscripciones = [];

    public function __construct() {
        $this->cargarInscripcionesDesdePostgres();
    }

    //Cuenta cuántos inscriptos hay en un curso específico
    protected function getInscriptosEnCurso($cursoElegido){
        $cantidadInscriptos = 0;    
        foreach ($this->inscripciones as $inscripcion) {
            if ($inscripcion['id_curso'] == $cursoElegido){
                $cantidadInscriptos++;
            }
        }
        return $cantidadInscriptos; 
    }

    public function cargarInscripciones() {
        $gestionEstudiante = new gestionEstudiante();
        $estudiantes = $gestionEstudiante->obtenerEstudiantesParaInscripcion();
    
        $gestionCurso = new gestionCurso();
        $cursos = $gestionCurso->obtenerCursosParaInscripcion();
    
        echo "Estudiantes Disponibles:\n";
        foreach ($estudiantes as $estudiante) {
            echo "DNI del Estudiante: {$estudiante->getDNI()}, - Nombre del Estudiante: {$estudiante->getNombre()}\n";
        }
    
        $estudianteElegido = readline("Ingrese el DNI del estudiante: ");
        echo "Cursos Disponibles:\n";
        foreach ($cursos as $curso) {
            echo "ID del Curso: {$curso->getId()}, - Nombre del Curso: {$curso->getNombre()}, - Cupo: {$curso->getCupo()}\n";
        }
    
        $cursoElegido = readline("Ingrese el ID del curso: ");
    
        //verificar el cupo del curso antes de permitir nueva inscripción
        foreach ($cursos as $curso){
            if ($curso->getId() == $cursoElegido){
                $inscriptosEnCurso = $this->getInscriptosEnCurso($cursoElegido);        
                if ( $inscriptosEnCurso >= $curso->getCupo()){
                    echo "No hay más vacantes para inscribirse en este curso. \n";
                    return;
                } 
            }
        }  //se agregó esta funcion

        
        // Comprobar si el estudiante ya está inscrito en el mismo curso
        foreach ($this->inscripciones as $inscripcionExistente) {
            if ($inscripcionExistente['dni_estudiante'] == $estudianteElegido && $inscripcionExistente['id_curso'] == $cursoElegido) {
                echo "El estudiante ya está inscrito en este curso.\n";
                return;
            }
        }
    
        // Comprobar si el estudiante y el curso existen en los arreglos
        $estudianteExiste = false;
        $cursoExiste = false;
    
        foreach ($estudiantes as $estudiante) {
            if ((int)$estudiante->getDNI() === (int)$estudianteElegido) {
                $estudianteExiste = true;
                break;
            }
        }
    
        foreach ($cursos as $curso) {
            if ((int)$curso->getId() === (int)$cursoElegido) {
                $cursoExiste = true;
                break;
            }
        }
    
        if (!$estudianteExiste) {
            echo "El estudiante con DNI {$estudianteElegido} no se encontró.\n";
            return;
        }
    
        if (!$cursoExiste) {
            echo "El curso con ID {$cursoElegido} no se encontró.\n";
            return;
        }
    
        $inscripcion = [
            "id" => null,
            "id_curso" => $cursoElegido,
            "dni_estudiante" => $estudianteElegido,
            "calificacion" => null
        ];
    
        $this->inscripciones[] = $inscripcion;
        echo "Inscripcion exitosa\n";
        $this->guardarInscripciones();
       
        $this->cargarInscripcionesDesdePostgres();
    }
    
    
    
    public function cargarInscripcionesDesdePostgres() {
        $conexion = Conexion::getConexion();
        $query = $conexion->query("SELECT * FROM inscripcion");
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

        // Vaciar el arreglo de inscripciones antes de cargar las inscripciones nuevamente
        $this->inscripciones = [];

        foreach ($resultados as $inscripcionData) {
            $inscripcion = [
                "id" => $inscripcionData['id'],
                "id_curso" => $inscripcionData['id_curso'],
                "dni_estudiante" => $inscripcionData['dni_estudiante'],
                "calificacion" => $inscripcionData['calificacion']
            ];
            $this->inscripciones[] = $inscripcion;
        }
        
    }

    public function guardarInscripciones() {
        $inscripciones = $this->inscripciones;
        $conexion = Conexion::getConexion();
    
        foreach ($inscripciones as $inscripcion) {
            $idCurso = $inscripcion['id_curso'];
            $dniEstudiante = $inscripcion['dni_estudiante'];
            $calificacion = $inscripcion['calificacion'];
            if ($calificacion){
                $sqlInsercion = "INSERT INTO inscripcion (id_curso, dni_estudiante, calificacion) VALUES ('$idCurso', '$dniEstudiante', '$calificacion') ON CONFLICT (id_curso, dni_estudiante, calificacion) DO NOTHING";
            } else{
                $sqlInsercion = "INSERT INTO inscripcion (id_curso, dni_estudiante) VALUES ('$idCurso', '$dniEstudiante') ON CONFLICT (id_curso, dni_estudiante) DO NOTHING";
            }
            
            Conexion::ejecutar($sqlInsercion);
        }
    
    }
    

    public function listarInscripciones() {
        if (empty($this->inscripciones)) {
            echo "No hay inscripciones disponibles.\n";
        } else {
            echo "Lista de inscripciones:\n";
            foreach ($this->inscripciones as $inscripcion) {
                $calificacion = $inscripcion['calificacion'];
                if ($calificacion){
                   echo "ID de inscripción: " . $inscripcion['id'] . ", ID del Curso: " . $inscripcion['id_curso'] . ", DNI del Estudiante: " . $inscripcion['dni_estudiante'] . ", calificación: " . $inscripcion['calificacion'] . "\n";
                }else
                {
                    echo "ID de inscripción: " . $inscripcion['id'] . ", ID del Curso: " . $inscripcion['id_curso'] . ", DNI del Estudiante: " . $inscripcion['dni_estudiante'] . ", calificación: --" . "\n";
                }
            }
        }
    }

    public function eliminarInscripcionPorID($idInscripcion) {
        $conexion = Conexion::getConexion();
        try {
            $sql = "DELETE FROM inscripcion WHERE id = '$idInscripcion'";
            Conexion::ejecutar($sql);
    
            $indice = null;
            foreach ($this->inscripciones as $key => $inscripcion) {
                if ((int)$inscripcion['id'] === (int)$idInscripcion) {
                    $indice = $key;
                    break;
                }
            }
            if ($indice !== null) {
                unset($this->inscripciones[$indice]);
                echo "Inscripción eliminada exitosamente.\n";
            } else {
                echo "No se encontró ninguna inscripción con el ID especificado.\n";
            }
        } catch (PDOException $e) {
            echo 'Error al eliminar inscripción.'();
        }
    }

    public function mostrarInscripcionesPorDNI($dni) {
        $inscripcionesEncontradas = [];
    
        $gestionCurso = new gestionCurso();
        $cursos = $gestionCurso->obtenerCursos(); // Obtener la lista completa de cursos
    
        foreach ($this->inscripciones as $inscripcion) {
            if ((int)$inscripcion['dni_estudiante'] === (int)$dni) {
                $idInscripcion = $inscripcion['id'];
                $idCurso = $inscripcion['id_curso'];
    
                // Buscar el nombre del curso correspondiente
                $nombreCurso = "Curso no encontrado"; // Valor predeterminado si no se encuentra el curso
                foreach ($cursos as $curso) {
                    if ((int)$curso->getId() === (int)$idCurso) {
                        $nombreCurso = $curso->getNombre();
                        break;
                    }
                }
    
                $inscripcionesEncontradas[] = "ID de inscripción: {$idInscripcion}, Asociado al ID de curso: {$idCurso} - Nombre del Curso: {$nombreCurso}";
            }
        }
    
        if (empty($inscripcionesEncontradas)) {
            echo "No se encontraron inscripciones asociadas al DNI proporcionado.\n";
        } else {
            echo "Inscripciones encontradas para el DNI {$dni}:\n";
            foreach ($inscripcionesEncontradas as $inscripcionEncontrada) {
                echo $inscripcionEncontrada . "\n";
            }
        }
    }
}

