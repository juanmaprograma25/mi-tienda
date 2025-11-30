<?php 
require_login();
require_once 'includes/header.php';
$conn = db_connect();
prepare_statements($conn);

$id = (int)($_GET['id'] ?? 0);
$result = pg_execute($conn, "product_by_id", [$id]);
$product = pg_fetch_assoc($result);

if (!$product || (int)$product['user_id'] !== current_user_id()) {
    flash('No tienes permiso', 'danger');
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf'] ?? '')) {
    $name = trim($_POST['name'] ?? '');
    $desc = $_POST['description'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $cat = trim($_POST['category'] ?? '');

    $image_path = $product['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $new_path = save_image($_FILES['image']);
        if ($new_path) $image_path = $new_path;
    }

    pg_execute($conn, "product_update", [$name, $desc, $price, $stock, $cat ?: null, $image_path, $id, current_user_id()]);
    flash('Producto actualizado');
    redirect('index.php');
}
?>
<h1>Editar producto</h1>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="mb-3"><input type="text" name="name" value="<?= h($product['name']) ?>" class="form-control" required></div>
    <div class="mb-3"><textarea name="description" class="form-control" rows="4"><?= h($product['description'] ?? '') ?></textarea></div>
    <div class="mb-3"><input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" class="form-control" required></div>
    <div class="mb-3"><input type="number" name="stock" value="<?= $product['stock'] ?>" class="form-control" required></div>
    <div class="mb-3"><input type="text" name="category" value="<?= h($product['category'] ?? '') ?>" class="form-control"></div>
    <div class="mb-3"><input type="file" name="image" accept="image/*" class="form-control"></div>
    <?php if ($product['image_path']): ?>
        <div class="mb-3"><img src="<?= h($product['image_path']) ?>" width="200"></div>
    <?php endif; ?>
    <button type="submit" class="btn btn-warning">Actualizar</button>
</form>
<?php require_once 'includes/footer.php'; ?>