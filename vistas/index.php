<?php
// vistas/index.php
require_once 'plantillas/encabezado.php';
require_once '../configuracion/Conexion.php';

// Conectamos a la base de datos para traer los totales del dashboard
$conexion = (new Conexion())->conectar();

// 1. Traer conteos rapidos para las tarjetas
$total_cotizaciones = $conexion->query("SELECT COUNT(*) FROM cotizaciones")->fetchColumn();
$total_proveedores = $conexion->query("SELECT COUNT(*) FROM proveedores")->fetchColumn();
$total_examenes = $conexion->query("SELECT COUNT(*) FROM examenes")->fetchColumn();
$total_ciudades = $conexion->query("SELECT COUNT(*) FROM ciudades")->fetchColumn();

// 2. Traer las ultimas 5 cotizaciones para el resumen
$cotizaciones_recientes = $conexion->query("SELECT id, cliente_nombre, total, fecha FROM cotizaciones ORDER BY fecha DESC LIMIT 5")->fetchAll();
?>


<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cotizaciones Creadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_cotizaciones; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calculator fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">IPS / Proveedores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_proveedores; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-hospital fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Examenes Registrados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_examenes; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-stethoscope fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ciudades Cobertura</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_ciudades; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-map-marker-alt fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Atajos Rapidos</h6>
            </div>
            <div class="card-body text-center">
                
                <a href="cotizador.php" class="btn btn-primary btn-icon-split btn-lg mb-3 w-75 shadow-sm">
                    <span class="icon text-white-50"><i class="fas fa-calculator"></i></span>
                    <span class="text w-100">Generar Cotizacion</span>
                </a><br>

                <?php if($_SESSION['rol'] != 'visualizador'): ?>
                    <a href="tarifas_proveedor.php" class="btn btn-success btn-icon-split btn-lg mb-3 w-75 shadow-sm">
                        <span class="icon text-white-50"><i class="fas fa-dollar-sign"></i></span>
                        <span class="text w-100">Gestionar Tarifas</span>
                    </a><br>

                    <a href="crear_proveedor.php" class="btn btn-info btn-icon-split btn-lg mb-3 w-75 shadow-sm">
                        <span class="icon text-white-50"><i class="fas fa-hospital-user"></i></span>
                        <span class="text w-100">Nuevo Proveedor</span>
                    </a><br>
                <?php endif; ?>

                <a href="crear_examen.php" class="btn btn-secondary btn-icon-split btn-lg w-75 shadow-sm">
                    <span class="icon text-white-50"><i class="fas fa-stethoscope"></i></span>
                    <span class="text w-100">Nuevo examen</span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">Ultimas Cotizaciones Guardadas</h6>
                <a href="listar_cotizaciones.php" class="btn btn-sm btn-outline-success">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if(count($cotizaciones_recientes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <!--th>Total</th-->
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cotizaciones_recientes as $cot): ?>
                                <tr>
                                    <td class="align-middle"><small><?php echo date('d/m/Y', strtotime($cot['fecha'])); ?></small></td>
                                    <td class="align-middle font-weight-bold"><?php echo empty($cot['cliente_nombre']) ? 'Sin Nombre' : strtoupper($cot['cliente_nombre']); ?></td>
                                    <!--td class="align-middle text-success font-weight-bold">$ <?php echo number_format($cot['total'], 0, ',', '.'); ?></td-->
                                    <td class="align-middle text-right">
                                        <a href="ver_cotizacion.php?id=<?php echo $cot['id']; ?>" class="btn btn-sm btn-info btn-circle" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>No hay cotizaciones registradas todavia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>