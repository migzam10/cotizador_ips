<?php
// controladores/ControladorTarifaAjax.php
session_start();
require_once '../modelos/ModeloTarifa.php';

// Proteccion basica
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 'visualizador') {
    echo json_encode(['exito' => false, 'mensaje' => 'Acceso denegado']);
    exit();
}

$modelo = new ModeloTarifa();
$accion = $_POST['accion'] ?? '';

if ($accion == 'guardar') {
    $id_prov = $_POST['id_proveedor'];
    $id_ex = $_POST['id_examen'];
    $anio = $_POST['anio'];
    $costo = $_POST['costo'];
    $venta = $_POST['venta'];

    $resultado = $modelo->guardarOActualizarTarifa($id_prov, $id_ex, $anio, $costo, $venta);
    echo json_encode(['exito' => $resultado]);
    exit();
}

if ($accion == 'eliminar') {
    $id_prov = $_POST['id_proveedor'];
    $id_ex = $_POST['id_examen'];
    $anio = $_POST['anio'];

    $resultado = $modelo->eliminarTarifaParametros($id_prov, $id_ex, $anio);
    echo json_encode(['exito' => $resultado]);
    exit();
}

echo json_encode(['exito' => false, 'mensaje' => 'Accion no valida']);
?>