<?php
// controladores/ControladorSalir.php
session_start();

// Destruimos todas las variables de sesion
session_unset();
session_destroy();

// Redirigimos al login
header("Location: ../vistas/login.php");
exit();
?>