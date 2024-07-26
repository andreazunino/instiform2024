<?php
   require_once('Vista/menuEstudianteVista.php');
   require_once('Modelo/Boletin.php');

   class MenuEstudianteControlador {
       private $vista;
       private $gestionEstudiante;
       private $gestionCurso;
       private $inscripcion;
       private $boletin;
       private $estudiante;
       
       public function __construct($gestionEstudiante, $gestionCurso, $inscripcion) {
           $this->gestionEstudiante = $gestionEstudiante;
           $this->gestionCurso = $gestionCurso;
           $this->vista = new MenuEstudianteVista();
           $this->inscripcion = $inscripcion;
           $this->boletin = null;
           $this->estudiante = null;
       }
   
        public function run() {
            echo "Vas a operar como estudiante registrado en el sistema\n";     
            $idEstudiante = readline("Ingrese su número de DNI: ");
            $this->estudiante = $this->gestionEstudiante->buscarEstudiantePorDNI($idEstudiante);
            if ($this->estudiante){
                $this->vista->bienvenida($this->estudiante);
                $this->menu();
            }else {
                $this->vista->mostrarMensajeError("No se encuentra registrado en el sistema, comuníquese con un administrador");
            }
       }

    private function verNotas(){
        $boletin = new Boletin($this->estudiante);
        $boletin->generarBoletin();
        $boletin->mostrarBoletin();
    }


    private function menu() {
            while (true) {
                $this->vista->mostrarMenuEstudiantes();
                $opcionEstudiantes = readline("Selecciona una opción: ");
                switch ($opcionEstudiantes) {
                    case '1':
                        echo "Seleccionaste Inscribirte a curso\n";
                        $this->inscripcion->cargarInscripciones($this->estudiante->getDni());
                        break;
                    case '2':
                        echo "Seleccionaste Anular inscripción a curso\n";
                        $this->inscripcion->mostrarInscripcionesPorDNI($this->estudiante->getDni());
                        $idElim = readline("Ingrese el ID de la inscripción a eliminar: ");
                        $this->inscripcion->eliminarInscripcionPorID($idElim);
                        break;
                    case '3':
                        $this->inscripcion->mostrarInscripcionesPorDNI($this->estudiante->getDni());
                        break;
                    case '4':
                        echo "Seleccionaste Ver Notas\n";
                        $this->verNotas();
                        break;
                    case '0':
                        echo "Seleccionaste Volver al Menu Principal\n";
                        return;
                    default:
                        $this->vista->mostrarMensajeError("Opción no válida. Por favor, selecciona una opción válida.");
                        break;
                }
            }
        }
    }