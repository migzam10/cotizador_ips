<?php
// vistas/editar_proveedor.php

require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloProveedor.php';
require_once '../modelos/ModeloCiudad.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">No tienes permisos para editar o crear registros.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit(); // Detenemos la ejecucion para que no cargue el formulario
}

// Verificamos si existe el ID en la URL
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Error: No se selecciono ningun proveedor.</div>";
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$id_proveedor = $_GET['id'];
$modelo = new ModeloProveedor();
$datos = $modelo->obtenerProveedorPorId($id_proveedor);

$modelo_ciudad = new ModeloCiudad();
$ciudades = $modelo_ciudad->obtenerTodasLasCiudades();

if (!$datos) {
    echo "<div class='alert alert-danger'>Error: El proveedor no existe.</div>";
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>

<h1 class="h3 mb-4 text-gray-800">Editar Proveedor IPS</h1>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'error'): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    Ocurrio un error al intentar actualizar. Verifique que el NIT no este duplicado.
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
            aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Modificar Datos</h6>
    </div>
    <div class="card-body">

        <form action="../controladores/ControladorProveedor.php" method="POST">

            <input type="hidden" name="id" value="<?php echo $datos['id']; ?>">

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nit">NIT del Proveedor *</label>
                    <input type="text" class="form-control" id="nit" name="nit" required
                        value="<?php echo $datos['nit']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label for="nombre_ips">Nombre de la IPS *</label>
                    <input type="text" class="form-control" id="nombre_ips" name="nombre_ips" required
                        value="<?php echo $datos['nombre_ips']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="id_ciudad">Ciudad *</label>
                    <select class="form-control" id="id_ciudad" name="id_ciudad" required>
                        <option value="">Seleccione una ciudad...</option>
                        <?php foreach($ciudades as $ciu): ?>
                        <option value="<?php echo $ciu['id']; ?>"
                            <?php if($datos['id_ciudad'] == $ciu['id']) echo 'selected'; ?>>
                            <?php echo $ciu['nombre']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="direccion">Direccion</label>
                    <input type="text" class="form-control" id="direccion" name="direccion"
                        value="<?php echo $datos['direccion']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="nombre_contacto">Nombre del Contacto</label>
                    <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto"
                        placeholder="Persona de contacto" value="<?php echo $datos['nombre_contacto']; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="telefonos">Teléfono</label>
                    <input type="text" class="form-control" id="telefonos" name="telefonos" placeholder="Fijo o Celular"
                        value="<?php echo $datos['telefonos']; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="correos">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correos" name="correos" placeholder="contacto@ips.com"
                        value="<?php echo $datos['correos']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="enlace_conceptos">Enlace Conceptos</label>
                    <input type="text" class="form-control" id="enlace_conceptos" name="enlace_conceptos"
                        placeholder="Enlace para los conceptos" value="<?php echo $datos['enlace_conceptos']; ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label for="usu">Usuario</label>
                    <input type="text" class="form-control" id="usu" name="usu"
                        placeholder="Nombre de usuario" value="<?php echo $datos['usu']; ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label for="password">Contraseña</label>
                    <input type="text" class="form-control" id="password" name="password"
                        placeholder="Contraseña" value="<?php echo $datos['password']; ?>">
                </div>

            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="">Tipo de cuenta</label>
                    <select class="form-control" id="tipo_cuenta" name="tipo_cuenta"
                        required>
                        <option value="">Seleccione el tipo de cuenta...</option>
                        <option value="Ahorro" <?php if($datos['tipo_cuenta'] == 'Ahorro') echo 'selected'; ?>>Ahorro</option>
                        <option value="Corriente" <?php if($datos['tipo_cuenta'] == 'Corriente') echo 'selected'; ?>>Corriente</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="banco">Banco</label>
                    <input type="text" class="form-control" id="banco" name="banco"
                        placeholder="Banco del proveedor" value="<?php echo $datos['banco']; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="numero_cuenta">Número de cuenta</label>
                    <input type="text" class="form-control" id="numero_cuenta" name="numero_cuenta"
                        placeholder="Número de cuenta bancaria" value="<?php echo $datos['numero_cuenta']; ?>">
                </div>
            </div>

            <div class="row">

                <div class="col-md-12 form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones"
                        rows="2"><?php echo $datos['observaciones']; ?></textarea>
                </div>
            </div>
            <hr>

            <button type="submit" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50"><i class="fas fa-edit"></i></span>
                <span class="text">Actualizar Proveedor</span>
            </button>
            <a href="listar_proveedores.php" class="btn btn-secondary ml-2">Cancelar</a>

        </form>

    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>