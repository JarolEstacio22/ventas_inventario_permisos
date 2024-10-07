<?php
require_once 'auth.php';
require_login();
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
