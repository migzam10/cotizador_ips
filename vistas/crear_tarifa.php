<?php
// vistas/crear_tarifa.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloProveedor.php';
require_once '../modelos/ModeloExamen.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$modelo_prov = new ModeloProveedor();
$proveedores = $modelo_prov->obtenerTodosLosProveedores();

$modelo_ex = new ModeloExamen();
$examenes = $modelo_ex->obtenerTodosLosExamenes();
?>

<h1 class="h3 mb-4 text-gray-800">Asignar Tarifa Individual</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="../controladores/ControladorTarifa.php" method="POST">
            <input type="hidden" name="accion" value="crear">
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Proveedor IPS *</label>
                    <select class="form-control select-buscador js-example-basic-single" name="id_proveedor" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($proveedores as $prov): ?>
                            <?php if($prov['estado'] == 1): ?>
                                <option value="<?php echo $prov['id']; ?>"><?php echo $prov['nombre_ips'] . ' - ' . $prov['nombre_ciudad']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Examen *</label>
                    <select class="form-control select-buscador" name="id_examen" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($examenes as $ex): ?>
                            <?php if($ex['estado'] == 1): ?>
                                <option value="<?php echo $ex['id']; ?>"><?php echo $ex['nombre']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 form-group">
                    <label>Año *</label>
                    <input type="number" class="form-control" name="anio" required value="<?php echo date('Y'); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Precio Costo ($) *</label>
                    <input type="number" step="0.01" class="form-control" id="precio_costo" name="precio_costo" required placeholder="Ej: 15000">
                </div>
                
                <div class="col-md-2 form-group">
                    <label class="text-info font-weight-bold">(%)</label>
                    <input type="number" step="0.01" class="form-control border-info" id="porcentaje" placeholder="Ej: 6">
                    
                </div>

                <div class="col-md-4 form-group">
                    <label>Precio Venta ($) *</label>
                    <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" required placeholder="Ej: 15900">
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Tarifa</button>
            <a href="listar_tarifas.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'duplicado'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: '¡Tarifa Existente!',
            text: 'Este examen ya tiene un precio asignado para este proveedor en el año seleccionado.',
            confirmButtonColor: '#4e73df',
            confirmButtonText: 'Entendido'
        });
        // Esto limpia la URL para que si el usuario recarga la página, no vuelva a salir la alerta
        window.history.replaceState({}, document.title, window.location.pathname);
    });
</script>
<?php endif; ?>
<script>
$(document).ready(function() {
    
    // CASO 1: Escribes en la casilla de Porcentaje (%)
    $('#porcentaje').on('input', function() {
        var costo = parseFloat($('#precio_costo').val()) || 0;
        var porc = parseFloat($(this).val()) || 0;
        
        if(costo > 0) {
            // Formula: Costo + (Costo * Porcentaje / 100)
            var venta = costo + (costo * (porc / 100));
            // Ponemos el resultado en la casilla de venta, redondeado a 2 decimales
            $('#precio_venta').val(venta.toFixed(2));
        }
    });

    // CASO 2: Escribes directo en la casilla de Precio Venta
    $('#precio_venta').on('input', function() {
        var costo = parseFloat($('#precio_costo').val()) || 0;
        var venta = parseFloat($(this).val()) || 0;
        
        if(costo > 0 && venta >= costo) {
            // Formula inversa: ((Venta - Costo) / Costo) * 100
            var porc = ((venta - costo) / costo) * 100;
            $('#porcentaje').val(porc.toFixed(2));
        } else if (venta < costo) {
            // Si la venta es menor al costo, limpiamos el porcentaje (estaria perdiendo plata)
            $('#porcentaje').val('');
        }
    });

    // CASO 3: Cambias el Precio Costo cuando ya hay un porcentaje puesto
    $('#precio_costo').on('input', function() {
        var costo = parseFloat($(this).val()) || 0;
        var porc = $('#porcentaje').val();
        
        if (porc !== "" && costo > 0) {
            porc = parseFloat(porc);
            var venta = costo + (costo * (porc / 100));
            $('#precio_venta').val(venta.toFixed(2));
        } else if (costo == 0) {
            $('#precio_venta').val('');
            $('#porcentaje').val('');
        }
    });

});
</script>


