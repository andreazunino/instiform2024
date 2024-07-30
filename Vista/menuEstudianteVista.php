<?php

class MenuEstudianteVista extends Vista{
   
    public function mostrarMenuEstudiantes() {
        $opcionesEstudiantes = ["Inscribirse a Curso", "Anular Inscripcion a Curso", "Ver Cursos Inscriptos", "Ver Boletín"];
        $this->mostrarMenu($opcionesEstudiantes);
    }

    public function bienvenida($estudiante){
        echo "\nBienvenido {$estudiante->getNombre()} {$estudiante->getApellido()}.\n";
    }
       
}
