DROP DATABASE IF EXISTS scandiweb;
CREATE DATABASE scandiweb;
USE scandiweb;

-- CREATING TABLES --

CREATE TABLE categories (
    name VARCHAR(255) PRIMARY KEY
);

CREATE TABLE products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    inStock BOOLEAN DEFAULT TRUE,
    gallery TEXT,
    description TEXT,
    category VARCHAR(255),
    brand VARCHAR(255),
    FOREIGN KEY (category) REFERENCES categories(name) ON DELETE SET NULL
);

CREATE TABLE attributes (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL, 
    CONSTRAINT unique_attribute UNIQUE (name, type)
);

CREATE TABLE product_attributes (
    id VARCHAR(255) PRIMARY KEY,
    product_id VARCHAR(255) NOT NULL,
    attribute_id VARCHAR(255),
    displayValue VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE SET NULL,
    CONSTRAINT unique_product_attribute_value UNIQUE (product_id, attribute_id, value)
);

CREATE TABLE currencies (
    label VARCHAR(50) PRIMARY KEY,
    symbol VARCHAR(10) NOT NULL
);

CREATE TABLE prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10, 2),
    currency VARCHAR(50) NOT NULL,
    product_id VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (currency) REFERENCES currencies(label) ON DELETE CASCADE,
    CONSTRAINT unique_product_price_currency UNIQUE (product_id, currency)
);

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    total_amount DECIMAL(10, 2) NOT NULL,
    total_currency VARCHAR(50) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id VARCHAR(255),
    product_name VARCHAR(255) NOT NULL,
    attribute_values TEXT NOT NULL,
    quantity INT UNSIGNED DEFAULT 1,
    paid_amount DECIMAL(10, 2) NOT NULL,
    paid_currency VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (paid_currency) REFERENCES currencies(label)
);