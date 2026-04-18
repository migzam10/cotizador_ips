<?php
// vistas/editar_categoria.php
require_once 'plantillas/encabezado.php';
require_once '../modelos/ModeloCategoria.php';

if($_SESSION['rol'] == 'visualizador') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}

$id = $_GET['id'];
$modelo = new ModeloCategoria();
$datos = $modelo->obtenerCategoriaPorId($id);
?>
<h1 class="h3 mb-4 text-gray-800">Editar Categoria</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="../controladores/ControladorCategoria.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $datos['id']; ?>">
            <div class="form-group">
                <label>Nombre de la Categoria *</label>
                <input type="text" class="form-control" name="nombre" required value="<?php echo $datos['nombre']; ?>">
            </div>
            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Actualizar Categoria</button>
            <a href="listar_categorias.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>
    </div>
</div>
<?php require_once 'plantillas/pie_pagina.php'; ?>