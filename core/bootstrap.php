<?php
/**
 * Bootstrap - File khởi động ứng dụng
 * Include file này ở đầu mỗi entry point
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Định nghĩa đường dẫn gốc
define('BASE_PATH', dirname(__DIR__));

// Load config
require_once BASE_PATH . '/config/config.php';

// Load database
require_once BASE_PATH . '/config/database.php';

// Load core classes
require_once BASE_PATH . '/core/CSRF.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';

/**
 * Autoloader đơn giản cho các class
 */
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/model/',
        BASE_PATH . '/controller/',
        BASE_PATH . '/core/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Helper functions
 */

/**
 * Tạo URL đầy đủ
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Tạo URL asset (CSS, JS, images)
 */
function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Kiểm tra user đã đăng nhập
 */
function isLoggedIn() {
    return isset($_SESSION['username']);
}

/**
 * Lấy thông tin user hiện tại
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['userid'] ?? null,
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'] ?? 0,
    ];
}

/**
 * Kiểm tra role
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    $currentRole = (int)($_SESSION['role'] ?? -1);
    return in_array($currentRole, $roles);
}

/**
 * Flash messages
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}

/**
 * CSRF token field
 */
function csrf_field() {
    return CSRF::field();
}

/**
 * Format tiền tệ
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Debug helper
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
