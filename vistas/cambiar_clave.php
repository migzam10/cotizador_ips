<?php
// vistas/cambiar_clave.php

require_once 'plantillas/encabezado.php';

// Identificamos a quien se le cambiara la clave
$id_objetivo = $_SESSION['id_usuario']; 
$es_admin_modificando_a_otro = false;

// Si viene un ID y es admin, va a cambiar la clave de otra persona
if (isset($_GET['id']) && $_SESSION['rol'] == 'admin') {
    $id_objetivo = $_GET['id'];
    $es_admin_modificando_a_otro = true;
}

// Bloqueo: si no es admin e intenta meter un ID en la URL, se le deniega
if (isset($_GET['id']) && $_SESSION['rol'] != 'admin' && $_GET['id'] != $_SESSION['id_usuario']) {
    echo '<div class="alert alert-danger mt-4">Acceso denegado. Solo puedes cambiar tu propia clave.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>

<h1 class="h3 mb-4 text-gray-800">Cambiar Clave de Acceso</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <?php echo $es_admin_modificando_a_otro ? 'Modificando clave de otro usuario' : 'Actualizar mi clave'; ?>
        </h6>
    </div>
    <div class="card-body">
        
        <form action="../controladores/ControladorUsuario.php" method="POST">
            <input type="hidden" name="accion" value="cambiar_clave">
            <input type="hidden" name="id_usuario" value="<?php echo $id_objetivo; ?>">
            
            <?php if($es_admin_modificando_a_otro): ?>
                <input type="hidden" name="desde_admin" value="1">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nueva Clave *</label>
                    <input type="password" class="form-control" name="nueva_clave" required placeholder="Ingrese la nueva clave" minlength="6">
                    <small class="text-muted">Se recomienda usar minimo 6 caracteres.</small>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-warning"><i class="fas fa-key"></i> Actualizar Clave</button>
            
            <?php if($es_admin_modificando_a_otro): ?>
                <a href="listar_usuarios.php" class="btn btn-secondary ml-2">Cancelar</a>
            <?php else: ?>
                <a href="listar_proveedores.php" class="btn btn-secondary ml-2">Cancelar</a>
            <?php endif; ?>
        </form>

    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>