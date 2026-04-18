<?php
// vistas/listar_categorias.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCategoria.php';

$modelo = new ModeloCategoria();
$categorias = $modelo->obtenerTodasLasCategorias();
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Categorias de Examenes</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Categoria creada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Categoria actualizada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'estado_cambiado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Estado de la categoria modificado.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Categorias</h6>
        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
            <a href="crear_categoria.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nueva Categoria</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tablaCategorias" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de la Categoria</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categorias as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo $cat['nombre']; ?></td>
                        <td class="text-center">
                            <?php if($cat['estado'] == 1): ?>
                                <span class="badge badge-success">Activa</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                                <a href="editar_categoria.php?id=<?php echo $cat['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if($_SESSION['rol'] == 'admin'): ?>
                                <?php if($cat['estado'] == 1): ?>
                                    <a href="../controladores/ControladorCategoria.php?accion=cambiar_estado&id=<?php echo $cat['id']; ?>&estado=0" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea desactivar esta categoria?');"><i class="fas fa-ban"></i></a>
                                <?php else: ?>
                                    <a href="../controladores/ControladorCategoria.php?accion=cambiar_estado&id=<?php echo $cat['id']; ?>&estado=1" class="btn btn-success btn-sm" onclick="return confirm('¿Seguro que desea activar esta categoria?');"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="../controladores/ControladorCategoria.php?accion=eliminar&id=<?php echo $cat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar esta categoria?, ESTO ELIMINARÁ TODOS LOS EXÁMENES ASOCIADOS.');" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                 </a>
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

<script>
$(document).ready(function() {
    $('#tablaCategorias').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" }
    });
});
</script>