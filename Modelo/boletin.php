<?php
class Boletin {
    private $estudiante;
    private $inscripciones;
    private $promedio;
    private $aplazos;
    private $aprobadas;

   
    public function __construct($estudiante) {
        $this->estudiante = $estudiante;
        $this->inscripciones = [];
        $this->promedio = null;
        $this->aprobadas = 0;
        $this->aplazos = 0;
    }

    public function setPromedio($promedio){
        $this->promedio = $promedio;
    }

    public function getPromedio(){
        return $this->promedio;
    }
    
    public function setAplazos($aplazos){
        $this->aplazos = $aplazos;
    }

    public function getAplazos(){
        return $this->aplazos;
    }

    public function setAprobadas($aprobadas){
        $this->aprobadas = $aprobadas;
    }

    public function getAprobadas(){
        return $this->aprobadas;
    }
   
    private function cargarInscripcionesDesdePostgres() {
        $conexion = Conexion::getConexion();
        $sql = "SELECT * FROM inscripcion WHERE dni_estudiante = :dni_estudiante";
        $stmt = $conexion->prepare($sql);
        $dniEstudiante = $this->estudiante->getDni();
        $stmt->bindParam(':dni_estudiante', $dniEstudiante, PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    
    
    public function generarBoletin() {
        $this->cargarInscripcionesDesdePostgres();
        $promedio = $aplazos = $aprobadas = 0;
        foreach($this->inscripciones as $inscripcion){
            if ($inscripcion['calificacion'] != null){
                if ($inscripcion['calificacion'] >= 4){
                    $promedio += $inscripcion['calificacion'];
                    $aprobadas++;
                } else{
                    $aplazos ++;
                }
            }
        }
        if ($aprobadas){
            $this->setAprobadas($aprobadas);
            $promedio = $promedio/$aprobadas;
            $this->setPromedio($promedio);
        }
        $this->setAplazos($aplazos);
    }

    public function mostrarBoletin() {
        $this->estudiante->mostrar();
        echo "Materias aprobadas: {$this->getAprobadas()}\n";
        echo "Materias desaprobadas: {$this->getAplazos()}\n";
        echo "Promedio (materias aprobadas): {$this->getPromedio()}\n";
        if (empty($this->inscripciones)) {
            echo "No hay inscripciones disponibles.\n";
        } else {
            echo "Lista de materias:\n";
            foreach ($this->inscripciones as $inscripcion) {
                $calificacion = $inscripcion['calificacion'];
                $fechaCalificacion = $inscripcion['fecha_calificacion'];
                echo "ID del Curso: " . $inscripcion['id_curso'] . ", Calificación: " . ($calificacion ?? '--') . ", Fecha: " . ($fechaCalificacion ?? '--') . "\n";
            }
        }
    }
}
