<?php
// modelos/ModeloCategoria.php

require_once '../configuracion/Conexion.php';

class ModeloCategoria {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    public function obtenerTodasLasCategorias() {
        try {
            $consulta = "SELECT * FROM categorias_examen ORDER BY nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function obtenerCategoriaPorId($id) {
        try {
            $consulta = "SELECT * FROM categorias_examen WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function guardarCategoria($nombre) {
        try {
            $consulta = "INSERT INTO categorias_examen (nombre) VALUES (:nombre)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function actualizarCategoria($id, $nombre) {
        try {
            $consulta = "UPDATE categorias_examen SET nombre = :nombre WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function cambiarEstadoCategoria($id, $estado) {
        try {
            $consulta = "UPDATE categorias_examen SET estado = :estado WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':estado', $estado, PDO::PARAM_INT);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function eliminarCategoria($id) {
        try {
                // Iniciamos una transacción para asegurar que se borre todo o no se borre nada
            $this->conexion->beginTransaction();

            // 1. Obtener todos los IDs de los exámenes asociados a esta categoría
            $consultaExamenes = "SELECT id FROM examenes WHERE id_categoria = :id";
            $sentenciaExamenes = $this->conexion->prepare($consultaExamenes);
            $sentenciaExamenes->bindParam(':id', $id, PDO::PARAM_INT);
            $sentenciaExamenes->execute();
            $examenes = $sentenciaExamenes->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($examenes)) {
                // Preparamos los comodines (?,?,?) para borrar en bloque
                $placeholders = implode(',', array_fill(0, count($examenes), '?'));

                // 2. Eliminar de cotizaciones_detalle (para evitar errores de clave foránea)
                $this->conexion->prepare("DELETE FROM cotizaciones_detalle WHERE id_examen IN ($placeholders)")->execute($examenes);

                // 3. Eliminar de la tabla tarifas
                $this->conexion->prepare("DELETE FROM tarifas WHERE id_examen IN ($placeholders)")->execute($examenes);

                // 4. Eliminar los exámenes en sí
                $this->conexion->prepare("DELETE FROM examenes WHERE id_categoria = ?")->execute([$id]);
            }

            // 5. Finalmente, eliminar la categoría madre
            $this->conexion->prepare("DELETE FROM categorias_examen WHERE id = ?")->execute([$id]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $error) {
            $this->conexion->rollBack();
            return false;
        }
    }
}
?>