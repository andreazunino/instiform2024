<?php
    class Conexion{
    
        private static $db = null;
            
        //Obtiene los datos de ingresos a la DB de un archivo json local
        private static function getDatosDb(){
            $nombreArchivo = "Modelo\base.json";
            if (is_readable($nombreArchivo)){
                $datos = file_get_contents($nombreArchivo);
                $datos = json_decode($datos);
               return $datos;
            }
            return null;
        }
        
        private function __construct(){
            try {
                // Cadena de conexión
                $datosDb = self::getDatosDb();
                $dsn = "pgsql:host=$datosDb->host;port=$datosDb->port;dbname=$datosDb->database;user=$datosDb->user;password=$datosDb->password";
        
                // Crear una instancia de PDO
                self::$db = new PDO($dsn);
        
                // Configurar el modo de error de PDO para manejar excepciones
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
                // Puedes usar esta conexión para realizar consultas
            } catch (PDOException $e) {
                // Manejo de errores
                echo 'Error de conexión: ' . $e->getMessage();
            }
        }
            
        /*
        / Retorna la conexión ya establecida a la DB, si no existe la establece
        */
        static function getConexion(){
            if (isset(self::$db)) {
                return self::$db;
            } else {
                new self();
                return self::$db;
            }
        }
        
        /**
        * Recibe un sql de consulta y devuelve un arreglo de objetos
         */
        static function query($sql) {
            $pDO = self::getConexion();
            $statement = $pDO->query($sql);
            if ($statement) {
                $resultado = $statement->fetchAll(PDO::FETCH_OBJ);
                return $resultado;
            } else {
                return [];
            }
        }
            
        
        /**
         * Recibe un sql de ejecutcion
         */
        static function ejecutar($sql) {
            $pDO = self::getConexion();
            $pDO->query($sql);
        }

        /**
         * Prepara la sentencia sql
         */
        static function prepare($sql) {
            $pDO = self::getConexion();
            return $pDO->prepare($sql);
        }

 
        static function getLastId() {
            $pDO = self::getConexion();
            $lastId = $pDO->lastInsertId();
            
            return $lastId;
        }
 
        
        static function closeConexion() {
            self::$db = null;
        }
}