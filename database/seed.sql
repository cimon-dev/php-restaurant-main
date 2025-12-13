-- ========================================
-- RESTAURANT MANAGEMENT SYSTEM - SAMPLE DATA
-- ========================================
-- Mục đích: Thêm dữ liệu mẫu cho tất cả các bảng
-- Cách chạy: mysql -u root -p restaurant_db < database/seed.sql

-- ========================================
-- 1. CATEGORIES (Loại nguyên liệu)
-- ========================================
INSERT INTO ingredient_category (name, description) VALUES
('Rau tươi', 'Các loại rau tươi sạch'),
('Thịt cá', 'Thịt bò, gà, heo, cá tươi'),
('Gia vị', 'Muối, tiêu, mì chính, cà chua...'),
('Bột mì', 'Bột mì, bột ngô, bột chiên'),
('Đồ uống', 'Nước, nước ngọt, cà phê, trà'),
('Tương ớt', 'Mệ, tương, ớt, nước tương'),
('Dầu mỡ', 'Dầu ăn, mỡ, bơ'),
('Sữa phô mai', 'Sữa, phô mai, sốt');

-- ========================================
-- 2. INGREDIENTS (Nguyên liệu)
-- ========================================
INSERT INTO ingredient (code, name, category, unit, purchase_price, min_stock, description, main_supplier) VALUES
-- Rau tươi
('RAU001', 'Cà chua', 'Rau tươi', 'kg', 15000, 5, 'Cà chua đỏ tươi', 'Chợ Bến Thành'),
('RAU002', 'Dưa chuột', 'Rau tươi', 'kg', 12000, 5, 'Dưa chuột xanh', 'Chợ Bến Thành'),
('RAU003', 'Bắp cải', 'Rau tươi', 'kg', 10000, 10, 'Bắp cải trắng', 'Chợ Bến Thành'),
('RAU004', 'Hành tây', 'Rau tươi', 'kg', 20000, 5, 'Hành tây nhập khẩu', 'Metro'),
('RAU005', 'Tỏi', 'Rau tươi', 'kg', 25000, 3, 'Tỏi tươi', 'Chợ Bến Thành'),
('RAU006', 'Cải luộc', 'Rau tươi', 'kg', 8000, 10, 'Cải xanh luộc', 'Chợ Bến Thành'),

-- Thịt cá
('TH001', 'Gà tươi', 'Thịt cá', 'kg', 80000, 10, 'Gà tươi nguyên con', 'Công ty An Phúc'),
('TH002', 'Thịt bò', 'Thịt cá', 'kg', 150000, 5, 'Thịt bò mềm', 'Công ty An Phúc'),
('TH003', 'Cá hồi', 'Thịt cá', 'kg', 200000, 3, 'Cá hồi tươi', 'Công ty Thủy Sản'),
('TH004', 'Tôm', 'Thịt cá', 'kg', 120000, 5, 'Tôm sú tươi', 'Công ty Thủy Sản'),
('TH005', 'Thịt heo', 'Thịt cá', 'kg', 85000, 5, 'Thịt heo sạch', 'Công ty An Phúc'),

-- Gia vị
('GV001', 'Muối ăn', 'Gia vị', 'kg', 5000, 10, 'Muối ăn tinh', 'Kho tàng'),
('GV002', 'Tiêu đen', 'Gia vị', 'kg', 80000, 1, 'Tiêu đen hạt', 'Kho tàng'),
('GV003', 'Mì chính', 'Gia vị', 'kg', 60000, 2, 'Mì chính Knorr', 'Metro'),
('GV004', 'Đường trắng', 'Gia vị', 'kg', 20000, 5, 'Đường trắng mịn', 'Metro'),
('GV005', 'Bột ngọt', 'Gia vị', 'kg', 45000, 2, 'Bột ngọt Aji-go', 'Metro'),

-- Bột mì
('BM001', 'Bột mì số 8', 'Bột mì', 'kg', 12000, 20, 'Bột mì trắng', 'Metro'),
('BM002', 'Bột ngô', 'Bột mì', 'kg', 15000, 10, 'Bột ngô vàng', 'Metro'),
('BM003', 'Bánh mì', 'Bột mì', 'cái', 25000, 20, 'Bánh mì thường', 'Lò Bánh Minh'),

-- Dầu mỡ
('DM001', 'Dầu ăn', 'Dầu mỡ', 'lít', 35000, 5, 'Dầu ăn bắp', 'Metro'),
('DM002', 'Mỡ gà', 'Dầu mỡ', 'kg', 50000, 2, 'Mỡ gà tươi', 'Công ty An Phúc'),

-- Đồ uống
('UO001', 'Nước lọc', 'Đồ uống', 'lít', 5000, 20, 'Nước lọc tinh khiết', 'Aquafina'),
('UO002', 'Nước ngọt', 'Đồ uống', 'chai', 8000, 30, 'Coca Cola', 'Công ty TNHH Coca'),
('UO003', 'Cà phê', 'Đồ uống', 'kg', 120000, 2, 'Cà phê Robusta', 'Công ty Trung Nguyên'),

