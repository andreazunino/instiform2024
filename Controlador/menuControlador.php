<?php

require_once('Modelo/estudiante.php');
require_once('Modelo/curso.php');
require_once('Modelo/gestionEstudiante.php');
require_once('Modelo/gestionCurso.php');
require_once('./Vista/menuVista.php');
require_once('Modelo/inscripcion.php');
require_once('Modelo/boletin.php');
require_once('Modelo/gestionBoletin.php');

class Controlador {
    private $gestionEstudiante;
    private $gestionCurso;
    private $vista;
    private $inscripcion;
    private $boletin;
    private $gestionBoletin;

    public function __construct($gestionEstudiante, $gestionCurso, $vista, $inscripcion) {
        $this->gestionEstudiante = $gestionEstudiante;
        $this->gestionCurso = $gestionCurso;
        $this->vista = $vista;
        $this->inscripcion = $inscripcion;
        //$this->boletin = $boletin;
        //$this->gestionBoletin = $gestionBoletin;
    }

    public function run() {
        $opcionMenu = 1;
        $this->menu($opcionMenu);
    }

    public function menu($opcionMenu) {
        while (true){
            $this->vista->mostrarLogin();
            $opcionMenu = readline("Selecciona una opción: ");
            switch ($opcionMenu) {
                case '1':
                    $this->vista->mostrarMenuEstudiantes();
                    $this->menuEstudiantes();
                    break;
                case '2':
                    $this->vista->mostrarMenuAdministradores();
                    $this->menuAdmin();
                    break;         
                case '0':
                    echo "Que tengas Buen Día";
                    exit;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
        }
    }
    }

    
    protected function menuAdmin(){
        $opcionMenu = readline("Selecciona una opción: ");
        switch ($opcionMenu) {
            case '1':
                echo "Seleccionaste Administración de Estudiantes";
                $this->admEstudiantes();
            break;
            case '2':
                echo "Seleccionaste Administración de Cursos";
                $this->admCursos();
            break;
            case '3':
                echo "Seleccionaste Administración de Inscripciones";
                $this->admInscripciones();
                break;
            case '0':
                echo "\nSeleccionaste Volver Al Menu Principal\n";
                break;
            default:
                $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
        }
    }                
                
    
    
    function menuEstudiantes() {
    
        $opcionEstudiantes = readline("Selecciona una opción: ");

       switch ($opcionEstudiantes){
        case '1':
           echo "Seleccionaste Inscribirse a curso\n";
           $this->inscripcion->cargarInscripciones();
           break;
       case '2':
           echo "Seleccionaste Anular inscripcion a curso\n";
           $this->inscripcion->listarInscripciones();
           $idElim = readline("Ingrese el ID de la inscripcion a eliminar: ");
           $this->inscripcion->eliminarInscripcionPorID($idElim);
           break;
       case '3':
           echo "Seleccionaste Ver cursos inscriptos\n";
           $dniVer = readline("Ingrese el DNI del estudiante para ampliar su información: ");
           $this->gestionEstudiante->verDatosEInscripcionesPorDNI($dniVer);
           $this->inscripcion->mostrarInscripcionesPorDNI($dniVer);
           break;
       case '0':
           echo "Seleccionaste Volver al Menu Principal\n";
           $this->menu($opcionEstudiantes);
           self::run();
       }
   }


    
    function admEstudiantes(){
// REVISAR COMPLETO -----------------------------------------ANDA
        $this->vista->mostrarSubMenuEstudiantes();
        $opcionAdmEstudiantes = readline("Selecciona una opción: \n");
        switch ($opcionAdmEstudiantes){
            case '1':
                echo "Seleccionaste Dar de Alta Estudiante\n";
                $nombre = readline("Ingrese nombre del Estudiante: ");
                $apellido = readline("Ingrese apellido del Estudiante: ");
                $dni = readline("Ingrese dni del Estudiante: ");
                $email = readline("Ingrese email del Estudiante: ");
                $estudiante = new Estudiante($nombre, $apellido, $dni, $email);
                $this->gestionEstudiante->agregarEstudiante($estudiante);
                break;
            case '2':
                echo "Seleccionaste Dar de Baja Estudiante\n";
                $this->gestionEstudiante->listarEstudiantes();
                echo "Ingrese el DNI del estudiante a eliminar: ";
                $dni = readline();
                $estudianteEliminado = $this->gestionEstudiante->eliminarEstudiantePorDNI($dni);
                if ($estudianteEliminado) {
                    echo "Estudiante con DNI $dni ha sido eliminado correctamente.\n";
                } else {
                    echo "\n No se pudo eliminar Estudiante con el DNI $dni. \n";
                }
                break;
            case '3':
                echo "Seleccionaste Modificar Datos de Estudiante\n";
                $dniModificar = readline("Ingrese el DNI del estudiante a modificar: ");
                $estudianteEncontrado = $this->gestionEstudiante->buscarEstudiantePorDNI($dniModificar);
                if ($estudianteEncontrado) {
                    $nombreNuevo = readline("Ingrese el nuevo nombre del estudiante: ");
                    $apellidoNuevo = readline("Ingrese el nuevo apellido del estudiante: ");
                    $emailNuevo = readline("Ingrese el nuevo email del estudiante: ");
                    $this->gestionEstudiante->modificarEstudiantePorDNI($dniModificar, $nombreNuevo, $apellidoNuevo, $emailNuevo);
                    echo "Los datos del estudiante con DNI $dniModificar han sido modificados correctamente.\n";
                } else {
                    echo "No se encontró ningún estudiante con el DNI $dniModificar.\n";
                }
                break;
            case '4':
                $this->gestionEstudiante->listarEstudiantes();
                $dniVer = readline("Ingrese el DNI del estudiante para ampliar su información: ");
                $this->gestionEstudiante->verDatosEInscripcionesPorDNI($dniVer);
                $this->inscripcion->mostrarInscripcionesPorDNI($dniVer);
                break;
            case '0':
                echo "Seleccionaste Volver Al Menu Principal\n";
                $this->menu($opcionAdmEstudiantes);
        }
    }
    
