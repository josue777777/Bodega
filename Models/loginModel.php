<?php
// Función para validar el inicio de sesión de un usuario
function ValidarInicioSesionModel($usuario, $clave)
{
    // Conectar a la base de datos actualizada
    $conexion = new mysqli("localhost", "usuario_bodega", "Clave_Bodega123.", "BODEGAS_DB", 3307);

    // Verificar si hubo error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Preparar consulta para obtener datos del usuario junto con su rol
    $stmt = $conexion->prepare("
        SELECT 
            U.ID, 
            U.NOMBRE, 
            U.USERNAME,
            U.CORREO, 
            U.PASSWORD, 
            R.NOMBRE AS ROL
        FROM USUARIO U
        JOIN USUARIO_ROL UR ON U.ID = UR.USUARIO_ID
        JOIN ROL R ON UR.ROL_ID = R.ID
        WHERE U.USERNAME = ? OR U.CORREO = ?
    ");

    // Asignar los valores al statement
    $stmt->bind_param("ss", $usuario, $usuario);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Si se encuentra algún usuario
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($clave, $row['PASSWORD'])) {
            // Devolver los datos del usuario para la sesión
            return [
                'id' => $row['ID'],
                'nombre' => $row['NOMBRE'],
                'username' => $row['USERNAME'],
                'correo' => $row['CORREO'],
                'rol' => $row['ROL']
            ];
        }
    }

    // Si no se encuentra o contraseña incorrecta
    return null;
}
?>