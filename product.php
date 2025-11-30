<?php 
require_once 'includes/header.php';
$conn = db_connect();
prepare_statements($conn);

$id = (int)($_GET['id'] ?? 0);
$result = pg_execute($conn, "product_by_id", [$id]);
$product = pg_fetch_assoc($result);

if (!$product) {
    flash('Producto no encontrado', 'danger');
    redirect('index.php');
}
?>
<h1><?= h($product['name']) ?></h1>
<?php if ($product['image_path']): ?>
    <img src="<?= h($product['image_path']) ?>" class="img-fluid mb-3" style="max-height:400px;">
<?php endif; ?>
<p><strong>Precio:</strong> <?= number_format((float)$product['price'], 2) ?> €</p>
<p><strong>Stock:</strong> <?= $product['stock'] ?></p>
<p><strong>Categoría:</strong> <?= h($product['category'] ?? 'N/A') ?></p>
<p><strong>Descripción:</strong><br><?= nl2br(h($product['description'] ?? 'Sin descripción')) ?></p>
<p><strong>Vendedor:</strong> <?= h($product['owner_name']) ?></p>
<a href="index.php" class="btn btn-secondary">Volver</a>
<?php require_once 'includes/footer.php'; ?>