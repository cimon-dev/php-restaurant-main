-- Restaurant Management System Database
-- Tạo database nếu chưa có
CREATE DATABASE IF NOT EXISTS restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_db;

-- Bảng user với role đơn giản
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100),
    role ENUM('admin','manager','user') NOT NULL,
    active BOOLEAN DEFAULT TRUE
);

-- Danh mục nguyên liệu
CREATE TABLE ingredient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    unit VARCHAR(20),
    purchase_price DECIMAL(14,2),
    min_stock INT DEFAULT 0,
    description TEXT,
    main_supplier VARCHAR(100)
);

-- Bảng loại nguyên liệu (category)
CREATE TABLE IF NOT EXISTS ingredient_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

-- Danh mục món ăn
CREATE TABLE menu_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(14,2) NOT NULL,
    description TEXT
);

-- Công thức món ăn
CREATE TABLE recipe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT,
    ingredient_id INT,
    qty DECIMAL(10,3) NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menu_item(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)
);

-- Phiếu nhập kho
CREATE TABLE inventory_receipt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT,
    supplier VARCHAR(100),
    receipt_date DATE,
    status ENUM('pending','completed') DEFAULT 'pending',
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inventory_receipt_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT,
    ingredient_id INT,
    qty INT,
    unit_price DECIMAL(14,2),
    FOREIGN KEY (receipt_id) REFERENCES inventory_receipt(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)
);

-- Phiếu xuất kho/thao tác kho
CREATE TABLE inventory_issue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT,
    issue_type ENUM('sale','manual','waste') DEFAULT 'sale',
    issue_date DATE,
    status ENUM('pending','completed') DEFAULT 'pending',
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inventory_issue_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issue_id INT,
    ingredient_id INT,
    qty INT,
    FOREIGN KEY (issue_id) REFERENCES inventory_issue(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)
);

-- Nhật ký kho
CREATE TABLE inventory_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT,
    qty_change INT,
    type ENUM('receipt','issue','adjust','expire'),
    related_id INT,
    note TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)
);

-- Bảng bàn ăn
CREATE TABLE restaurant_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(10) UNIQUE,
    status ENUM('free','occupied','reserved') DEFAULT 'free'
);

-- Đơn bán hàng
CREATE TABLE sale_order (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT,
    waiter_id INT,
    cashier_id INT,
    order_time DATETIME,
    status ENUM('open','served','paid','cancel') DEFAULT 'open',
    discount DECIMAL(14,2),
    vat_rate DECIMAL(4,2),
    total_amount DECIMAL(14,2),
    FOREIGN KEY (table_id) REFERENCES restaurant_table(id)
);

CREATE TABLE sale_order_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_order_id INT,
    menu_id INT,
    qty INT,
    price DECIMAL(14,2),
    status ENUM('ordered','cooked','served','canceled') DEFAULT 'ordered',
    FOREIGN KEY (sale_order_id) REFERENCES sale_order(id),
    FOREIGN KEY (menu_id) REFERENCES menu_item(id)
);

-- Chi phí khác
CREATE TABLE expense (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_type VARCHAR(50),
    amount DECIMAL(14,2),
    description TEXT,
    created_by INT,
    expense_date DATE
);

-- Điều chỉnh/kho kiểm kê
CREATE TABLE stock_adjustment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT,
    old_qty INT,
    new_qty INT,
    adjust_date DATE,
    reason VARCHAR(100),
    note TEXT,
    adjusted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)
);

-- Nhật ký thao tác hệ thống (audit)
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    target VARCHAR(100),
    detail TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert dữ liệu mẫu
-- User mặc định (password: admin123)
-- Hash tạo bằng: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, fullname, role, active) VALUES
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'Administrator', 'admin', TRUE),
('manager', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'Manager', 'manager', TRUE),
('user', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'User', 'user', TRUE);

-- Bàn ăn mẫu
INSERT INTO restaurant_table (number, status) VALUES
('B01', 'free'),
('B02', 'free'),
('B03', 'free'),
('B04', 'free'),
('B05', 'free'),
('B06', 'free'),
('B07', 'free'),
('B08', 'free'),
('B09', 'free'),
('B10', 'free');
 
