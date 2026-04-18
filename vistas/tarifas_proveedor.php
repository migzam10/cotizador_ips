<?php
// vistas/tarifas_proveedor.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloTarifa.php';
require_once '../configuracion/Conexion.php';

$conexion = (new Conexion())->conectar();

// MAGIA AQUI: Traemos los proveedores pegados con su ciudad para mostrarlos juntos
$proveedores = $conexion->query("SELECT p.id, p.nombre_ips, c.nombre as nombre_ciudad 
                                 FROM proveedores p 
                                 INNER JOIN ciudades c ON p.id_ciudad = c.id 
                                 WHERE p.estado = 1 
                                 ORDER BY p.nombre_ips ASC")->fetchAll();

$id_busqueda = $_GET['id_proveedor'] ?? '';
$anio_busqueda = $_GET['anio'] ?? date('Y');

$todos_examenes = $conexion->query("SELECT * FROM examenes WHERE estado = 1 ORDER BY nombre ASC")->fetchAll();

$tarifas_existentes = [];
$nombre_proveedor_actual = '';

if ($id_busqueda != '') {
    $modelo_tarifa = new ModeloTarifa();
    $tarifas_bd = $modelo_tarifa->obtenerTarifasPorProveedorYAno($id_busqueda, $anio_busqueda);
    
    foreach($tarifas_bd as $t) {
        $tarifas_existentes[$t['id_examen']] = $t; 
    }
    foreach($proveedores as $p) {
        if($p['id'] == $id_busqueda) { 
            $nombre_proveedor_actual = $p['nombre_ips'] . ' (' . $p['nombre_ciudad'] . ')'; 
            break; 
        }
    }
}
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Tarifas por Proveedor</h1>
<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'clonado_prov'): ?>
        <div class="alert alert-primary alert-dismissible fade show">Tarifas copiadas con exito a la nueva IPS.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'error_existe_dest'): ?>
        <div class="alert alert-danger alert-dismissible fade show"><strong>Atencion:</strong> La IPS destino ya tiene tarifas asignadas en ese ano. No se puede copiar para evitar mezcla de precios.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-body bg-light">
        <form action="tarifas_proveedor.php" method="GET">
            <div class="row">
                <div class="col-md-8">
                    <label class="font-weight-bold text-gray-700">Proveedor y Ciudad:</label>
                    <select class="form-control select-buscador" name="id_proveedor" required>
                        <option value="">Seleccione un proveedor...</option>
                        <?php foreach($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id']; ?>" <?php if($id_busqueda == $prov['id']) echo 'selected'; ?>>
                                <?php echo $prov['nombre_ips'] . ' - ' . $prov['nombre_ciudad']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="font-weight-bold text-gray-700">Ano de Vigencia:</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="anio" value="<?php echo $anio_busqueda; ?>" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cargar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if($id_busqueda != ''): ?>

    <input type="hidden" id="prov_seleccionado" value="<?php echo $id_busqueda; ?>">
    <input type="hidden" id="anio_seleccionado" value="<?php echo $anio_busqueda; ?>">

    <div class="row mb-4">
        <div class="col-md-12">
            <label class="font-weight-bold text-success">Seleccionar Examenes</label>
            <input type="text" id="buscador_examenes" class="form-control form-control-sm mb-2 border-success" placeholder="Escriba para buscar un examen rapidamente...">
            
            <div class="border rounded p-3 border-success" style="height: 195px; overflow-y: scroll; background-color: #f8f9fc;">
                <div class="row">
                    <?php foreach($todos_examenes as $ex): 
                        $esta_marcado = isset($tarifas_existentes[$ex['id']]) ? 'checked' : '';
                    ?>
                    <div class="col-md-6 item-examen mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input check-examen" id="ex_<?php echo $ex['id']; ?>" value="<?php echo $ex['id']; ?>" data-nombre="<?php echo $ex['nombre']; ?>" <?php echo $esta_marcado; ?>>
                            <label class="custom-control-label nombre-examen" for="ex_<?php echo $ex['id']; ?>"><small class="text-gray-700"><?php echo $ex['nombre']; ?></small></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-info">Tarifas de <?php echo $nombre_proveedor_actual; ?> - Ano <?php echo $anio_busqueda; ?></h6>
            <div>
                <button type="button" class="btn btn-sm btn-primary shadow-sm mr-2" data-toggle="modal" data-target="#modalClonarProveedor">
                    <i class="fas fa-exchange-alt fa-sm text-white-50"></i> Clonar a otra IPS
                </button>
                <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalClonar">
                    <i class="fas fa-copy fa-sm text-white-50"></i> Clonar este año
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaMasiva" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40%;">Nombre del Producto o Servicio</th>
                            <th style="width: 15%;">Precio Costo</th>
                            <th style="width: 15%;">%</th>
                            <th style="width: 15%;">Precio Venta</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($tarifas_existentes as $id_ex => $tar): 
                            
                            // CALCULAMOS EL PORCENTAJE INICIAL EN PHP
                            $costo_bd = floatval($tar['precio_costo']);
                            $venta_bd = floatval($tar['precio_venta']);
                            $porcentaje_calculado = '';
                            // Validamos que el costo no sea 0 para evitar errores matematicos (division por cero)
                            if ($costo_bd > 0 && $venta_bd >= $costo_bd) {
                                $porcentaje_calculado = round((($venta_bd - $costo_bd) / $costo_bd) * 100, 2);
                            }
                        ?>
                        <tr id="fila_<?php echo $id_ex; ?>">
                            <td class="font-weight-bold"><?php echo $tar['nombre_examen']; ?></td>
                            
                            <td><input type="number" step="0.01" class="form-control costo-row" value="<?php echo $tar['precio_costo']; ?>"></td>
                            
                            <td><input type="number" step="0.01" class="form-control border-info porc-row" placeholder="Ej: 6" value="<?php echo $porcentaje_calculado; ?>"></td>
                            
                            <td><input type="number" step="0.01" class="form-control venta-row" value="<?php echo $tar['precio_venta']; ?>"></td>
                            
                            <td class="text-center align-middle">
                                <button class="btn btn-warning btn-sm btn-guardar shadow-sm" data-id="<?php echo $id_ex; ?>" title="Guardar"><i class="fas fa-save"></i></button>
                                <button class="btn btn-danger btn-sm btn-eliminar shadow-sm" data-id="<?php echo $id_ex; ?>" title="Eliminar"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php if(isset($id_busqueda) && $id_busqueda != '' && $_SESSION['rol'] != 'visualizador'): ?>
<div class="modal fade" id="modalClonar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Clonar Tarifas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../controladores/ControladorTarifa.php" method="POST">
                <div class="modal-body">
                    <p>Se copiaran las tarifas del ano <strong><?php echo $anio_busqueda; ?></strong> para este proveedor.</p>
                    <input type="hidden" name="accion" value="clonar">
                    <input type="hidden" name="origen" value="proveedor">
                    <input type="hidden" name="id_proveedor" value="<?php echo $id_busqueda; ?>">
                    <input type="hidden" name="anio_base" value="<?php echo $anio_busqueda; ?>">
                    <div class="form-group">
                        <label>Ano Destino (Nuevo):</label>
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

<?php if(isset($id_busqueda) && $id_busqueda != '' && $_SESSION['rol'] != 'visualizador'): ?>
<div class="modal fade" id="modalClonarProveedor"  role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Copiar Portafolio a otra IPS</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../controladores/ControladorTarifa.php" method="POST">
                <div class="modal-body">
                    <p>Se copiaran todos los examenes de <strong><?php echo $nombre_proveedor_actual; ?></strong> a la IPS que elijas.</p>
                    
                    <input type="hidden" name="accion" value="clonar_proveedor">
                    <input type="hidden" name="id_proveedor_origen" value="<?php echo $id_busqueda; ?>">
                    <input type="hidden" name="anio_origen" value="<?php echo $anio_busqueda; ?>">

                    <div class="form-group">
                        <label>IPS Destino *:</label>
                        <select class="form-control select-buscador" name="id_proveedor_destino" style="width: 100%;" required>
                            <option value="">Seleccione a donde copiar...</option>
                            <?php foreach($proveedores as $prov): 
                                // Evitamos mostrar en la lista a la misma IPS en la que ya estamos parados
                                if($prov['id'] != $id_busqueda):
                            ?>
                                <option value="<?php echo $prov['id']; ?>">
                                    <?php echo $prov['nombre_ips'] . ' - ' . $prov['nombre_ciudad']; ?>
                                </option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Ano Destino *:</label>
                            <input type="number" class="form-control" name="anio_destino" value="<?php echo $anio_busqueda; ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Aumento (%):</label>
                            <input type="number" step="0.01" class="form-control" name="porcentaje" placeholder="Ej: 0">
                            <small class="text-muted">Dejalo vacio para copiar el precio exacto.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Iniciar Copiado</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'plantillas/pie_pagina.php'; ?>

<script>
$(document).ready(function() {
    
    var tablaTarifas = $('#tablaMasiva').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
        "pageLength": 50
    });

    $('#buscador_examenes').on('keyup', function() {
        var texto = $(this).val().toLowerCase();
        $('.item-examen').each(function() {
            var nombre = $(this).find('.nombre-examen').text().toLowerCase();
            if (nombre.indexOf(texto) > -1) { $(this).show(); } else { $(this).hide(); }
        });
    });


    $('.check-examen').on('change', function() {
        var id_ex = $(this).val();
        var nombre = $(this).data('nombre');

        if (this.checked) {
            var inputCosto = `<input type="number" step="0.01" class="form-control costo-row" value="">`;
            var inputPorc = `<input type="number" step="0.01" class="form-control border-info porc-row" placeholder="Ej: 6">`;
            var inputVenta = `<input type="number" step="0.01" class="form-control venta-row" value="">`;
            var botones = `
                <button class="btn btn-warning btn-sm btn-guardar shadow-sm" data-id="${id_ex}" title="Guardar"><i class="fas fa-save"></i></button>
                <button class="btn btn-danger btn-sm btn-eliminar shadow-sm" data-id="${id_ex}" title="Eliminar"><i class="fas fa-times"></i></button>
            `;

            var nodo = tablaTarifas.row.add([
                `<span class="text-xs font-weight-bold text-gray-800">${nombre}</span>`, inputCosto, inputPorc, inputVenta, botones
            ]).draw(false).node(); 

            $(nodo).attr('id', 'fila_' + id_ex);
            $(nodo).find('td').addClass('align-middle');
            $(nodo).find('td:last').addClass('text-center');

        } else {
            $('#fila_' + id_ex).find('.btn-eliminar').click();
        }
    });

    $('#tablaMasiva tbody').on('input', '.costo-row, .porc-row, .venta-row', function() {
        var tr = $(this).closest('tr'); 
        var costo = parseFloat(tr.find('.costo-row').val()) || 0;
        var porc = parseFloat(tr.find('.porc-row').val()) || 0;
        var venta = parseFloat(tr.find('.venta-row').val()) || 0;

        if ($(this).hasClass('porc-row') && costo > 0) {
            var nuevaVenta = costo + (costo * (porc / 100));
            tr.find('.venta-row').val(nuevaVenta.toFixed(2));
        }
        else if ($(this).hasClass('venta-row') && costo > 0 && venta >= costo) {
            var nuevoPorc = ((venta - costo) / costo) * 100;
            tr.find('.porc-row').val(nuevoPorc.toFixed(2));
        }
        else if ($(this).hasClass('costo-row')) {
            if (tr.find('.porc-row').val() !== "") {
                var nuevaVenta = costo + (costo * (porc / 100));
                tr.find('.venta-row').val(nuevaVenta.toFixed(2));
            }
        }
    });

    // AJAX GUARDAR
    $('#tablaMasiva tbody').on('click', '.btn-guardar', function(e) {
        e.preventDefault();
        var btn = $(this);
        var tr = btn.closest('tr');
        var id_ex = btn.data('id');
        var prov = $('#prov_seleccionado').val();
        var anio = $('#anio_seleccionado').val();
        var costo = tr.find('.costo-row').val();
        var venta = tr.find('.venta-row').val();

        if(!costo || !venta || costo == 0 || venta == 0) {
            alert("Atencion: Debes ingresar el costo y la venta.");
            return;
        }

        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '../controladores/ControladorTarifaAjax.php',
            type: 'POST',
            data: { accion: 'guardar', id_proveedor: prov, id_examen: id_ex, anio: anio, costo: costo, venta: venta },
            success: function(respuesta) {
                var data = JSON.parse(respuesta);
                if(data.exito) {
                    btn.html('<i class="fas fa-check"></i>').removeClass('btn-warning').addClass('btn-success');
                    setTimeout(() => { 
                        btn.html('<i class="fas fa-save"></i>').removeClass('btn-success').addClass('btn-warning').prop('disabled', false); 
                    }, 2000);
                } else {
                    alert("Ocurrio un error en la base de datos.");
                    btn.html('<i class="fas fa-save"></i>').prop('disabled', false);
                }
            }
        });
    });

    // AJAX ELIMINAR
    $('#tablaMasiva tbody').on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        if(!confirm("¿Seguro que deseas quitar este examen de la tabla y borrarlo de la BD?")) {
            var id_ex = $(this).data('id');
            $('#ex_' + id_ex).prop('checked', true);
            return;
        }

        var btn = $(this);
        var id_ex = btn.data('id');
        var prov = $('#prov_seleccionado').val();
        var anio = $('#anio_seleccionado').val();

        btn.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '../controladores/ControladorTarifaAjax.php',
            type: 'POST',
            data: { accion: 'eliminar', id_proveedor: prov, id_examen: id_ex, anio: anio },
            success: function() {
                tablaTarifas.row(btn.closest('tr')).remove().draw(false);
                $('#ex_' + id_ex).prop('checked', false);
            }
        });
    });

});
</script>