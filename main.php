<?php
require_once('Modelo/estudiante.php');
require_once('Modelo/curso.php');
require_once('Modelo/gestionEstudiante.php');
require_once('Modelo/gestionCurso.php');
require_once('Vista/menuVista.php');
require_once('Controlador/menuControlador.php');
require_once('Modelo/conexionNueva.php');


$db = Conexion::getConexion();

if ($db != null) {
    echo "Conexion Establecida";
    echo PHP_EOL;
    echo PHP_EOL;
    
    $gestionEstudiante = new GestionEstudiante();
    $gestionCurso = new GestionCurso();
    $inscripcion = new Inscripcion();
    $vista = new Vista();

    $menu = new Controlador($gestionEstudiante, $gestionCurso, $vista, $inscripcion);

    $menu->run();
}else{
    echo PHP_EOL;
    echo "Problemas al intentar conectar con la Base de Datos";
    echo PHP_EOL;
}

