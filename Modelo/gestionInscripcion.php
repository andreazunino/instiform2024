<?php
require_once('Modelo/gestionCurso.php');
require_once('Modelo/gestionEstudiante.php');

class Inscripcion {
    private $inscripciones = [];

    public function __construct() {
        $this->cargarInscripcionesDesdePostgres();
    }

    // Cuenta cuántos inscriptos hay en un curso específico
    protected function getInscriptosEnCurso($cursoElegido) {
        $cantidadInscriptos = 0;
        foreach ($this->inscripciones as $inscripcion) {
            if ($inscripcion['id_curso'] == $cursoElegido) {
                $cantidadInscriptos++;
            }
        }
        return $cantidadInscriptos;
    }

    public function cargarInscripciones($estudianteElegido) {
       
        $cursos = new GestionCurso();
        $cursos = $cursos->obtenerCursos();
        $estudiantes = new GestionEstudiante();
        $estudiantes = $estudiantes->obtenerEstudiantesParaInscripcion();

        echo "Cursos Disponibles:\n";
        foreach ($cursos as $curso) {
            echo "ID del Curso: {$curso->getId()}, - Nombre del Curso: {$curso->getNombre()}, - Cupo: {$curso->getCupo()}\n";
        }
    
        $cursoElegido = readline("Ingrese el ID del curso: ");
    
        // Verificar el cupo del curso antes de permitir nueva inscripción
        foreach ($cursos as $curso) {
            if ($curso->getId() == $cursoElegido) {
                $inscriptosEnCurso = $this->getInscriptosEnCurso($cursoElegido);
                if ($inscriptosEnCurso >= $curso->getCupo()) {
                    echo "No hay más vacantes para inscribirse en este curso.\n";
                    return;
                }
            }
        }
    
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
            "calificacion" => null,
            "fecha_calificacion" => null
        ];
    
        $this->inscripciones[] = $inscripcion;
        echo "Inscripción exitosa\n";
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
                "calificacion" => $inscripcionData['calificacion'],
                "fecha_calificacion" => $inscripcionData['fecha_calificacion']
            ];
            $this->inscripciones[] = $inscripcion;
        }
    }
    
    public function guardarInscripciones() {
        $conexion = Conexion::getConexion();
    
        foreach ($this->inscripciones as $inscripcion) {
            $idCurso = $inscripcion['id_curso'];
            $dniEstudiante = $inscripcion['dni_estudiante'];
            $calificacion = $inscripcion['calificacion'];
            $fechaCalificacion = $inscripcion['fecha_calificacion'];
            $sqlInsercion = "INSERT INTO inscripcion (id_curso, dni_estudiante, calificacion, fecha_calificacion) VALUES (:id_curso, :dni_estudiante, :calificacion, :fecha_calificacion)
                            ON CONFLICT (id_curso, dni_estudiante) DO UPDATE SET calificacion = :calificacion";
            $stmt = $conexion->prepare($sqlInsercion);
            $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_INT);
            $stmt->bindParam(':dni_estudiante', $dniEstudiante, PDO::PARAM_STR);
            $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_calificacion', $fechaCalificacion, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    
    public function listarInscripciones() {
        if (empty($this->inscripciones)) {
            echo "No hay inscripciones disponibles.\n";
        } else {
            echo "Lista de inscripciones:\n";
            foreach ($this->inscripciones as $inscripcion) {
                $calificacion = $inscripcion['calificacion'];
                echo "ID de inscripción: " . $inscripcion['id'] . ", ID del Curso: " . $inscripcion['id_curso'] . ", DNI del Estudiante: " . $inscripcion['dni_estudiante'] . ", Calificación: " . ($calificacion ?? '--') . "\n";
            }
        }
    }

    public function eliminarInscripcionPorID($idInscripcion) {
        $conexion = Conexion::getConexion();
        try {
            $sql = "DELETE FROM inscripcion WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id', $idInscripcion, PDO::PARAM_INT);
            $stmt->execute();
    
            $indice = null;
            foreach ($this->inscripciones as $key => $inscripcion) {
                if ((int)$inscripcion['id'] === (int)$idInscripcion) {
                    $indice = $key;
                    break;
                }
            }
            if ($indice !== null) {
                unset($this->inscripciones[$indice]);
                $this->guardarInscripciones();
                echo "Inscripción eliminada exitosamente.\n";
                return true;
            } else {
                echo "No se encontró ninguna inscripción con el ID especificado.\n";
            }
        } catch (PDOException $e) {
            echo 'Error al eliminar inscripción: ' . $e->getMessage() . "\n";
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
    
    public function actualizarCalificacion($dniEstudiante, $idCurso, $calificacion, $fechaCalificacion) {
        $conexion = Conexion::getConexion();
        $sql = "UPDATE inscripcion SET calificacion = :calificacion, fecha_calificacion = :fecha_calificacion WHERE dni_estudiante = :dni_estudiante AND id_curso = :id_curso";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_calificacion', $fechaCalificacion, PDO::PARAM_STR);
        $stmt->bindParam(':dni_estudiante', $dniEstudiante, PDO::PARAM_STR);
        $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function mostrarInscripcionesPorCurso($idCurso) {
        $inscripcionesPorCurso = [];
    
        foreach ($this->inscripciones as $inscripcion) {
            if ((int)$inscripcion['id_curso'] === (int)$idCurso) {
                $inscripcionesPorCurso[] = $inscripcion;
            }
        }
    
        if (empty($inscripcionesPorCurso)) {
            echo "No se encontraron inscripciones para el curso con ID {$idCurso}.\n";
        } else {
            echo "Inscripciones para el curso con ID {$idCurso}:\n";
            foreach ($inscripcionesPorCurso as $inscripcion) {
                echo "ID de inscripción: " . $inscripcion['id'] . ", DNI del Estudiante: " . $inscripcion['dni_estudiante'] . ", Calificación: " . ($inscripcion['calificacion'] ?? '--') . "\n";
            }
        }
    }

    
    //Carga las notas en un curso
    public function ingresarNotasPorCurso($idCurso) {
        $inscripcionesPorCurso = [];
    
        foreach ($this->inscripciones as $inscripcion) {
            if ((int)$inscripcion['id_curso'] === (int)$idCurso) {
                $inscripcionesPorCurso[] = $inscripcion;
            }
        }
    
        if (empty($inscripcionesPorCurso)) {
            echo "No se encontraron inscripciones para el curso con ID {$idCurso}.\n";
        } else {
            foreach ($inscripcionesPorCurso as $inscripcion) {
                $dniEstudiante = $inscripcion['dni_estudiante'];
                echo "Ingrese calificación para el estudiante: " . $inscripcion['dni_estudiante'] . " ENTER para no ingresar nota.". PHP_EOL;
                $notaIngresada = readline();
                // Obtener la fecha actual del sistema
                $fechaCalificacion = date('Y-m-d');
                if ($notaIngresada && $notaIngresada != PHP_EOL){
                    $this->actualizarCalificacion($dniEstudiante, $idCurso, $notaIngresada, $fechaCalificacion);       
                }    
            }
        }
        $this->cargarInscripcionesDesdePostgres();  //Actualiza las inscripciones luego de los cambios
    }
}
