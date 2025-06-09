<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["username"])) {
    header("Location: ../Login/login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Bodega</title>
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>

    <div id="supercontainer">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="logo-details">
                <img src="../../assets/img/logo.png" alt="Logo">
                <span class="logo_name">Mi Bodega</span>
            </div>

            <ul class="nav-links">
                <li><a href="../Home/home.php"><span class="link_name">Inicio</span></a></li>
                <li><a href="../Pages/kardex.php"><span class="link_name">Kardex</span></a></li>
                <li><a href="#"><span class="link_name">Inventarios</span></a></li>
                <li><a href="#"><span class="link_name">Configuración</span></a></li>
                <li><a href="#"><span class="link_name">Ayuda</span></a></li>
            </ul>

            <div class="profile-details">
                <img src="../../assets/img/user.png" alt="Usuario">
                <div class="name-job">
                    <div class="profile_name"><?= htmlspecialchars($_SESSION["nombre_usuario"]) ?></div>
                    <div class="job">Sistema Bodega</div>
                </div>
                <a href="../../Login/logout.php">Cerrar sesión</a>
            </div>
        </div>

        <!-- CONTENIDO -->
        <section class="home-section">
            <div class="home-content">
                <span class="text">Mi Bodega - Sistema de Inventario</span>
            </div>

            <?= $contenido ?>
        </section>
    </div>

</body>

</html>