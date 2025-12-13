<?php

/**
 * Migration: Add status column to inventory_issue table
 */

require 'config/config.php';

$db = getDB();

try {
    // Check if column already exists
    $result = $db->query("SHOW COLUMNS FROM inventory_issue LIKE 'status'");
    if ($result->rowCount() > 0) {
        echo "✓ Column 'status' already exists.\n";
        exit(0);
    }

    // Add status column
    $db->exec("ALTER TABLE inventory_issue ADD COLUMN status ENUM('pending','completed') DEFAULT 'pending' AFTER issue_date");

    echo "✓ Column 'status' added successfully to inventory_issue table!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
