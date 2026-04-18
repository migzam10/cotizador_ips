<?php
// vistas/tarifas_proveedor.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloProveedor.php';
require_once '../modelos/ModeloTarifa.php';

$modelo_prov = new ModeloProveedor();
$proveedores = $modelo_prov->obtenerTodosLosProveedores();

$tarifas = [];
$id_busqueda = '';
$anio_busqueda = date('Y');
$nombre_proveedor_actual = '';

// Si el usuario envio el filtro
if (isset($_GET['id_proveedor']) && isset($_GET['anio'])) {
    $id_busqueda = $_GET['id_proveedor'];
    $anio_busqueda = $_GET['anio'];
    
    $modelo_tarifa = new ModeloTarifa();
    $tarifas = $modelo_tarifa->obtenerTarifasPorProveedorYAno($id_busqueda, $anio_busqueda);

    // Obtener el nombre del proveedor para el titulo
    foreach($proveedores as $p) {
        if($p['id'] == $id_busqueda) {
            $nombre_proveedor_actual = $p['nombre_ips'];
            break;
        }
    }
}
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Tarifas por Proveedor</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Tarifa eliminada con exito.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'clonado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Tarifas clonadas con exito. Ahora esta viendo el nuevo ano.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'error_existe'): ?>
        <div class="alert alert-danger alert-dismissible fade show"><strong>Error:</strong> El proveedor ya tiene tarifas asignadas para el año destino. No se puede clonar para evitar duplicados.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'vacio'): ?>
        <div class="alert alert-warning alert-dismissible fade show">No hay tarifas en el año base para clonar.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtrar Informacion</h6>
    </div>
    <div class="card-body">
        <form action="tarifas_prov.php" method="GET">
    <div class="row align-items-end">
        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Proveedor:</label>
            <select class="form-control select-buscador js-example-basic-single" name="id_proveedor" required>
                <option value="">Seleccione...</option>
                <?php foreach($proveedores as $prov): ?>
                    <option value="<?php echo $prov['id']; ?>" <?php if($id_busqueda == $prov['id']) echo 'selected'; ?>>
                        <?php echo $prov['nombre_ips'] . ' - ' . $prov['nombre_ciudad']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Año:</label>
            <div class="input-group">
                <input type="number" class="form-control" name="anio" value="<?php echo $anio_busqueda; ?>" required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar Tarifas
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
    </div>
</div>

<?php if($id_busqueda != ''): ?>
<div class="card shadow mb-4 border-left-info">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-info">
            Tarifas de <?php echo $nombre_proveedor_actual; ?> - Año <?php echo $anio_busqueda; ?>
        </h6>
        
        <?php if($_SESSION['rol'] != 'visualizador'): ?>
            <div>
                <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalClonar">
                    <i class="fas fa-copy fa-sm text-white-50"></i> Clonar este año
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card-body">
        <?php if(count($tarifas) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaEspecifca" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Examen</th>
                            <th>Precio Costo</th>
                            <th>Precio Venta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tarifas as $tar): ?>
                        <tr>
                            <td><?php echo $tar['nombre_examen']; ?></td>
                            <td>$ <?php echo number_format($tar['precio_costo'], 2); ?></td>
                            <td>$ <?php echo number_format($tar['precio_venta'], 2); ?></td>
                            <td>
                                <?php if($_SESSION['rol'] != 'visualizador'): ?>
                                    <a href="editar_tarifa.php?id=<?php echo $tar['id']; ?>" class="btn btn-warning btn-sm" title="Editar Precio">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../controladores/ControladorTarifa.php?accion=eliminar&id=<?php echo $tar['id']; ?>&id_prov=<?php echo $id_busqueda; ?>&anio=<?php echo $anio_busqueda; ?>" class="btn btn-danger btn-sm" title="Quitar Examen" onclick="return confirm('¿Seguro que desea quitar esta tarifa?');">
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
        <?php else: ?>
            <div class="alert alert-warning text-center">No se encontraron tarifas registradas para este proveedor en el año <?php echo $anio_busqueda; ?>.</div>
        <?php endif; ?>
    </div>
</div>

<?php if($_SESSION['rol'] != 'visualizador'): ?>
<div class="modal fade" id="modalClonar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Clonar Tarifas a un Nuevo Año</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../controladores/ControladorTarifa.php" method="POST">
                <div class="modal-body">
                    <p>Se copiaran las tarifas del año <strong><?php echo $anio_busqueda; ?></strong> para <strong><?php echo $nombre_proveedor_actual; ?></strong>.</p>
                    
                    <input type="hidden" name="accion" value="clonar">
                    <input type="hidden" name="origen" value="proveedor"> <input type="hidden" name="id_proveedor" value="<?php echo $id_busqueda; ?>">
                    <input type="hidden" name="anio_base" value="<?php echo $anio_busqueda; ?>">

                    <div class="form-group">
                        <label>Año Destino (Nuevo):</label>
                        <input type="number" class="form-control" name="anio_nuevo" value="<?php echo $anio_busqueda + 1; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Porcentaje de Aumento (%):</label>
                        <input type="number" step="0.01" class="form-control" name="porcentaje" required placeholder="Ejemplo: 6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-copy"></i> Confirmar Clonacion</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; // Fin del bloque de resultados ?>

<?php require_once 'plantillas/pie_pagina.php'; ?>

<script>
$(document).ready(function() {
    $('#tablaEspecifca').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" }
    });
});
</script>