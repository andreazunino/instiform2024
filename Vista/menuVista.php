<?php

class Vista {
   
    public function mostrarLogin() {
        $opcionesLogin = [
            "Soy Estudiante",
            "Soy Profesor",
            "Soy Administrador"
        ];
        $this->mostrarMenu($opcionesLogin);
    }

    public function mostrarMenuEstudiantes() {
        $opcionesEstudiantes = ["Inscribirse a curso", "Anular inscripcion a curso", "Ver cursos inscriptos"];
        $this->mostrarMenu($opcionesEstudiantes);
    }

    public function mostrarMenuProfesores(){
        $opcionesProfesores = ["Cargar notas", "Ver notas de alumnos", "Ver Cursos"];
        $this->mostrarMenu($opcionesProfesores);
    }


    public function mostrarMenuAdministradores(){
        $opcionesAdministradores = ["Administración de Estudiantes", "Administración de Cursos", "Administración de Inscripciones", "Administración de Profesores"];
        $this->mostrarMenu($opcionesAdministradores);

    }

    public function mostrarSubMenuEstudiantes() {
        $opcionesUsuarios = ["Dar de Alta", "Dar de Baja", "Modificar Datos", "Ver Datos e Inscripciones"];
        $this->mostrarMenu($opcionesUsuarios);
    } 

    public function mostrarSubMenuCursos() {
        $opcionesCursos = ["Dar de Alta", "Dar de Baja", "Modificar Datos", "Listar"];
        $this->mostrarMenu($opcionesCursos);
    }

    public function mostrarSubMenuInscripciones() {
        $opcionesInscripciones = ["Inscribir", "Borrar Inscripcion", "Listar Inscripciones"];
        $this->mostrarMenu($opcionesInscripciones);
    }

    public function mostrarSubMenuProfesores() {
        $opcionesProfesores = ["Dar de Alta", "Dar de Baja", "Modificar Datos", "Listar Profesores"];
        $this->mostrarMenu($opcionesProfesores);
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

