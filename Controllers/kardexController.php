<?php
// ====================================
// 0. Conexión a la base de datos
// ====================================
function obtenerConexion()
{
    $conexion = new mysqli("localhost", "usuario_bodega", "Clave_Bodega123.", "BODEGAS_DB", 3307);
    if ($conexion->connect_error) {
        die("❌ Error de conexión: " . $conexion->connect_error);
    }
    return $conexion;
}

// ====================================
// 1. Mostrar movimientos del Kardex
// ====================================
function mostrarHistorialKardex($imprimir = true, $codigoProducto)
{
    $saldo = 0;
    $conn = obtenerConexion();

    $stmt = $conn->prepare("
        SELECT k.* 
        FROM kardex k 
        INNER JOIN producto p ON k.producto_id = p.id 
        WHERE p.codigo = ? 
        ORDER BY k.fecha ASC, k.id ASC
    ");
    $stmt->bind_param("s", $codigoProducto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $filas = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $entrada = $row['tipo'] === 'entrada' ? $row['cantidad'] : 0;
            $salida = $row['tipo'] === 'salida' ? $row['cantidad'] : 0;
            $saldo += $entrada - $salida;

            $filas[] = [
                'fecha' => $row['fecha'],
                'entrada' => $entrada,
                'salida' => $salida,
                'saldo' => $saldo,
                'descripcion' => $row['descripcion'],
                'empresa' => $row['empresa'] ?? '-'
            ];
        }

        if ($imprimir) {
            foreach ($filas as $row) {
                echo "<tr>";
                echo "<td>{$row['fecha']}</td>";
                echo "<td>{$row['entrada']}</td>";
                echo "<td>{$row['salida']}</td>";
                echo "<td>{$row['saldo']}</td>";
                echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                echo "<td>{$row['empresa']}</td>";
                echo "</tr>";
            }
        }
    } elseif ($imprimir) {
        echo "<tr><td colspan='6'>⚠️ Producto no encontrado para el código: <b>" . htmlspecialchars($codigoProducto) . "</b></td></tr>";
    }

    $conn->close();
    return $saldo;
}

// ====================================
// 2. Utilidades para obtener datos
// ====================================
function obtenerProductoIdPorCodigo($codigo)
{
    $conn = obtenerConexion();
    $stmt = $conn->prepare("SELECT id FROM producto WHERE codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->bind_result($id);
    $idObtenido = $stmt->fetch() ? $id : null;
    $stmt->close();
    $conn->close();
    return $idObtenido;
}

function obtenerOLoteId($producto_id, $numero_lote, $fecha_vencimiento)
{
    $conn = obtenerConexion();
    $stmt = $conn->prepare("SELECT id FROM lote WHERE producto_id = ? AND numero_lote = ?");
    $stmt->bind_param("is", $producto_id, $numero_lote);
    $stmt->execute();
    $stmt->bind_result($id);
    if ($stmt->fetch()) {
        $stmt->close();
        $conn->close();
        return $id;
    }

    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO lote (producto_id, numero_lote, fecha_vencimiento) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $producto_id, $numero_lote, $fecha_vencimiento);
    $stmt->execute();
    $nuevo_id = $conn->insert_id;
    $stmt->close();
    $conn->close();
    return $nuevo_id;
}

function obtenerProductoCompleto($codigo)
{
    $conn = obtenerConexion();
    $stmt = $conn->prepare("SELECT descripcion, unidad_medida FROM producto WHERE codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->bind_result($descripcion, $unidad);
    $producto = $stmt->fetch() ? ['descripcion' => $descripcion, 'unidad_medida' => $unidad] : null;
    $stmt->close();
    $conn->close();
    return $producto;
}

function obtenerConfigStock($codigo)
{
    $conn = obtenerConexion();
    $stmt = $conn->prepare("
        SELECT kc.stock_minimo, kc.stock_maximo 
        FROM kardex_config kc 
        INNER JOIN producto p ON kc.producto_id = p.id 
        WHERE p.codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->bind_result($minimo, $maximo);
    $config = $stmt->fetch() ? ['minimo' => $minimo, 'maximo' => $maximo] : null;
    $stmt->close();
    $conn->close();
    return $config;
}

// ====================================
// 3. Registrar movimiento completo
// ====================================
function registrarMovimientoCompleto($datos)
{
    $conn = obtenerConexion();
    $stmt = $conn->prepare("
        INSERT INTO kardex 
        (producto_id, lote_id, fecha, tipo, cantidad, descripcion, empresa) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iississ",
        $datos['producto_id'],
        $datos['lote_id'],
        $datos['fecha'],
        $datos['tipo'],
        $datos['cantidad'],
        $datos['descripcion'],
        $datos['empresa']
    );
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

// ====================================
// 4. Enrutamiento para POST
// ====================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    // Guardar configuración de stock
    if (isset($_POST['actualizar_config_stock'])) {
        $codigo = $_POST['codigo'];
        $min = $_POST['stock_minimo'];
        $max = $_POST['stock_maximo'];
        $conn = obtenerConexion();
        $producto_id = obtenerProductoIdPorCodigo($codigo);
        $stmt = $conn->prepare("INSERT INTO kardex_config (producto_id, stock_minimo, stock_maximo)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE stock_minimo = VALUES(stock_minimo), stock_maximo = VALUES(stock_maximo)");
        $stmt->bind_param("iii", $producto_id, $min, $max);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("Location: ../Views/Pages/kardex.php?codigo=" . urlencode($codigo));
        exit();
    }

    // Registrar movimiento solo si hay datos requeridos
    if (isset($_POST['fecha'], $_POST['tipo'], $_POST['cantidad'], $_POST['descripcion'])) {
        $codigo = $_POST['codigo'];
        $producto_id = obtenerProductoIdPorCodigo($codigo);

        if (!$producto_id) {
            die("❌ Error: Producto con código '$codigo' no encontrado.");
        }

        $numero_lote = $_POST['numero_lote'] ?? null;
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
        $lote_id = !empty($numero_lote) ? obtenerOLoteId($producto_id, $numero_lote, $fecha_vencimiento) : null;

        $empresa = !empty($_POST['empresa']) ? $_POST['empresa'] : null;

        $datos = [
            'producto_id' => $producto_id,
            'lote_id' => $lote_id,
            'fecha' => $_POST['fecha'],
            'tipo' => $_POST['tipo'],
            'cantidad' => (int) $_POST['cantidad'],
            'descripcion' => $_POST['descripcion'],
            'empresa' => $empresa
        ];

        if (registrarMovimientoCompleto($datos)) {
            header("Location: ../Views/Pages/kardex.php?codigo=" . urlencode($codigo));
            exit();
        } else {
            echo "❌ Error al registrar el movimiento.";
        }
    }
}

// ====================================
// 5. Vista previa desde GET
// ====================================
if (isset($_GET['mostrar']) && isset($_GET['codigo'])) {
    mostrarHistorialKardex(true, $_GET['codigo']);
    exit();
}
