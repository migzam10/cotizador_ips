<?php
// vistas/cotizador.php
require_once 'plantillas/encabezado.php';

// Modelos necesarios para armar el formulario
require_once '../configuracion/Conexion.php'; // Para consultas directas simples aqui
$conexion = (new Conexion())->conectar();

// Traemos ciudades y examenes activos para los filtros
$ciudades = $conexion->query("SELECT * FROM ciudades ORDER BY nombre ASC")->fetchAll();
$examenes_lista = $conexion->query("SELECT * FROM examenes WHERE estado = 1 ORDER BY nombre ASC")->fetchAll();

// Variables para guardar los resultados
$resultados = [];
$anio_busqueda = date('Y');
$ciudades_seleccionadas = [];
$examenes_seleccionados = [];
$nombres_examenes_seleccionados = [];

// Procesamos el formulario si se envio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generar'])) {
    require_once '../modelos/ModeloCotizador.php';
    
    $anio_busqueda = $_POST['anio'];
    $ciudades_seleccionadas = $_POST['ciudades'] ?? [];
    $examenes_seleccionados = $_POST['examenes'] ?? [];

    if (!empty($ciudades_seleccionadas) && !empty($examenes_seleccionados)) {
        $modelo_cotizador = new ModeloCotizador();
        $datos_planos = $modelo_cotizador->obtenerMatrizCotizacion($anio_busqueda, $ciudades_seleccionadas, $examenes_seleccionados);

        // EXTRAER NOMBRES DE EXAMENES: Para imprimir las filas correctamente
        foreach ($examenes_lista as $ex) {
            if (in_array($ex['id'], $examenes_seleccionados)) {
                $nombres_examenes_seleccionados[] = $ex['nombre'];
            }
        }

        // LOGICA DE AGRUPACION: Transformar los datos planos a la matriz
        foreach ($datos_planos as $fila) {
            $ciudad = $fila['ciudad'];
            $proveedor = $fila['proveedor'];
            $examen = $fila['examen'];
            
            // Agrupamos por Ciudad -> Proveedor -> Examen
            $resultados[$ciudad][$proveedor][$examen] = [
                'costo' => $fila['precio_costo'],
                'venta' => $fila['precio_venta']
            ];
        }
    }
}
?>

<h1 class="h3 mb-4 text-gray-800">Generador de Cotizaciones</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Parametros de Busqueda</h6>
    </div>
    <div class="card-body">
        <form action="cotizador.php" method="POST">
            <div class="row">
                <div class="col-md-2 form-group">
                    <label class="font-weight-bold">Año de Tarifa</label>
                    <input type="number" class="form-control" name="anio" value="<?php echo $anio_busqueda; ?>" required>
                </div>

                <div class="col-md-4 form-group">
                    <label class="font-weight-bold">Ciudades (Seleccione una o varias)</label>
                    <div class="border rounded p-2" style="height: 150px; overflow-y: scroll; background-color: #f8f9fc;">
                        <?php foreach($ciudades as $ciu): ?>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="ciu_<?php echo $ciu['id']; ?>" name="ciudades[]" value="<?php echo $ciu['id']; ?>" <?php echo in_array($ciu['id'], $ciudades_seleccionadas) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="ciu_<?php echo $ciu['id']; ?>"><?php echo $ciu['nombre']; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="font-weight-bold">Examenes Requeridos</label>
                    <div class="border rounded p-2" style="height: 150px; overflow-y: scroll; background-color: #f8f9fc;">
                        <div class="row">
                            <?php foreach($examenes_lista as $ex): ?>
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="ex_<?php echo $ex['id']; ?>" name="examenes[]" value="<?php echo $ex['id']; ?>" <?php echo in_array($ex['id'], $examenes_seleccionados) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="ex_<?php echo $ex['id']; ?>"><?php echo $ex['nombre']; ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <button type="submit" name="generar" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50"><i class="fas fa-search"></i></span>
                <span class="text">Generar Matriz</span>
            </button>
            <a href="cotizador.php" class="btn btn-secondary ml-2">Limpiar</a>
        </form>
    </div>
</div>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generar'])): ?>
    
    <?php if (empty($resultados)): ?>
        <div class="alert alert-warning">No se encontraron tarifas para los parametros seleccionados. Revise si hay proveedores y tarifas asignadas para el año <?php echo $anio_busqueda; ?>.</div>
    <?php else: ?>

        <?php foreach ($resultados as $ciudad => $proveedores): ?>
            <div class="card shadow mb-5 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success text-uppercase">CIUDAD: <?php echo $ciudad; ?> - AÑO <?php echo $anio_busqueda; ?></h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.9rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th class="align-middle text-left" rowspan="2" style="width: 20%;">EXAMENES</th>
                                    <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                                        <th colspan="2" class="bg-primary text-white"><?php echo strtoupper($nombre_prov); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                                        <th>Costo</th>
                                        <th>Venta</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Arrays para ir sumando los totales por proveedor
                                $totales_costo = [];
                                $totales_venta = [];
                                
                                // Recorremos los examenes seleccionados para armar las filas
                                foreach ($nombres_examenes_seleccionados as $nombre_ex): 
                                ?>
                                    <tr>
                                        <td class="text-left font-weight-bold"><?php echo $nombre_ex; ?></td>
                                        
                                        <?php 
                                        // Recorremos los proveedores para ver si tienen precio de ese examen
                                        foreach ($proveedores as $nombre_prov => $examenes_del_prov): 
                                            
                                            // Si el proveedor tiene tarifa para este examen, la mostramos y sumamos
                                            if (isset($examenes_del_prov[$nombre_ex])) {
                                                $costo = $examenes_del_prov[$nombre_ex]['costo'];
                                                $venta = $examenes_del_prov[$nombre_ex]['venta'];
                                                
                                                // Inicializamos sumadores si no existen
                                                if(!isset($totales_costo[$nombre_prov])) $totales_costo[$nombre_prov] = 0;
                                                if(!isset($totales_venta[$nombre_prov])) $totales_venta[$nombre_prov] = 0;

                                                $totales_costo[$nombre_prov] += $costo;
                                                $totales_venta[$nombre_prov] += $venta;

                                                echo "<td>$ " . number_format($costo, 0, ',', '.') . "</td>";
                                                echo "<td>$ " . number_format($venta, 0, ',', '.') . "</td>";
                                            } else {
                                                // Si el proveedor NO tiene tarifa para ese examen, mostramos raya
                                                echo "<td class='text-muted'>-</td>";
                                                echo "<td class='text-muted'>-</td>";
                                            }
                                        endforeach; 
                                        ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="font-weight-bold bg-light">
                                <tr>
                                    <td class="text-left">TOTALES</td>
                                    <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                                        <td class="text-danger">$ <?php echo isset($totales_costo[$nombre_prov]) ? number_format($totales_costo[$nombre_prov], 0, ',', '.') : '0'; ?></td>
                                        <td class="text-success">$ <?php echo isset($totales_venta[$nombre_prov]) ? number_format($totales_venta[$nombre_prov], 0, ',', '.') : '0'; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-right mb-4">
            <button class="btn btn-success btn-lg shadow" data-toggle="modal" data-target="#modalGuardarCotizacion">
                <i class="fas fa-save"></i> Guardar Cotizacion
            </button>
        </div>

    <?php endif; ?>
<?php endif; ?>

<?php require_once 'plantillas/pie_pagina.php'; ?>