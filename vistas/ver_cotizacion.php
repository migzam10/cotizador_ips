<?php
// vistas/ver_cotizacion.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCotizador.php';

if (!isset($_GET['id'])) {
    exit('<div class="alert alert-danger mt-4">Error: ID requerido.</div>');
}

$id_cotizacion = $_GET['id'];
$modelo = new ModeloCotizador();

$cabecera = $modelo->obtenerCotizacionPorId($id_cotizacion);
$detalles_planos = $modelo->obtenerDetallesDeCotizacion($id_cotizacion);

if (!$cabecera) {
    exit('<div class="alert alert-danger mt-4">La cotizacion no existe.</div>');
}

// Transformar datos planos a la matriz
$resultados = [];
$examenes_por_ciudad = [];

foreach ($detalles_planos as $fila) {
    $ciudad = $fila['ciudad'];
    $proveedor = $fila['proveedor'];
    $examen = $fila['examen'];
    
    if (!isset($examenes_por_ciudad[$ciudad])) {
        $examenes_por_ciudad[$ciudad] = [];
    }
    if (!in_array($examen, $examenes_por_ciudad[$ciudad])) {
        $examenes_por_ciudad[$ciudad][] = $examen;
    }
    
    $resultados[$ciudad][$proveedor][$examen] = [
        'costo' => $fila['precio_costo'],
        'venta' => $fila['precio_venta']
    ];
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detalle de Cotizacion # <?php echo $cabecera['id']; ?></h1>
    <div>
        <button onclick="exportarExcelNativo()" class="btn btn-sm btn-success shadow-sm mr-2">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Exportar a Excel
        </button>
        <a href="listar_cotizaciones.php" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Datos del Cliente</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php echo empty($cabecera['cliente_nombre']) ? 'Sin Nombre' : strtoupper($cabecera['cliente_nombre']); ?>
                </div>
                <div class="mt-2 text-gray-600">
                    <strong>NIT:</strong> <?php echo empty($cabecera['cliente_nit']) ? 'N/A' : $cabecera['cliente_nit']; ?><br>
                    <strong>Fecha:</strong> <?php echo date('d/m/Y h:i A', strtotime($cabecera['fecha'])); ?>
                </div>
            </div>
        </div>
    </div>
    <!--div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Cotizado (Venta)</div>
                <div class="h2 mb-0 font-weight-bold text-gray-800">
                    $ <?php echo number_format($cabecera['total'], 0, ',', '.'); ?>
                </div>
            </div>
        </div>
    </div-->
</div>

<div id="areaVisual">
    <?php foreach ($resultados as $ciudad => $proveedores): ?>
        <div class="card shadow mb-5 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success text-uppercase">CIUDAD: <?php echo $ciudad; ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.9rem;">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-left" style="width: 20%;">EXAMENES</th>
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
                            $totales_costo = [];
                            $totales_venta = [];
                            foreach ($examenes_por_ciudad[$ciudad] as $nombre_ex): 
                            ?>
                                <tr>
                                    <td class="text-left font-weight-bold"><?php echo $nombre_ex; ?></td>
                                    <?php foreach ($proveedores as $nombre_prov => $examenes_del_prov): 
                                        if (isset($examenes_del_prov[$nombre_ex])) {
                                            $costo = $examenes_del_prov[$nombre_ex]['costo'];
                                            $venta = $examenes_del_prov[$nombre_ex]['venta'];
                                            
                                            if(!isset($totales_costo[$nombre_prov])) $totales_costo[$nombre_prov] = 0;
                                            if(!isset($totales_venta[$nombre_prov])) $totales_venta[$nombre_prov] = 0;

                                            $totales_costo[$nombre_prov] += $costo;
                                            $totales_venta[$nombre_prov] += $venta;

                                            echo "<td>$ " . number_format($costo, 0, ',', '.') . "</td>";
                                            echo "<td>$ " . number_format($venta, 0, ',', '.') . "</td>";
                                        } else {
                                            echo "<td class='text-muted'>-</td><td class='text-muted'>-</td>";
                                        }
                                    endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-left font-weight-bold">TOTALES</td>
                                <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                                    <td class="text-danger font-weight-bold">$ <?php echo isset($totales_costo[$nombre_prov]) ? number_format($totales_costo[$nombre_prov], 0, ',', '.') : '0'; ?></td>
                                    <td class="text-success font-weight-bold">$ <?php echo isset($totales_venta[$nombre_prov]) ? number_format($totales_venta[$nombre_prov], 0, ',', '.') : '0'; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<table id="tablaOcultaExcel" style="display: none;">
    <tr>
        <th data-f-sz="14" data-f-bold="true" colspan="4">Cotizacion # <?php echo $cabecera['id']; ?> - <?php echo empty($cabecera['cliente_nombre']) ? 'Cliente General' : strtoupper($cabecera['cliente_nombre']); ?></th>
    </tr>
    <tr></tr> <?php foreach ($resultados as $ciudad => $proveedores): 
        // Calculamos cuantas columnas ocupa esta ciudad (1 de examen + 2 por cada proveedor)
        $columnas_totales = 1 + (count($proveedores) * 2);
    ?>
        <tr>
            <th colspan="<?php echo $columnas_totales; ?>" data-fill-color="1cc88a" data-f-color="ffffff" data-f-bold="true" data-a-h="left">CIUDAD: <?php echo $ciudad; ?></th>
        </tr>
        
        <tr>
            <th data-fill-color="f8f9fc" data-f-bold="true" data-a-v="middle">EXAMENES</th>
            <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                <th colspan="2" data-fill-color="4e73df" data-f-color="ffffff" data-f-bold="true" data-a-h="center" data-a-v="middle"><?php echo strtoupper($nombre_prov); ?></th>
            <?php endforeach; ?>
        </tr>
        
        <tr>
            <th data-fill-color="f8f9fc"></th>
            <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                <th data-fill-color="eaecf4" data-f-bold="true" data-a-h="center">Costo</th>
                <th data-fill-color="eaecf4" data-f-bold="true" data-a-h="center">Venta</th>
            <?php endforeach; ?>
        </tr>

        <?php 
        $totales_costo = [];
        $totales_venta = [];
        foreach ($examenes_por_ciudad[$ciudad] as $nombre_ex): ?>
            <tr>
                <td data-f-bold="true"><?php echo $nombre_ex; ?></td>
                <?php foreach ($proveedores as $nombre_prov => $examenes_del_prov): 
                    if (isset($examenes_del_prov[$nombre_ex])) {
                        $costo = $examenes_del_prov[$nombre_ex]['costo'];
                        $venta = $examenes_del_prov[$nombre_ex]['venta'];
                        
                        if(!isset($totales_costo[$nombre_prov])) $totales_costo[$nombre_prov] = 0;
                        if(!isset($totales_venta[$nombre_prov])) $totales_venta[$nombre_prov] = 0;
                        $totales_costo[$nombre_prov] += $costo;
                        $totales_venta[$nombre_prov] += $venta;

                        echo "<td data-a-h=\"center\">$ " . number_format($costo, 0, ',', '.') . "</td>";
                        echo "<td data-a-h=\"center\">$ " . number_format($venta, 0, ',', '.') . "</td>";
                    } else {
                        echo "<td data-a-h=\"center\">-</td><td data-a-h=\"center\">-</td>";
                    }
                endforeach; ?>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td data-f-bold="true" data-fill-color="f8f9fc">TOTALES</td>
            <?php foreach ($proveedores as $nombre_prov => $datos_prov): ?>
                <td data-f-color="e74a3b" data-f-bold="true" data-a-h="center">$ <?php echo isset($totales_costo[$nombre_prov]) ? number_format($totales_costo[$nombre_prov], 0, ',', '.') : '0'; ?></td>
                <td data-f-color="1cc88a" data-f-bold="true" data-a-h="center">$ <?php echo isset($totales_venta[$nombre_prov]) ? number_format($totales_venta[$nombre_prov], 0, ',', '.') : '0'; ?></td>
            <?php endforeach; ?>
        </tr>
        <tr></tr> <?php endforeach; ?>
</table>

<?php require_once 'plantillas/pie_pagina.php'; ?>

<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@1.0.4/dist/tableToExcel.js"></script>

<script>
function exportarExcelNativo() {
    // Tomamos la tabla oculta que ya tiene las etiquetas de colores especiales
    let tabla = document.getElementById("tablaOcultaExcel");
    
    // Le pedimos a la libreria que arme el archivo nativo .xlsx
    TableToExcel.convert(tabla, {
        name: "Cotizacion_<?php echo $id_cotizacion; ?>.xlsx",
        sheet: {
            name: "Matriz de Precios"
        }
    });
}
</script>