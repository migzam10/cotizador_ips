<?php

session_start();

// Si un usuario general o visualizador intenta desactivar un proveedor, lo bloqueamos
if ($_GET['accion'] == 'cambiar_estado') {
    if ($_SESSION['rol'] != 'admin') {
        die("No tienes permisos para realizar esta accion.");
    }
}
// controladores/ControladorProveedor.php

require_once '../modelos/ModeloProveedor.php';
$modelo_proveedor = new ModeloProveedor();

// Manejo de peticiones POST (Crear y Editar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nit = trim($_POST['nit']);
    $nombre_ips = trim($_POST['nombre_ips']);
    $id_ciudad = $_POST['id_ciudad'];
    $direccion = trim($_POST['direccion']);
    $telefonos = trim($_POST['telefonos']);
    $nombre_contacto = trim($_POST['nombre_contacto']);
    $correos = trim($_POST['correos']);
    $observaciones = trim($_POST['observaciones']);
    $enlace_conceptos = trim($_POST['enlace_conceptos']);
    $usu = trim($_POST['usu']);
    $password = trim($_POST['password']);
    $tipo_cuenta = trim($_POST['tipo_cuenta']);
    $banco = trim($_POST['banco']);
    $numero_cuenta = trim($_POST['numero_cuenta']);

    // Si el formulario envia un ID, significa que es una actualizacion
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $resultado = $modelo_proveedor->actualizarProveedor($id, $id_ciudad, $nit, $nombre_ips, $direccion, $telefonos, $nombre_contacto, $correos, $observaciones, $enlace_conceptos, $usu, $password, $tipo_cuenta, $banco, $numero_cuenta);
        
        if ($resultado === true) {
            header("Location: ../vistas/listar_proveedores.php?mensaje=actualizado");
        } else {
            header("Location: ../vistas/editar_proveedor.php?id=".$id."&mensaje=error");
        }
        exit();

    } else {
        // Si no hay ID, es un registro nuevo (Crear)
        $resultado = $modelo_proveedor->guardarProveedor($id_ciudad, $nit, $nombre_ips, $direccion, $telefonos, $nombre_contacto, $correos, $observaciones, $enlace_conceptos, $usu, $password, $tipo_cuenta, $banco, $numero_cuenta);
        
        if ($resultado === true) {
            header("Location: ../vistas/crear_proveedor.php?mensaje=guardado");
        } else {
            header("Location: ../vistas/crear_proveedor.php?mensaje=error");
        }
        exit();
    }
}

// Manejo de peticiones GET (Cambiar estado)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['accion']) && $_GET['accion'] == 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
        
        $id = $_GET['id'];
        $nuevo_estado = $_GET['estado']; // 1 para activar, 0 para desactivar
        
        $modelo_proveedor->cambiarEstadoProveedor($id, $nuevo_estado);
        
        header("Location: ../vistas/listar_proveedores.php?mensaje=estado_cambiado");
        exit();
    }
}
?>