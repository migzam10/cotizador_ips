<?php
// modelos/ModeloCiudad.php

require_once '../configuracion/Conexion.php';

class ModeloCiudad {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    public function obtenerTodasLasCiudades() {
        try {
            $consulta = "SELECT * FROM ciudades ORDER BY nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function obtenerCiudadPorId($id) {
        try {
            $consulta = "SELECT * FROM ciudades WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function guardarCiudad($nombre) {
        try {
            $consulta = "INSERT INTO ciudades (nombre) VALUES (:nombre)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function actualizarCiudad($id, $nombre) {
        try {
            $consulta = "UPDATE ciudades SET nombre = :nombre WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function eliminarCiudad($id) {
        try {
            $consulta = "DELETE FROM ciudades WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }
}
?>