<?php
require_once('Vista/menuAdminVista.php');

class MenuAdminControlador {
    private $vista;
    private $gestionEstudiante;
    private $gestionCurso;
    private $inscripcion;
    
    public function __construct($gestionEstudiante, $gestionCurso, $inscripcion) {
        $this->gestionEstudiante = $gestionEstudiante;
        $this->gestionCurso = $gestionCurso;
        $this->vista = new MenuAdminVista();
        $this->inscripcion = $inscripcion;
    }

    public function run() {
        $this->menu();
    }

    private function menu() {
        while (true) {
            $this->vista->mostrarMenuAdministradores();
            $opcionMenu = readline("Selecciona una opción: ");
            switch ($opcionMenu) {
                case '1':
                    echo "Seleccionaste Administración de Estudiantes\n";
                    $this->admEstudiantes();
                    break;
                case '2':
                    echo "Seleccionaste Administración de Cursos\n";
                    $this->admCursos();
                    break;
                case '3':
                    echo "Seleccionaste Administración de Inscripciones\n";
                    $this->admInscripciones();
                    break;
                case '0':
                    echo "Seleccionaste Volver Al Menu Principal\n";
                    return;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
            }
        }
    }

    //Un administrador administra estudiantes
    private function admEstudiantes() {
        while (true) {
            $this->vista->mostrarSubMenuEstudiantes();
            $opcionAdmEstudiantes = readline("Selecciona una opción: ");
            switch ($opcionAdmEstudiantes) {
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
                        echo "No se pudo eliminar Estudiante con el DNI $dni.\n";
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
                    return;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
            }
        }
    }

    //Un administrador administra cursos
    private function admCursos() {
        while (true) {
            $this->vista->mostrarSubMenuCursos();
            $opcionAdmCursos = readline("Selecciona una opción: ");
            switch ($opcionAdmCursos) {
                case '1':
                    echo "Seleccionaste Dar de Alta Cursos\n";
                    $nombre = readline("Ingrese el nombre del curso: ");
                    $cupo = intval(readline("Ingrese el cupo máximo del curso: "));
                    $curso = new Curso(null, $nombre, $cupo);
                    $this->gestionCurso->agregarCurso($curso);
                    break;
                case '2':
                    echo "Seleccionaste Dar de Baja Cursos\n";
                    echo "1. Buscar por Nombre\n";
                    echo "0. Volver al Menú de Cursos\n";
                    $opcionBuscar = readline("Selecciona una opción: ");
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
                                    echo "No se pudo eliminar el Curso con el ID $idEliminar.\n";
                                }
                            }
                            break;
                        case '0':
                            break;
                    }
                    break;
                case '3':
                    echo "Seleccionaste Modificar Datos de Curso\n";
                    $idModificar = readline("Ingrese el ID del curso a modificar: ");
                    $cursoEncontrado = $this->gestionCurso->buscarCursosPorCodigo($idModificar);
                    if ($cursoEncontrado) {
                        $nombreNuevo = readline("Ingrese el nuevo nombre del curso: ");
                        $cupoNuevo = intval(readline("Ingrese el nuevo cupo máximo del curso: "));
                        $this->gestionCurso->modificarCursoPorID($idModificar, $nombreNuevo, $cupoNuevo);
                        echo "Los datos del curso con ID $idModificar han sido modificados correctamente.\n";
                    } else {
                        echo "No se encontró ningún curso con el ID $idModificar.\n";
                    }
                    break;
                case '4':
                    echo "Seleccionaste Ver Cursos Inscritos\n";
                    $cursos = $this->gestionCurso->obtenerCursos();
                    $this->gestionCurso->mostrarCursosEncontrados($cursos);
                    $idVer = readline("Ingrese el ID del curso para ver los estudiantes inscritos: ");
                    $this->inscripcion->mostrarInscripcionesPorCurso($idVer);
                    break;
                case '5':   
                    echo "Seleccionaste Gestión de Notas\n";// Nueva opción para manejar notas
                    $cursos = $this->gestionCurso->obtenerCursos();
                    $this->gestionCurso->mostrarCursosEncontrados($cursos);
                    $idVer = readline("Ingrese el ID del curso para ingresar notas: ");
                    $this->inscripcion->ingresarNotasPorCurso($idVer); 
                    break;
                case '0':
                    echo "Seleccionaste Volver Al Menu Principal\n";
                    return;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
            }
        }
    }

    private function admInscripciones() {
        while (true) {
            $this->vista->mostrarSubMenuInscripciones(); //["Inscribir", "Borrar Inscripcion", "Listar Inscripciones"]
            $opcionAdmInscripciones = readline("Selecciona una opción: ");
            switch ($opcionAdmInscripciones) {
                case '1':
                    echo "Seleccionaste Ver Inscripciones\n";
                    $this->inscripcion->listarInscripciones();
                    break;
                case '2':
                    echo "Seleccionaste Dar de Baja Inscripciones\n";
                    $this->inscripcion->listarInscripciones();
                    echo "Ingrese el ID de la inscripción a eliminar: ";
                    $idEliminar = readline();
                    $inscripcionEliminada = $this->inscripcion->eliminarInscripcionPorID($idEliminar);
                    if ($inscripcionEliminada) {
                        echo "Inscripción con ID $idEliminar ha sido eliminada correctamente.\n";
                    } else {
                        echo "No se pudo eliminar la inscripción con el ID $idEliminar.\n";
                    }
                    break;
                case '3':
                    echo "Seleccionaste Dar de Alta Inscripciones\n";
                    
                    $estudiantes = $this->gestionEstudiante->obtenerEstudiantesParaInscripcion();
                                
                    echo "Estudiantes Disponibles:\n";
                    foreach ($estudiantes as $estudiante) {
                        echo "DNI del Estudiante: {$estudiante->getDNI()}, - Nombre del Estudiante: {$estudiante->getNombre()}\n";
                    }
                
                    $estudianteElegido = readline("Ingrese el DNI del estudiante: ");
                    $this->inscripcion->cargarInscripciones($estudianteElegido);
                    break;
                case '0':
                    echo "Seleccionaste Volver Al Menu Principal\n";
                    return;
                default:
                    $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                    break;
            }
        }
    }

}