<?php
// includes/db.php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'mi_tienda');
define('DB_USER', 'mi_tienda_user');
define('DB_PASS', 'tu_password_segura');

function db_connect() {
    $conn_str = "host=" . DB_HOST .
                " port=" . DB_PORT .
                " dbname=" . DB_NAME .
                " user=" . DB_USER .
                " password=" . DB_PASS;

    $conn = pg_connect($conn_str);
    if (!$conn) {
        die("Error de conexión a PostgreSQL");
    }
    return $conn;
}

// Preparar statements comunes una sola vez
function prepare_statements($conn) {
    pg_prepare($conn, "user_get_by_email", "SELECT * FROM users WHERE email = $1");
    pg_prepare($conn, "user_insert", "INSERT INTO users (name, email, password_hash) VALUES ($1, $2, $3) RETURNING id");
    pg_prepare($conn, "product_list", "SELECT p.*, u.name as owner_name FROM products p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
    pg_prepare($conn, "product_by_id", "SELECT p.*, u.name as owner_name FROM products p JOIN users u ON p.user_id = u.id WHERE p.id = $1");
    pg_prepare($conn, "product_insert", "INSERT INTO products (user_id, name, description, price, stock, category, image_path) VALUES ($1,$2,$3,$4,$5,$6,$7) RETURNING id");
    pg_prepare($conn, "product_update", "UPDATE products SET name=$1, description=$2, price=$3, stock=$4, category=$5, image_path=$6, updated_at=now() WHERE id=$7 AND user_id=$8");
    pg_prepare($conn, "product_delete", "DELETE FROM products WHERE id=$1 AND user_id=$2");
    pg_prepare($conn, "products_by_user", "SELECT * FROM products WHERE user_id = $1");
}