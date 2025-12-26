<?php

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    // Add order_token to restaurant_table
    $col = $db->query("SHOW COLUMNS FROM restaurant_table LIKE 'order_token'")->fetch();
    if (!$col) {
        $db->exec("ALTER TABLE restaurant_table ADD COLUMN order_token VARCHAR(64) UNIQUE NULL");
        echo "✓ Added restaurant_table.order_token\n";
    } else {
        echo "• restaurant_table.order_token exists\n";
    }

    // Add public order metadata to sale_order
    $col = $db->query("SHOW COLUMNS FROM sale_order LIKE 'source'")->fetch();
    if (!$col) {
        $db->exec("ALTER TABLE sale_order ADD COLUMN source ENUM('internal','qr') DEFAULT 'internal'");
        echo "✓ Added sale_order.source\n";
    } else {
        echo "• sale_order.source exists\n";
    }

    $col = $db->query("SHOW COLUMNS FROM sale_order LIKE 'customer_name'")->fetch();
    if (!$col) {
        $db->exec("ALTER TABLE sale_order ADD COLUMN customer_name VARCHAR(100) NULL");
        echo "✓ Added sale_order.customer_name\n";
    } else {
        echo "• sale_order.customer_name exists\n";
    }

    $col = $db->query("SHOW COLUMNS FROM sale_order LIKE 'customer_phone'")->fetch();
    if (!$col) {
        $db->exec("ALTER TABLE sale_order ADD COLUMN customer_phone VARCHAR(30) NULL");
        echo "✓ Added sale_order.customer_phone\n";
    } else {
        echo "• sale_order.customer_phone exists\n";
    }
} catch (Exception $e) {
    echo 'Migration error: ' . $e->getMessage() . "\n";
    exit(1);
}
