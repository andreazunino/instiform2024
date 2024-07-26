<?php
require_once('Modelo/gestionInscripcion.php');
require_once('Modelo/curso.php');

class GestionCurso {
    private $cursos = [];

    public function __construct() {
        $this->cargarCursosDesdePostgres();
    }

    public function obtenerIdCurso($curso) {
        return $curso->getId();
    }

    public function obtenerNombreCurso($curso) {
        return $curso->getNombre();
    }

    public function obtenerCupoCurso($curso) {
        return $curso->getCupo();
    }

    public function agregarCurso($curso) {
        foreach ($this->cursos as $cursoExistente) {
            if ($cursoExistente->getNombre() === $curso->getNombre()) {
                echo "El curso con Nombre: {$curso->getNombre()} ya existe en la lista de cursos.\n";
                return;
            }
        }
        
        // Asignar un ID null para indicar que el ID debe ser generado por la base de datos
        $curso->setId(null);

        echo "El curso se agregó exitosamente\n";
        $this->cursos[] = $curso;
        $this->guardarCursos();
    }

    public function buscarCursosPorNombre($nombre) {
        $cursosEncontrados = [];
        foreach ($this->cursos as $curso) {
            if (strtolower($curso->getNombre()) === strtolower($nombre)) {
                $cursosEncontrados[] = $curso;
            }
        }
        return $cursosEncontrados;
    }

    public function buscarCursosPorCodigo($id) {
        $cursosEncontrados = [];
        foreach ($this->cursos as $curso) {
            if ($curso->getId() == $id) {
                $cursosEncontrados[] = $curso;
            }
        }
        return $cursosEncontrados;
    }

    public function mostrarCursosEncontrados($cursos) {
        if (empty($cursos)) {
            echo "No se encontraron cursos.\n";
        } else {
            echo "Cursos encontrados:\n";
            foreach ($cursos as $curso) {
                $this->mostrarCurso($curso);
            }
        }
    }

    public function mostrarCurso($curso) {
        echo "ID: " . $curso->getId() . "\n";
        echo "Nombre: " . $curso->getNombre() . "\n";
        echo "Cupo: " . $curso->getCupo() . "\n";
        echo "===============================\n";
    }

    public function eliminarCursoPorID($id) {
        $conexion = Conexion::getConexion();
        try {
            if (!empty($id)) {
                $sql = "DELETE FROM curso WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                foreach ($this->cursos as $key => $curso) {
                    if ((int)$curso->getId() === (int)$id) {
                        unset($this->cursos[$key]);
                        $this->guardarCursos();
                        return true;
                    }
                }
                return false;
            }
        } catch (PDOException $e) {
            echo 'Error al eliminar curso: No puedes eliminar un Curso con Estudiantes Inscriptos en él.' . PHP_EOL;
        }
        return false;
    }

    public function modificarCursoPorId($id, $nuevoNombre, $nuevoCupo) {
        $conexion = Conexion::getConexion();
        try {
            // Primero, actualizamos el curso en la base de datos
            $sqlActualizacion = "UPDATE curso SET nombre = :nombre, cupo = :cupo WHERE id = :id";
            $stmtActualizacion = $conexion->prepare($sqlActualizacion);
            $stmtActualizacion->bindParam(':nombre', $nuevoNombre, PDO::PARAM_STR);
            $stmtActualizacion->bindParam(':cupo', $nuevoCupo, PDO::PARAM_INT);
            $stmtActualizacion->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtActualizacion->execute();
            
            // Luego, actualizamos el curso en el array local
            foreach ($this->cursos as $curso) {
                if ($curso->getId() == $id) {
                    $curso->setNombre($nuevoNombre);
                    $curso->setCupo($nuevoCupo);
                    return true;
                }
            }
        } catch (PDOException $e) {
            echo "Error al modificar curso: " . $e->getMessage() . PHP_EOL;
        }
        return false; // Retornar false si no se encontró el curso con el ID especificado
    }

    public function obtenerCursos() {
        return $this->cursos;
    }

    public function cargarCursosDesdePostgres() {
        try {
            $conexion = Conexion::getConexion();
            $query = $conexion->query("SELECT * FROM curso");
            $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

            // Vaciar el arreglo de cursos antes de cargar los cursos nuevamente
            $this->cursos = [];

            foreach ($resultados as $cursoData) {
                $curso = new Curso($cursoData['id'], $cursoData['nombre'], $cursoData['cupo']);
                $this->cursos[] = $curso;
            }
        } catch (PDOException $e) {
            echo "Error al cargar cursos desde la base de datos: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function guardarCursos() {
        try {
            $conexion = Conexion::getConexion();

            foreach ($this->cursos as $curso) {
                $id = $curso->getId();
                $nombre = $curso->getNombre();
                $cupo = $curso->getCupo();

                if ($id) {
                    // Si el curso ya tiene un ID, actualiza el curso existente
                    $sqlActualizacion = "UPDATE curso SET nombre = :nombre, cupo = :cupo WHERE id = :id";
                    $stmtActualizacion = $conexion->prepare($sqlActualizacion);
                    $stmtActualizacion->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                    $stmtActualizacion->bindParam(':cupo', $cupo, PDO::PARAM_INT);
                    $stmtActualizacion->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmtActualizacion->execute();
                } else {
                    // Si el curso no tiene ID, insértalo como nuevo
                    $sqlInsercion = "INSERT INTO curso (nombre, cupo) VALUES (:nombre, :cupo) RETURNING id";
                    $stmtInsercion = $conexion->prepare($sqlInsercion);
                    $stmtInsercion->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                    $stmtInsercion->bindParam(':cupo', $cupo, PDO::PARAM_INT);
                    $stmtInsercion->execute();
                    $id = $stmtInsercion->fetchColumn();
                    $curso->setId($id); // Asignar el ID generado al objeto Curso
                }
            }
        } catch (PDOException $e) {
            echo "Error al guardar cursos en la base de datos: " . $e->getMessage() . PHP_EOL;
        }
    }

    public function obtenerCursosParaInscripcion() {
        $conexion = Conexion::getConexion();
        $query = $conexion->query("SELECT id, nombre, cupo FROM curso");
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

        $cursos = [];
        foreach ($resultados as $cursoData) {
            $curso = new Curso($cursoData['id'], $cursoData['nombre'], $cursoData['cupo']);
            $cursos[] = $curso;
        }

        return $cursos;
    }
}

