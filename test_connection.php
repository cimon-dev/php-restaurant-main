<?php
/**
 * File kiểm tra kết nối database
 * Truy cập file này để test kết nối
 */

require_once __DIR__ . '/config/config.php';

try {
    $db = getDB();
    
    echo "<h2>✅ Kết nối database thành công!</h2>";
    
    // Lấy thông tin database
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p><strong>Database:</strong> " . $result['db_name'] . "</p>";
    
    // Đếm số bảng
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<p><strong>Số bảng:</strong> " . count($tables) . "</p>";
    
    // Liệt kê các bảng
    echo "<h3>Danh sách bảng:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        
        // Đếm số record trong mỗi bảng
        $stmt = $db->query("SELECT COUNT(*) as count FROM `$tableName`");
        $count = $stmt->fetch()['count'];
        
        echo "<li><strong>$tableName</strong> - $count records</li>";
    }
    echo "</ul>";
    
    // Test query users
    echo "<h3>Danh sách Users:</h3>";
    $stmt = $db->query("SELECT id, username, fullname, role, active FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Fullname</th><th>Role</th><th>Active</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['fullname']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>" . ($user['active'] ? '✓' : '✗') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><em>Password mặc định cho tất cả user: <strong>admin123</strong></em></p>";
    } else {
        echo "<p>Chưa có user nào. Vui lòng import file database/schema.sql</p>";
    }
    
    echo "<hr>";
    echo "<p><strong>Hướng dẫn:</strong></p>";
    echo "<ol>";
    echo "<li>Đảm bảo XAMPP đang chạy (Apache + MySQL)</li>";
    echo "<li>Tạo database 'restaurant_db' trong phpMyAdmin</li>";
    echo "<li>Import file database/schema.sql vào database</li>";
    echo "<li>Cấu hình thông tin database trong config/database.php nếu cần</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Lỗi kết nối database!</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
    
    echo "<hr>";
    echo "<p><strong>Các bước khắc phục:</strong></p>";
    echo "<ol>";
    echo "<li>Kiểm tra XAMPP đã khởi động MySQL chưa</li>";
    echo "<li>Kiểm tra thông tin cấu hình trong <code>config/database.php</code>:
        <ul>
            <li>DB_HOST: " . DB_HOST . "</li>
            <li>DB_NAME: " . DB_NAME . "</li>
            <li>DB_USER: " . DB_USER . "</li>
        </ul>
    </li>";
    echo "<li>Tạo database 'restaurant_db' trong phpMyAdmin nếu chưa có</li>";
    echo "<li>Kiểm tra username/password MySQL có đúng không</li>";
    echo "</ol>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 30px; }
    table { 
        background: white; 
        width: 100%;
        margin: 20px 0;
    }
    code {
        background: #eee;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
    ul, ol {
        line-height: 1.8;
    }
</style>
