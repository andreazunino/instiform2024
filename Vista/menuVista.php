<?php

class Vista {
   
   public function mostrarLogin() {
       $opcionesLogin = [
           "Soy Estudiante",
           "Soy Administrador"
       ];
       $this->mostrarMenuInicial($opcionesLogin);
   }

 
   // Añadir métodos de vista para mostrar menús relacionados con boletines

   protected function mostrarSubMenuBoletines() {
       echo "Gestión de Boletines:\n";
       echo "1. Crear Boletín\n";
       echo "2. Agregar Nota\n";
       echo "0. Volver al Menú Principal\n";
   }


   public function mostrarMensajeError($mensaje) {
       echo "Error: " . $mensaje . "\n";
   }

   protected function mostrarMenu(array $opciones) {
       echo "============= Menú ==============\n";
       foreach ($opciones as $index => $opcion) {
           printf("%-2s. %s\n", $index + 1, $opcion);
       }
       echo "0 . Volver al Menú Anterior\n";
       echo "=================================\n";
       echo "=========== Instiform ===========\n";
       echo "\n";
   }

   private function mostrarMenuInicial(array $opciones) {
       echo "\n=========== Bienvenido ==========\n";
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

