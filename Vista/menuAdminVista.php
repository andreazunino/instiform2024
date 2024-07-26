<?php

class MenuAdminVista extends Vista{
   
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
        $opcionesInscripciones = ["Listar Inscripciones", "Borrar Inscripcion", "Inscribir"];
        $this->mostrarMenu($opcionesInscripciones);
    }
}
