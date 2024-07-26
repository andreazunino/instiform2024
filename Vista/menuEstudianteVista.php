<?php

class MenuEstudianteVista extends Vista{
   
    public function mostrarMenuEstudiantes() {
        $opcionesEstudiantes = ["Inscribirse a curso", "Anular inscripcion a curso", "Ver cursos inscriptos", "Ver boletín"];
        $this->mostrarMenu($opcionesEstudiantes);
    }

    public function bienvenida($estudiante){
        echo "\nBienvenido {$estudiante->getNombre()} {$estudiante->getApellido()}.\n";
    }
       
}
