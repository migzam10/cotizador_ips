<?php
// controladores/ControladorUsuario.php
session_start();
require_once '../modelos/ModeloUsuario.php';

// Si no hay sesion, lo devolvemos al login
if (!isset($_SESSION['rol'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$modelo_usuario = new ModeloUsuario();

// ACCION: CREAR USUARIO (Solo Admin)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    
    if ($_SESSION['rol'] != 'admin') { die("Acceso denegado. Solo administradores."); }

    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];
    $rol = $_POST['rol'];

    $resultado = $modelo_usuario->crearUsuario($nombre, $usuario, $clave, $rol);

    if ($resultado === true) {
        header("Location: ../vistas/listar_usuarios.php?mensaje=creado");
    } else {
        header("Location: ../vistas/crear_usuario.php?mensaje=error");
    }
    exit();
}

// ACCION: ELIMINAR USUARIO (Solo Admin)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    
    if ($_SESSION['rol'] != 'admin') { die("Acceso denegado. Solo administradores."); }

    $id_eliminar = $_GET['id'];

    // Validacion para que el admin no se borre a si mismo
    if ($id_eliminar == $_SESSION['id_usuario']) {
        header("Location: ../vistas/listar_usuarios.php?mensaje=error_propio");
        exit();
    }

    $modelo_usuario->eliminarUsuario($id_eliminar);
    header("Location: ../vistas/listar_usuarios.php?mensaje=eliminado");
    exit();
}

// ACCION: CAMBIAR CLAVE (Admin o el propio usuario)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'cambiar_clave') {
    
    $id_modificar = $_POST['id_usuario'];
    $nueva_clave = $_POST['nueva_clave'];

    // Validamos que sea el Admin o que el usuario se este cambiando la clave a si mismo
    if ($_SESSION['rol'] == 'admin' || $_SESSION['id_usuario'] == $id_modificar) {
        
        $resultado = $modelo_usuario->cambiarClave($id_modificar, $nueva_clave);

        // Si es el admin cambiando a otro desde el panel, vuelve al panel
        if ($_SESSION['rol'] == 'admin' && isset($_POST['desde_admin'])) {
            header("Location: ../vistas/listar_usuarios.php?mensaje=clave_cambiada");
        } else {
            // Si es un usuario normal, va al inicio
            header("Location: ../vistas/listar_proveedores.php?mensaje=clave_actualizada");
        }

    } else {
        die("No tienes permisos para cambiar esta clave.");
    }
    exit();
}
?>