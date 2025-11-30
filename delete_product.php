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
    pg_execute($conn, "product_delete", [$id, current_user_id()]);
    flash('Producto eliminado');
    redirect('index.php');
}
?>
<h1>¿Eliminar producto?</h1>
<p>¿Estás seguro de eliminar <strong><?= h($product['name']) ?></strong>?</p>
<form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php require_once 'includes/footer.php'; ?>