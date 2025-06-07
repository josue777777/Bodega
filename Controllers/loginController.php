<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si se presionó el botón "Entrar"
if (isset($_POST["btnIniciarSesion"])) {
    $usuario = $_POST["txtNombreUsuario"];
    $contrasena = $_POST["txtContrasenna"];

    require_once '../Models/loginModel.php';
    $datosUsuario = ValidarInicioSesionModel($usuario, $contrasena);

    if ($datosUsuario) {
        $_SESSION["id_usuario"] = $datosUsuario['id'];
        $_SESSION["nombre_usuario"] = $datosUsuario['nombre'];
        $_SESSION["username"] = $datosUsuario['username'];
        $_SESSION["correo_usuario"] = $datosUsuario['correo'];
        $_SESSION["rol"] = $datosUsuario['rol'];

        echo "Login exitoso. Redirigiendo...";
        sleep(2);
        header("Location: /bodegas/Views/Home/home.php");
        exit();
    } else {
        $_SESSION["mensaje"] = "Su información no es válida";
        header("Location: /bodegas/Views/Login/login.php");
        exit();
    }
}

// Si se presionó el botón "Registrarse"
if (isset($_POST["btnRegistro"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $usuario = $_POST["usuario"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);

    $conexion = new mysqli("localhost", "usuario_bodega", "Clave_Bodega123.", "BODEGAS_DB", 3307);
    if ($conexion->connect_error) {
        die("❌ Error de conexión: " . $conexion->connect_error);
    }

    // Verificar existencia
    $verificar = $conexion->prepare("SELECT ID FROM USUARIO WHERE USERNAME = ? OR CORREO = ?");
    $verificar->bind_param("ss", $usuario, $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION["mensaje"] = "El nombre de usuario o correo ya está en uso.";
    } else {
        // Insertar usuario
        $stmt = $conexion->prepare("INSERT INTO USUARIO (NOMBRE, CORREO, USERNAME, PASSWORD) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contrasena);

        if ($stmt->execute()) {
            $idInsertado = $conexion->insert_id;

            // Rol por defecto (1 = USER)
            $rol = 1;
            $stmtRol = $conexion->prepare("INSERT INTO USUARIO_ROL (USUARIO_ID, ROL_ID) VALUES (?, ?)");
            $stmtRol->bind_param("ii", $idInsertado, $rol);
            $stmtRol->execute();

            $_SESSION["mensaje"] = "Usuario registrado con éxito";
        } else {
            $_SESSION["mensaje"] = "Error al registrar usuario";
        }
    }

    header("Location: /bodegas/Views/Login/login.php");
    exit();
}
?>