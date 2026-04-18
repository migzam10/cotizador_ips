<?php

require_once __DIR__ . '/dotenv_loader.php';

loadEnv(__DIR__ . '/../.env');

class Conexion {
    private $servidor;
    private $usuario;
    private $contrasena; 
    private $base_datos; 


    public function __construct() {
        
        $this->servidor   = $_ENV['DB_HOST'] ?? 'localhost';
        $this->usuario    = $_ENV['DB_USER'] ?? 'root';
        $this->contrasena = $_ENV['DB_PASS'] ?? '';
        $this->base_datos = $_ENV['DB_NAME'] ?? 'cotizador_ips';
    }
    public function conectar() {
        try {
            
            $ruta = "mysql:host=" . $this->servidor . ";dbname=" . $this->base_datos . ";charset=utf8";
            
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            
            $enlace = new PDO($ruta, $this->usuario, $this->contrasena, $opciones);
            return $enlace;

        } catch (PDOException $error) {
           
            die("Paila, falló la conexión a la base de datos: " . $error->getMessage());
        }
    }
}
?>