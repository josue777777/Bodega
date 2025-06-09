<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../Login/login.php");
    exit();
}

require_once __DIR__ . '/../../Controllers/kardexController.php';

$codigoSeleccionado = $_GET['codigo'] ?? null;
$productoSeleccionado = $codigoSeleccionado ? obtenerProductoCompleto($codigoSeleccionado) : null;
$descripcionProducto = $productoSeleccionado['descripcion'] ?? null;
$unidadMedida = $productoSeleccionado['unidad_medida'] ?? null;
$config = $codigoSeleccionado ? obtenerConfigStock($codigoSeleccionado) : ['minimo' => '', 'maximo' => ''];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kardex - Mi Bodega</title>
    <link rel="stylesheet" href="../assets/CSS/kardex.css" />
</head>

<body>
    <?php ob_start(); ?>

    <div class="kardex-topbar">
        <span>
            <?php if ($descripcionProducto): ?>
                Producto: <?= htmlspecialchars($descripcionProducto) ?> (Código:
                <?= htmlspecialchars($codigoSeleccionado) ?>)
            <?php elseif ($codigoSeleccionado): ?>
                Producto: Descripción no encontrada (Código: <?= htmlspecialchars($codigoSeleccionado) ?>)
            <?php else: ?>
                Producto no seleccionado
            <?php endif; ?>
        </span>
        <span>
            Unidad de medida: <?= htmlspecialchars($unidadMedida ?? '-') ?> |
            Usuario: <?= htmlspecialchars($_SESSION["nombre_usuario"]) ?>
        </span>
    </div>

    <form method="GET" action="kardex.php" style="padding: 15px;">
        <label>Ingrese el código del producto:
            <input type="text" name="codigo" value="<?= htmlspecialchars($codigoSeleccionado ?? '') ?>" required />
        </label>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($codigoSeleccionado): ?>
        <!-- 🔧 Formulario de configuración de stock mínimo/máximo -->
        <form method="POST" action="../../Controllers/kardexController.php" style="margin: 0 15px;">
            <input type="hidden" name="actualizar_config_stock" value="1">
            <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigoSeleccionado) ?>">

            <label>Stock mínimo:
                <input type="number" name="stock_minimo" value="<?= htmlspecialchars($config['minimo']) ?>" required>
            </label>
            <label>Stock máximo:
                <input type="number" name="stock_maximo" value="<?= htmlspecialchars($config['maximo']) ?>" required>
            </label>
            <button type="submit">Guardar Configuración</button>
        </form>
    <?php endif; ?>

    <main class="kardex-main">
        <section class="kardex-left">
            <form method="POST" action="../../Controllers/kardexController.php">
                <h3>Nuevo Movimiento</h3>

                <input type="hidden" name="codigo" value="<?= htmlspecialchars($codigoSeleccionado ?? '') ?>">

                <label>Número de lote:
                    <input type="text" name="numero_lote" />
                </label>

                <label>Fecha de vencimiento:
                    <input type="date" name="fecha_vencimiento" />
                </label>

                <label>Fecha: <input type="date" name="fecha" required></label>

                <label>Tipo:
                    <select name="tipo" required>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                    </select>
                </label>

                <label>Cantidad: <input type="number" name="cantidad" required></label>
                <label>Descripción: <input type="text" name="descripcion" required></label>

                <label><input type="checkbox" id="toggleEmpresa"> Incluir empresa</label>
                <input type="text" name="empresa" id="empresaInput" placeholder="Empresa" disabled>

                <button type="submit">Registrar</button>
            </form>
        </section>

        <section class="kardex-right">
            <h3>Historial de Movimientos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Saldo</th>
                        <th>Descripción</th>
                        <th>Empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($codigoSeleccionado) {
                        $stockMinimo = $config['minimo'];
                        $stockMaximo = $config['maximo'];
                        $saldo = mostrarHistorialKardex(false, $codigoSeleccionado);

                        if ($saldo < $stockMinimo) {
                            echo "<tr><td colspan='6'><div class='alert-box alert-low'>⚠️ El stock está por debajo del mínimo ({$saldo} unidades)</div></td></tr>";
                        } elseif ($saldo > $stockMaximo) {
                            echo "<tr><td colspan='6'><div class='alert-box alert-high'>⚠️ El stock ha superado el máximo ({$saldo} unidades)</div></td></tr>";
                        }

                        mostrarHistorialKardex(true, $codigoSeleccionado);
                    } else {
                        echo "<tr><td colspan='6'>Ingrese un código para mostrar el historial.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.getElementById('toggleEmpresa').addEventListener('change', function () {
            const empresaInput = document.getElementById('empresaInput');
            empresaInput.disabled = !this.checked;
            if (!this.checked) empresaInput.value = '';
        });
    </script>

    <?php
    $contenido = ob_get_clean();
    include_once __DIR__ . '/../layout/layout.php';
    ?>

</body>

</html>