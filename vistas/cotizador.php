<?php
// vistas/cotizador.php
require_once 'plantillas/encabezado.php';
require_once '../configuracion/Conexion.php'; 
$conexion = (new Conexion())->conectar();

$ciudades = $conexion->query("SELECT * FROM ciudades ORDER BY nombre ASC")->fetchAll();
$categorias = $conexion->query("SELECT * FROM categorias_examen WHERE estado = 1 ORDER BY nombre ASC")->fetchAll();

// Traemos los examenes con su categoria
$examenes_lista = $conexion->query("SELECT e.*, c.nombre as categoria FROM examenes e LEFT JOIN categorias_examen c ON e.id_categoria = c.id WHERE e.estado = 1 AND c.estado = 1 ORDER BY c.nombre ASC, e.nombre ASC")->fetchAll();

$resultados = [];
$anio_busqueda = date('Y');
$ciudades_seleccionadas = [];
$examenes_seleccionados = [];
$nombres_examenes_seleccionados = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generar'])) {
    require_once '../modelos/ModeloCotizador.php';
    
    $anio_busqueda = $_POST['anio'];
    $ciudades_seleccionadas = $_POST['ciudades'] ?? [];
    $examenes_seleccionados = $_POST['examenes'] ?? [];

    if (!empty($ciudades_seleccionadas) && !empty($examenes_seleccionados)) {
        $modelo_cotizador = new ModeloCotizador();
        $datos_planos = $modelo_cotizador->obtenerMatrizCotizacion($anio_busqueda, $ciudades_seleccionadas, $examenes_seleccionados);

        foreach ($examenes_lista as $ex) {
            if (in_array($ex['id'], $examenes_seleccionados)) {
                $nombres_examenes_seleccionados[] = $ex['nombre'];
            }
        }

        foreach ($datos_planos as $fila) {
            $ciudad = $fila['ciudad'];
            $proveedor = $fila['proveedor'];
            $examen = $fila['examen'];
            
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

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold">Año de Tarifa</label>
                        <input type="number" class="form-control" name="anio" value="<?php echo $anio_busqueda; ?>"
                            required>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold text-primary">Ciudades</label>

                        <input type="text" id="buscador_ciudades"
                            class="form-control form-control-sm mb-2 border-primary" placeholder="Buscar ciudad...">

                        <div class="border rounded p-2 border-primary"
                            style="height: 115px; overflow-y: scroll; background-color: #f8f9fc;">
                            <?php foreach($ciudades as $ciu): ?>
                            <div class="custom-control custom-checkbox item-ciudad">
                                <input type="checkbox" class="custom-control-input" id="ciu_<?php echo $ciu['id']; ?>"
                                    name="ciudades[]" value="<?php echo $ciu['id']; ?>"
                                    <?php echo in_array($ciu['id'], $ciudades_seleccionadas) ? 'checked' : ''; ?>>
                                <label class="custom-control-label nombre-ciudad"
                                    for="ciu_<?php echo $ciu['id']; ?>"><small><?php echo $ciu['nombre']; ?></small></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 form-group">
                    <label class="font-weight-bold text-info">1. Filtrar por Categoria</label>
                    <div class="border rounded p-2 border-info"
                        style="height: 235px; overflow-y: scroll; background-color: #f8f9fc;">
                        <p class="small text-muted mb-2">Seleccione para ver examenes:</p>
                        <?php foreach($categorias as $cat): ?>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input check-categoria"
                                id="cat_<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>"
                                onchange="filtrarExamenes()">
                            <label class="custom-control-label"
                                for="cat_<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-success">2. Seleccionar Examenes</label>

                    <input type="text" id="buscador_examenes" class="form-control form-control-sm mb-2 border-success"
                        placeholder="Escriba para buscar un examen rapidamente...">

                    <div class="border rounded p-2 border-success"
                        style="height: 195px; overflow-y: scroll; background-color: #f8f9fc;">
                        <div class="row" id="contenedor_examenes">
                            <?php foreach($examenes_lista as $ex): ?>
                            <div class="col-md-6 item-examen" data-categoria="<?php echo $ex['id_categoria']; ?>">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ex_<?php echo $ex['id']; ?>"
                                        name="examenes[]" value="<?php echo $ex['id']; ?>"
                                        <?php echo in_array($ex['id'], $examenes_seleccionados) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="ex_<?php echo $ex['id']; ?>"><small
                                            class="nombre-examen"><?php echo $ex['nombre']; ?></small></label>
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
<div class="alert alert-warning">No se encontraron tarifas para los parametros seleccionados.</div>
<?php else: ?>

<?php foreach ($resultados as $ciudad => $proveedores): ?>
<div class="card shadow mb-5 border-left-success">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-success text-uppercase">CIUDAD: <?php echo $ciudad; ?> - AÑO
            <?php echo $anio_busqueda; ?></h6>
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
                                $totales_costo = [];
                                $totales_venta = [];
                                
                                foreach ($nombres_examenes_seleccionados as $nombre_ex): 
                                ?>
                    <tr>
                        <td class="text-left font-weight-bold"><?php echo $nombre_ex; ?></td>

                        <?php 
                                        foreach ($proveedores as $nombre_prov => $examenes_del_prov): 
                                            
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
                        <td class="text-danger">$
                            <?php echo isset($totales_costo[$nombre_prov]) ? number_format($totales_costo[$nombre_prov], 0, ',', '.') : '0'; ?>
                        </td>
                        <td class="text-success">$
                            <?php echo isset($totales_venta[$nombre_prov]) ? number_format($totales_venta[$nombre_prov], 0, ',', '.') : '0'; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div class="text-right mb-4">
    <?php if ($_SESSION['rol'] != 'visualizador'): ?>
    <button class="btn btn-success btn-lg shadow" data-toggle="modal" data-target="#modalGuardarCotizacion">
        <i class="fas fa-save"></i> Guardar Cotizacion
    </button>
    <?php endif; ?>

    <div class="modal fade" id="modalGuardarCotizacion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Guardar Registro de Cotizacion</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div id="mensaje_ajax" class="alert d-none"></div>

                    <div class="form-group">
                        <label>Nombre de la Empresa o Cliente:</label>
                        <input type="text" id="cliente_nombre" class="form-control"
                            placeholder="Empresa o persona a quien se le esta cotizando">
                    </div>
                    <div class="form-group">
                        <label>NIT / Cedula:</label>
                        <input type="text" id="cliente_nit" class="form-control" placeholder="Numero de documento del cliente">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btnProcesarGuardado"
                        onclick="enviarCotizacionAjax()"><i class="fas fa-save"></i> Confirmar y Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<?php endif; ?>

<?php require_once 'plantillas/pie_pagina.php'; ?>

<script>
var datosMatrizGenerada = <?php echo isset($datos_planos) ? json_encode($datos_planos) : '[]'; ?>;

function enviarCotizacionAjax() {
    // Capturamos los datos del modal
    var nombre = $('#cliente_nombre').val();
    var nit = $('#cliente_nit').val();
    var btn = $('#btnProcesarGuardado');
    var msj = $('#mensaje_ajax');

    // Validamos que haya algo que guardar
    if (datosMatrizGenerada.length === 0) {
        msj.removeClass('d-none alert-success').addClass('alert-danger').text(
        'No hay datos en la matriz para guardar.');
        return;
    }

    // Cambiamos el estado del boton para que no le den doble clic
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    // Armamos el paquete de datos
    var payload = {
        cliente_nombre: nombre,
        cliente_nit: nit,
        detalles: datosMatrizGenerada
    };

    // Hacemos la peticion AJAX con la funcion fetch
    fetch('../controladores/ControladorCotizacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                msj.removeClass('d-none alert-danger').addClass('alert-success').text(data.mensaje);
                btn.html('<i class="fas fa-check"></i> Guardado');
                // Limpiamos los campos opcionalmente
                $('#cliente_nombre').val('');
                $('#cliente_nit').val('');
            } else {
                msj.removeClass('d-none alert-success').addClass('alert-danger').text(data.mensaje);
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Intentar de nuevo');
            }
        })
        .catch(error => {
            msj.removeClass('d-none alert-success').addClass('alert-danger').text(
                'Error de conexion con el servidor.');
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Intentar de nuevo');
        });
}

