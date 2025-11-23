# MVC Structure Rules - Restaurant Management System

## Quy tắc đọc mỗi khi tạo request

### 1. Cấu trúc thư mục MVC

```
restaurant/
├── config/              # Cấu hình
│   ├── config.php      # Cấu hình chung
│   ├── database.php    # Kết nối database
│   └── jwt.php         # Cấu hình JWT
│
├── core/               # Core MVC files
│   ├── App.php         # Router chính
│   ├── Controller.php  # Base Controller
│   └── Model.php       # Base Model
│
├── helpers/            # Helper classes
│   └── JWT.php         # JWT Helper
│
├── app/
│   ├── controllers/    # Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── HomeController.php
│   │
│   ├── models/         # Models
│   │   └── User.php
│   │
│   └── views/          # Views
│       ├── auth/
│       │   └── login.php
│       └── dashboard/
│           └── index.php
│
├── public/             # Public assets
│   ├── css/
│   ├── js/
│   └── images/
│
├── database/           # Database files
│   └── schema.sql
│
├── .htaccess          # URL rewriting
└── index.php          # Entry point
```

### 2. Routing

**URL Pattern:**

```
http://localhost/restaurant/{controller}/{method}/{params}
```

**Examples:**

-   `/` → HomeController::index()
-   `/auth/login` → AuthController::login()
-   `/auth/doLogin` → AuthController::doLogin()
-   `/dashboard` → DashboardController::index()
-   `/ingredient/edit/5` → IngredientController::edit(5)

### 3. Controller Rules

**File naming:** `{Name}Controller.php`
**Class naming:** `{Name}Controller extends Controller`

```php
<?php
require_once BASE_PATH . '/core/Controller.php';

class ExampleController extends Controller {

    // Load model trong constructor
    private $model;

    public function __construct() {
        $this->model = $this->model('ModelName');
    }

    // Method để hiển thị view
    public function index() {
        $data = ['key' => 'value'];
        $this->view('folder/file', $data);
    }

    // Method để trả về JSON (API)
    public function apiMethod() {
        $result = ['success' => true, 'data' => []];
        $this->json($result, 200);
    }
}
```

### 4. Model Rules

**File naming:** `{Name}.php`
**Class naming:** `{Name} extends Model`

```php
<?php
require_once BASE_PATH . '/core/Model.php';

class Example extends Model {

    protected $table = 'table_name';

    // Custom methods
    public function customMethod() {
        // Your logic here
    }
}
```

**Available Base Methods:**

-   `all()` - Get all records
-   `find($id)` - Find by ID
-   `findBy($field, $value)` - Find by field
-   `where($conditions)` - Get with conditions
-   `insert($data)` - Insert new record
-   `update($id, $data)` - Update record
-   `delete($id)` - Delete record
-   `count($conditions)` - Count records
-   `query($sql, $params)` - Custom query

### 5. View Rules

**File location:** `app/views/{folder}/{file}.php`

**Loading view:**

```php
$this->view('folder/file', ['data' => $value]);
```

**In view file:**

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle ?? 'Default Title'; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Content -->
    <?php echo $data; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### 6. JWT Authentication Rules

**Creating JWT:**

```php
require_once BASE_PATH . '/helpers/JWT.php';

$payload = [
    'id' => $user['id'],
    'username' => $user['username'],
    'fullname' => $user['fullname'],
    'role' => $user['role'],
    'active' => (bool)$user['active']
];

$token = JWT::encode($payload);
```

**Verifying JWT:**

```php
$user = JWT::getCurrentUser();

if (!$user) {
    $this->redirect('auth/login');
    return;
}
```

**In JavaScript:**

```javascript
// Store token
localStorage.setItem("jwt_token", token);

// Send token in request
fetch(url, {
    headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt_token")}`,
    },
});
```

### 7. Password Hashing Rules

**ALWAYS hash passwords before saving:**

```php
// Creating new user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$data = [
    'username' => $username,
    'password' => $hashedPassword,
    'fullname' => $fullname,
    'role' => $role
];

$userId = $userModel->insert($data);
```

**Verifying password:**

```php
$user = $userModel->findByUsername($username);

if ($user && password_verify($password, $user['password'])) {
    // Password is correct
    return $user;
}
```

**NEVER:**

-   ❌ Store plain text passwords
-   ❌ Use MD5 or SHA1 for passwords
-   ❌ Use custom encryption

**ALWAYS:**

-   ✅ Use `password_hash()` with PASSWORD_DEFAULT
-   ✅ Use `password_verify()` for checking
-   ✅ Hash in Model layer, not Controller

### 8. Database Query Rules

**Prepared Statements ALWAYS:**

```php
// ✅ CORRECT - Using prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

// ❌ WRONG - SQL Injection risk
$query = "SELECT * FROM users WHERE username = '$username'";
```

**Using Base Model methods:**

```php
// Find by ID
$user = $userModel->find(1);

// Find by field
$user = $userModel->findBy('username', 'admin');

// Insert
$id = $userModel->insert([
    'username' => 'newuser',
    'password' => password_hash('password', PASSWORD_DEFAULT)
]);

