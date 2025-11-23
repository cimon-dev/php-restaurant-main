# Hướng Dẫn Cài Đặt và Sử Dụng

## 🚀 Cài Đặt

### Bước 1: Cấu hình XAMPP

1. Khởi động **Apache** và **MySQL** trong XAMPP Control Panel
2. Đảm bảo Apache chạy ở port 80 (hoặc cấu hình lại BASE_URL)

### Bước 2: Tạo Database

1. Mở **phpMyAdmin**: http://localhost/phpmyadmin
2. Tạo database mới tên: `restaurant`
3. Chọn Collation: `utf8mb4_unicode_ci`
4. Import file `database/schema.sql`

### Bước 3: Cấu hình Database

Mở file `config/database.php` và cập nhật:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurant');      // Tên database bạn tạo
define('DB_USER', 'root');            // Username MySQL
define('DB_PASS', '');                // Password MySQL (mặc định XAMPP để trống)
```

### Bước 4: Enable mod_rewrite (Apache)

1. Mở `C:\xampp\apache\conf\httpd.conf`
2. Tìm dòng: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Bỏ dấu `#` để enable module
4. Tìm `AllowOverride None` và đổi thành `AllowOverride All`
5. Restart Apache

### Bước 5: Kiểm Tra Kết Nối

Truy cập: http://localhost/restaurant/test_connection.php

Nếu thành công, bạn sẽ thấy:

-   ✅ Kết nối database thành công
-   Danh sách bảng và số records
-   Danh sách users mặc định

### Bước 6: Truy Cập Hệ Thống

Truy cập: http://localhost/restaurant

Hệ thống sẽ tự động redirect đến trang login.

---

## 👤 Tài Khoản Mặc Định

| Username | Password | Role          |
| -------- | -------- | ------------- |
| admin    | admin123 | Administrator |
| manager  | admin123 | Manager       |
| user     | admin123 | User          |

**Lưu ý:** Mật khẩu đã được hash bằng `password_hash()` trong database.

---

## 📁 Cấu Trúc Dự Án (MVC)

```
restaurant/
├── config/                  # Cấu hình
│   ├── config.php          # Cấu hình chung
│   ├── database.php        # Kết nối database
│   └── jwt.php             # Cấu hình JWT
│
├── core/                   # Core MVC
│   ├── App.php            # Router
│   ├── Controller.php     # Base Controller
│   └── Model.php          # Base Model
│
├── helpers/               # Helpers
│   └── JWT.php           # JWT Helper
│
├── app/
│   ├── controllers/      # Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── HomeController.php
│   │
│   ├── models/          # Models
│   │   └── User.php
│   │
│   └── views/           # Views
│       ├── auth/
│       │   └── login.php
│       └── dashboard/
│           └── index.php
│
├── database/            # Database
│   └── schema.sql
│
├── .htaccess           # URL Rewriting
├── index.php           # Entry Point
├── MVC_RULES.md        # Quy tắc MVC
└── README.md           # Hướng dẫn
```

---

## 🔗 URL Routing

### Cách hoạt động:

```
http://localhost/restaurant/{controller}/{method}/{params}
```

### Ví dụ:

| URL                  | Controller           | Method    |
| -------------------- | -------------------- | --------- |
| `/`                  | HomeController       | index()   |
| `/auth/login`        | AuthController       | login()   |
| `/auth/doLogin`      | AuthController       | doLogin() |
| `/dashboard`         | DashboardController  | index()   |
| `/ingredient`        | IngredientController | index()   |
| `/ingredient/create` | IngredientController | create()  |
| `/ingredient/edit/5` | IngredientController | edit(5)   |

---

## 🔐 JWT Authentication

### Flow đăng nhập:

1. User nhập username/password → POST `/auth/doLogin`
2. Server verify password bằng `password_verify()`
3. Nếu đúng → tạo JWT token với payload:
    ```json
    {
        "id": 1,
        "username": "admin",
        "fullname": "Administrator",
        "role": "admin",
        "active": true,
        "iat": 1234567890,
        "exp": 1234654290,
        "iss": "restaurant-management-system"
    }
    ```
