<?php
// controladores/ControladorTarifa.php
session_start();
require_once '../modelos/ModeloTarifa.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../vistas/login.php");
    exit();
}

// Visualizadores no pueden hacer cambios
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['rol'] == 'visualizador') {
    die("Acceso denegado.");
}

$modelo_tarifa = new ModeloTarifa();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $accion = $_POST['accion'];

    // ACCION: CREAR TARIFA NUEVA
    if ($accion == 'crear') {
        
        // CREAR NUEVA TARIFA
        $id_proveedor = $_POST['id_proveedor'];
        $id_examen = $_POST['id_examen'];
        $anio = $_POST['anio'];
        $precio_costo = $_POST['precio_costo'];
        $precio_venta = $_POST['precio_venta'];

        // 1. Validar si ya existe
        if ($modelo_tarifa->verificarTarifaDuplicada($id_proveedor, $id_examen, $anio)) {
            // Si existe, lo devolvemos con mensaje de duplicado
            header("Location: ../vistas/crear_tarifa.php?mensaje=duplicado");
            exit();
        }

        // 2. Si no existe, procedemos a guardar
        $resultado = $modelo_tarifa->guardarTarifa($id_proveedor, $id_examen, $anio, $precio_costo, $precio_venta);
        
        // 3. AQUI ESTA EL EXITO: Redirigimos con el mensaje de guardado
        if ($resultado) {
            header("Location: ../vistas/listar_tarifas.php?mensaje=guardado");
        } else {
            header("Location: ../vistas/listar_tarifas.php?mensaje=error");
        }
        exit();
    
    }

    // ACCION: EDITAR TARIFA EXISTENTE
    if ($accion == 'editar') {
        $id = $_POST['id'];
        $precio_costo = $_POST['precio_costo'];
        $precio_venta = $_POST['precio_venta'];

        $resultado = $modelo_tarifa->actualizarTarifa($id, $precio_costo, $precio_venta);
        
        if ($resultado) {
            header("Location: ../vistas/listar_tarifas.php?mensaje=actualizado");
        } else {
            header("Location: ../vistas/listar_tarifas.php?mensaje=error");
        }
        exit();
    }

    // ACCION: CLONAR TARIFA POR PORCENTAJE
   if ($accion == 'clonar') {
        $id_proveedor = $_POST['id_proveedor'];
        $anio_base = $_POST['anio_base'];
        $anio_nuevo = $_POST['anio_nuevo'];
        $porcentaje = $_POST['porcentaje'];
        
        // Saber de que pantalla viene para devolverlo a la misma
        $origen = isset($_POST['origen']) ? $_POST['origen'] : 'general'; 

        // 1. Verificamos si ya hay tarifas creadas para ese nuevo anio
        if ($modelo_tarifa->verificarTarifasExistentes($id_proveedor, $anio_nuevo)) {
            $ruta = ($origen == 'proveedor') ? "../vistas/tarifas_proveedor.php?id_proveedor=$id_proveedor&anio=$anio_base&mensaje=error_existe" : "../vistas/clonar_tarifas.php?mensaje=error_existe";
            header("Location: " . $ruta);
            exit();
        }

        // 2. Si no existen, procedemos a clonar
        $resultado = $modelo_tarifa->clonarTarifasPorcentaje($id_proveedor, $anio_base, $anio_nuevo, $porcentaje);
        
        if ($resultado === "vacio") {
            $ruta = ($origen == 'proveedor') ? "../vistas/tarifas_proveedor.php?id_proveedor=$id_proveedor&anio=$anio_base&mensaje=vacio" : "../vistas/clonar_tarifas.php?mensaje=vacio";
        } elseif ($resultado === true) {
            // Si viene de la vista especifica, lo mandamos a ver el nuevo anio clonado
            $ruta = ($origen == 'proveedor') ? "../vistas/tarifas_proveedor.php?id_proveedor=$id_proveedor&anio=$anio_nuevo&mensaje=clonado" : "../vistas/listar_tarifas.php?mensaje=clonado";
        } else {
            $ruta = ($origen == 'proveedor') ? "../vistas/tarifas_proveedor.php?id_proveedor=$id_proveedor&anio=$anio_base&mensaje=error" : "../vistas/clonar_tarifas.php?mensaje=error";
        }
        header("Location: " . $ruta);
        exit();
    }

    // ACCION NUEVA: CLONAR HACIA OTRA IPS
    if ($accion == 'clonar_proveedor') {
        $id_prov_origen = $_POST['id_proveedor_origen'];
        $anio_origen = $_POST['anio_origen'];
        
        $id_prov_destino = $_POST['id_proveedor_destino'];
        $anio_destino = $_POST['anio_destino'];
        $porcentaje = isset($_POST['porcentaje']) && $_POST['porcentaje'] !== '' ? $_POST['porcentaje'] : 0;

        // 1. Verificamos que la IPS destino no tenga tarifas ya creadas en ese ano para no duplicarlas
        if ($modelo_tarifa->verificarTarifasExistentes($id_prov_destino, $anio_destino)) {
            header("Location: ../vistas/tarifas_proveedor.php?id_proveedor=$id_prov_origen&anio=$anio_origen&mensaje=error_existe_dest");
            exit();
        }

        // 2. Ejecutamos la copia masiva
        $resultado = $modelo_tarifa->clonarTarifasAProveedor($id_prov_origen, $anio_origen, $id_prov_destino, $anio_destino, $porcentaje);
        
        if ($resultado === "vacio") {
            header("Location: ../vistas/tarifas_proveedor.php?id_proveedor=$id_prov_origen&anio=$anio_origen&mensaje=vacio");
        } elseif ($resultado === true) {
            // ¡Exito! Lo mandamos a ver la nueva IPS que acaba de recibir la clonacion
            header("Location: ../vistas/tarifas_proveedor.php?id_proveedor=$id_prov_destino&anio=$anio_destino&mensaje=clonado_prov");
        } else {
            header("Location: ../vistas/tarifas_proveedor.php?id_proveedor=$id_prov_origen&anio=$anio_origen&mensaje=error");
        }
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'eliminar') {
    if ($_SESSION['rol'] == 'visualizador') {
        die("Acceso denegado.");
    }

    $id = $_GET['id'];
    $id_prov = $_GET['id_prov'];
    $anio = $_GET['anio'];

    $modelo_tarifa->eliminarTarifa($id);
    
    // Lo devolvemos a la misma pagina filtrada
    header("Location: ../vistas/listar_tarifas.php?mensaje=eliminado");
    exit();
}
?>