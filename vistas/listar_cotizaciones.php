<?php
// vistas/listar_cotizaciones.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCotizador.php';

$modelo = new ModeloCotizador();
$cotizaciones = $modelo->obtenerTodasLasCotizaciones();
?>

<h1 class="h3 mb-4 text-gray-800">Historial de Cotizaciones</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Cotizacion eliminada permanentemente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">Error al intentar eliminar la cotizacion.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Registros Guardados</h6>
        <a href="cotizador.php" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nueva Cotizacion</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tablaCotizaciones" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente / Empresa</th>
                        <th>NIT</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cotizaciones as $cot): ?>
                    <tr>
                        <td># <?php echo $cot['id']; ?></td>
                        <td><?php echo date('d/m/Y h:i A', strtotime($cot['fecha'])); ?></td>
                        <td class="font-weight-bold"><?php echo empty($cot['cliente_nombre']) ? 'Sin Nombre' : strtoupper($cot['cliente_nombre']); ?></td>
                        <td><?php echo empty($cot['cliente_nit']) ? 'N/A' : $cot['cliente_nit']; ?></td>
                        <td>
                            <a href="ver_cotizacion.php?id=<?php echo $cot['id']; ?>" class="btn btn-info btn-sm" title="Ver Detalle completo">
                                <i class="fas fa-eye"></i>
                            </a>

                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                                <a href="../controladores/ControladorCotizacion.php?accion=eliminar&id=<?php echo $cot['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Esta completamente seguro de eliminar esta cotizacion? Se borraran todos sus detalles.');" title="Eliminar Registro">
                                    <i class="fas fa-trash"></i>
                                </a>
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

<script>
$(document).ready(function() {
    $('#tablaCotizaciones').DataTable({
        "order": [[ 0, "desc" ]], // Ordenar por ID descendente (las mas nuevas primero)
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" }
    });
});
</script>