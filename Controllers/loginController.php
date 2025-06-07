<?php
// Iniciar sesión si no está iniciada (importante para trabajar con $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Si se presionó el botón "Entrar" del formulario de login
if (isset($_POST["btnIniciarSesion"])) {
    // Obtener valores ingresados por el usuario
    $usuario = $_POST["txtNombreUsuario"];     // Puede ser username o correo
    $contrasena = $_POST["txtContrasenna"];    // Contraseña en texto plano

    // Incluir el modelo que contiene la función de validación
    require_once '../Models/loginModel.php';

    // Validar credenciales (devuelve los datos si son válidos o null si fallan)
    $datosUsuario = ValidarInicioSesionModel($usuario, $contrasena);

    if ($datosUsuario) {
        // Guardar los datos del usuario autenticado en variables de sesión
        $_SESSION["id_usuario"] = $datosUsuario['id'];
        $_SESSION["nombre_usuario"] = $datosUsuario['nombre'];
        $_SESSION["username"] = $datosUsuario['username'];
        $_SESSION["correo_usuario"] = $datosUsuario['correo'];
        $_SESSION["rol"] = $datosUsuario['rol'];



        // Mensaje opcional de éxito
        echo "Login exitoso. Redirigiendo...";
        sleep(2); // Pausa de 2 segundos antes de redirigir

        // Redirigir al dashboard (home.php)
        header("Location: /bodegas/Views/Home/home.php");
        exit();
    } else {
        // Si las credenciales son inválidas, guardar mensaje de error y redirigir
        $_SESSION["mensaje"] = "Su información no es válida";
        header("Location: /bodegas/Views/Login/login.php");
        exit();
    }
}


// Si se presionó el botón "Registrarse" del formulario de registro
if (isset($_POST["btnRegistro"])) {
    // Obtener valores del formulario
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $usuario = $_POST["usuario"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Conexión a la base de datos (ajustar si cambian las credenciales)
    $conexion = new mysqli("localhost", "usuario_bodega", "Clave_Bodega123.", "BodegasDataBase", 3307);

    // Verificar si hay error de conexión
    if ($conexion->connect_error) {
        die("❌ Error de conexión: " . $conexion->connect_error);
    }

    // Verificar que el username o correo no estén ya registrados
    $verificar = $conexion->prepare("SELECT id FROM usuario WHERE username = ? OR correo = ?");
    $verificar->bind_param("ss", $usuario, $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        // Si ya existe el correo o username
        $_SESSION["mensaje"] = "El nombre de usuario o correo ya está en uso.";
    } else {
        // Insertar nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO usuario(nombre, correo, username, password) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $usuario, $contrasena);

        if ($stmt->execute()) {
            $idInsertado = $conexion->insert_id; // Obtener el ID del usuario recién creado

            // Asignar rol por defecto (1 = USER)
            $rol = 1;
            $stmtRol = $conexion->prepare("INSERT INTO usuario_rol (usuario_id, rol_id) VALUES (?, ?)");
            $stmtRol->bind_param("ii", $idInsertado, $rol);
            $stmtRol->execute();

            $_SESSION["mensaje"] = "Usuario registrado con éxito";
        } else {
            $_SESSION["mensaje"] = "Error al registrar usuario";
        }
    }

    // Redirigir nuevamente al formulario de login
    header("Location: /bodegas/Views/Login/login.php");
    exit();
}
?>