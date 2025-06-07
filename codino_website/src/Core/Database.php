<?php
// src/Core/Database.php

namespace Codino\Core;

use PDO;
use PDOException;

class Database {
    private static \$instance = null;
    private \$conn;

    private \$host;
    private \$db_name;
    private \$username;
    private \$password;

    private function __construct() {
        // Require config file here to get DB constants
        // BASE_PATH should be defined by the entry point (public/index.php)
        if (!defined('BASE_PATH')) {
            // This is a fallback if Database.php is somehow called directly without index.php defining BASE_PATH
            // Adjust the number of '/..' parts based on Database.php's location relative to the project root.
            // Assuming Database.php is in src/Core/, so two levels down from project root.
            define('BASE_PATH', dirname(dirname(__DIR__)));
        }
        require_once BASE_PATH . '/config/config.php';

        \$this->host = DB_HOST;
        \$this->db_name = DB_NAME;
        \$this->username = DB_USER;
        \$this->password = DB_PASS;

        \$dsn = 'mysql:host=' . \$this->host . ';dbname=' . \$this->db_name . ';charset=utf8mb4';
        \$options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Important for error handling
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Default fetch mode to associative array
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
        ];

        try {
            \$this->conn = new PDO(\$dsn, \$this->username, \$this->password, \$options);
        } catch (PDOException \$e) {
            // In a real application, log this error and show a user-friendly message
            error_log("Database Connection Error: " . \$e->getMessage());
            // For development, you might want to see the error directly.
            // For production, you'd show a generic error message.
            die("Database connection failed. Please check logs or contact support. Error: " . htmlspecialchars(\$e->getMessage())); // Added error message for clarity
        }
    }

    public static function getInstance() {
        if (self::\$instance == null) {
            self::\$instance = new Database();
        }
        return self::\$instance;
    }

    public function getConnection() {
        return \$this->conn;
    }

    // Optional: Convenience methods for common operations (examples)
    // public function query(\$sql, \$params = []) {
    //     \$stmt = \$this->conn->prepare(\$sql);
    //     \$stmt->execute(\$params);
    //     return \$stmt;
    // }

    // public function fetchAll(\$sql, \$params = []) {
    //     return \$this->query(\$sql, \$params)->fetchAll();
    // }

    // public function fetchOne(\$sql, \$params = []) {
    //     return \$this->query(\$sql, \$params)->fetch();
    // }
}
?>
