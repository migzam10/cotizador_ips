<?php
// controladores/ControladorExamen.php
session_start();
require_once '../modelos/ModeloExamen.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$modelo_examen = new ModeloExamen();

// CREAR Y EDITAR (Permitido para admin y general)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_SESSION['rol'] == 'visualizador') {
        die("Acceso denegado.");
    }

    $nombre = trim($_POST['nombre']);
    $resultado = $modelo_examen->guardarExamen($id_categoria, $nombre);

    if (isset($_POST['id']) && !empty($_POST['id'])) {  
        // Editar
        $id = $_POST['id'];
        $resultado = $modelo_examen->guardarExamen($id_categoria, $nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_examenes.php?mensaje=actualizado");
        } else {
            header("Location: ../vistas/editar_examen.php?id=".$id."&mensaje=error");
        }
    } else {
        // Crear
        $resultado = $modelo_examen->guardarExamen($id_categoria, $nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_examenes.php?mensaje=guardado");
        } else {
            header("Location: ../vistas/crear_examen.php?mensaje=error");
        }
    }
    exit();
}

// CAMBIAR ESTADO (SOLO ADMIN)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'cambiar_estado') {
    
    if ($_SESSION['rol'] != 'admin') {
        die("Acceso denegado. Solo el administrador puede habilitar o deshabilitar examenes.");
    }

    $id = $_GET['id'];
    $estado = $_GET['estado'];
    
    $modelo_examen->cambiarEstadoExamen($id, $estado);
    header("Location: ../vistas/listar_examenes.php?mensaje=estado_cambiado");
    exit();
}

// ELIMINAR (SOLO ADMIN)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'eliminar') { 
    
    if ($_SESSION['rol'] != 'admin') {
        die("Acceso denegado. Solo el administrador puede eliminar examenes.");
    }

    $id = $_GET['id'];
    $modelo_examen->eliminarExamen($id);
    header("Location: ../vistas/listar_examenes.php?mensaje=eliminado");
    exit();
}
?>