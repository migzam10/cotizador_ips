<?php
// vistas/crear_proveedor.php

require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCiudad.php';
require_once '../modelos/ModeloProveedor.php'; 

// Bloqueo de seguridad
if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$modelo_ciudad = new ModeloCiudad();
$ciudades = $modelo_ciudad->obtenerTodasLasCiudades();

// Clonacion de datos
$titulo_pantalla = 'Registrar Nuevo Proveedor IPS';
$es_clon = false;
$nit = '';
$nombre_ips = '';
$direccion = '';
$telefonos = '';
$correos = '';
$nombre_contacto = '';
$enlace_conceptos = '';
$observaciones = '';
$usu = '';
$password = '';
$tipo_cuenta = '';
$banco = '';
$numero_cuenta = '';


if (isset($_GET['clonar'])) {
    $modelo_prov = new ModeloProveedor();
    $datos_clon = $modelo_prov->obtenerProveedorPorId($_GET['clonar']);
    
    if ($datos_clon) {
        $es_clon = true;
        $titulo_pantalla = 'Clonar IPS: ' . $datos_clon['nombre_ips'];
        $nit = $datos_clon['nit'];
        $nombre_ips = $datos_clon['nombre_ips'];
        $direccion = $datos_clon['direccion'];
        
        $telefonos = isset($datos_clon['telefonos']) ? $datos_clon['telefonos'] : (isset($datos_clon['telefono']) ? $datos_clon['telefono'] : '');
        $correos = isset($datos_clon['correos']) ? $datos_clon['correos'] : (isset($datos_clon['correo']) ? $datos_clon['correo'] : '');
        
        $nombre_contacto = $datos_clon['nombre_contacto'] ?? '';
        $enlace_conceptos = $datos_clon['enlace_conceptos'] ?? '';
        $observaciones = $datos_clon['observaciones'] ?? '';
    }
}

?>

<h1 class="h3 mb-4 text-gray-800"><?php echo $titulo_pantalla; ?></h1>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'guardado'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>El proveedor se guardó correctamente.</strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php elseif(isset($_GET['mensaje']) && $_GET['mensaje'] == 'error'): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    Revisa que el NIT no esté repetido o bloqueado.
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Datos del Proveedor</h6>
    </div>
    <div class="card-body">

        <form action="../controladores/ControladorProveedor.php" method="POST">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nit">NIT del Proveedor *</label>
                    <input type="text" class="form-control" id="nit" name="nit" required placeholder="Ej: 900.123.456-7"
                        value="<?php echo $nit; ?>" <?php echo $es_clon ? 'readonly' : ''; ?>>
                </div>
                <div class="col-md-6 form-group">
                    <label for="nombre_ips">Razon Social *</label>
                    <input type="text" class="form-control" id="nombre_ips" name="nombre_ips" required
                        placeholder="Razon social del proveedor" value="<?php echo $nombre_ips; ?>"
                        <?php echo $es_clon ? 'readonly' : ''; ?>>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="id_ciudad">Ciudad *</label>
                    <select class="form-control" id="id_ciudad" name="id_ciudad" required>
                        <option value="">Seleccione la ciudad de la sucursal...</option>
                        <?php foreach($ciudades as $ciu): ?>
                        <option value="<?php echo $ciu['id']; ?>"><?php echo $ciu['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="direccion">Direccion</label>
                    <input type="text" class="form-control" id="direccion" name="direccion"
                        placeholder="Calle, Carrera, Barrio..." value="<?php echo $direccion; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="nombre_contacto">Nombre del Contacto</label>
                    <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto"
                        placeholder="Persona de contacto" value="<?php echo $nombre_contacto; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="telefonos">Teléfono</label>
                    <input type="text" class="form-control" id="telefonos" name="telefonos" placeholder="Fijo o Celular"
                        value="<?php echo $telefonos; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="correos">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correos" name="correos" placeholder="contacto@ips.com"
                        value="<?php echo $correos; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="enlace_conceptos">Enlace Conceptos</label>
                    <input type="text" class="form-control" id="enlace_conceptos" name="enlace_conceptos"
                        placeholder="Enlace para los conceptos" value="<?php echo $enlace_conceptos; ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label for="usu">Usuario</label>
                    <input type="text" class="form-control" id="usu" name="usu"
                        placeholder="Nombre de usuario" value="<?php echo $usu; ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label for="password">Contraseña</label>
                    <input type="text" class="form-control" id="password" name="password"
                        placeholder="Contraseña" value="<?php echo $password; ?>">
                </div>

            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="">Tipo de cuenta</label>
                    <select class="form-control" id="tipo_cuenta" name="tipo_cuenta"
                        required>
                        <option value="">Seleccione el tipo de cuenta...</option>
                        <option value="Ahorro">Ahorro</option>
                        <option value="Corriente">Corriente</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="banco">Banco</label>
                    <input type="text" class="form-control" id="banco" name="banco"
                        placeholder="Banco del proveedor">
                </div>
                <div class="col-md-4 form-group">
                    <label for="numero_cuenta">Número de cuenta</label>
                    <input type="text" class="form-control" id="numero_cuenta" name="numero_cuenta"
                        placeholder="Número de cuenta bancaria">
                </div>
            </div>

            <div class="row">

                <div class="col-md-12 form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                        placeholder="Notas adicionales..."><?php echo $observaciones; ?></textarea>
                </div>
            </div>

            <hr>

            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                <span class="text">Guardar Proveedor</span>
            </button>

        </form>

    </div>
</div>

<?php

require_once 'plantillas/pie_pagina.php';
?>