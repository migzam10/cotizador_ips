<?php
// vistas/crear_categoria.php
require_once 'plantillas/encabezado.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>
<h1 class="h3 mb-4 text-gray-800">Registrar Categoria</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="../controladores/ControladorCategoria.php" method="POST">
            <div class="form-group">
                <label>Nombre de la Categoria *</label>
                <input type="text" class="form-control" name="nombre" required placeholder="Ej: Laboratorio Clinico">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Categoria</button>
            <a href="listar_categorias.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>
<?php require_once 'plantillas/pie_pagina.php'; ?>