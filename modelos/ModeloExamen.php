<?php
// modelos/ModeloExamen.php
require_once '../configuracion/Conexion.php';

class ModeloExamen {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    public function obtenerTodosLosExamenes() {
        try {
            // Hacemos JOIN con categorias para traer el nombre
            $consulta = "SELECT e.*, c.nombre AS nombre_categoria 
                         FROM examenes e 
                         LEFT JOIN categorias_examen c ON e.id_categoria = c.id 
                         ORDER BY c.nombre ASC, e.nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function obtenerExamenPorId($id) {
        try {
            $consulta = "SELECT * FROM examenes WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function guardarExamen($id_categoria, $nombre) {
        try {
            $consulta = "INSERT INTO examenes (id_categoria, nombre) VALUES (:id_categoria, :nombre)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function actualizarExamen($id, $id_categoria, $nombre) {
        try {
            $consulta = "UPDATE examenes SET id_categoria = :id_categoria, nombre = :nombre WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function cambiarEstadoExamen($id, $estado) {
        try {
            $consulta = "UPDATE examenes SET estado = :estado WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':estado', $estado, PDO::PARAM_INT);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function eliminarExamen($id) {
        try {
            $consulta = "DELETE FROM examenes WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }
}
?>