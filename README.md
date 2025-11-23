# 🍽️ Restaurant Management System

Hệ thống quản lý nhà hàng, nguyên liệu và chi phí được xây dựng bằng **PHP MVC**, **Bootstrap 5**, và **JWT Authentication**.

## ✨ Tính năng

-   ✅ **MVC Architecture** - Cấu trúc rõ ràng, dễ bảo trì
-   ✅ **JWT Authentication** - Bảo mật với JSON Web Tokens
-   ✅ **Password Hashing** - Mã hóa mật khẩu bằng bcrypt
-   ✅ **Bootstrap 5 UI** - Giao diện đẹp, responsive
-   ✅ **PDO Database** - Prepared Statements chống SQL Injection
-   ✅ **Role-based Access** - Phân quyền Admin/Manager/User
-   ✅ **Audit Logging** - Ghi nhận mọi thao tác
-   🔄 **Quản lý nguyên liệu** - (Đang phát triển)
-   🔄 **Quản lý món ăn** - (Đang phát triển)
-   🔄 **Quản lý kho** - (Đang phát triển)
-   🔄 **Quản lý đơn hàng** - (Đang phát triển)
-   🔄 **Báo cáo thống kê** - (Đang phát triển)

## 🚀 Cài Đặt Nhanh

### 1. Requirements

-   PHP 7.4+
-   MySQL 5.7+
-   XAMPP/LAMP/WAMP
-   Apache mod_rewrite enabled

### 2. Clone Project

```bash
cd C:\xampp\htdocs\
git clone [your-repo] restaurant
# hoặc copy thư mục vào C:\xampp\htdocs\restaurant
```

### 3. Tạo Database

```sql
-- Trong phpMyAdmin (http://localhost/phpmyadmin)
CREATE DATABASE restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Import Database

Import file `database/schema.sql` vào database `restaurant` vừa tạo.

### 5. Cấu hình

Mở `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurant');    // Tên database
define('DB_USER', 'root');          // MySQL username
define('DB_PASS', '');              // MySQL password
```

### 6. Enable mod_rewrite

**Windows (XAMPP):**

1. Mở `C:\xampp\apache\conf\httpd.conf`
2. Tìm `#LoadModule rewrite_module` và bỏ dấu `#`
3. Đổi `AllowOverride None` → `AllowOverride All`
4. Restart Apache

### 7. Truy cập

🌐 **URL:** http://localhost/restaurant

📋 **Tài khoản mặc định:**

| Username | Password | Role    |
| -------- | -------- | ------- |
| admin    | admin123 | Admin   |
| manager  | admin123 | Manager |
| user     | admin123 | User    |

## 📖 Documentation

-   📘 [Installation Guide](INSTALLATION.md) - Hướng dẫn cài đặt chi tiết
-   📗 [MVC Rules](MVC_RULES.md) - Quy tắc code MVC (ĐỌC TRƯỚC KHI CODE)

## 📁 Cấu Trúc MVC

```
restaurant/
├── 📂 config/              # Cấu hình
│   ├── config.php         # Cấu hình chung, helper functions
│   ├── database.php       # Kết nối PDO
│   └── jwt.php            # Cấu hình JWT
│
├── 📂 core/               # Core MVC Framework
│   ├── App.php           # Router - URL mapping
│   ├── Controller.php    # Base Controller
│   └── Model.php         # Base Model với CRUD
│
├── 📂 helpers/           # Helper Classes
│   └── JWT.php          # JWT encode/decode
│
├── 📂 app/
│   ├── 📂 controllers/  # Controllers (xử lý request)
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── HomeController.php
│   │
│   ├── 📂 models/       # Models (xử lý database)
│   │   └── User.php
│   │
│   └── 📂 views/        # Views (hiển thị giao diện)
│       ├── auth/
│       │   └── login.php
│       └── dashboard/
│           └── index.php
│
├── 📂 database/
│   └── schema.sql       # Database schema + seed data
│
├── 📄 .htaccess         # URL Rewriting
├── 📄 index.php         # Entry Point
├── 📄 MVC_RULES.md      # ⚠️ Quy tắc code MVC - ĐỌC TRƯỚC KHI CODE
└── 📄 INSTALLATION.md   # Hướng dẫn cài đặt chi tiết
```

