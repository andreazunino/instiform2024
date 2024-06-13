<?php

class Vista {
   
    public function mostrarLogin() {
        $opcionesLogin = [
            "Soy Estudiante",
            "Soy Administrador"
        ];
        $this->mostrarMenu($opcionesLogin);
    }

    public function mostrarMenuEstudiantes() {
        $opcionesEstudiantes = ["Inscribirse a curso", "Anular inscripcion a curso", "Ver cursos inscriptos"];
        $this->mostrarMenu($opcionesEstudiantes);
    }



    public function mostrarMenuAdministradores(){
        $opcionesAdministradores = ["Administración de Estudiantes", "Administración de Cursos", "Administración de Inscripciones"];
        $this->mostrarMenu($opcionesAdministradores);

    }

    public function mostrarSubMenuEstudiantes() {
        $opcionesUsuarios = ["Dar de Alta", "Dar de Baja", "Modificar Datos", "Ver Datos e Inscripciones"];
        $this->mostrarMenu($opcionesUsuarios);
    } 

    public function mostrarSubMenuCursos() {
        $opcionesCursos = ["Dar de Alta", "Dar de Baja", "Modificar Datos", "Listar cursos", "Notas"];
        $this->mostrarMenu($opcionesCursos);
    }


    public function mostrarSubMenuInscripciones() {
        $opcionesInscripciones = ["Inscribir", "Borrar Inscripcion", "Listar Inscripciones"];
        $this->mostrarMenu($opcionesInscripciones);
    }



    public function mostrarMensajeError($mensaje) {
        echo "Error: " . $mensaje . "\n";
    }

      private function mostrarMenu(array $opciones) {
        echo "=========== Bienvenido ==========\n";
        echo "============= Menú ==============\n";
        foreach ($opciones as $index => $opcion) {
            printf("%-2s. %s\n", $index + 1, $opcion);
        }
        echo "0 . Salir\n";
        echo "=================================\n";
        echo "=========== Instiform ===========\n";
        echo "\n";
    }

    
}

