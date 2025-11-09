<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'true');
// Suppress all errors for production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Load .env file
function load_env() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}
load_env();

// Get environment variable
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Generate CSRF token
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('CSRF token validation failed');
        }
    }
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Sanitize output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

$BASE_PATH = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
function url($path=''){
  global $BASE_PATH;
  $path = ltrim($path, '/');
  return ($BASE_PATH ? $BASE_PATH : '') . '/' . $path;
}
function asset($path){ return url($path); }

// Load settings
function load_settings() {
    $file = __DIR__ . '/data/settings.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return ['offer_enabled' => false];
}

// Save settings
function save_settings($settings) {
    $file = __DIR__ . '/data/settings.json';
    return file_put_contents($file, json_encode($settings, JSON_PRETTY_PRINT));
}

// Get setting value
function get_setting($key, $default = null) {
    $settings = load_settings();
    return $settings[$key] ?? $default;
}
?>