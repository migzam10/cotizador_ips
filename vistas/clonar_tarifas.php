<?php
// vistas/clonar_tarifas.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloProveedor.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$modelo_prov = new ModeloProveedor();
$proveedores = $modelo_prov->obtenerTodosLosProveedores();
?>

<h1 class="h3 mb-4 text-gray-800">Clonar y Aumentar Tarifas</h1>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'vacio'): ?>
    <div class="alert alert-warning">El proveedor seleccionado no tiene tarifas registradas en el año base.</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info">Herramienta de aumento masivo</h6>
    </div>
    <div class="card-body">
        <p>Esta herramienta copiara todos los examenes de un proveedor de un año especifico, les aplicara el aumento porcentual y los guardara en el nuevo año.</p>
        
        <form action="../controladores/ControladorTarifa.php" method="POST">
            <input type="hidden" name="accion" value="clonar">
            
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Proveedor IPS *</label>
                    <select class="form-control" name="id_proveedor" required>
                        <option value="">Seleccione el proveedor...</option>
                        <?php foreach($proveedores as $prov): ?>
                            <?php if($prov['estado'] == 1): ?>
                                <option value="<?php echo $prov['id']; ?>"><?php echo $prov['nombre_ips'] . ' - ' . $prov['nombre_ciudad']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Año Base (Origen) *</label>
                    <input type="number" class="form-control" name="anio_base" required placeholder="Ej: <?php echo date('Y') - 1; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Año Nuevo (Destino) *</label>
                    <input type="number" class="form-control" name="anio_nuevo" required value="<?php echo date('Y'); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Porcentaje de Aumento (%) *</label>
                    <input type="number" step="0.01" class="form-control" name="porcentaje" required placeholder="Ej: 6">
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-info" onclick="return confirm('¿Esta seguro de realizar este clonado masivo?');">
                <i class="fas fa-copy"></i> Ejecutar Clonacion
            </button>
            <a href="listar_tarifas.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>