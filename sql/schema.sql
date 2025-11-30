-- sql/schema.sql
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10,2) NOT NULL DEFAULT 0.00 CHECK (price >= 0),
    stock INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0),
    category VARCHAR(100),
    image_path VARCHAR(512),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Índices útiles
CREATE INDEX idx_products_user_id ON products(user_id);
CREATE INDEX idx_products_category ON products(category);

-- Usuarios de ejemplo
-- Contraseña para ambos: "123456"
-- Generados con password_hash('123456', PASSWORD_DEFAULT) en PHP 8.3
INSERT INTO users (name, email, password_hash) VALUES
('Admin User', 'admin@mitienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Juan Pérez', 'juan@mitienda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Productos de ejemplo
INSERT INTO products (user_id, name, description, price, stock, category, image_path) VALUES
(1, 'Laptop Gamer', 'Potente laptop para gaming', 1299.99, 5, 'Electrónica', NULL),
(1, 'Auriculares Bluetooth', 'Cancelación de ruido', 89.90, 20, 'Audio', NULL),
(2, 'Teclado Mecánico RGB', 'Switch blue', 75.00, 15, 'Periféricos', NULL);