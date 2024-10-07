<div class="dashboard">
        <aside>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="usuarios.php" class="active">Usuarios</a>
                <a href="ventas.php">Ventas</a>
                <a href="inventario.php">Inventario</a>
            </nav>
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </aside>
        <main>
            <h1>Gestión de Usuarios</h1>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>