4. Client lưu token vào `localStorage` và Cookie
5. Mỗi request kèm token trong header: `Authorization: Bearer {token}`

### Bảo vệ route:

```php
public function index() {
    $user = JWT::getCurrentUser();

    if (!$user) {
        $this->redirect('auth/login');
        return;
    }

    // Check role
    if ($user['role'] !== 'admin') {
        $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        return;
    }

    // Your code here
}
```

---

## 🔒 Password Hashing

### ✅ Đúng cách:

```php
// Khi tạo user mới
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$user->insert([
    'username' => $username,
    'password' => $hashedPassword,
    'fullname' => $fullname,
    'role' => $role
]);

// Khi verify password
if (password_verify($inputPassword, $user['password'])) {
    // Đăng nhập thành công
}
```

### ❌ Sai lầm thường gặp:

-   Lưu password dạng plain text
-   Dùng MD5, SHA1 (không an toàn)
-   Tự tạo thuật toán mã hóa

---

## 📊 Database Schema

### Bảng chính:

-   **users** - Người dùng & phân quyền
-   **ingredient** - Nguyên liệu
-   **menu_item** - Món ăn
-   **recipe** - Công thức món ăn
-   **inventory_receipt** - Phiếu nhập kho
-   **inventory_issue** - Phiếu xuất kho
-   **inventory_log** - Nhật ký kho
-   **restaurant_table** - Bàn ăn
-   **sale_order** - Đơn hàng
-   **expense** - Chi phí
-   **stock_adjustment** - Điều chỉnh kho
-   **audit_log** - Log hệ thống

---

## 🎨 Frontend

### Bootstrap 5

Đã tích hợp Bootstrap 5.3.0 qua CDN:

```html
<!-- CSS -->
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
/>

<!-- Icons -->
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"
/>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

### Components có sẵn:

-   ✅ Login page (responsive, animated)
-   ✅ Dashboard page (sidebar, navbar, stats cards)
-   ✅ Toast notifications
-   ✅ Loading states

---

## 🛠️ Development

### Tạo Module Mới

#### 1. Tạo Model (`app/models/Ingredient.php`)

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

#### 2. Tạo Controller (`app/controllers/IngredientController.php`)

```php
<?php
require_once BASE_PATH . '/core/Controller.php';

class IngredientController extends Controller {

    private $model;

    public function __construct() {
        $this->model = $this->model('Ingredient');
    }

