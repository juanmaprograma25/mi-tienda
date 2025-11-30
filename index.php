<?php require_once 'includes/header.php'; 
$conn = db_connect();
prepare_statements($conn);
$result = pg_execute($conn, "product_list", []);
$products = pg_fetch_all($result) ?: [];
?>
<h1>Productos disponibles</h1>
<table class="table table-striped">
    <thead><tr>
        <th>Imagen</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Categoría</th><th>Propietario</th><th>Acciones</th>
    </tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?php if ($p['image_path']): ?><img src="<?= h($p['image_path']) ?>" width="50"><?php endif; ?></td>
            <td><?= h($p['name']) ?></td>
            <td><?= number_format((float)$p['price'], 2) ?> €</td>
            <td><?= $p['stock'] ?></td>
            <td><?= h($p['category'] ?? 'Sin categoría') ?></td>
            <td><?= h($p['owner_name']) ?></td>
            <td>
                <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                <?php if (is_logged_in() && (int)$p['user_id'] === current_user_id()): ?>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro?')">Borrar</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require_once 'includes/footer.php'; ?>