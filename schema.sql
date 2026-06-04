-- ============================================
-- GlamCart Database Schema
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS glamcart;
USE glamcart;

-- --------------------------
-- Users Table
-- --------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------
-- Categories Table
-- --------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- --------------------------
-- Products Table
-- --------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category_id INT,
    stock INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- --------------------------
-- Cart Table
-- --------------------------
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- --------------------------
-- Orders Table
-- --------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    zip VARCHAR(20),
    total DECIMAL(10,2),
    status ENUM('pending','processing','shipped','delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------
-- Order Items Table
-- --------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- --------------------------
-- Contact Messages Table
-- --------------------------
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150),
    subject VARCHAR(200),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert Categories
INSERT INTO categories (name) VALUES
('Clothing'),
('Footwear'),
('Accessories'),
('Makeup');

-- Insert Admin User (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Tamanna Dadhwal', 'tamanna@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Simran', 'simran@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- NOTE: The hashed password above corresponds to 'password' for demo users.
-- For admin, use this INSERT to set password = admin123:
-- We'll handle this via PHP in schema setup.

-- Insert Products — Clothing
INSERT INTO products (name, description, price, image, category_id, stock) VALUES
('Floral Summer Dress', 'Beautiful floral print midi dress, perfect for summers. Light chiffon fabric.', 1299.00, 'images/dress1.jpg', 1, 15),
('Pink Crop Top', 'Trendy cotton crop top in baby pink. Pair with jeans or skirts.', 499.00, 'images/dress2.jpg', 1, 20),
('Denim Mini Skirt', 'Classic denim mini skirt with button front. Versatile and stylish.', 799.00, 'images/dress3.jpg', 1, 12),
('Boho Maxi Dress', 'Flowing boho-style maxi dress with embroidery detail. Festival ready.', 1599.00, 'images/dress4.jpg', 1, 8),
('Lace Corset Top', 'Elegant lace corset-style top. Perfect for evening outings.', 899.00, 'images/dress5.jpg', 1, 18);

-- Insert Products — Footwear
INSERT INTO products (name, description, price, image, category_id, stock) VALUES
('Strappy Heeled Sandals', 'Delicate strappy sandals with block heel. Available in nude and pink.', 1499.00, 'images/shoes1.jpg', 2, 10),
('White Sneakers', 'Clean white sneakers with rose gold accents. Casual everyday wear.', 999.00, 'images/shoes2.jpg', 2, 25),
('Platform Wedges', 'Comfortable espadrille wedge sandals. Perfect for summer.', 1299.00, 'images/shoes3.jpg', 2, 14),
('Kitten Heel Mules', 'Chic kitten heel mules in blush pink. Office to dinner ready.', 1799.00, 'images/shoes4.jpg', 2, 7);

-- Insert Products — Accessories
INSERT INTO products (name, description, price, image, category_id, stock) VALUES
('Quilted Mini Bag', 'Trendy quilted crossbody mini bag in pink. Gold chain strap.', 1899.00, 'images/bag1.jpg', 3, 9),
('Pearl Drop Earrings', 'Dainty freshwater pearl drop earrings. Timeless and elegant.', 399.00, 'images/bag2.jpg', 3, 30),
('Beaded Charm Bracelet', 'Colorful beaded bracelet with heart charms. Stack it up!', 299.00, 'images/bag3.jpg', 3, 22),
('Silk Scrunchie Set', 'Set of 5 premium silk scrunchies in pastel shades.', 249.00, 'images/bag4.jpg', 3, 40),
('Tote Bag - Floral', 'Large canvas tote with floral print. Perfect for college or shopping.', 699.00, 'images/bag5.jpg', 3, 16);

-- Insert Products — Makeup
INSERT INTO products (name, description, price, image, category_id, stock) VALUES
('Rosy Lip Gloss Set', 'Set of 6 non-sticky glossy lip glosses in rosy shades.', 599.00, 'images/makeup1.jpg', 4, 20),
('Glow Highlighter Palette', '4-shade highlighter palette for a radiant glow. Buildable coverage.', 899.00, 'images/makeup2.jpg', 4, 15),
('Nude Eyeshadow Palette', '12-shade nude and warm-toned eyeshadow palette. Matte & shimmer finish.', 1199.00, 'images/makeup3.jpg', 4, 11),
('Pink Blush Compact', 'Silky smooth blush in soft rose pink. Buildable and long-lasting.', 499.00, 'images/makeup4.jpg', 4, 18),
('Mascara Volume Boost', 'Volumizing and lengthening mascara. Smudge-proof formula.', 699.00, 'images/makeup5.jpg', 4, 24);

-- ============================================
-- Fix admin password to 'admin123'
-- Run this separately after importing:
-- UPDATE users SET password = '$2y$10$YourHashHere' WHERE email = 'admin@gmail.com';
-- Or just register via signup and update role to 'admin' in phpMyAdmin
-- ============================================
