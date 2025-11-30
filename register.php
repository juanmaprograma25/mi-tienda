<?php 
require_once 'includes/header.php';
$conn = db_connect();
prepare_statements($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($pass !== $pass2 || strlen($pass) < 6) {
        flash('Las contraseñas no coinciden o son muy cortas', 'danger');
    } else {
        $result = pg_execute($conn, "user_get_by_email", [$email]);
        if (pg_fetch_row($result)) {
            flash('El email ya está registrado', 'danger');
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $result = pg_execute($conn, "user_insert", [$name, $email, $hash]);
            $row = pg_fetch_row($result);
            login_user((int)$row[0], $email);
            flash('Registro exitoso');
            redirect('index.php');
        }
    }
}
?>
<h1>Registrarse</h1>
<form method="post">
    <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Nombre completo" required></div>
    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Contraseña (mín 6)" required></div>
    <div class="mb-3"><input type="password" name="password2" class="form-control" placeholder="Repetir contraseña" required></div>
    <button type="submit" class="btn btn-success">Registrarse</button>
</form>
<?php require_once 'includes/footer.php'; ?>