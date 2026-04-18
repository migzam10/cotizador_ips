<?php
// vistas/editar_tarifa.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloTarifa.php';
require_once '../modelos/ModeloProveedor.php';
require_once '../modelos/ModeloExamen.php';

// Bloqueo de seguridad
if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

// Verificamos que venga un ID
if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger mt-4">Error: No se ha seleccionado ninguna tarifa.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$id_tarifa = $_GET['id'];
$modelo_tarifa = new ModeloTarifa();
$datos_tarifa = $modelo_tarifa->obtenerTarifaPorId($id_tarifa);

if (!$datos_tarifa) {
    echo '<div class="alert alert-danger mt-4">Error: La tarifa no existe.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

// Traemos proveedores y examenes para mostrar los nombres en el formulario
$modelo_prov = new ModeloProveedor();
$proveedores = $modelo_prov->obtenerTodosLosProveedores();

$modelo_ex = new ModeloExamen();
$examenes = $modelo_ex->obtenerTodosLosExamenes();
?>

<h1 class="h3 mb-4 text-gray-800">Editar Precios de Tarifa</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Modificar Valores Manualmente</h6>
    </div>
    <div class="card-body">
        <form action="../controladores/ControladorTarifa.php" method="POST">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" value="<?php echo $datos_tarifa['id']; ?>">
            
            <div class="row">
                <div class="col-md-5 form-group">
                    <label>Proveedor IPS (No editable)</label>
                    <select class="form-control" disabled>
                        <?php foreach($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id']; ?>" <?php if($datos_tarifa['id_proveedor'] == $prov['id']) echo 'selected'; ?>>
                                <?php echo $prov['nombre_ips']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5 form-group">
                    <label>Examen (No editable)</label>
                    <select class="form-control" disabled>
                        <?php foreach($examenes as $ex): ?>
                            <option value="<?php echo $ex['id']; ?>" <?php if($datos_tarifa['id_examen'] == $ex['id']) echo 'selected'; ?>>
                                <?php echo $ex['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label>Año</label>
                    <input type="number" class="form-control" value="<?php echo $datos_tarifa['anio']; ?>" disabled>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Precio Costo ($) *</label>
                    <input type="number" step="0.01" class="form-control" name="precio_costo" required value="<?php echo $datos_tarifa['precio_costo']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>Precio Venta ($) *</label>
                    <input type="number" step="0.01" class="form-control" name="precio_venta" required value="<?php echo $datos_tarifa['precio_venta']; ?>">
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Actualizar Precios</button>
            <a href="listar_tarifas.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>