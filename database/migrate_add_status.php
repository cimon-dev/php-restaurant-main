<?php

/**
 * Add status column to inventory_receipt table if not exists
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    // Check if column exists
    $result = $db->query("SHOW COLUMNS FROM inventory_receipt LIKE 'status'");
    $columnExists = $result->fetch() !== false;

    if (!$columnExists) {
        echo "Adding 'status' column to inventory_receipt table...\n";

        // Add column
        $db->exec("ALTER TABLE inventory_receipt ADD COLUMN status ENUM('pending','completed') DEFAULT 'pending'");

        echo "✓ Column 'status' added successfully!\n";
    } else {
        echo "✓ Column 'status' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
