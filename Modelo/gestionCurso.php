<?php
require_once('Modelo/inscripcion.php');
class gestionCurso {
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

    //se agrega get de cupo
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
        echo "El curso se agrego exitosamente\n";
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
        echo "Cupo: " . $curso->getCupo() . "\n"; //se agrega la linea para mostrar cupo
        echo "===============================\n";
    }

    public function eliminarCursoPorID($id) {
        $conexion = Conexion::getConexion();
        try {
            if($id != ""){
                $sql = "DELETE FROM curso WHERE id = '$id'";
                Conexion::ejecutar($sql);
    
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
                echo 'Error al eliminar curso: No puedes eliminar un Curso con Estudiantes Inscriptos en él.'.PHP_EOL;
            }
    }
    
    
//ver si se puede modificar cupo o no
    public function modificarCursoPorId($id, $nuevoNombre, $nuevoCupo) {
        foreach ($this->cursos as $curso) {
            if ($curso->getId() == $id) {
                $curso->setNombre($nuevoNombre);
                $curso->setCupo($nuevoCupo);
                $this->guardarCursos();  //No funciona bien, porque lo agrega, no lo modifica
                return true;
            }
        }
        $this->guardarCursos();
    }

    public function obtenerCursos() {
        return $this->cursos;
    }

    public function cargarCursosDesdePostgres() {
        $conexion = Conexion::getConexion();
        $query = $conexion->query("SELECT * FROM curso");
        $resultados = $query->fetchAll(PDO::FETCH_ASSOC);
    
        // Vaciar el arreglo de cursos antes de cargar los cursos nuevamente
        $this->cursos = [];
        
        //se agrega cupo, hay que agregarlo a la bd!!-->listo
        foreach ($resultados as $cursoData) {
            $curso = new Curso($cursoData['id'], $cursoData['nombre'], $cursoData['cupo']);
            $this->cursos[] = $curso;
        }
    }
    

    public function guardarCursos() {
        $cursos = $this->cursos;
        $conexion = Conexion::getConexion();
    
        foreach ($cursos as $curso) {
            $nombre = $curso->getNombre();
            $cupo = $curso->getCupo();
    
            // Verificar si el curso ya existe en la base de datos
            $sqlVerificacion = "SELECT COUNT(*) FROM curso WHERE nombre = :nombre";
            $stmtVerificacion = $conexion->prepare($sqlVerificacion);
            $stmtVerificacion->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmtVerificacion->execute();
    
            // Obtener el resultado de la consulta
            $cantidadCursos = $stmtVerificacion->fetchColumn();
    
            // Si el curso no existe, insertarlo en la base de datos
            if ($cantidadCursos == 0) {
                $sqlInsercion = "INSERT INTO curso (nombre, cupo) VALUES (:nombre, :cupo)";
                $stmtInsercion = $conexion->prepare($sqlInsercion);
                $stmtInsercion->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmtInsercion->bindParam(':cupo', $cupo, PDO::PARAM_INT);
                $stmtInsercion->execute();
            }
        }
        $this->cargarCursosDesdePostgres();
    }
    
    
    

    public function obtenerCursosParaInscripcion() {
        $cursosParaInscripcion = [];
        foreach ($this->cursos as $curso) {
            $cursosParaInscripcion[] = $curso;
        }
        return $cursosParaInscripcion;
    }
    
}
