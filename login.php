<?php 
require_once 'includes/header.php';
$conn = db_connect();
prepare_statements($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $result = pg_execute($conn, "user_get_by_email", [$email]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password_hash'])) {
        login_user((int)$user['id'], $user['email']);
        flash('Bienvenido');
        redirect('index.php');
    } else {
        flash('Credenciales incorrectas', 'danger');
    }
}
?>
<h1>Iniciar sesión</h1>
<form method="post">
    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Contraseña" required></div>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>
<?php require_once 'includes/footer.php'; ?>