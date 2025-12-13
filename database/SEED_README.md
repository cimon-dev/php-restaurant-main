# Hướng dẫn sử dụng dữ liệu mẫu (Seed Data)

## Tổng quan

File `database/seed.sql` chứa dữ liệu mẫu cho tất cả các bảng trong hệ thống quản lý nhà hàng:
- 8 loại nguyên liệu (Ingredient Categories)
- 25 nguyên liệu (Ingredients)
- 12 món ăn (Menu Items)
- Công thức nấu ăn (Recipes)
- 12 bàn ăn (Restaurant Tables)
- 4 phiếu nhập kho (Inventory Receipts)
- 3 phiếu xuất kho (Inventory Issues)
- 5 đơn bán hàng (Sale Orders)
- 5 chi phí (Expenses)
- Logs và audit trails

## Cách chạy

### Cách 1: Dùng PHP script (Recommended)

```bash
cd C:\xampp\htdocs\php-restaurant-main
php database/seed.php
```

**Kết quả:**
- ✓ Thêm tất cả dữ liệu mẫu
- ✓ Hiển thị thông báo thành công/lỗi
- ✓ Dễ kiểm tra kết quả

### Cách 2: Import file SQL trực tiếp

```bash
mysql -u root -p restaurant_db < database/seed.sql
```

Nhập mật khẩu khi được yêu cầu (mặc định là trống nếu không đặt mật khẩu MySQL)

### Cách 3: Dùng phpMyAdmin

1. Mở **phpMyAdmin** → http://localhost/phpmyadmin/
2. Chọn database `restaurant_db`
3. Tìm tab **Import**
4. Chọn file `database/seed.sql`
5. Nhấp **Go**

## Dữ liệu được thêm

### 1. Người dùng (Users)
- `admin` / `admin123` (Quản trị viên)
- `manager` / `admin123` (Quản lý)
- `user` / `admin123` (Nhân viên)

### 2. Loại nguyên liệu
- Rau tươi
- Thịt cá
- Gia vị
- Bột mì
- Đồ uống
- Tương ớt
- Dầu mỡ
- Sữa phô mai

### 3. Nguyên liệu (25 items)
Ví dụ:
- Cà chua: 15,000₫/kg
- Gà tươi: 80,000₫/kg
- Cá hồi: 200,000₫/kg
- Tôm: 120,000₫/kg
- ... v.v

### 4. Món ăn (12 items)
- Cơm gà chiên: 85,000₫
- Bún chả cá: 75,000₫
- Phở bò: 60,000₫
- Mỳ ý carbonara: 90,000₫
- Tôm xào bơ tỏi: 120,000₫
- ... v.v

### 5. Công thức nấu ăn (Recipes)
Mỗi món ăn có công thức với danh sách nguyên liệu và lượng cần dùng

### 6. Bàn ăn
- 12 bàn (B01-B12)
- Một số bàn đang có khách (occupied)
- Một số bàn đã được đặt trước (reserved)

### 7. Phiếu nhập/xuất
- 4 phiếu nhập kho từ các nhà cung cấp khác nhau
- 3 phiếu xuất kho (bán hàng, hỏng, chỉnh lý)

### 8. Đơn bán hàng (5 orders)
- Một số đơn đã thanh toán (paid)
- Một số đơn đang phục vụ (served)
- Một số đơn đang mở (open)

### 9. Chi phí
- Điện nước
- Vệ sinh
- Bảo trì
- Marketing
- Vật tư

### 10. Logs & Audit
- Lịch sử đăng nhập
- Lịch sử thao tác hệ thống

## Lưu ý quan trọng

⚠️ **Nếu database đã có dữ liệu:**
- Script sẽ cố gắng INSERT dữ liệu mới
- Nếu gặp lỗi KEY UNIQUE (trùng code/name), script sẽ bỏ qua
- Để reset hoàn toàn, xóa database và import schema.sql trước seed.sql

### Reset database (nếu cần)

```bash
mysql -u root -p -e "DROP DATABASE restaurant_db;"
mysql -u root -p < database/schema.sql
php database/seed.php
```

## Dữ liệu mẫu được thiết kế để

✓ Giúp testing các tính năng của hệ thống
✓ Hiển thị giao diện đẹp với dữ liệu thực tế
✓ Kiểm tra các quan hệ giữa bảng
✓ Demo cho khách hàng
✓ Phát triển tính năng reporting

## Chỉnh sửa dữ liệu mẫu

Để thêm/sửa dữ liệu:

1. Mở file `database/seed.sql`
2. Tìm phần dữ liệu muốn sửa
3. Chỉnh sửa và lưu
4. Chạy lại script

Ví dụ: Thêm một món ăn mới

```sql
INSERT INTO menu_item (code, name, price, description) VALUES
('M013', 'Mực xào tương đen', 105000, 'Mực tươi xào tương đen');
```

## Troubleshooting

### ❌ Error: "database not found"
- Kiểm tra database đã được tạo: `mysql -u root -e "SHOW DATABASES;"`
- Nếu chưa có, chạy: `mysql -u root -p < database/schema.sql`

### ❌ Error: "Access denied for user"
- Kiểm tra user/password trong `config/database.php`
- Mặc định: user=`root`, password=`` (trống)

### ❌ Error: "Table already exists"
- Xóa dữ liệu cũ: `TRUNCATE TABLE table_name;` hoặc drop database

### ❌ Dữ liệu không hiển thị trên dashboard
- Kiểm tra xem script chạy thành công hay không
- Reload trang (Ctrl+F5)
- Kiểm tra browser console (F12) xem có lỗi không

## Liên hệ hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. MySQL đang chạy
2. Database tồn tại
3. Cấu hình kết nối trong `config/database.php` đúng
4. File `database/seed.sql` tồn tại

---

**Happy testing! 🎉**
