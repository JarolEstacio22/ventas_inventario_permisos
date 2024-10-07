<?php
require_once 'auth.php';
require_once 'db_connect.php';
require_login();

if (!has_access('usuarios')) {
    header("Location: index.php");
    exit();
}

$error = $success = '';
$usuario_editar = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Si estamos editando un usuario, se usa el método 'edit'
                $nombre = $_POST['nombre'];
                $documento = $_POST['documento'];
                $cargo = $_POST['cargo'];
                $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
                $acceso = json_encode($_POST['acceso']);

                if (!empty($_POST['id'])) {
                    // Editar usuario
                    $id = $_POST['id'];
                    if ($password) {
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, documento = ?, cargo = ?, password = ?, acceso = ? WHERE id = ?");
                        $result = $stmt->execute([$nombre, $documento, $cargo, $password, $acceso, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, documento = ?, cargo = ?, acceso = ? WHERE id = ?");
                        $result = $stmt->execute([$nombre, $documento, $cargo, $acceso, $id]);
                    }

                    if ($result) {
                        $success = "Usuario actualizado exitosamente";
                    } else {
                        $error = "Error al actualizar usuario";
                    }
                } else {
                    // Agregar usuario
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, documento, cargo, password, acceso) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$nombre, $documento, $cargo, $password, $acceso])) {
                        $success = "Usuario agregado exitosamente";
                    } else {
                        $error = "Error al agregar usuario";
                    }
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $success = "Usuario eliminado exitosamente";
                } else {
                    $error = "Error al eliminar usuario";
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario_editar = $stmt->fetch();
                break;
        }
    }
}

$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Panel de Control</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'dashboard.php';?>

            <!-- Formulario para agregar/editar usuario -->
            <form method="POST" class="add-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= isset($usuario_editar) ? $usuario_editar['id'] : '' ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= isset($usuario_editar) ? htmlspecialchars($usuario_editar['nombre']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="documento">Documento:</label>
                    <input type="text" id="documento" name="documento" value="<?= isset($usuario_editar) ? htmlspecialchars($usuario_editar['documento']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <input type="text" id="cargo" name="cargo" value="<?= isset($usuario_editar) ? htmlspecialchars($usuario_editar['cargo']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña (déjalo en blanco si no quieres cambiarla):</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-group">
                    <label>Acceso:</label>
                    <div class="checkbox-group">
                        <?php
                        $acceso_actual = isset($usuario_editar) ? json_decode($usuario_editar['acceso'], true) : [];
                        ?>
                        <label><input type="checkbox" name="acceso[]" value="inicio" <?= in_array('inicio', $acceso_actual) ? 'checked' : '' ?>> Inicio</label>
                        <label><input type="checkbox" name="acceso[]" value="usuarios" <?= in_array('usuarios', $acceso_actual) ? 'checked' : '' ?>> Usuarios</label>
                        <label><input type="checkbox" name="acceso[]" value="ventas" <?= in_array('ventas', $acceso_actual) ? 'checked' : '' ?>> Ventas</label>
                        <label><input type="checkbox" name="acceso[]" value="inventario" <?= in_array('inventario', $acceso_actual) ? 'checked' : '' ?>> Inventario</label>
                    </div>
                </div>
                <button type="submit"><?= isset($usuario_editar) ? 'Actualizar Usuario' : 'Agregar Usuario' ?></button>
            </form>

            <!-- Listado de usuarios -->
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Cargo</th>
                        <th>Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['documento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['cargo']); ?></td>
                            <td><?php echo implode(', ', json_decode($usuario['acceso'], true)); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit">Editar</button>
                                </form>

                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</button>
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
