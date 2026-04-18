<?php
// vistas/listar_ciudades.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCiudad.php';

$modelo = new ModeloCiudad();
$ciudades = $modelo->obtenerTodasLasCiudades();
?>

<h1 class="h3 mb-4 text-gray-800">Gestion de Ciudades</h1>

<?php if(isset($_GET['mensaje'])): ?>
    <?php if($_GET['mensaje'] == 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Ciudad registrada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php elseif($_GET['mensaje'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show">Ciudad actualizada correctamente.<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Listado de Ciudades de Cobertura</h6>
        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
            <a href="crear_ciudad.php" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Nueva Ciudad</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display stripe mi-datatable" id="tablaCiudades" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de la Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ciudades as $ciu): ?>
                    <tr>
                        <td><?php echo $ciu['id']; ?></td>
                        <td><?php echo $ciu['nombre']; ?></td>
                        <td>
                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                                <a href="editar_ciudad.php?id=<?php echo $ciu['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">Sin permisos</span>
                            <?php endif; ?>
                            <?php if($_SESSION['rol'] == 'admin'): ?>
                                <a href="../controladores/ControladorCiudad.php?eliminar=<?php echo $ciu['id']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Confirma que desea eliminar esta ciudad?');">
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


