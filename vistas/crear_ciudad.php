<?php
// vistas/crear_ciudad.php
require_once 'plantillas/encabezado.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>
<h1 class="h3 mb-4 text-gray-800">Registrar Nueva Ciudad</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="../controladores/ControladorCiudad.php" method="POST">
            <div class="form-group">
                <label>Nombre de la Ciudad *</label>
                <input type="text" class="form-control" name="nombre" required placeholder="Ej: Barranquilla">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="listar_ciudades.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>
<?php require_once 'plantillas/pie_pagina.php'; ?>