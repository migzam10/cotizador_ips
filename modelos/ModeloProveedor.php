<?php
// modelos/ModeloProveedor.php

require_once '../configuracion/Conexion.php';

class ModeloProveedor {
    private $conexion;

    public function __construct() {
        // Al instanciar la clase, nos conectamos de una vez
        $instancia_conexion = new Conexion();
        $this->conexion = $instancia_conexion->conectar();
    }

    // Función para guardar un proveedor nuevo
    public function guardarProveedor($id_ciudad, $nit, $nombre_ips, $direccion, $telefonos, $nombre_contacto, $correos, $observaciones, $enlace_conceptos, $usu, $password, $tipo_cuenta, $banco, $numero_cuenta) {
        try {
            $consulta_sql = "INSERT INTO proveedores (id_ciudad, nit, nombre_ips, direccion, telefonos, nombre_contacto, correos, observaciones,enlace_conceptos, usu, password, tipo_cuenta, banco, numero_cuenta) 
                             VALUES (:id_ciudad, :nit, :nombre_ips, :direccion, :telefonos, :nombre_contacto, :correos, :observaciones, :enlace_conceptos, :usu, :password, :tipo_cuenta, :banco, :numero_cuenta)";
            
            // Preparamos la consulta para evitar inyecciones SQL
            $sentencia = $this->conexion->prepare($consulta_sql);
            
            // Enlazamos los datos
            $sentencia->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
            $sentencia->bindParam(':nit', $nit, PDO::PARAM_STR);
            $sentencia->bindParam(':nombre_ips', $nombre_ips, PDO::PARAM_STR);
            $sentencia->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $sentencia->bindParam(':telefonos', $telefonos, PDO::PARAM_STR);
            $sentencia->bindParam(':nombre_contacto', $nombre_contacto, PDO::PARAM_STR);
            $sentencia->bindParam(':correos', $correos, PDO::PARAM_STR);
            $sentencia->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
            $sentencia->bindParam(':enlace_conceptos', $enlace_conceptos, PDO::PARAM_STR);
            $sentencia->bindParam(':usu', $usu, PDO::PARAM_STR);
            $sentencia->bindParam(':password', $password, PDO::PARAM_STR);
            $sentencia->bindParam(':tipo_cuenta', $tipo_cuenta, PDO::PARAM_STR);
            $sentencia->bindParam(':banco', $banco, PDO::PARAM_STR);
            $sentencia->bindParam(':numero_cuenta', $numero_cuenta, PDO::PARAM_STR);

            // Ejecutamos y retornamos verdadero si todo salió bien
            return $sentencia->execute();

        } catch (PDOException $error) {
            // Si el NIT ya existe, PDO lanzará un error aquí
            return "Error al guardar: " . $error->getMessage();
        }
    }

    // Función para traer todos los proveedores (ideal para el DataTables)
    public function obtenerTodosLosProveedores() {
        try {
            // Hacemos un JOIN con la tabla ciudades para traer el nombre de la ciudad y no solo el ID
            $consulta_sql = "SELECT p.*, c.nombre as nombre_ciudad 
                             FROM proveedores p 
                             INNER JOIN ciudades c ON p.id_ciudad = c.id 
                             ORDER BY p.nombre_ips ASC";
                             
            $sentencia = $this->conexion->prepare($consulta_sql);
            $sentencia->execute();
            
            return $sentencia->fetchAll();
            
        } catch (PDOException $error) {
            return "Error al consultar: " . $error->getMessage();
        }
    }


    // Funcion para obtener los datos de un solo proveedor por su ID
    public function obtenerProveedorPorId($id) {
        try {
            $consulta_sql = "SELECT * FROM proveedores WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta_sql);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Funcion para actualizar los datos de un proveedor existente
    public function actualizarProveedor($id, $id_ciudad, $nit, $nombre_ips, $direccion, $telefonos, $nombre_contacto, $correos, $observaciones, $enlace_conceptos, $usu, $password, $tipo_cuenta, $banco, $numero_cuenta) {
        try {
            $consulta_sql = "UPDATE proveedores SET 
                            id_ciudad = :id_ciudad, nit = :nit, nombre_ips = :nombre_ips, 
                            direccion = :direccion, telefonos = :telefonos, 
                            nombre_contacto = :nombre_contacto, correos = :correos, 
                            observaciones = :observaciones, enlace_conceptos = :enlace_conceptos, 
                            usu = :usu, password = :password, tipo_cuenta = :tipo_cuenta, 
                            banco = :banco, numero_cuenta = :numero_cuenta 
                            WHERE id = :id";
            
            $sentencia = $this->conexion->prepare($consulta_sql);
            
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
            $sentencia->bindParam(':nit', $nit, PDO::PARAM_STR);
            $sentencia->bindParam(':nombre_ips', $nombre_ips, PDO::PARAM_STR);
            $sentencia->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $sentencia->bindParam(':telefonos', $telefonos, PDO::PARAM_STR);
            $sentencia->bindParam(':nombre_contacto', $nombre_contacto, PDO::PARAM_STR);
            $sentencia->bindParam(':correos', $correos, PDO::PARAM_STR);
            $sentencia->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
            $sentencia->bindParam(':enlace_conceptos', $enlace_conceptos, PDO::PARAM_STR);
            $sentencia->bindParam(':usu', $usu, PDO::PARAM_STR);
            $sentencia->bindParam(':password', $password, PDO::PARAM_STR);
            $sentencia->bindParam(':tipo_cuenta', $tipo_cuenta, PDO::PARAM_STR);
            $sentencia->bindParam(':banco', $banco, PDO::PARAM_STR);
            $sentencia->bindParam(':numero_cuenta', $numero_cuenta, PDO::PARAM_STR);

            return $sentencia->execute();
        } catch (PDOException $error) {
            return "Error al actualizar: " . $error->getMessage();
        }
    }

    // Funcion para activar o desactivar un proveedor
    public function cambiarEstadoProveedor($id, $estado) {
        try {
            $consulta_sql = "UPDATE proveedores SET estado = :estado WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta_sql);
            $sentencia->bindParam(':estado', $estado, PDO::PARAM_INT);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }


    
}
?>