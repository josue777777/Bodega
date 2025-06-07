<?php
session_start();
session_unset();   // Eliminar variables de sesión
session_destroy(); // Destruir la sesión
header("Location: /bodegas/Views/Login/login.php");
exit();
?>