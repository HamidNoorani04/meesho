<?php
// Database connection with error suppression for production
require_once __DIR__ . '/config.php';

function get_db_connection() {
    static $conn = null;
    
    if ($conn === null) {
        $host = env('DB_HOST', 'localhost');
        $dbname = env('DB_NAME', 'meesho_shop');
        $username = env('DB_USER', 'root');
        $password = env('DB_PASS', '');
        
        try {
            $conn = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Log error but don't show to users
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection error. Please contact administrator.");
        }
    }
    
    return $conn;
}

// Test connection
function test_db_connection() {
    try {
        $db = get_db_connection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
