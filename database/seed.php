<?php

/**
 * Script tải dữ liệu mẫu vào database
 * Chạy: php database/seed.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

echo "\n";
echo "========================================\n";
echo "THÊM DỮ LIỆU MẪU VÀO DATABASE\n";
echo "========================================\n\n";

try {
    $db = getDB();

    // Đọc file seed.sql
    $seedFile = __DIR__ . '/seed.sql';

    if (!file_exists($seedFile)) {
        echo "❌ Lỗi: File seed.sql không tồn tại!\n";
        echo "Đường dẫn: $seedFile\n";
        exit(1);
    }

    $sql = file_get_contents($seedFile);

    // Loại bỏ các dòng comment và các lệnh khác
    $sql = preg_replace('/^--.*$/m', '', $sql); // Xóa comment
    $sql = preg_replace('/^\/\/.*$/m', '', $sql); // Xóa comment //
    $sql = preg_replace('/^\s*[\r\n]/m', '', $sql); // Xóa dòng trống

    // Tách các câu lệnh SQL
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "Tổng cộng " . count($statements) . " câu lệnh SQL\n";
    echo "Bắt đầu thực thi...\n\n";

    $count = 0;
    $errors = [];

    foreach ($statements as $i => $statement) {
        if (empty($statement)) continue;

        try {
            $db->exec($statement);
            $count++;
            echo "✓ Lệnh " . ($i + 1) . " thành công\n";
        } catch (PDOException $e) {
            $errors[] = [
                'query' => substr($statement, 0, 100) . '...',
                'error' => $e->getMessage()
            ];
            echo "✗ Lệnh " . ($i + 1) . " thất bại: " . $e->getMessage() . "\n";
        }
    }

    echo "\n========================================\n";
    echo "HOÀN THÀNH\n";
    echo "========================================\n";
    echo "✓ Thành công: $count/{{total}}\n";

    if (!empty($errors)) {
        echo "❌ Lỗi: " . count($errors) . "\n\n";
        foreach ($errors as $err) {
            echo "  Query: " . $err['query'] . "\n";
            echo "  Error: " . $err['error'] . "\n\n";
        }
    }

    echo "\n📊 Dữ liệu mẫu đã được thêm vào database!\n";
    echo "Bạn có thể đăng nhập và xem dữ liệu trên dashboard.\n";
    echo "Tài khoản: admin / Mật khẩu: admin123\n\n";
} catch (PDOException $e) {
    echo "❌ Lỗi kết nối database:\n";
    echo $e->getMessage() . "\n\n";
    echo "Vui lòng kiểm tra:\n";
    echo "- Database đang chạy\n";
    echo "- Cổng MySQL: " . DB_PORT . "\n";
    echo "- Database: " . DB_NAME . "\n";
    echo "- User: " . DB_USER . "\n";
    exit(1);
}