    public function index() {
        $ingredients = $this->model->all();
        $this->view('ingredient/index', ['ingredients' => $ingredients]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPost();
            $id = $this->model->insert($data);
            $this->redirect('ingredient');
        }

        $this->view('ingredient/create');
    }
}
```

#### 3. Tạo View (`app/views/ingredient/index.php`)

```php
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Nguyên liệu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Danh sách nguyên liệu</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Đơn vị</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredients as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['unit']; ?></td>
                    <td><?php echo formatCurrency($item['purchase_price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
```

#### 4. Access

URL: http://localhost/restaurant/ingredient

---

## 📝 API Endpoints

### Authentication

| Method | Endpoint        | Description       |
| ------ | --------------- | ----------------- |
| GET    | `/auth/login`   | Login page        |
| POST   | `/auth/doLogin` | Process login     |
| GET    | `/auth/logout`  | Logout            |
| GET    | `/auth/verify`  | Verify JWT token  |
| POST   | `/auth/refresh` | Refresh JWT token |

### Example POST `/auth/doLogin`:

**Request:**

```json
{
    "username": "admin",
    "password": "admin123",
    "remember": true
}
```

**Response (Success):**

```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
        "id": 1,
        "username": "admin",
        "fullname": "Administrator",
        "role": "admin",
        "active": true
    }
}
```

**Response (Error):**

```json
{
    "success": false,
    "message": "Tên đăng nhập hoặc mật khẩu không đúng"
}
```

---

## 🔧 Troubleshooting

### Lỗi: "Database connection failed"

**Nguyên nhân:**

-   MySQL chưa khởi động
-   Thông tin kết nối sai
-   Database chưa tạo

**Giải pháp:**

1. Kiểm tra MySQL trong XAMPP Control Panel
2. Kiểm tra `config/database.php`
3. Tạo database `restaurant` trong phpMyAdmin
4. Import `database/schema.sql`

### Lỗi: "404 Not Found" khi truy cập URL

**Nguyên nhân:**

-   mod_rewrite chưa enable
-   .htaccess không hoạt động

**Giải pháp:**

1. Enable mod_rewrite trong `httpd.conf`
2. Đổi `AllowOverride None` → `AllowOverride All`
3. Restart Apache

### Lỗi: "Call to undefined function password_hash()"

**Nguyên nhân:**

-   PHP version < 5.5

**Giải pháp:**

-   Nâng cấp PHP lên version 7.4 trở lên

### JWT Token không hoạt động

**Kiểm tra:**

1. Token có được lưu vào localStorage không?
2. Token có được gửi trong header không?
3. Check browser Console và Network tab

---

## 📚 Helper Functions

### Sẵn có trong `config/config.php`:

```php
// Database
getDB() // Lấy PDO connection

// Formatting
formatCurrency($amount)      // Format: 10,000 đ
formatDate($date)            // Format: 17/11/2025
formatDateTime($datetime)    // Format: 17/11/2025 14:30

// Security
clean($data)                 // XSS protection

// Routing
redirect($url)               // Redirect to URL

// Flash Messages
setFlash($type, $message)    // Set flash message
getFlash()                   // Get and clear flash message

// Authentication
isLoggedIn()                 // Check if logged in
hasRole($roles)              // Check user role
requireLogin()               // Require login (redirect if not)
requireRole($roles)          // Require specific role

// Utilities
generateCode($prefix, $length) // Generate unique code
logAudit($action, $target, $detail) // Log to audit_log table
```

---

## 🚀 Next Steps

### Modules cần phát triển:

1. ✅ **Authentication** - Hoàn thành
2. 🔄 **Ingredient Management** - Quản lý nguyên liệu
3. 🔄 **Menu Management** - Quản lý món ăn
4. 🔄 **Recipe Management** - Công thức món ăn
5. 🔄 **Inventory** - Quản lý kho (nhập/xuất)
6. 🔄 **Table Management** - Quản lý bàn
7. 🔄 **Orders** - Quản lý đơn hàng
8. 🔄 **Expenses** - Quản lý chi phí
9. 🔄 **Reports** - Báo cáo thống kê
10. 🔄 **User Management** - Quản lý người dùng

---

## 📖 Documentation

-   **MVC Rules:** Đọc file `MVC_RULES.md` để hiểu quy tắc code
-   **Database Schema:** Xem file `database/schema.sql`
-   **API Documentation:** Sẽ bổ sung sau

---

## 🤝 Support

Nếu gặp vấn đề:

1. Check logs: `php_error.log` trong XAMPP
2. Check browser Console (F12)
3. Check Network tab để xem API responses
4. Đọc kỹ `MVC_RULES.md`

---

## 📝 Change Log

### Version 1.0.0 (17/11/2025)

-   ✅ Cấu trúc MVC hoàn chỉnh
-   ✅ JWT Authentication
-   ✅ Password Hashing (bcrypt)
-   ✅ Login/Logout system
-   ✅ Dashboard UI with Bootstrap 5
-   ✅ Base Model with CRUD operations
-   ✅ Base Controller with helper methods
-   ✅ URL Routing system
-   ✅ Security: XSS protection, Prepared Statements
-   ✅ Audit Logging
-   ✅ Database schema with 15 tables
-   ✅ Responsive UI

---

**Happy Coding! 🎉**
