<?php
// controladores/ControladorLogin.php
session_start();
require_once '../modelos/ModeloUsuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    $modelo_usuario = new ModeloUsuario();
    $datos_usuario = $modelo_usuario->obtenerUsuarioPorCredencial($usuario);

    // Verificamos si el usuario existe y si la clave ingresada coincide con el hash guardado
    if ($datos_usuario && password_verify($clave, $datos_usuario['clave'])) {
        
        // Si todo esta correcto, creamos las variables de sesion
        $_SESSION['id_usuario'] = $datos_usuario['id'];
        $_SESSION['nombre_usuario'] = $datos_usuario['nombre'];
        $_SESSION['rol'] = $datos_usuario['rol'];

        // Redirigimos al modulo de proveedores (nuestra pagina principal por ahora)
        header("Location: ../vistas/index.php");
        exit();
        
    } else {
        // Si falla, lo devolvemos al login con un mensaje de error
        header("Location: ../vistas/login.php?mensaje=error");
        exit();
    }
}
?>