# Restaurant Management System (PHP MVC)

Restaurant, inventory, and expense management built with a lightweight PHP MVC stack, Bootstrap 5 UI, PDO, and JWT-based authentication.

## Features

- MVC core with routing plus base Controller/Model using PDO prepared statements
- JWT authentication with role-based access (admin, manager, user) and password hashing
- Responsive Bootstrap 5 interface with vanilla JS/Fetch
- Modules for ingredients, menu items, recipes, stock receipts/issues, sales orders, and expenses
- Audit logging and helper utilities for common tasks

## Quick Start

1. Requirements: PHP 7.4+, MySQL 5.7+, Apache with mod_rewrite enabled.
2. Get code: place the project under your web root (for XAMPP: C:\xampp\htdocs\php-restaurant-main).
3. Database:
   - Create database `restaurant_db` with utf8mb4 collation.
   - Import `database/schema.sql` (DDL only).
4. Configure app:
   - Edit `config/database.php` (set DB_HOST, DB_NAME=restaurant_db, DB_USER, DB_PASS).
   - Update `config/jwt.php` if you change the secret or expiry.
5. Create an admin user:
   - CLI: `php scripts/create_admin.php admin yourpassword`
   - HTTP (local only): `/scripts/create_admin.php?username=admin&password=yourpassword`
6. Run:
   - Start Apache/MySQL, then open `http://localhost/php-restaurant-main/` (adjust if the folder name differs).
   - Routing follows `/{controller}/{method}/{params}` (details in `MVC_RULES.md`).

## Project Structure

- config/: application, database, and JWT configuration
- core/: router/App, base Controller, base Model
- helpers/: shared helpers (JWT)
- app/controllers | app/models | app/views: MVC modules
- database/: schema.sql (DDL), seed.sql (optional sample data), migrate_* utilities
- public/: css/js assets
- scripts/: maintenance utilities (e.g., create_admin.php)

## Documentation

- INSTALLATION.md for step-by-step setup
- MVC_RULES.md for coding conventions and routing details

## Notes

- `database/schema.sql` now contains only table definitions. Use `database/seed.sql` if you need sample data.
- Remove or lock down `scripts/create_admin.php` in production environments.
-   **recipe** - Công thức món ăn (nguyên liệu của từng món)
-   **inventory_receipt** - Phiếu nhập kho
-   **inventory_issue** - Phiếu xuất kho
-   **inventory_log** - Nhật ký kho
-   **restaurant_table** - Quản lý bàn ăn
-   **sale_order** - Đơn bán hàng
-   **expense** - Chi phí khác
-   **stock_adjustment** - Điều chỉnh/kiểm kê kho
-   **audit_log** - Nhật ký hệ thống

## Cấu trúc thư mục

```
restaurant/
├── config/
│   ├── database.php      # Cấu hình kết nối DB
│   └── config.php        # Cấu hình chung
├── database/
│   └── schema.sql        # File SQL tạo database
├── test_connection.php   # Test kết nối
└── README.md            # File hướng dẫn này
```

## Tính năng chính (sẽ phát triển)

-   ✅ Kết nối database với PDO
-   ✅ Quản lý người dùng và phân quyền
-   🔄 Quản lý nguyên liệu
-   🔄 Quản lý món ăn và công thức
-   🔄 Quản lý kho (nhập/xuất/tồn kho)
-   🔄 Quản lý bàn ăn và order
-   🔄 Quản lý chi phí
-   🔄 Báo cáo doanh thu và chi phí
-   🔄 Nhật ký audit log

## Công nghệ sử dụng

-   **Backend:** PHP (PDO)
-   **Database:** MySQL
-   **Pattern:** Singleton, MVC (dự kiến)
-   **Security:** Prepared Statements, Password Hashing, XSS Protection

## Liên hệ & Hỗ trợ

Nếu có vấn đề, vui lòng kiểm tra:

1. XAMPP đã khởi động Apache và MySQL chưa
2. Database đã được tạo và import đúng chưa
3. Cấu hình trong `config/database.php` có đúng không
