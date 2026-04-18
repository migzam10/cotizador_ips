<?php
// vistas/listar_proveedores.php

require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloProveedor.php';

$modelo = new ModeloProveedor();
$proveedores = $modelo->obtenerTodosLosProveedores();
?>

<h1 class="h3 mb-4 text-gray-800">Listado de Proveedores</h1>

<?php if(isset($_GET['mensaje'])): ?>
<?php if($_GET['mensaje'] == 'actualizado'): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Proveedor actualizado correctamente.
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
            aria-hidden="true">&times;</span></button>
</div>
<?php elseif($_GET['mensaje'] == 'estado_cambiado'): ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    El estado del proveedor ha sido modificado.
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
            aria-hidden="true">&times;</span></button>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Directorio de IPS Registradas</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display stripe mi-datatable" id="tablaProveedores" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIT</th>
                        <th>Nombre IPS</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($proveedores as $prov): ?>
                    <tr>
                        <td><?php echo $prov['id']; ?></td>
                        <td><?php echo $prov['nit']; ?></td>
                        <td><?php echo $prov['nombre_ips']; ?></td>
                        <td><?php echo $prov['nombre_ciudad']; ?></td>
                        <td class="text-center">
                            <?php if($prov['estado'] == 1): ?>
                            <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                            <a href="crear_proveedor.php?clonar=<?php echo $prov['id']; ?>"
                                class="btn btn-primary btn-sm shadow-sm" title="Clonar para crear nueva sucursal">
                                <i class="fas fa-copy"></i>
                            </a>
                            <a href="editar_proveedor.php?id=<?php echo $prov['id']; ?>" class="btn btn-warning btn-sm"
                                title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>

                            <?php if($_SESSION['rol'] == 'admin'): ?>
                            <?php if($prov['estado'] == 1): ?>
                            <a href="../controladores/ControladorProveedor.php?accion=cambiar_estado&id=<?php echo $prov['id']; ?>&estado=0"
                                class="btn btn-danger btn-sm" title="Desactivar"
                                onclick="return confirm('Seguro que desea desactivar este proveedor?');">
                                <i class="fas fa-ban"></i>
                            </a>
                            <?php else: ?>
                            <a href="../controladores/ControladorProveedor.php?accion=cambiar_estado&id=<?php echo $prov['id']; ?>&estado=1"
                                class="btn btn-success btn-sm" title="Activar"
                                onclick="return confirm('Seguro que desea activar este proveedor?');">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
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