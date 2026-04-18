<?php
// vistas/crear_usuario.php

require_once 'plantillas/encabezado.php';

// SEGURIDAD: Bloquear si no es admin
if($_SESSION['rol'] != 'admin') {
    echo '<div class="alert alert-danger mt-4">Acceso denegado.</div>';
    require_once 'plantillas/pie_pagina.php';
    exit();
}
?>

<h1 class="h3 mb-4 text-gray-800">Registrar Nuevo Usuario</h1>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'error'): ?>
    <div class="alert alert-danger">Error al crear el usuario. Es posible que el nombre de usuario ya exista.</div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Datos de la Cuenta</h6>
    </div>
    <div class="card-body">
        
        <form action="../controladores/ControladorUsuario.php" method="POST">
            <input type="hidden" name="accion" value="crear">
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nombre Completo *</label>
                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Juan Perez">
                </div>
                <div class="col-md-6 form-group">
                    <label>Nombre de Usuario (Login) *</label>
                    <input type="text" class="form-control" name="usuario" required placeholder="Ej: jperez">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Clave de Acceso *</label>
                    <input type="password" class="form-control" name="clave" required placeholder="Minimo 6 caracteres">
                </div>
                <div class="col-md-6 form-group">
                    <label>Rol en el Sistema *</label>
                    <select class="form-control" name="rol" required>
                        <option value="">Seleccione un rol...</option>
                        <option value="general">General (Puede editar, no puede borrar ni crear usuarios)</option>
                        <option value="visualizador">Visualizador (Solo lectura)</option>
                        <option value="admin">Administrador (Acceso total)</option>
                    </select>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Usuario</button>
            <a href="listar_usuarios.php" class="btn btn-secondary ml-2">Cancelar</a>
        </form>

    </div>
</div>

<?php require_once 'plantillas/pie_pagina.php'; ?>99