## 🔗 URL Routing

Hệ thống tự động route URL theo pattern:

```
http://localhost/restaurant/{controller}/{method}/{params}
```

**Ví dụ:**

| URL                  | Controller → Method           |
| -------------------- | ----------------------------- |
| `/`                  | HomeController::index()       |
| `/auth/login`        | AuthController::login()       |
| `/dashboard`         | DashboardController::index()  |
| `/ingredient`        | IngredientController::index() |
| `/ingredient/edit/5` | IngredientController::edit(5) |

## 🔐 Authentication Flow

1. User login → POST `/auth/doLogin`
2. Server verify password bằng `password_verify()`
3. Generate JWT token với payload:
    ```json
    {
        "id": 1,
        "username": "admin",
        "fullname": "Administrator",
        "role": "admin",
        "active": true,
        "iat": 1234567890,
        "exp": 1234654290
    }
    ```
4. Client lưu token vào `localStorage` + Cookie
5. Mỗi request gửi token qua header: `Authorization: Bearer {token}`

## 🛡️ Security Features

-   ✅ **Password Hashing** - `password_hash()` với bcrypt
-   ✅ **Prepared Statements** - Chống SQL Injection
-   ✅ **XSS Protection** - `htmlspecialchars()` + `clean()` function
-   ✅ **JWT Token** - Stateless authentication
-   ✅ **Role-based Access** - Admin, Manager, User
-   ✅ **Audit Logging** - Ghi nhận mọi thao tác quan trọng

## 🎨 Frontend

-   **Framework:** Bootstrap 5.3.0
-   **Icons:** Bootstrap Icons 1.11.0
-   **JavaScript:** Vanilla JS (ES6+)
-   **AJAX:** Fetch API
-   **Responsive:** Mobile-first design

## 📊 Database Schema

### 15 Tables:

1. **users** - Người dùng & phân quyền
2. **ingredient** - Nguyên liệu
3. **menu_item** - Món ăn
4. **recipe** - Công thức (nguyên liệu của món)
5. **inventory_receipt** - Phiếu nhập kho
6. **inventory_receipt_detail** - Chi tiết phiếu nhập
7. **inventory_issue** - Phiếu xuất kho
8. **inventory_issue_detail** - Chi tiết phiếu xuất
9. **inventory_log** - Nhật ký kho
10. **restaurant_table** - Bàn ăn
11. **sale_order** - Đơn hàng
12. **sale_order_detail** - Chi tiết đơn hàng
13. **expense** - Chi phí
14. **stock_adjustment** - Điều chỉnh kho
15. **audit_log** - Log hệ thống

## 🛠️ Development

### Tạo Module Mới (Ví dụ: Ingredient)

**1. Model** (`app/models/Ingredient.php`):

```php
<?php
require_once BASE_PATH . '/core/Model.php';

class Ingredient extends Model {
    protected $table = 'ingredient';

    public function getByCategory($category) {
        return $this->where(['category' => $category]);
    }
}
```

**2. Controller** (`app/controllers/IngredientController.php`):

```php
<?php
require_once BASE_PATH . '/core/Controller.php';

class IngredientController extends Controller {
    private $model;

    public function __construct() {
        $this->model = $this->model('Ingredient');
    }

    public function index() {
        $data = $this->model->all();
        $this->view('ingredient/index', ['items' => $data]);
    }
}
```

**3. View** (`app/views/ingredient/index.php`):

```php
<!-- Bootstrap 5 HTML -->
<div class="container">
    <h1>Nguyên liệu</h1>
    <?php foreach($items as $item): ?>
        <div><?= $item['name'] ?></div>
    <?php endforeach; ?>
</div>
```

