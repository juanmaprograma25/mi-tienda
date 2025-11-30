<?php
header('Content-Type: application/json');
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$conn = db_connect();
prepare_statements($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    // Públicos
    case 'list':
        $result = pg_execute($conn, "product_list", []);
        $products = pg_fetch_all($result) ?: [];
        echo json_encode(['products' => $products]);
        break;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        $result = pg_execute($conn, "product_by_id", [$id]);
        $product = pg_fetch_assoc($result);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
        }
        break;

    // Registro
    case 'register':
        $data = json_decode(file_get_contents('php://input'), true);
        if (pg_execute($conn, "user_get_by_email", [$data['email'] ?? ''])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email ya existe']);
            break;
        }
        $hash = password_hash($data['password'] ?? '', PASSWORD_DEFAULT);
        $res = pg_execute($conn, "user_insert", [$data['name'] ?? '', $data['email'] ?? '', $hash]);
        $row = pg_fetch_row($res);
        echo json_encode(['user_id' => (int)$row[0]]);
        break;

    // Login (devuelve cookie de sesión normal)
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = pg_execute($conn, "user_get_by_email", [$data['email'] ?? '']);
        $user = pg_fetch_assoc($result);
        if ($user && password_verify($data['password'] ?? '', $user['password_hash'])) {
            login_user((int)$user['id'], $user['email']);
            echo json_encode(['success' => true, 'user' => ['id' => $user['id'], 'name' => $user['name']]]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
        }
        break;

    // Requieren autenticación
    default:
        if (!is_logged_in()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }

        switch ($action) {
            case 'create':
                $data = json_decode(file_get_contents('php://input'), true);
                $image_path = null;
                // Aquí podrías aceptar imagen vía multipart, pero por simplicidad se omite
                pg_execute($conn, "product_insert", [
                    current_user_id(),
                    $data['name'] ?? '',
                    $data['description'] ?? '',
                    $data['price'] ?? 0,
                    $data['stock'] ?? 0,
                    $data['category'] ?? null,
                    $image_path
                ]);
                echo json_encode(['success' => true]);
                break;

            case 'update':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = (int)($data['id'] ?? 0);
                $result = pg_execute($conn, "product_by_id", [$id]);
                $p = pg_fetch_assoc($result);
                if (!$p || (int)$p['user_id'] !== current_user_id()) {
                    http_response_code(403);
                    echo json_encode(['error' => 'No autorizado']);
                    break;
                }
                pg_execute($conn, "product_update", [
                    $data['name'] ?? $p['name'],
                    $data['description'] ?? $p['description'],
                    $data['price'] ?? $p['price'],
                    $data['stock'] ?? $p['stock'],
                    $data['category'] ?? $p['category'],
                    $p['image_path'],
                    $id,
                    current_user_id()
                ]);
                echo json_encode(['success' => true]);
                break;

            case 'delete':
                $id = (int)($_GET['id'] ?? 0);
                $result = pg_execute($conn, "product_by_id", [$id]);
                $p = pg_fetch_assoc($result);
                if (!$p || (int)$p['user_id'] !== current_user_id()) {
                    http_response_code(403);
                    echo json_encode(['error' => 'No autorizado']);
                    break;
                }
                pg_execute($conn, "product_delete", [$id, current_user_id()]);
                echo json_encode(['success' => true]);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
        }
}