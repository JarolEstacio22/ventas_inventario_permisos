<?php
require_once 'auth.php';
require_once 'db_connect.php';
require_login();

if (!has_access('inventario')) {
    header("Location: index.php");
    exit();
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nombre = $_POST['nombre'];
                $cantidad = $_POST['cantidad'];
                $precio = $_POST['precio'];

                $stmt = $pdo->prepare("INSERT INTO inventario (nombre, cantidad, precio) VALUES (?, ?, ?)");
                if ($stmt->execute([$nombre, $cantidad, $precio])) {
                    $success = "Producto agregado exitosamente";
                } else {
                    $error = "Error al agregar producto";
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $pdo->prepare("DELETE FROM inventario WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $success = "Producto eliminado exitosamente";
                } else {
                    $error = "Error al eliminar producto";
                }
                break;
        }
    }
}

$stmt = $pdo->query("SELECT * FROM inventario");
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Panel de Control</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'dashboard.php';?>
        <main>
            <h1>Gestión de Inventario</h1>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" class="add-form">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="nombre">Nombre del Producto:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" required>
                </div>
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" required>
                </div>
                <button type="submit">Agregar Producto</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>