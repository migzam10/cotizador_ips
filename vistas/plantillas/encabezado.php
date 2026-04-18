<?php
// vistas/plantillas/encabezado.php

session_start();

// Si no hay una sesion activa, lo redirigimos al login
if(!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>IPS</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="../assets/img/Logo.png" alt="Logo" width="100%">
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCotizador" aria-expanded="true" aria-controls="collapseCotizador">
                    <i class="fas fa-fw fa-calculator"></i>
                    <span>Cotizaciones</span>
                </a>
                <div id="collapseCotizador" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="cotizador.php">Nueva Cotizacion</a>
                        <a class="collapse-item" href="listar_cotizaciones.php">Historial Guardado</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Directorios</div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProveedores" aria-expanded="true" aria-controls="collapseProveedores">
                    <i class="fas fa-fw fa-building"></i>
                    <span>Proveedores</span>
                </a>
                <div id="collapseProveedores" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="listar_proveedores.php">Listado de IPS</a>
                        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                            <a class="collapse-item" href="crear_proveedor.php">Nuevo Proveedor</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExamenes" aria-expanded="true" aria-controls="collapseExamenes">
                    <i class="fas fa-fw fa-stethoscope"></i>
                    <span>Examenes</span>
                </a>
                <div id="collapseExamenes" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        
                        
                        <a class="collapse-item" href="listar_examenes.php">Listado de Examenes</a>
                        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                            <a class="collapse-item" href="crear_examen.php">Nuevo Examen</a>
                            <a class="collapse-item" href="listar_categorias.php">Categorias</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTarifas" aria-expanded="true" aria-controls="collapseTarifas">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                    <span>Tarifas</span>
                </a>
                <div id="collapseTarifas" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="listar_tarifas.php">Gestion de Tarifas</a>
<?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                        <a class="collapse-item" href="tarifas_proveedor.php">Gestion por Proveedor</a>
                        
                            <a class="collapse-item" href="crear_tarifa.php">Asignar Tarifa</a>
                            <a class="collapse-item" href="clonar_tarifas.php">Clonar y Aumentar</a>
                        <?php elseif($_SESSION['rol'] == 'visualizador'): ?>
                            <a class="collapse-item" href="tarifas_prov.php">Gestion por Proveedor</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCiudades" aria-expanded="true" aria-controls="collapseCiudades">
                    <i class="fas fa-fw fa-map-marker-alt"></i>
                    <span>Ciudades</span>
                </a>
                <div id="collapseCiudades" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="listar_ciudades.php">Listado de Ciudades</a>
                        <?php if($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'general'): ?>
                            <a class="collapse-item" href="crear_ciudad.php">Nueva Ciudad</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            

            <?php if($_SESSION['rol'] == 'admin'): ?>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">Seguridad</div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsuarios" aria-expanded="true" aria-controls="collapseUsuarios">
                        <i class="fas fa-fw fa-users-cog"></i>
                        <span>Usuarios</span>
                    </a>
                    <div id="collapseUsuarios" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="listar_usuarios.php">Gestionar Usuarios</a>
                        </div>
                    </div>
                </li>
            <?php endif; ?>

            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['nombre_usuario']; ?> 
                                    (<?php echo strtoupper($_SESSION['rol']); ?>)
                                </span>
                                <img class="img-profile rounded-circle" src="../assets/img/_logo.png">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="cambiar_clave.php">
                                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cambiar mi Clave
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../controladores/ControladorSalir.php" >
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesion
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <div class="container-fluid pt-2">