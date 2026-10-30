<?php
// Prevent multiple inclusions
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'women_ecommerce');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage() . "<br>Please ensure MySQL is running in XAMPP and the database 'women_ecommerce' exists.");
}

// Base URL helper
function base_url($path = '') {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    // Normalize to root /women-ecommerce or root /
    if (strpos($scriptDir, '/admin') !== false) {
        $root = dirname($scriptDir);
    } else {
        $root = $scriptDir;
    }
    $root = rtrim(str_replace('\\', '/', $root), '/');
    return ($root ? $root : '') . '/' . ltrim($path, '/');
}

// Authentication Helpers
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'customer'
    ];
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'Access denied: Admin credentials required.';
        header('Location: ' . base_url('admin/login.php'));
        exit;
    }
}

// Formatting & Flash Messages
function format_price($amount) {
    return '₹' . number_format((float)$amount, 2);
}

function set_flash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

function cart_count() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)($item['quantity'] ?? 1);
        }
    }
    return $count;
}
