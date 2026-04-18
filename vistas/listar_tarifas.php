<?php
// vistas/listar_tarifas.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloTarifa.php';

$modelo = new ModeloTarifa();
$tarifas = $modelo->obtenerTodasLasTarifas();
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Tarifas</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Tarifa registrada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Tarifa actualizada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'clonado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Tarifas clonadas y aumentadas con exito.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'duplicado'): ?>
        <div class="alert alert-warning alert-dismissible fade show">Error: Esta tarifa ya existe.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
         <?php elseif($_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Tarifa eliminada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?> <?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Precios</h6>
        <div>
            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                <a href="clonar_tarifas.php" class="btn btn-sm btn-info shadow-sm mr-2"><i class="fas fa-copy fa-sm text-white-50"></i> Clonar Año</a>
                <a href="crear_tarifa.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nueva Tarifa</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display stripe mi-datatable" id="tablaTarifas" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Proveedor IPS</th>
                        <th>Examen</th>
                        <th>Ciudad</th>
                        <th>Costo</th>
                        <th>Venta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tarifas as $tar): ?>
                    <tr>
                        <td><?php echo $tar['anio']; ?></td>
                        <td><?php echo $tar['nombre_ips']; ?></td>
                        <td><?php echo $tar['nombre_examen']; ?></td>
                        <td><?php echo $tar['nombre_ciudad']; ?></td>
                        <td>$ <?php echo number_format($tar['precio_costo'], 2); ?></td>
                        <td>$ <?php echo number_format($tar['precio_venta'], 2); ?></td>
                        <td>
                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                                <a href="editar_tarifa.php?id=<?php echo $tar['id']; ?>" class="btn btn-warning btn-sm" title="Editar Precio">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../controladores/ControladorTarifa.php?accion=eliminar&id=<?php echo $tar['id']; ?>" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('¿Seguro que deseas eliminar esta tarifa permanentemente?');" title="Eliminar">
    <i class="fas fa-trash"></i>
</a>
                                    
                            <?php else: ?>
                                <span class="text-muted small">Solo vista</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>