<?php
// Función para validar el inicio de sesión de un usuario
function ValidarInicioSesionModel($usuario, $clave)
{
    // Conectar a la base de datos (ajusta host, usuario y contraseña si cambian)
    $conexion = new mysqli("localhost", "usuario_bodega", "Clave_Bodega123.", "BodegasDataBase", 3307);

    // Verificar si hubo error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar consulta para obtener datos del usuario junto con su rol
    // Se busca por `username` o `correo`, y se une con la tabla `usuario_rol` y `rol`
    $stmt = $conexion->prepare("
        SELECT 
            u.id, 
            u.nombre, 
            u.username,
            u.correo, 
            u.password, 
            r.nombre AS rol
        FROM usuario u
        JOIN usuario_rol ur ON u.id = ur.usuario_id
        JOIN rol r ON ur.rol_id = r.id
        WHERE u.username = ? OR u.correo = ?
    ");

    // Asignar los valores al statement (doble uso porque se puede ingresar username o correo)
    $stmt->bind_param("ss", $usuario, $usuario);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Si se encuentra algún usuario con ese username o correo
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        // Verificar que la contraseña ingresada coincida con la contraseña encriptada en la base
        if (password_verify($clave, $row['password'])) {
            // Devolver los datos del usuario para usarlos en la sesión
            return [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'username' => $row['username'],
                'correo' => $row['correo'],
                'rol' => $row['rol']
            ];
        }
    }

    // Si no se encontró el usuario o la contraseña no coincide
    return null;
}
?>