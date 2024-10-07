<?php
require_once 'auth.php';
require_once 'db_connect.php';
require_login();

if (!has_access('ventas')) {
    header("Location: index.php");
    exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    $stmt = $pdo->prepare("SELECT precio FROM inventario WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if ($producto) {
        $precio_unitario = $producto['precio'];
        $total = $cantidad * $precio_unitario;

        $stmt = $pdo->prepare("INSERT INTO ventas (producto_id, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$producto_id, $cantidad, $precio_unitario, $total])) {
            $stmt = $pdo->prepare("UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?");
            $stmt->execute([$cantidad, $producto_id]);
            $success = "Venta registrada exitosamente";
        } else {
            $error = "Error al registrar la venta";
        }
    } else {
        $error = "Producto no encontrado";
    }
}

$stmt = $pdo->query("SELECT * FROM inventario");
$productos = $stmt->fetchAll();

$stmt = $pdo->query("SELECT v.*, i.nombre as producto_nombre FROM ventas v JOIN inventario i ON v.producto_id = i.id ORDER BY fecha_venta DESC");
$ventas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas - Panel de Control</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <aside>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="usuarios.php">Usuarios</a>
                <a href="ventas.php" class="active">Ventas</a>
                <a href="inventario.php">Inventario</a>
            </nav>
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </aside>
        <main>
            <h1>Gestión de Ventas</h1>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" class="add-form">
                <div class="form-group">
                    <label for="producto_id">Producto:</label>
                    <select id="producto_id" name="producto_id" required>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['id']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?> - $<?php echo number_format($producto['precio'], 2); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" required>
                </div>
                <button type="submit">Registrar Venta</button>
            </form>
            <h2>Registro de Ventas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo $venta['fecha_venta']; ?></td>
                            <td><?php echo htmlspecialchars($venta['producto_nombre']); ?></td>
                            <td><?php echo $venta['cantidad']; ?></td>
                            <td>$<?php echo number_format($venta['precio_unitario'], 2); ?></td>
                            <td>$<?php echo number_format($venta['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>