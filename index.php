<?php
require_once 'auth.php';
require_once 'db_connect.php';
require_login();

// Consulta para obtener el total de usuarios
$stmt = $pdo->query("SELECT COUNT(*) as total_usuarios FROM usuarios");
$total_usuarios = $stmt->fetch()['total_usuarios'];

// Consulta para obtener el total de productos
$stmt = $pdo->query("SELECT COUNT(*) as total_productos FROM inventario");
$total_productos = $stmt->fetch()['total_productos'];

// Consulta para obtener las ventas del día
$hoy = date('Y-m-d');
$stmt = $pdo->prepare("SELECT SUM(total) as total_ventas_hoy FROM ventas WHERE DATE(fecha) = ?");
$stmt->execute([$hoy]);
$total_ventas_hoy = $stmt->fetch()['total_ventas_hoy'] ?? 0;

// Consulta para obtener las ventas de la semana
$inicio_semana = date('Y-m-d', strtotime('monday this week'));
$stmt = $pdo->prepare("SELECT SUM(total) as total_ventas_semana FROM ventas WHERE DATE(fecha) BETWEEN ? AND ?");
$stmt->execute([$inicio_semana, $hoy]);
$total_ventas_semana = $stmt->fetch()['total_ventas_semana'] ?? 0;

// Consulta para obtener las ventas del mes
$inicio_mes = date('Y-m-01');
$stmt = $pdo->prepare("SELECT SUM(total) as total_ventas_mes FROM ventas WHERE DATE(fecha) BETWEEN ? AND ?");
$stmt->execute([$inicio_mes, $hoy]);
$total_ventas_mes = $stmt->fetch()['total_ventas_mes'] ?? 0;

// Consulta para obtener las ventas del año
$inicio_anio = date('Y-01-01');
$stmt = $pdo->prepare("SELECT SUM(total) as total_ventas_anio FROM ventas WHERE DATE(fecha) BETWEEN ? AND ?");
$stmt->execute([$inicio_anio, $hoy]);
$total_ventas_anio = $stmt->fetch()['total_ventas_anio'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <aside>
            <nav>
                <?php if (has_access('inicio')): ?>
                    <a href="index.php">Inicio</a>
                <?php endif; ?>
                <?php if (has_access('usuarios')): ?>
                    <a href="usuarios.php">Usuarios</a>
                <?php endif; ?>
                <?php if (has_access('ventas')): ?>
                    <a href="ventas.php">Ventas</a>
                <?php endif; ?>
                <?php if (has_access('inventario')): ?>
                    <a href="inventario.php">Inventario</a>
                <?php endif; ?>
            </nav>
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </aside>
        <main>
            <h1>Bienvenido, <?php echo $_SESSION['user_name']; ?></h1>
            <p>Selecciona una opción del menú para comenzar.</p>

            <div class="stats">
                <div class="stat-item">
                    <h3>Total de Usuarios</h3>
                    <p><?php echo $total_usuarios; ?></p>
                </div>
                <div class="stat-item">
                    <h3>Total de Productos</h3>
                    <p><?php echo $total_productos; ?></p>
                </div>
                <div class="stat-item">
                    <h3>Total de Ventas Hoy</h3>
                    <p>$<?php echo number_format($total_ventas_hoy, 2); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Total de Ventas de la Semana</h3>
                    <p>$<?php echo number_format($total_ventas_semana, 2); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Total de Ventas del Mes</h3>
                    <p>$<?php echo number_format($total_ventas_mes, 2); ?></p>
                </div>
                <div class="stat-item">
                    <h3>Total de Ventas del Año</h3>
                    <p>$<?php echo number_format($total_ventas_anio, 2); ?></p>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