// Update
$userModel->update($id, ['fullname' => 'New Name']);

// Delete
$userModel->delete($id);
```

### 9. API Response Rules

**Success Response:**

```php
$this->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $result
], 200);
```

**Error Response:**

```php
$this->json([
    'success' => false,
    'message' => 'Error message',
    'errors' => ['field' => 'error detail']
], 400);
```

**HTTP Status Codes:**

-   200: OK
-   201: Created
-   400: Bad Request
-   401: Unauthorized
-   403: Forbidden
-   404: Not Found
-   500: Internal Server Error

### 10. Security Rules

**Input Validation:**

```php
// Validate required fields
$errors = $this->validateRequired($_POST, ['username', 'password']);

// Clean input
$username = clean($_POST['username']);

// Validate in Model
public function validate($data) {
    $errors = [];

    if (empty($data['username'])) {
        $errors['username'] = 'Username is required';
    }

    if (strlen($data['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    return $errors;
}
```

**XSS Protection:**

```php
// In view
echo clean($user['username']);
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
```

**CSRF Protection (TODO):**

-   Implement CSRF tokens for forms
-   Validate on POST requests

### 11. Naming Conventions

**PHP:**

-   Classes: `PascalCase` (UserController, IngredientModel)
-   Methods: `camelCase` (getUserById, createOrder)
-   Variables: `camelCase` ($userName, $totalAmount)
-   Constants: `UPPER_CASE` (DB_HOST, JWT_SECRET_KEY)
-   Database tables: `snake_case` (users, menu_item, sale_order)

**JavaScript:**

-   Variables: `camelCase` (userName, totalAmount)
-   Functions: `camelCase` (getUserData, showAlert)
-   Constants: `UPPER_CASE` (BASE_URL, API_ENDPOINT)

**Files:**

-   Controllers: `{Name}Controller.php`
-   Models: `{Name}.php`
-   Views: `{folder}/{name}.php`

### 12. Error Handling

**Try-Catch in Controllers:**

```php
public function create() {
    try {
        $data = $this->getPost();

        // Validate
        $errors = $this->validateRequired($data, ['name', 'price']);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

        // Save
        $id = $this->model->insert($data);

        // Log
        logAudit('create', 'ingredient', "Created ingredient ID: $id");

        $this->json(['success' => true, 'id' => $id], 201);

    } catch (Exception $e) {
        error_log($e->getMessage());
        $this->json(['success' => false, 'message' => 'Server error'], 500);
    }
}
```

### 13. Logging Rules

**Use logAudit() function:**

```php
// Defined in config/config.php
logAudit($action, $target, $detail);

// Examples
logAudit('login', 'auth', "User: admin");
logAudit('create', 'ingredient', "Created: Tomato");
logAudit('update', 'menu_item', "Updated: Pizza ID 5");
logAudit('delete', 'sale_order', "Deleted order ID 10");
```

### 14. Helper Functions (Available globally)

From `config/config.php`:

-   `getDB()` - Get database connection
-   `formatCurrency($amount)` - Format tiền VNĐ
-   `formatDate($date)` - Format ngày
-   `formatDateTime($datetime)` - Format ngày giờ
-   `clean($data)` - XSS protection
-   `redirect($url)` - Redirect
-   `setFlash($type, $message)` - Set flash message
-   `getFlash()` - Get flash message
-   `isLoggedIn()` - Check if logged in
-   `hasRole($roles)` - Check user role
-   `requireLogin()` - Require login
-   `requireRole($roles)` - Require specific role
-   `generateCode($prefix, $length)` - Generate unique code
-   `logAudit($action, $target, $detail)` - Log audit

### 15. Development Workflow

**Creating new module:**

1. **Create Model** (`app/models/{Name}.php`)
2. **Create Controller** (`app/controllers/{Name}Controller.php`)
3. **Create Views** (`app/views/{folder}/`)
4. **Test routes** (Automatically work via MVC router)

**Example - Creating Ingredient Module:**

```bash
# Files to create:
app/models/Ingredient.php
app/controllers/IngredientController.php
app/views/ingredient/index.php
app/views/ingredient/create.php
app/views/ingredient/edit.php
```

### 16. Git Ignore Rules

Create `.gitignore`:

```
vendor/
node_modules/
.env
*.log
.DS_Store
Thumbs.db
/uploads/*
!/uploads/.gitkeep
```

---

## Quick Reference

**Create new controller:**

```php
require_once BASE_PATH . '/core/Controller.php';

class NewController extends Controller {
    public function index() {
        $this->view('folder/file');
    }
}
```

**Create new model:**

```php
require_once BASE_PATH . '/core/Model.php';

class NewModel extends Model {
    protected $table = 'table_name';
}
```

**Protect route with auth:**

```php
public function index() {
    $user = JWT::getCurrentUser();
    if (!$user) {
        $this->redirect('auth/login');
        return;
    }

    // Your code here
}
```

**Check role:**

```php
if ($user['role'] !== 'admin') {
    $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
    return;
}
```
