<?php
// Iniciar sesión (necesario para acceder a variables como $_SESSION)
session_start();

// Verificar si el usuario ha iniciado sesión
// Si no hay sesión activa, se redirige al formulario de login
if (!isset($_SESSION["username"])) {
  header("Location: ../Login/login.php");
  exit();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi Bodega</title>
  <link rel="icon" href="assets/img/favicon.ico" />
  <link rel="stylesheet" href="Proyecto\Views\assets\CSS\styles.css" />
</head>

<body>

  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Bodega</title>

    <!-- Ícono del navegador -->
    <link rel="icon" href="assets/img/favicon.ico" />

    <!-- Estilos personalizados (verifica la ruta si no se aplican bien) -->
    <link rel="stylesheet" href="../../Views/assets/CSS/styles.css" />
  </head>

  <body>

    <!-- Encabezado con mensaje de bienvenida y botón de cerrar sesión -->


    <header>
      <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre_usuario"]); ?>!</h1>
      <p>Has iniciado sesión correctamente en el sistema de bodegas.</p>
      <!-- Enlace para cerrar sesión -->
      <a href="../Login/logout.php">Cerrar sesión</a>
    </header>






    <div id="supercontainer">
      <div class="sidebar">
        <div class="logo-details">
          <!-- Logo de la app (puedes reemplazar por un .png real en assets/img) -->
          <img src="file-GVifn6SG2qG5wi5vtq9CHg" alt="Logo" />
          <span class="logo_name">Mi Bodega</span>
        </div>

        <!-- Menú lateral -->
        <ul class="nav-links">
          <li><a href="#"><span class="link_name">Inicio</span></a></li>
          <li><a href="kardex.html"><span class="link_name">Kardex</span></a></li>
          <li><a href="#"><span class="link_name">Inventarios</span></a></li>
          <li><a href="#"><span class="link_name">Configuración</span></a></li>
          <li><a href="#"><span class="link_name">Ayuda</span></a></li>
        </ul>

        <!-- Perfil de usuario (dinámico con $_SESSION) -->
        <!-- Perfil del usuario -->
        <div class="profile-details">
          <img src="file-JhNYnk3th5djzYbA6C439M" alt="Usuario" />
          <div class="name-job">
            <div class="profile_name"><?php echo htmlspecialchars($_SESSION["nombre_usuario"]); ?></div>
            <div class="job">
              <?php
              echo isset($_SESSION["correo_usuario"])
                ? htmlspecialchars($_SESSION["correo_usuario"])
                : "Correo no disponible";
              ?>
            </div>

          </div>
          <a href="/bodegas/logout.php">Cerrar sesión</a>
        </div>
      </div>



      <section class="home-section">
        <div class="home-content">
          <span class="text">Mi Bodega - Sistema de Inventario</span>
        </div>

        <div id="task">
          <h2>Bienvenido a Mi Bodega</h2>
          <p>Seleccione una opción del menú lateral para comenzar.</p>
        </div>
      </section>
    </div>
  </body>

  </html>