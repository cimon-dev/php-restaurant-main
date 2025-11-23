<?php
/**
 * Generate Password Hash
 * Chạy file này để tạo password hash mới
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Plain Password:</strong> $password</p>";
echo "<p><strong>Hashed Password:</strong></p>";
echo "<pre>$hash</pre>";

echo "<hr>";
echo "<h3>Verify Test:</h3>";

if (password_verify($password, $hash)) {
    echo "<p style='color: green;'>✅ Password verification successful!</p>";
} else {
    echo "<p style='color: red;'>❌ Password verification failed!</p>";
}

echo "<hr>";
echo "<h3>SQL Insert Statement:</h3>";
echo "<pre>";
echo "INSERT INTO users (username, password, fullname, role, active) VALUES\n";
echo "('admin', '$hash', 'Administrator', 'admin', TRUE),\n";
echo "('manager', '$hash', 'Manager', 'manager', TRUE),\n";
echo "('user', '$hash', 'User', 'user', TRUE);\n";
echo "</pre>";
?>