function filtrarExamenes() {
    // 1. Obtenemos todas las categorias que el usuario ha marcado con un gancho (check)
    var categoriasSeleccionadas = [];
    $('.check-categoria:checked').each(function() {
        categoriasSeleccionadas.push($(this).val());
    });

    // 2. Si no ha marcado ninguna categoria, mostramos todos los examenes
    if (categoriasSeleccionadas.length === 0) {
        $('.item-examen').show();
    } else {
        // 3. Si marco al menos una, ocultamos todos los examenes primero...
        $('.item-examen').hide();

        // 4. ...y luego recorremos todos los examenes mostrando SOLO los que coincidan con la categoria
        $('.item-examen').each(function() {
            var idCategoriaDelExamen = $(this).data('categoria').toString();

            // Si el ID de la categoria del examen esta dentro de la lista de seleccionadas, lo mostramos
            if (categoriasSeleccionadas.includes(idCategoriaDelExamen)) {
                $(this).show();
            }
        });
    }
}

$(document).ready(function() {
    // Escuchamos cuando el usuario hace clic en una categoria
    $('.check-categoria').on('change', aplicarFiltrosExamenes);

    // Escuchamos cuando el usuario escribe en el buscador (keyup)
    $('#buscador_examenes').on('keyup', aplicarFiltrosExamenes);

    $('#buscador_ciudades').on('keyup', filtrarCiudades);
});

function aplicarFiltrosExamenes() {
    // 1. Capturamos el texto que escribio el usuario (en minusculas para que no importe como escriba)
    var textoBusqueda = $('#buscador_examenes').val().toLowerCase();

    // 2. Capturamos las categorias seleccionadas
    var categoriasSeleccionadas = [];
    $('.check-categoria:checked').each(function() {
        categoriasSeleccionadas.push($(this).val());
    });

    // 3. Recorremos cada examen para decidir si lo mostramos o lo ocultamos
    $('.item-examen').each(function() {
        var mostrarPorCategoria = false;
        var mostrarPorTexto = false;

        var idCategoriaDelExamen = $(this).data('categoria').toString();
        // Leemos el texto del span que contiene el nombre del examen
        var nombreExamen = $(this).find('.nombre-examen').text().toLowerCase();

        // Evaluacion 1: ¿Pasa el filtro de categoria?
        if (categoriasSeleccionadas.length === 0 || categoriasSeleccionadas.includes(idCategoriaDelExamen)) {
            mostrarPorCategoria = true;
        }

        // Evaluacion 2: ¿Pasa el filtro de texto?
        if (textoBusqueda === '' || nombreExamen.indexOf(textoBusqueda) > -1) {
            mostrarPorTexto = true;
        }

        // Decision final: Solo mostramos si pasa ambos filtros
        if (mostrarPorCategoria && mostrarPorTexto) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function filtrarCiudades() {
    var textoBusquedaCiudad = $('#buscador_ciudades').val().toLowerCase();

    $('.item-ciudad').each(function() {
        var nombreCiudad = $(this).find('.nombre-ciudad').text().toLowerCase();

        if (textoBusquedaCiudad === '' || nombreCiudad.indexOf(textoBusquedaCiudad) > -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
</script>