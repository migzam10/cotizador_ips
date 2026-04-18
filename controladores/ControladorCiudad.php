<?php
// controladores/ControladorCiudad.php
session_start();
require_once '../modelos/ModeloCiudad.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../vistas/login.php");
    exit();
}

$modelo_ciudad = new ModeloCiudad();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_SESSION['rol'] == 'visualizador') {
        die("Acceso denegado.");
    }

    $nombre = trim($_POST['nombre']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Editar
        $id = $_POST['id'];
        $resultado = $modelo_ciudad->actualizarCiudad($id, $nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_ciudades.php?mensaje=actualizado");
        } else {
            header("Location: ../vistas/editar_ciudad.php?id=".$id."&mensaje=error");
        }
    } else {
        // Crear
        $resultado = $modelo_ciudad->guardarCiudad($nombre);
        if ($resultado) {
            header("Location: ../vistas/listar_ciudades.php?mensaje=guardado");
        } else {
            header("Location: ../vistas/crear_ciudad.php?mensaje=error");
        }
    }
    exit();
}
// Eliminar
if (isset($_GET['eliminar']) && $_SESSION['rol'] != 'visualizador' && $_SESSION['rol'] != 'general') {
    $id = $_GET['eliminar'];
    $resultado = $modelo_ciudad->eliminarCiudad($id);
    if ($resultado) {
        header("Location: ../vistas/listar_ciudades.php?mensaje=eliminado");
    } else {
        header("Location: ../vistas/listar_ciudades.php?mensaje=error");
    }
    exit();
}
?>