<?php

/**
 * File cấu hình chung của hệ thống
 */

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (development mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đường dẫn gốc
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/php-restaurant-main');

// Require database config
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/jwt.php';

// Cấu hình phân trang
define('RECORDS_PER_PAGE', 20);

// Cấu hình upload
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// VAT mặc định
define('DEFAULT_VAT', 10); // 10%

// Format tiền tệ
function formatCurrency($amount)
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

// Format ngày tháng
function formatDate($date, $format = 'd/m/Y')
{
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i')
{
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

// Xử lý XSS
function clean($data)
{
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Redirect
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

// Flash message
function setFlash($type, $message)
{
    $_SESSION['flash_type'] = $type; // success, error, warning, info
    $_SESSION['flash_message'] = $message;
}

function getFlash()
{
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Kiểm tra đăng nhập
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Kiểm tra quyền
function hasRole($roles)
{
    if (!isLoggedIn()) return false;
    if (is_string($roles)) {
        $roles = [$roles];
    }
    return in_array($_SESSION['user_role'], $roles);
}

// Require login
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/login.php');
    }
}

// Require role
function requireRole($roles)
{
    requireLogin();
    if (!hasRole($roles)) {
        setFlash('error', 'Bạn không có quyền truy cập chức năng này!');
        redirect(BASE_URL . '/index.php');
    }
}

// Generate unique code
function generateCode($prefix, $length = 8)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = $prefix;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Log audit
function logAudit($action, $target, $detail = '')
{
    if (!isLoggedIn()) return;

    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO audit_log (user_id, action, target, detail) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $target,
            $detail
        ]);
    } catch (PDOException $e) {
        error_log("Audit log error: " . $e->getMessage());
    }
}
