<?php
require_once('Modelo/conexionNueva.php');
require_once ('Modelo/curso.php');
require_once ('Modelo/gestionCurso.php');
require_once ('Modelo/estudiante.php');
require_once ('Modelo/gestionEstudiante.php');
require_once ('Modelo/boletin.php');


/*class GestionBoletin {
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



// falta que muestre las notas por curso o por estudiante y falta vincular cada nota a un curso
*/



class GestionBoletin {
    private $boletines = [];

    public function agregarBoletin($nota, $fechaNota, Curso $curso, Estudiante $estudiante) {
        $boletin = new Boletin($nota, $fechaNota, $curso, $estudiante);
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

    public function verBoletinesPorCurso(Curso $curso) {
        $boletinesCurso = array_filter($this->boletines, function($boletin) use ($curso) {
            return $boletin->getCurso() === $curso;
        });

        if (empty($boletinesCurso)) {
            echo "No hay boletines disponibles para el curso '{$curso->getNombre()}'.\n";
        } else {
            echo "Lista de Boletines para el curso '{$curso->getNombre()}':\n";
            foreach ($boletinesCurso as $boletin) {
                echo "Nota: " . $boletin->getNota() . ", Fecha de Nota: " . $boletin->getFechaNota() . "\n";
            }
            echo PHP_EOL;
        }
    }

    public function verBoletinesPorEstudiante(Estudiante $estudiante) {
        $boletinesAlumno = array_filter($this->boletines, function($boletin) use ($estudiante) {
            return $boletin->getEstudiante() === $estudiante;
        });

        if (empty($boletinesEstudiante)) {
            echo "No hay boletines disponibles para el estudiante '{$estudiante->getNombre()}'.\n";
        } else {
            echo "Lista de Boletines para el estudiante '{$estudiante->getNombre()}':\n";
            foreach ($boletinesEstudiante as $boletin) {
                echo "Nota: " . $boletin->getNota() . ", Fecha de Nota: " . $boletin->getFechaNota() . "\n";
            }
            echo PHP_EOL;
        }
    }
}
