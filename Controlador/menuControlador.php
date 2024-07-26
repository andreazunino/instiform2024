<?php

require_once('Modelo/GestionEstudiante.php');
require_once('Modelo/GestionCurso.php');
require_once('Vista/menuVista.php');
require_once('Modelo/gestionInscripcion.php');
require_once('menuAdminControlador.php');
require_once('menuEstudianteControlador.php');

class Controlador {
    private $gestionEstudiante;
    private $gestionCurso;
    private $vista;
    private $inscripcion;
    private $menuAdminControlador;
    private $menuEstudianteControlador;

    public function __construct() {
        $this->gestionEstudiante = new gestionEstudiante();
        $this->gestionCurso = new gestionCurso();
        $this->vista = new Vista();
        $this->inscripcion = new Inscripcion();
        $this->menuAdminControlador = new MenuAdminControlador($this->gestionEstudiante, $this->gestionCurso, $this->inscripcion);
        $this->menuEstudianteControlador = new MenuEstudianteControlador($this->gestionEstudiante, $this->gestionCurso, $this->inscripcion);
    }

    public function run() {
        $this->menu();
    }

    public function menu() {
        while (true) {
            $this->vista->mostrarLogin();
            $opcionMenu = readline("Selecciona una opción: ");
            switch ($opcionMenu) {
                case '1':
                    $this->menuEstudianteControlador->run();
                    break;
                case '2':
                    $this->menuAdminControlador->run();
                    break;
                case '0':
                    echo "Que tengas Buen Día\n";
                    exit;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
            }
        }
    }

}
        