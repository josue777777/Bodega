<?php
// Iniciar sesión para poder trabajar con variables de sesión (por ejemplo, mensajes)
session_start();

// Si hay un mensaje de alerta (por ejemplo: usuario registrado, error de login, etc.)
if (isset($_SESSION["mensaje"])) {
    echo "<script>alert('{$_SESSION["mensaje"]}');</script>";
    unset($_SESSION["mensaje"]); // Limpiar el mensaje para que no se repita
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login y Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Ruta al archivo de estilos CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="contenedor" id="contenedor">
        <!-- Panel Izquierdo: Contiene ambos formularios -->
        <div class="panel izquierdo">
            <!-- Formulario de inicio de sesión -->
            <form class="formulario login" id="form-login" method="POST" action="../../Controllers/loginController.php">
                <h2>Iniciar Sesión</h2>
                <!-- Campo para ingresar el nombre de usuario o correo -->
                <input type="text" name="txtNombreUsuario" placeholder="Usuario o correo" required>
                <!-- Campo para ingresar la contraseña -->
                <input type="password" name="txtContrasenna" placeholder="Contraseña" required>
                <!-- Botón para enviar el formulario de login -->
                <button type="submit" name="btnIniciarSesion">Entrar</button>
            </form>

            <!-- Formulario de registro de nuevos usuarios -->
            <form class="formulario registro" id="form-register" method="POST"
                action="../../Controllers/loginController.php">
                <h2>Registrarse</h2>
                <!-- Campo para el nombre completo -->
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <!-- Campo para el correo electrónico -->
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <!-- Campo para el nombre de usuario -->
                <input type="text" name="usuario" placeholder="Usuario" required>
                <!-- Campo para la contraseña -->
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <!-- Botón para enviar el formulario de registro -->
                <button type="submit" name="btnRegistro">Registrarse</button>
            </form>
        </div>

        <!-- Panel Derecho: Mensaje y botón para alternar entre formularios -->
        <div class="panel derecho">
            <h3 id="titulo-panel">¿Aún no tienes una cuenta?</h3>
            <p id="texto-panel">Regístrate para que puedas iniciar sesión</p>
            <!-- Botón que alterna entre login y registro -->
            <button id="btn-cambiar">Registrarse</button>
        </div>
    </div>

    <!-- Script JavaScript para alternar entre formularios -->
    <script>
        const contenedor = document.getElementById('contenedor');
        const btnCambiar = document.getElementById('btn-cambiar');
        const formLogin = document.getElementById('form-login');
        const formRegister = document.getElementById('form-register');
        const titulo = document.getElementById('titulo-panel');
        const texto = document.getElementById('texto-panel');

        let mostrandoLogin = true; // Estado actual del formulario visible

        btnCambiar.addEventListener('click', () => {
            mostrandoLogin = !mostrandoLogin;

            if (mostrandoLogin) {
                // Mostrar login y ocultar registro
                contenedor.classList.remove('mover');
                formLogin.style.display = 'flex';
                formRegister.style.display = 'none';
                btnCambiar.innerText = 'Registrarse';
                titulo.innerText = '¿Aún no tienes una cuenta?';
                texto.innerText = 'Regístrate para que puedas iniciar sesión';
            } else {
                // Mostrar registro y ocultar login
                contenedor.classList.add('mover');
                formLogin.style.display = 'none';
                formRegister.style.display = 'flex';
                btnCambiar.innerText = 'Iniciar Sesión';
                titulo.innerText = '¿Ya tienes una cuenta?';
                texto.innerText = 'Inicia sesión para acceder';
            }
        });

        // Mostrar solo el formulario de login al cargar
        window.onload = () => {
            formLogin.style.display = 'flex';
            formRegister.style.display = 'none';
        };
    </script>
</body>

</html>