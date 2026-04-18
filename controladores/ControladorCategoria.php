<?php
// controladores/ControladorCategoria.php
session_start();
require_once '../modelos/ModeloCategoria.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$modelo_categoria = new ModeloCategoria();

// ACCION: CREAR Y EDITAR (Admin y General)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_SESSION['rol'] == 'visualizador') {
        die("Acceso denegado.");
    }

    $nombre = trim($_POST['nombre']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Editar
        $id = $_POST['id'];
        $resultado = $modelo_categoria->actualizarCategoria($id, $nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_categorias.php?mensaje=actualizado");
        } else {
            header("Location: ../vistas/editar_categoria.php?id=".$id."&mensaje=error");
        }
    } else {
        // Crear
        $resultado = $modelo_categoria->guardarCategoria($nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_categorias.php?mensaje=guardado");
        } else {
            header("Location: ../vistas/crear_categoria.php?mensaje=error");
        }
    }
    exit();
}

// ACCION: CAMBIAR ESTADO (Solo Admin)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'cambiar_estado') {
    
    if ($_SESSION['rol'] != 'admin') {
        die("Acceso denegado. Solo el administrador puede habilitar o deshabilitar.");
    }

    $id = $_GET['id'];
    $estado = $_GET['estado'];
    
    $modelo_categoria->cambiarEstadoCategoria($id, $estado);
    header("Location: ../vistas/listar_categorias.php?mensaje=estado_cambiado");
    exit();
}

// ACCION: ELIMINAR (Solo Admin)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    
    if ($_SESSION['rol'] != 'admin') {
        die("Acceso denegado. Solo el administrador puede eliminar.");
    }

    $id = $_GET['id'];
    $modelo_categoria->eliminarCategoria($id);
    header("Location: ../vistas/listar_categorias.php?mensaje=eliminado");
    exit();
}

?>