**4. Access:** http://localhost/restaurant/ingredient

### Helper Functions

```php
getDB()                          // Get PDO connection
formatCurrency($amount)          // 10000 → "10,000 đ"
formatDate($date)                // Format ngày
clean($data)                     // XSS protection
redirect($url)                   // Redirect
isLoggedIn()                     // Check login
hasRole(['admin', 'manager'])    // Check role
requireLogin()                   // Require login or redirect
logAudit($action, $target, $detail) // Log to audit_log
```

## 🧪 Testing

### Test Connection:

```
http://localhost/restaurant/test_connection.php
```

### Test Login:

1. Truy cập: http://localhost/restaurant/auth/login
2. Login với: `admin` / `admin123`
3. Redirect về Dashboard

## 📝 API Endpoints

### Authentication

| Method | Endpoint        | Description          |
| ------ | --------------- | -------------------- |
| GET    | `/auth/login`   | Login page           |
| POST   | `/auth/doLogin` | Process login (JSON) |
| GET    | `/auth/logout`  | Logout               |
| GET    | `/auth/verify`  | Verify JWT token     |
| POST   | `/auth/refresh` | Refresh token        |

**Example POST `/auth/doLogin`:**

Request:

```json
{
    "username": "admin",
    "password": "admin123",
    "remember": true
}
```

Response:

```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "token": "eyJhbGc...",
    "user": {
        "id": 1,
        "username": "admin",
        "fullname": "Administrator",
        "role": "admin",
        "active": true
    }
}
```

## 🔧 Troubleshooting

### Database connection error

-   ✅ MySQL đã chạy trong XAMPP?
-   ✅ Database `restaurant` đã tạo?
-   ✅ File schema.sql đã import?
-   ✅ Config trong `config/database.php` đúng?

### 404 Not Found

-   ✅ mod_rewrite đã enable?
-   ✅ File `.htaccess` tồn tại?
-   ✅ `AllowOverride All` trong httpd.conf?
-   ✅ Apache đã restart?

### JWT Token không hoạt động

-   ✅ Token lưu trong localStorage?
-   ✅ Token gửi trong header?
-   ✅ Check Console và Network tab

## 🚀 Roadmap

-   [x] MVC Framework
-   [x] JWT Authentication
-   [x] Login/Logout
-   [x] Dashboard UI
-   [ ] Quản lý nguyên liệu (CRUD)
-   [ ] Quản lý món ăn (CRUD)
-   [ ] Quản lý công thức
-   [ ] Nhập/Xuất kho
-   [ ] Quản lý bàn ăn
-   [ ] Quản lý đơn hàng
-   [ ] Báo cáo thống kê
-   [ ] Quản lý người dùng
-   [ ] Export Excel/PDF

## 🤝 Contributing

1. Fork project
2. Tạo branch mới: `git checkout -b feature/AmazingFeature`
3. Commit changes: `git commit -m 'Add some AmazingFeature'`
4. Push to branch: `git push origin feature/AmazingFeature`
5. Open Pull Request

**⚠️ Lưu ý:** Đọc kỹ file `MVC_RULES.md` trước khi code!

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Author

Restaurant Management System - 2025

## 📞 Support

-   📧 Email: [your-email]
-   📖 Documentation: Xem `INSTALLATION.md` và `MVC_RULES.md`
-   🐛 Issues: [GitHub Issues]

---

**Made with ❤️ using PHP MVC + Bootstrap 5 + JWT**

| Username | Password | Role    |
| -------- | -------- | ------- |
| admin    | admin123 | Admin   |
| manager  | admin123 | Manager |
| user     | admin123 | User    |

## Cấu trúc Database

### Bảng chính

-   **users** - Quản lý người dùng và phân quyền
-   **ingredient** - Danh mục nguyên liệu
-   **menu_item** - Danh mục món ăn
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
