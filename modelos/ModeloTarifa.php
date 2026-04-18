<?php
// modelos/ModeloTarifa.php

require_once '../configuracion/Conexion.php';

class ModeloTarifa {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->conectar();
    }

    // Listar todas las tarifas cruzando datos con proveedores y examenes
    public function obtenerTodasLasTarifas() {
        try {
            $consulta = "SELECT t.*, p.nombre_ips, e.nombre AS nombre_examen, c.nombre AS nombre_ciudad
                         FROM tarifas t 
                         INNER JOIN proveedores p ON t.id_proveedor = p.id 
                         INNER JOIN examenes e ON t.id_examen = e.id 
                         INNER JOIN ciudades c ON p.id_ciudad = c.id

                         ORDER BY t.anio DESC, p.nombre_ips ASC, e.nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function obtenerTarifaPorId($id) {
        try {
            $consulta = "SELECT * FROM tarifas WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetch();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function guardarTarifa($id_proveedor, $id_examen, $anio, $precio_costo, $precio_venta) {
        try {
            $consulta = "INSERT INTO tarifas (id_proveedor, id_examen, anio, precio_costo, precio_venta) 
                         VALUES (:id_proveedor, :id_examen, :anio, :precio_costo, :precio_venta)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
            $sentencia->bindParam(':id_examen', $id_examen, PDO::PARAM_INT);
            $sentencia->bindParam(':anio', $anio, PDO::PARAM_INT);
            $sentencia->bindParam(':precio_costo', $precio_costo, PDO::PARAM_STR);
            $sentencia->bindParam(':precio_venta', $precio_venta, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    public function actualizarTarifa($id, $precio_costo, $precio_venta) {
        try {
            $consulta = "UPDATE tarifas SET precio_costo = :precio_costo, precio_venta = :precio_venta WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':precio_costo', $precio_costo, PDO::PARAM_STR);
            $sentencia->bindParam(':precio_venta', $precio_venta, PDO::PARAM_STR);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    // LOGICA ESTRELLA: Clonar y aumentar por porcentaje
    public function clonarTarifasPorcentaje($id_proveedor, $anio_base, $anio_nuevo, $porcentaje) {
        try {
            // 1. Buscamos todas las tarifas del proveedor en el año base
            $consulta_base = "SELECT * FROM tarifas WHERE id_proveedor = :id_proveedor AND anio = :anio_base";
            $sentencia_base = $this->conexion->prepare($consulta_base);
            $sentencia_base->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
            $sentencia_base->bindParam(':anio_base', $anio_base, PDO::PARAM_INT);
            $sentencia_base->execute();
            $tarifas_viejas = $sentencia_base->fetchAll();

            if (count($tarifas_viejas) == 0) {
                return "vacio"; // No hay tarifas para clonar
            }

            // 2. Calculamos el factor de multiplicacion (Ej: 6% = 1.06)
            $factor = 1 + ($porcentaje / 100);

            // 3. Preparamos el insert para el nuevo año
            $consulta_insert = "INSERT INTO tarifas (id_proveedor, id_examen, anio, precio_costo, precio_venta) 
                                VALUES (:id_proveedor, :id_examen, :anio_nuevo, :precio_costo_nuevo, :precio_venta_nuevo)";
            $sentencia_insert = $this->conexion->prepare($consulta_insert);

            // 4. Recorremos e insertamos
            foreach ($tarifas_viejas as $tarifa) {
                $costo_nuevo = round($tarifa['precio_costo'] * $factor, 2);
                $venta_nuevo = round($tarifa['precio_venta'] * $factor, 2);

                $sentencia_insert->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
                $sentencia_insert->bindParam(':id_examen', $tarifa['id_examen'], PDO::PARAM_INT);
                $sentencia_insert->bindParam(':anio_nuevo', $anio_nuevo, PDO::PARAM_INT);
                $sentencia_insert->bindParam(':precio_costo_nuevo', $costo_nuevo, PDO::PARAM_STR);
                $sentencia_insert->bindParam(':precio_venta_nuevo', $venta_nuevo, PDO::PARAM_STR);
                $sentencia_insert->execute();
            }

            return true;

        } catch (PDOException $error) {
            return false;
        }
    }// Obtener las tarifas de un proveedor y anio especifico
    public function obtenerTarifasPorProveedorYAno($id_proveedor, $anio) {
        try {
            $consulta = "SELECT t.*, p.nombre_ips, e.nombre AS nombre_examen 
                         FROM tarifas t 
                         INNER JOIN proveedores p ON t.id_proveedor = p.id 
                         INNER JOIN examenes e ON t.id_examen = e.id 
                         WHERE t.id_proveedor = :id_proveedor AND t.anio = :anio
                         ORDER BY e.nombre ASC";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
            $sentencia->bindParam(':anio', $anio, PDO::PARAM_INT);
            $sentencia->execute();
            return $sentencia->fetchAll();
        } catch (PDOException $error) {
            return [];
        }
    }

    // Verificar si ya existen tarifas para un proveedor en un anio (Para evitar sobreescribir al clonar)
    public function verificarTarifasExistentes($id_proveedor, $anio) {
        try {
            $consulta = "SELECT COUNT(*) as total FROM tarifas WHERE id_proveedor = :id_proveedor AND anio = :anio";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
            $sentencia->bindParam(':anio', $anio, PDO::PARAM_INT);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
            return $resultado['total'] > 0;
        } catch (PDOException $error) {
            return true; // Por seguridad, si hay error decimos que si existen
        }
    }

    // Eliminar una tarifa especifica
    public function eliminarTarifa($id) {
        try {
            $consulta = "DELETE FROM tarifas WHERE id = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return false;
        }
    }

    // Verificar si un proveedor ya tiene un precio asignado para un examen en un anio especifico
    public function verificarTarifaDuplicada($id_proveedor, $id_examen, $anio) {
        try {
            $consulta = "SELECT COUNT(*) as total FROM tarifas WHERE id_proveedor = :id_proveedor AND id_examen = :id_examen AND anio = :anio";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
            $sentencia->bindParam(':id_examen', $id_examen, PDO::PARAM_INT);
            $sentencia->bindParam(':anio', $anio, PDO::PARAM_INT);
            $sentencia->execute();
            
            $resultado = $sentencia->fetch();
            
            // Si el total es mayor a 0, significa que la tarifa ya existe
            return $resultado['total'] > 0;
        } catch (PDOException $error) {
            // Por seguridad, si hay un error en la base de datos, decimos que si existe para bloquear el guardado
            return true; 
        }
    }
    // Funcion inteligente: Si la tarifa existe la actualiza, si no, la crea.
    public function guardarOActualizarTarifa($id_proveedor, $id_examen, $anio, $precio_costo, $precio_venta) {
        try {
            // 1. Verificamos si ya existe
            $consulta_check = "SELECT id FROM tarifas WHERE id_proveedor = :prov AND id_examen = :exam AND anio = :anio";
            $stmt = $this->conexion->prepare($consulta_check);
            $stmt->execute([':prov' => $id_proveedor, ':exam' => $id_examen, ':anio' => $anio]);
            $existe = $stmt->fetch();

            if ($existe) {
                // UPDATE
                $consulta = "UPDATE tarifas SET precio_costo = :costo, precio_venta = :venta WHERE id = :id";
                $stmt_up = $this->conexion->prepare($consulta);
                return $stmt_up->execute([':costo' => $precio_costo, ':venta' => $precio_venta, ':id' => $existe['id']]);
            } else {
                // INSERT
                $consulta = "INSERT INTO tarifas (id_proveedor, id_examen, anio, precio_costo, precio_venta) VALUES (:prov, :exam, :anio, :costo, :venta)";
                $stmt_in = $this->conexion->prepare($consulta);
                return $stmt_in->execute([':prov' => $id_proveedor, ':exam' => $id_examen, ':anio' => $anio, ':costo' => $precio_costo, ':venta' => $precio_venta]);
            }
        } catch (PDOException $error) {
            return false;
        }
    }

    // Funcion para eliminar directo por parametros
    public function eliminarTarifaParametros($id_proveedor, $id_examen, $anio) {
        try {
            $consulta = "DELETE FROM tarifas WHERE id_proveedor = :prov AND id_examen = :exam AND anio = :anio";
            $stmt = $this->conexion->prepare($consulta);
            return $stmt->execute([':prov' => $id_proveedor, ':exam' => $id_examen, ':anio' => $anio]);
        } catch (PDOException $error) {
            return false;
        }
    }

    // Clonar tarifas de un proveedor hacia otro diferente
    public function clonarTarifasAProveedor($id_prov_origen, $anio_origen, $id_prov_destino, $anio_destino, $porcentaje) {
        try {
            // 1. Obtenemos las tarifas que tiene el proveedor origen
            $tarifas_origen = $this->obtenerTarifasPorProveedorYAno($id_prov_origen, $anio_origen);
            
            if (empty($tarifas_origen)) {
                return "vacio";
            }

            // Iniciamos transaccion
            $this->conexion->beginTransaction();

            $consulta = "INSERT INTO tarifas (id_proveedor, id_examen, anio, precio_costo, precio_venta) 
                         VALUES (:prov_dest, :exam, :anio_dest, :costo, :venta)";
            $stmt = $this->conexion->prepare($consulta);

            foreach ($tarifas_origen as $tar) {
                $costo_base = $tar['precio_costo'];
                $venta_base = $tar['precio_venta'];

                $nuevo_costo = $costo_base;
                $nueva_venta = $venta_base;

                // Si le mandaron un porcentaje, lo calculamos
                if (!empty($porcentaje) && $porcentaje != 0) {
                    $nuevo_costo = $costo_base + ($costo_base * ($porcentaje / 100));
                    $nueva_venta = $venta_base + ($venta_base * ($porcentaje / 100));
                }

                $stmt->execute([
                    ':prov_dest' => $id_prov_destino,
                    ':exam' => $tar['id_examen'],
                    ':anio_dest' => $anio_destino,
                    ':costo' => $nuevo_costo,
                    ':venta' => $nueva_venta
                ]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            $this->conexion->rollBack();
            return false;
        }
    }
}
?>
