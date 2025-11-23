<?php
/**
 * Debug Login - Kiểm tra password hash
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>🔍 Debug Login System</h2>";
echo "<hr>";

// Test password
$testPassword = 'admin123';
echo "<h3>Test Password: <code>$testPassword</code></h3>";

try {
    $db = getDB();
    
    // Get all users
    $stmt = $db->query("SELECT id, username, password, fullname, role, active FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p style='color: red;'>❌ Không có user nào trong database!</p>";
        echo "<p>Vui lòng chạy SQL:</p>";
        echo "<pre>";
        echo "INSERT INTO users (username, password, fullname, role, active) VALUES\n";
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        echo "('admin', '$hash', 'Administrator', 'admin', TRUE),\n";
        echo "('manager', '$hash', 'Manager', 'manager', TRUE),\n";
        echo "('user', '$hash', 'User', 'user', TRUE);";
        echo "</pre>";
        exit;
    }
    
    echo "<h3>Danh sách Users trong Database:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>
            <th>ID</th>
            <th>Username</th>
            <th>Fullname</th>
            <th>Role</th>
            <th>Active</th>
            <th>Password (Hash)</th>
            <th>Verify Test</th>
          </tr>";
    
    foreach ($users as $user) {
        $verifyResult = password_verify($testPassword, $user['password']);
        $verifyColor = $verifyResult ? 'green' : 'red';
        $verifyIcon = $verifyResult ? '✅' : '❌';
        
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['fullname']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>" . ($user['active'] ? '✓' : '✗') . "</td>";
        echo "<td><small>" . substr($user['password'], 0, 40) . "...</small></td>";
        echo "<td style='color: $verifyColor; font-weight: bold;'>$verifyIcon " . ($verifyResult ? 'PASS' : 'FAIL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Test Authentication Function:</h3>";
    
    require_once BASE_PATH . '/app/models/User.php';
    $userModel = new User();
    
    $testUser = $userModel->authenticate('admin', 'admin123');
    
    if ($testUser) {
        echo "<p style='color: green; font-size: 18px;'>✅ <strong>Authentication SUCCESS!</strong></p>";
        echo "<pre>";
        print_r($testUser);
        echo "</pre>";
    } else {
        echo "<p style='color: red; font-size: 18px;'>❌ <strong>Authentication FAILED!</strong></p>";
        
        // Debug steps
        echo "<h4>Debug Steps:</h4>";
        $user = $userModel->findByUsername('admin');
        
        if (!$user) {
            echo "<p style='color: red;'>1. ❌ User 'admin' not found in database</p>";
        } else {
            echo "<p style='color: green;'>1. ✅ User 'admin' found</p>";
            
            if (!$user['active']) {
                echo "<p style='color: red;'>2. ❌ User is not active</p>";
            } else {
                echo "<p style='color: green;'>2. ✅ User is active</p>";
                
                $passwordCheck = password_verify('admin123', $user['password']);
                if (!$passwordCheck) {
                    echo "<p style='color: red;'>3. ❌ Password verification failed</p>";
                    echo "<p>Stored hash: <code>{$user['password']}</code></p>";
                    
                    // Generate new hash
                    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
                    echo "<p><strong>Fix:</strong> Update password with new hash:</p>";
                    echo "<pre>UPDATE users SET password = '$newHash' WHERE username = 'admin';</pre>";
                } else {
                    echo "<p style='color: green;'>3. ✅ Password verification passed</p>";
                }
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Generate New Password Hash:</h3>";
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "<p><strong>New Hash for 'admin123':</strong></p>";
    echo "<pre>$newHash</pre>";
    
    echo "<p><strong>SQL to update all users:</strong></p>";
    echo "<pre>";
    echo "UPDATE users SET password = '$newHash' WHERE username = 'admin';\n";
    echo "UPDATE users SET password = '$newHash' WHERE username = 'manager';\n";
    echo "UPDATE users SET password = '$newHash' WHERE username = 'user';";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2, h3, h4 { color: #333; }
    code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
    pre {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
        font-family: 'Courier New', monospace;
    }
    table {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
