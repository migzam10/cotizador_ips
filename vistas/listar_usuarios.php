<?php
// vistas/listar_usuarios.php

require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloUsuario.php';

// Bloquear si no es admin
if($_SESSION['rol'] != 'admin') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado. Solo administradores pueden ver esta pagina.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$modelo = new ModeloUsuario();
$usuarios = $modelo->obtenerTodosLosUsuarios();
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Usuarios</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'creado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Usuario creado correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Usuario eliminado del sistema.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'error_propio'): ?>
        <div class="alert alert-warning alert-dismissible fade show">Accion denegada. No puedes eliminar tu propia cuenta.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'clave_cambiada'): ?>
        <div class="alert alert-success alert-dismissible fade show">La clave del usuario fue actualizada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Usuarios Registrados</h6>
        <a href="crear_usuario.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Usuario</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display stripe mi-datatable" id="tablaUsuarios" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usr): ?>
                    <tr>
                        <td><?php echo $usr['nombre']; ?></td>
                        <td><?php echo $usr['usuario']; ?></td>
                        <td>
                            <?php 
                                if($usr['rol'] == 'admin') echo '<span class="badge badge-primary">Administrador</span>';
                                if($usr['rol'] == 'general') echo '<span class="badge badge-success">General</span>';
                                if($usr['rol'] == 'visualizador') echo '<span class="badge badge-secondary">Visualizador</span>';
                            ?>
                        </td>
                        <td>
                            <a href="cambiar_clave.php?id=<?php echo $usr['id']; ?>" class="btn btn-warning btn-sm" title="Cambiar Clave">
                                <i class="fas fa-key"></i>
                            </a>

                            <?php if($usr['id'] != $_SESSION['id_usuario']): ?>
                                <a href="../controladores/ControladorUsuario.php?accion=eliminar&id=<?php echo $usr['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Seguro que desea eliminar este usuario permanentemente?');" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small ml-2">Tu cuenta</span>
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

