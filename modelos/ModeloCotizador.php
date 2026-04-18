<?php
// modelos/ModeloCotizador.php

require_once '../configuracion/Conexion.php';

class ModeloCotizador {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    // Funcion maestra que cruza toda la informacion
    public function obtenerMatrizCotizacion($anio, $ciudades, $examenes) {
        try {
            // Preparamos los comodines para las consultas IN (...) dependiendo de cuantos seleccionen
            $comodines_ciudades = str_repeat('?,', count($ciudades) - 1) . '?';
            $comodines_examenes = str_repeat('?,', count($examenes) - 1) . '?';

            // Armamos la consulta uniendo todas las tablas
            $consulta = "SELECT 
                            c.nombre AS ciudad, 
                            p.id AS id_proveedor, 
                            p.nombre_ips AS proveedor, 
                            e.id AS id_examen, 
                            e.nombre AS examen,
                            t.precio_costo, 
                            t.precio_venta
                         FROM tarifas t
                         INNER JOIN proveedores p ON t.id_proveedor = p.id
                         INNER JOIN ciudades c ON p.id_ciudad = c.id
                         INNER JOIN examenes e ON t.id_examen = e.id
                         WHERE t.anio = ? 
                         AND p.id_ciudad IN ($comodines_ciudades) 
                         AND t.id_examen IN ($comodines_examenes) 
                         AND p.estado = 1 
                         AND e.estado = 1
                         ORDER BY c.nombre ASC, p.nombre_ips ASC, e.nombre ASC";

            $sentencia = $this->conexion->prepare($consulta);
            
            // Juntamos todos los parametros en un solo arreglo (Año + Ciudades + Examenes)
            $parametros = array_merge([$anio], $ciudades, $examenes);
            
            $sentencia->execute($parametros);
            return $sentencia->fetchAll();

        } catch (PDOException $error) {
            return []; // Si hay error, retorna arreglo vacio
        }
    }

    public function guardarCotizacionCompleta($cliente_nombre, $cliente_nit, $detalles) {
        try {
            // Iniciamos una transaccion. Si algo falla en el ciclo, deshace todo.
            $this->conexion->beginTransaction();

            // 1. Calculamos un total sumando todos los precios de venta de la matriz
            $total_cotizacion = 0;
            foreach ($detalles as $item) {
                $total_cotizacion += $item['precio_venta'];
            }

            // 2. Guardamos la cabecera
            $consulta_cabecera = "INSERT INTO cotizaciones (cliente_nombre, cliente_nit, total) VALUES (:nombre, :nit, :total)";
            $sentencia_cabecera = $this->conexion->prepare($consulta_cabecera);
            $sentencia_cabecera->bindParam(':nombre', $cliente_nombre, PDO::PARAM_STR);
            $sentencia_cabecera->bindParam(':nit', $cliente_nit, PDO::PARAM_STR);
            $sentencia_cabecera->bindParam(':total', $total_cotizacion, PDO::PARAM_STR);
            $sentencia_cabecera->execute();

            // Capturamos el ID autoincrementable que acaba de generar MySQL
            $id_cotizacion = $this->conexion->lastInsertId();

            // 3. Guardamos los detalles (los examenes y proveedores)
            $consulta_detalle = "INSERT INTO cotizaciones_detalle (id_cotizacion, id_proveedor, id_examen, precio_costo, precio_venta) 
                                 VALUES (:id_cotizacion, :id_proveedor, :id_examen, :precio_costo, :precio_venta)";
            $sentencia_detalle = $this->conexion->prepare($consulta_detalle);

            foreach ($detalles as $det) {
                $sentencia_detalle->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
                $sentencia_detalle->bindParam(':id_proveedor', $det['id_proveedor'], PDO::PARAM_INT);
                $sentencia_detalle->bindParam(':id_examen', $det['id_examen'], PDO::PARAM_INT);
                $sentencia_detalle->bindParam(':precio_costo', $det['precio_costo'], PDO::PARAM_STR); // NUEVO
                $sentencia_detalle->bindParam(':precio_venta', $det['precio_venta'], PDO::PARAM_STR);
                $sentencia_detalle->execute();
            }

            // Confirmamos y guardamos todo permanentemente
            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            // Si hubo algun error, echamos todo para atras
            $this->conexion->rollBack();
            return false;
        }
    }

    // Obtener todo el historial de cotizaciones
    public function obtenerTodasLasCotizaciones() {
        try {
            $consulta = "SELECT * FROM cotizaciones ORDER BY fecha DESC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return [];
        }
    }

    // Eliminar una cotizacion
    public function eliminarCotizacion($id) {
        try {
            $consulta = "DELETE FROM cotizaciones WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Obtener los datos principales de una cotizacion especifica
    public function obtenerCotizacionPorId($id) {
        try {
            $consulta = "SELECT * FROM cotizaciones WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Obtener la lista exacta de examenes y precios de esa cotizacion
    public function obtenerDetallesDeCotizacion($id_cotizacion) {
        try {
            $consulta = "SELECT 
                            c.nombre AS ciudad, 
                            p.nombre_ips AS proveedor, 
                            e.nombre AS examen,
                            cd.precio_costo, 
                            cd.precio_venta
                         FROM cotizaciones_detalle cd
                         INNER JOIN proveedores p ON cd.id_proveedor = p.id
                         INNER JOIN ciudades c ON p.id_ciudad = c.id
                         INNER JOIN examenes e ON cd.id_examen = e.id
                         WHERE cd.id_cotizacion = :id_cotizacion
                         ORDER BY c.nombre ASC, p.nombre_ips ASC, e.nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return [];
        }
    }

}
?>