    function admCursos(){

        $this->vista->mostrarSubMenuCursos();
        $opcionAdmCursos = readline("Selecciona una opción: \n");
        switch ($opcionAdmCursos){
            case '1':  
                echo "Seleccionaste Dar de Alta Cursos\n";
                $nombre = readline("Ingrese el nombre del curso: ");
                $cupo = intval(readline("Ingrese el cupo maximo del curso: "));
                $id = null;
                $curso = new Curso( $id, $nombre, $cupo);
                $this->gestionCurso->agregarCurso($curso);
                break;
            case '2':  
                echo "Seleccionaste Dar de Baja Cursos\n";
                echo "1. Buscar por Nombre\n";
                echo "0. Volver al Menú de Cursos\n";
    
                $opcionBuscar = readline("Selecciona una opción: \n");
                switch ($opcionBuscar) {
                    case '1':
                        $nombreBuscar = readline("Ingrese el nombre del curso a buscar: ");
                        $cursosEncontrados = $this->gestionCurso->buscarCursosPorNombre($nombreBuscar);
                        $this->gestionCurso->mostrarCursosEncontrados($cursosEncontrados);
            
                        if (!empty($cursosEncontrados)) {
                            $idEliminar = readline("Ingrese el ID del curso a eliminar: ");
                            $cursoEliminado = $this->gestionCurso->eliminarCursoPorID($idEliminar);
                            if ($cursoEliminado) {
                                echo "Curso con ID $idEliminar eliminado correctamente.\n";
                            } else {
                                echo "No pudo ser eliminado el Curso con el ID $idEliminar.\n";
                            }
                        }
                        break;
            
                        case '0':
                        $this->admCursos();
                        break;
                    }
                break;
            case '3':
                echo "Seleccionaste Modificar Datos de Curso\n";
                $idModificar = readline("Ingrese el ID del curso a modificar: ");
                $cursoEncontrado = $this->gestionCurso->buscarCursosPorCodigo($idModificar);
                if ($cursoEncontrado) {
                    $nombreNuevo = readline("Ingrese el nuevo nombre del curso: ");
                    $cupoNuevo = intval(readline("Ingrese el nuevo cupo del curso: "));
                    $this->gestionCurso->modificarCursoPorId($idModificar, $nombreNuevo, $cupoNuevo);
                    echo "Los datos del curso id $idModificar han sido modificados correctamente.\n";
                } else {
                    echo "No se encontró ningún curso con el id $idModificar.\n";
                    }
                break;
            case '4':  
                echo "Seleccionaste Listar Cursos\n";
                $cursos = $this->gestionCurso->obtenerCursos();

                if (empty($cursos)) {
                    echo "No hay cursos registrados.\n";
                } else {
                echo "Lista de Cursos:\n";
                foreach ($cursos as $curso) {
                    $this->gestionCurso->mostrarCurso($curso);;
                }
                }
                break;
            case '0':
                echo "Seleccionaste Volver Al Menu Principal\n";
                $this->menu($opcionAdmCursos);
        }
    }
    
    function admInscripciones(){
        $this->vista->mostrarSubMenuInscripciones();
        $opcionAdmInscripciones = readline("Selecciona una opción: ");
                switch ($opcionAdmInscripciones) {
                    case '1':
                        echo "Seleccionaste Inscribir Estudiante en Curso\n";
                        $this->inscripcion->cargarInscripciones();
                        break;
                    case '2':
                        echo "Seleccionaste Borrar Inscripciones\n";
                        $this->inscripcion->listarInscripciones();
                        $idElim = readline("Ingrese el ID de la inscripcion a eliminar: ");
                        $this->inscripcion->eliminarInscripcionPorID($idElim);
                        break;
                    case '3':
                        $this->inscripcion->listarInscripciones();
                        break;
                    case '0':
                        echo "\nSeleccionaste Volver Al Menu Principal\n";
                        $this->menu($opcionAdmInscripciones);
                    
                    default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                }
    }
}
   