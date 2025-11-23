<?php

/**
 * Create or update a user with a hashed password using the project's User model.
 * Usage (CLI): php scripts/create_admin.php [username] [password]
 * Usage (HTTP): /scripts/create_admin.php?username=admin&password=admin123
 */

require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/app/models/User.php';

$isCli = php_sapi_name() === 'cli';

if ($isCli) {
    $username = $argv[1] ?? 'admin';
    $password = $argv[2] ?? 'admin123';
} else {
    $username = $_GET['username'] ?? 'admin';
    $password = $_GET['password'] ?? 'admin123';
}

try {
    $userModel = new User();

    $existing = $userModel->findByUsername($username);

    if ($existing) {
        $userModel->updateUser($existing['id'], ['password' => $password]);
        $msg = "Updated password for user '{$username}' (id={$existing['id']}).";
    } else {
        $id = $userModel->createUser([
            'username' => $username,
            'password' => $password,
            'fullname' => ucfirst($username),
            'role' => 'admin',
            'active' => 1
        ]);
        $msg = "Created user '{$username}' with id={$id}.";
    }

    $user = $userModel->findByUsername($username);

    $output = [
        'message' => $msg,
        'username' => $username,
        'stored_hash' => $user['password'] ?? null,
        'password_verify' => password_verify($password, $user['password'] ?? '') ? true : false
    ];

    if ($isCli) {
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    if ($isCli) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
    } else {
        header('Content-Type: application/json; charset=utf-8', true, 500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