-- Tương ớt
('TX001', 'Tương đỏ', 'Tương ớt', 'chai', 15000, 5, 'Tương đỏ Thái', 'Costco'),
('TX002', 'Nước tương', 'Tương ớt', 'chai', 25000, 3, 'Nước tương đậu nành', 'Costco'),
('TX003', 'Sốt cà chua', 'Tương ớt', 'chai', 30000, 3, 'Sốt cà chua Heinz', 'Metro');

-- ========================================
-- 3. MENU ITEMS (Danh mục món ăn)
-- ========================================
INSERT INTO menu_item (code, name, price, description) VALUES
('M001', 'Cơm gà chiên', 85000, 'Cơm gà chiên vàng với trứng'),
('M002', 'Bún chả cá', 75000, 'Bún tươi với cá nướng'),
('M003', 'Phở bò', 60000, 'Phở bò thơm ngon'),
('M004', 'Cơm tấm sườn nướng', 65000, 'Cơm tấm với sườn nướng'),
('M005', 'Mỳ ý carbonara', 90000, 'Mỳ ý kiểu Ý chuẩn'),
('M006', 'Gà nướng chiên', 95000, 'Gà nướng chiên vàng'),
('M007', 'Tôm xào bơ tỏi', 120000, 'Tôm tươi xào bơ tỏi'),
('M008', 'Salad cá hồi', 110000, 'Salad tươi với cá hồi'),
('M009', 'Cơm chiên dương châu', 70000, 'Cơm chiên thập cẩm'),
('M010', 'Bánh mì thịt', 35000, 'Bánh mì nướng với thịt'),
('M011', 'Nước chanh tươi', 15000, 'Nước chanh tươi ngon'),
('M012', 'Cà phê đen', 20000, 'Cà phê đen đậm đà');

-- ========================================
-- 4. RECIPES (Công thức nấu ăn)
-- ========================================
-- Cơm gà chiên (M001)
INSERT INTO recipe (menu_id, ingredient_id, qty) VALUES
(1, 1, 0.2),  -- Cà chua 200g
(1, 3, 0.1),  -- Bắp cải 100g
(1, 6, 0.05),  -- Cải luộc 50g
(1, 7, 0.3),  -- Gà 300g
(1, 21, 0.3),  -- Cơm 300g
(1, 26, 0.05); -- Dầu ăn 50ml

-- Bún chả cá (M002)
INSERT INTO recipe (menu_id, ingredient_id, qty) VALUES
(2, 9, 0.2),  -- Cá hồi 200g
(2, 6, 0.1),  -- Cải luộc 100g
(2, 1, 0.15), -- Cà chua 150g
(2, 2, 0.15); -- Dưa chuột 150g

-- Phở bò (M003)
INSERT INTO recipe (menu_id, ingredient_id, qty) VALUES
(3, 8, 0.15), -- Thịt bò 150g
(3, 1, 0.1),  -- Cà chua 100g
(3, 4, 0.1),  -- Hành tây 100g
(3, 5, 0.05); -- Tỏi 50g

-- ========================================
-- 5. RESTAURANT TABLES (Bàn ăn)
-- ========================================
INSERT INTO restaurant_table (number, status) VALUES
('B01', 'free'),
('B02', 'free'),
('B03', 'occupied'),
('B04', 'free'),
('B05', 'reserved'),
('B06', 'free'),
('B07', 'occupied'),
('B08', 'free'),
('B09', 'free'),
('B10', 'free'),
('B11', 'free'),
('B12', 'free');

-- ========================================
-- 6. INVENTORY RECEIPTS (Phiếu nhập)
-- ========================================
INSERT INTO inventory_receipt (created_by, supplier, receipt_date, note) VALUES
(1, 'Chợ Bến Thành', '2025-11-20', 'Nhập rau tươi hàng ngày'),
(1, 'Công ty An Phúc', '2025-11-20', 'Nhập thịt gà tươi'),
(2, 'Metro', '2025-11-19', 'Nhập gia vị và bột mì'),
(1, 'Công ty Thủy Sản', '2025-11-18', 'Nhập cá hồi tươi');

-- Chi tiết phiếu nhập
INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES
-- Receipt 1: Rau tươi
(1, 1, 5, 15000),   -- Cà chua 5kg
(1, 2, 3, 12000),   -- Dưa chuột 3kg
(1, 3, 5, 10000),   -- Bắp cải 5kg

-- Receipt 2: Thịt gà
(2, 7, 10, 80000),  -- Gà tươi 10kg

-- Receipt 3: Gia vị
(3, 13, 5, 60000),  -- Mì chính 5kg
(3, 21, 20, 12000), -- Bột mì 20kg

-- Receipt 4: Cá hồi
(4, 9, 3, 200000);  -- Cá hồi 3kg

