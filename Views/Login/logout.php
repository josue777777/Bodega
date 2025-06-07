<?php
session_start();             // Inicia la sesión
session_unset();             // Elimina todas las variables de sesión
session_destroy();           // Destruye la sesión

// Redirige al login
header("Location: /bodegas/Views/Login/login.php");
exit();
