<?php
// controladores/ControladorCotizacion.php
session_start();
require_once '../modelos/ModeloCotizador.php';

// Verificamos permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 'visualizador') {
    echo json_encode(['exito' => false, 'mensaje' => 'Acceso denegado.']);
    exit();
}

// Leemos los datos enviados por AJAX en formato JSON
$json_recibido = file_get_contents('php://input');
$datos = json_decode($json_recibido, true);

if ($datos) {
    $cliente_nombre = trim($datos['cliente_nombre']);
    $cliente_nit = trim($datos['cliente_nit']);
    $matriz_detalles = $datos['detalles']; // Esto trae la lista de examenes y proveedores

    if (empty($matriz_detalles)) {
        echo json_encode(['exito' => false, 'mensaje' => 'La cotizacion esta vacia.']);
        exit();
    }

    $modelo = new ModeloCotizador();
    $resultado = $modelo->guardarCotizacionCompleta($cliente_nombre, $cliente_nit, $matriz_detalles);

    if ($resultado) {
        echo json_encode(['exito' => true, 'mensaje' => 'Cotizacion guardada correctamente en el historial.']);
    } else {
        echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos.']);
    }
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos validos.']);
}


// ACCION: ELIMINAR COTIZACION (Metodo GET)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    
    // El visualizador no puede borrar
    if ($_SESSION['rol'] == 'visualizador') {
        die("Acceso denegado.");
    }

    $id = $_GET['id'];
    
    // Llamamos al modelo correcto
    require_once '../modelos/ModeloCotizador.php';
    $modelo_cotizador = new ModeloCotizador();
    
    $resultado = $modelo_cotizador->eliminarCotizacion($id);
    
    if ($resultado) {
        header("Location: ../vistas/listar_cotizaciones.php?mensaje=eliminado");
    } else {
        header("Location: ../vistas/listar_cotizaciones.php?mensaje=error");
    }
    exit();
}
?>
