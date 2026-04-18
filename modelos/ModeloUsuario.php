<?php
// modelos/ModeloUsuario.php

require_once '../configuracion/Conexion.php';

class ModeloUsuario {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    // Funcion para validar el inicio de sesion
    public function obtenerUsuarioPorCredencial($usuario) {
        try {
            $consulta = "SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 1";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Funcion para listar todos los usuarios (Solo para admin)
    public function obtenerTodosLosUsuarios() {
        try {
            $consulta = "SELECT id, nombre, usuario, rol, estado FROM usuarios ORDER BY nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Funcion para crear un nuevo usuario
    public function crearUsuario($nombre, $usuario, $clave, $rol) {
        try {
            // Encriptamos la clave por seguridad antes de guardarla
            $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);
            
            $consulta = "INSERT INTO usuarios (nombre, usuario, clave, rol) VALUES (:nombre, :usuario, :clave, :rol)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sentencia->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $sentencia->bindParam(':clave', $clave_encriptada, PDO::PARAM_STR);
            $sentencia->bindParam(':rol', $rol, PDO::PARAM_STR);
            
            return $sentencia->execute();
        } catch (PDOException $error) {
            return "Error al guardar: " . $error->getMessage();
        }
    }

    // Funcion para cambiar la clave
    public function cambiarClave($id, $nueva_clave) {
        try {
            $clave_encriptada = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $consulta = "UPDATE usuarios SET clave = :clave WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':clave', $clave_encriptada, PDO::PARAM_STR);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Funcion para eliminar un usuario fisicamente (Solo admin)
    public function eliminarUsuario($id) {
        try {
            $consulta = "DELETE FROM usuarios WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }
}
?>