-- ========================================
-- 7. INVENTORY ISSUES (Phiếu xuất)
-- ========================================
INSERT INTO inventory_issue (created_by, issue_type, issue_date, note) VALUES
(2, 'sale', '2025-11-26', 'Xuất kho bán hàng'),
(2, 'waste', '2025-11-25', 'Xuất hỏng do hết hạn'),
(1, 'manual', '2025-11-24', 'Xuất chỉnh lý kho');

-- Chi tiết phiếu xuất
INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES
-- Issue 1: Bán hàng
(1, 1, 2),   -- Cà chua 2kg
(1, 7, 1),   -- Gà tươi 1kg
(1, 9, 0.5), -- Cá hồi 0.5kg

-- Issue 2: Hỏng
(2, 2, 1),   -- Dưa chuột 1kg

-- Issue 3: Chỉnh lý
(3, 3, 2);   -- Bắp cải 2kg

-- ========================================
-- 8. SALE ORDERS (Đơn bán hàng)
-- ========================================
INSERT INTO sale_order (table_id, waiter_id, cashier_id, order_time, status, discount, vat_rate, total_amount) VALUES
(1, 3, 2, '2025-11-26 12:30:00', 'paid', 0, 10, 187000),
(2, 3, 2, '2025-11-26 13:00:00', 'served', 10000, 10, 75900),
(3, 3, 2, '2025-11-26 13:15:00', 'open', 0, 10, 165000),
(5, 3, 1, '2025-11-26 13:45:00', 'paid', 0, 10, 126500),
(7, 3, 2, '2025-11-26 14:00:00', 'served', 5000, 10, 227500);

-- Chi tiết đơn bán hàng
INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price, status) VALUES
-- Order 1: Cơm gà chiên + Bún chả cá + Nước
(1, 1, 1, 85000, 'served'),
(1, 2, 1, 75000, 'served'),
(1, 11, 1, 15000, 'served'),

-- Order 2: Phở bò + Bánh mì
(2, 3, 2, 60000, 'served'),
(2, 10, 1, 35000, 'served'),

-- Order 3: Cơm tấm + Gà nướng + Nước
(3, 4, 1, 65000, 'cooked'),
(3, 6, 1, 95000, 'ordered'),
(3, 11, 1, 15000, 'ordered'),

-- Order 4: Mỳ ý + Salad + Cà phê
(4, 5, 1, 90000, 'served'),
(4, 8, 1, 110000, 'served'),
(4, 12, 1, 20000, 'served'),

-- Order 5: Tôm xào bơ + Cơm chiên
(5, 7, 1, 120000, 'served'),
(5, 9, 1, 70000, 'served'),
(5, 11, 1, 15000, 'served');

-- ========================================
-- 9. EXPENSES (Chi phí)
-- ========================================
INSERT INTO expense (expense_type, amount, description, created_by, expense_date) VALUES
('Điện nước', 500000, 'Tiền điện nước tháng 11', 1, '2025-11-20'),
('Vệ sinh', 200000, 'Dịch vụ vệ sinh hàng tuần', 2, '2025-11-25'),
('Bảo trì', 300000, 'Sửa chữa máy lạnh', 1, '2025-11-24'),
('Marketing', 150000, 'In menu và tờ quảng cáo', 1, '2025-11-26'),
('Vật tư', 250000, 'Mua bàn ghế, đĩa bát', 2, '2025-11-22');

-- ========================================
-- 10. INVENTORY LOG (Nhật ký kho)
-- ========================================
INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES
(1, 5, 'receipt', 1, 'Nhập cà chua từ phiếu nhập 1', 1),
(7, 10, 'receipt', 2, 'Nhập gà tươi từ phiếu nhập 2', 1),
(1, 2, 'issue', 1, 'Xuất cà chua cho bán hàng', 2),
(7, 1, 'issue', 1, 'Xuất gà cho bán hàng', 2),
(2, 1, 'issue', 2, 'Xuất dưa chuột hỏng', 2);

-- ========================================
-- 11. STOCK ADJUSTMENT (Kiểm kê điều chỉnh)
-- ========================================
INSERT INTO stock_adjustment (ingredient_id, old_qty, new_qty, adjust_date, reason, note, adjusted_by) VALUES
(1, 10, 8, '2025-11-25', 'Hỏng', 'Cà chua héo do để lâu', 1),
(3, 15, 12, '2025-11-24', 'Hỏng', 'Bắp cải héo', 1);

-- ========================================
-- 12. AUDIT LOG (Nhật ký thao tác)
-- ========================================
INSERT INTO audit_log (user_id, action, target, detail) VALUES
(1, 'login', 'auth', 'Admin đăng nhập'),
(3, 'login', 'auth', 'Nhân viên đăng nhập'),
(1, 'create', 'menu_item', 'Tạo món ăn mới: Cơm gà chiên'),
(2, 'create', 'sale_order', 'Tạo đơn hàng #1'),
(2, 'update', 'sale_order', 'Cập nhật trạng thái đơn #1 thành paid'),
(1, 'create', 'inventory_receipt', 'Tạo phiếu nhập #1');

-- ========================================
-- DONE! Dữ liệu mẫu đã được thêm thành công
-- ========================================
