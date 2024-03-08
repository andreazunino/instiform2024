<?php
require_once('Modelo/conexionNueva.php');

class GestionBoletin {
    private $boletines = [];

    public function agregarBoletin($nota, $fechaNota) {
        $boletin = new Boletin($nota, $fechaNota);
        $this->boletines[] = $boletin;
        echo "Boletín agregado correctamente.\n";
    }

    public function modificarBoletin($indice, $nota, $fechaNota) {
        if (isset($this->boletines[$indice])) {
            $this->boletines[$indice]->setNota($nota);
            $this->boletines[$indice]->setFechaNota($fechaNota);
            echo "Boletín modificado correctamente.\n";
        } else {
            echo "Índice de boletín no válido.\n";
        }
    }

    public function eliminarBoletin($indice) {
        if (isset($this->boletines[$indice])) {
            unset($this->boletines[$indice]);
            echo "Boletín eliminado correctamente.\n";
        } else {
            echo "Índice de boletín no válido.\n";
        }
    }

    public function verBoletines() {
        if (empty($this->boletines)) {
            echo "No hay boletines disponibles.\n";
        } else {
            echo "\nLista de Boletines:\n";
            foreach ($this->boletines as $indice => $boletin) {
                echo "Índice: $indice, Nota: " . $boletin->getNota() . ", Fecha de Nota: " . $boletin->getFechaNota() . "\n";
            }
            echo PHP_EOL;
        }
    }

    // Ver parte de base de datos. en este codigo con el ID del estudiante y un objeto Boletin como parámetros, 
    //guarda la informacion en la tabla boletin de la base de datos.

    public function guardarBoletinEnBaseDeDatos($idEstudiante, Boletin $boletin) {
        $conexion = Conexion::getConexion();

        $query = $conexion->prepare("INSERT INTO boletin (id_estudiante, nota, fecha_nota) 
            VALUES (:id_estudiante, :nota, :fecha_nota)");

        $query->bindParam(':id_estudiante', $idEstudiante);
        $query->bindParam(':nota', $boletin->getNota());
        $query->bindParam(':fecha_nota', $boletin->getFechaNota());

        return $query->execute();
    }
}
