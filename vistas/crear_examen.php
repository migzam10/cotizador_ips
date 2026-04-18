<?php
// vistas/crear_examen.php
require_once 'plantillas/encabezado.php';

require_once '../configuracion/Conexion.php';
$conexion = (new Conexion())->conectar();
$categorias = $conexion->query("SELECT * FROM categorias_examen WHERE estado = 1 ORDER BY nombre ASC")->fetchAll();

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>
<h1 class="h3 mb-4 text-gray-800">Registrar Examen</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="../controladores/ControladorExamen.php" method="POST">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Categoria del Examen *</label>
                    <select class="form-control" name="id_categoria" required>
                        <option value="">Seleccione una categoria...</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Nombre del Examen *</label>
                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Audiometria">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="listar_examenes.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>
<?php require_once 'plantillas/pie_pagina.php'; ?>