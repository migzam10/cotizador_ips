<?php
// vistas/listar_examenes.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloExamen.php';

$modelo = new ModeloExamen();
$examenes = $modelo->obtenerTodosLosExamenes();
?>

<h1 class="h3 mb-4 text-gray-800">Listado de Examenes</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Examen creado correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Examen actualizado correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'estado_cambiado'): ?>
        <div class="alert alert-info alert-dismissible fade show">Estado del examen modificado.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-danger alert-dismissible fade show">Examen eliminado.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">Ocurrió un error.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Procedimientos</h6>
        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
            <a href="crear_examen.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Examen</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display stripe mi-datatable" id="tablaExamenes" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="4%" class="text-center">ID</th>
                        <th width="70%">Nombre del Examen</th>
                        <th width="5%" class="text-center">Categoría</th>
                        <th width="5%" class="text-center">Estado</th>
                        <th width="10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($examenes as $ex): ?>
                    <tr>
                        <td><?php echo $ex['id']; ?></td>
                        <td><?php echo $ex['nombre']; ?></td>
                        <td class="text-center"><?php echo $ex['nombre_categoria']; ?></td>
                        <td class="text-center">
                            <?php if($ex['estado'] == 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                                <a href="editar_examen.php?id=<?php echo $ex['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if($_SESSION['rol'] == 'admin'): ?>
                                <?php if($ex['estado'] == 1): ?>
                                    <a href="../controladores/ControladorExamen.php?accion=cambiar_estado&id=<?php echo $ex['id']; ?>&estado=0" class="btn btn-danger btn-sm" onclick="return confirm('Seguro que desea desactivar?');"><i class="fas fa-ban"></i></a>
                                <?php else: ?>
                                    <a href="../controladores/ControladorExamen.php?accion=cambiar_estado&id=<?php echo $ex['id']; ?>&estado=1" class="btn btn-success btn-sm" onclick="return confirm('Seguro que desea activar?');"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="../controladores/ControladorExamen.php?accion=eliminar&id=<?php echo $ex['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este examen?');" title="Eliminar">
                                    <i class="fas fa-trash"></i>
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

