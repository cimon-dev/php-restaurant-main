<?php

/**
 * Cấu hình kết nối cơ sở dữ liệu
 * Restaurant Management System
 */

// Cấu hình database
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'restaurant_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Class Database để kết nối
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }

    // Singleton pattern
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Lấy connection
    public function getConnection()
    {
        return $this->connection;
    }

    // Ngăn clone
    private function __clone() {}

    // Ngăn unserialize
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Hàm helper để lấy connection nhanh
function getDB()
{
    return Database::getInstance()->getConnection();
}
