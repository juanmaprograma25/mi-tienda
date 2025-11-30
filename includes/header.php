<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Tienda Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Mi Tienda</a>
        <div class="navbar-nav ms-auto">
            <?php if (is_logged_in()): ?>
                <span class="navbar-text me-3">Hola, <?= h(current_user()['name']) ?></span>
                <a class="nav-link" href="add_product.php">+ Producto</a>
                <a class="nav-link" href="logout.php">Salir</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <?= display_flash() ?>