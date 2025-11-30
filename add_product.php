<?php 
require_login();
require_once 'includes/header.php';
$conn = db_connect();
prepare_statements($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf'] ?? '')) {
    $name = trim($_POST['name'] ?? '');
    $desc = $_POST['description'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $cat = trim($_POST['category'] ?? '');

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image_path = save_image($_FILES['image']);
    }

    if ($price >= 0 && $stock >= 0 && $name !== '') {
        pg_execute($conn, "product_insert", [
            current_user_id(), $name, $desc, $price, $stock, $cat ?: null, $image_path
        ]);
        flash('Producto creado');
        redirect('index.php');
    } else {
        flash('Datos inválidos', 'danger');
    }
}
?>
<h1>Nuevo producto</h1>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Nombre" required></div>
    <div class="mb-3"><textarea name="description" class="form-control" rows="4" placeholder="Descripción"></textarea></div>
    <div class="mb-3"><input type="number" step="0.01" name="price" class="form-control" placeholder="Precio" required></div>
    <div class="mb-3"><input type="number" name="stock" class="form-control" placeholder="Stock" required></div>
    <div class="mb-3"><input type="text" name="category" class="form-control" placeholder="Categoría"></div>
    <div class="mb-3"><input type="file" name="image" accept="image/*" class="form-control"></div>
    <button type="submit" class="btn btn-success">Crear producto</button>
</form>
<?php require_once 'includes/footer.php'; ?>