<?php
/**
 * Auth Controller - Xử lý đăng nhập, đăng xuất
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Hiển thị trang login
     */
    public function login() {
        // Nếu đã login, redirect về dashboard
        $currentUser = JWT::getCurrentUser();
        if ($currentUser) {
            $this->redirect('dashboard');
            return;
        }
        
        $this->view('auth/login');
    }
    
    /**
     * Xử lý đăng nhập (API)
     */
    public function doLogin() {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        $remember = $input['remember'] ?? false;
        
        // Validate
        if (empty($username) || empty($password)) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập'
            ], 400);
            return;
        }
        
        // Debug: Check if user exists
        $userExists = $this->userModel->findByUsername($username);
        
        if (!$userExists) {
            // Log failed login
            logAudit('login_failed', 'auth', "User not found: $username");
            
            $this->json([
                'success' => false,
                'message' => 'Tên đăng nhập không tồn tại'
            ], 401);
            return;
        }
        
        // Authenticate
        $user = $this->userModel->authenticate($username, $password);
        
        if (!$user) {
            // Log failed login
            logAudit('login_failed', 'auth', "Wrong password for: $username");
            
            $this->json([
                'success' => false,
                'message' => 'Mật khẩu không đúng'
            ], 401);
            return;
        }
        
        // Generate JWT
        $payload = $this->userModel->generateJWTPayload($user);
        $token = JWT::encode($payload);
        
        // Set cookie if remember me
        if ($remember) {
            setcookie('jwt_token', $token, time() + JWT_EXPIRATION, '/', '', false, true);
        }
        
        // Log successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        logAudit('login_success', 'auth', "User: {$user['username']}");
        
        $this->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $payload
        ]);
    }
    
    /**
     * Đăng xuất
     */
    public function logout() {
        // Log audit
        if (isLoggedIn()) {
            logAudit('logout', 'auth', '');
        }
        
        // Clear session
        session_destroy();
        
        // Clear cookie
        if (isset($_COOKIE['jwt_token'])) {
            setcookie('jwt_token', '', time() - 3600, '/');
        }
        
        $this->redirect('auth/login');
    }
    
    /**
     * Verify token (API)
     */
    public function verify() {
        header('Content-Type: application/json; charset=utf-8');
        
        $user = JWT::getCurrentUser();
        
        if ($user) {
            $this->json([
                'success' => true,
                'user' => $user
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Token không hợp lệ hoặc đã hết hạn'
            ], 401);
        }
    }
    
    /**
     * Refresh token (API)
     */
    public function refresh() {
        header('Content-Type: application/json; charset=utf-8');
        
        $user = JWT::getCurrentUser();
        
        if ($user) {
            // Generate new token
            $newToken = JWT::encode([
                'id' => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'role' => $user['role'],
                'active' => $user['active']
            ]);
            
            $this->json([
                'success' => true,
                'token' => $newToken
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Token không hợp lệ'
            ], 401);
        }
    }
}
