<?php
/**
 * App Entry Point - Điểm vào chính cho ứng dụng MVC
 * 
 * URL Format: /QLShopDT_API/app.php/controller/action/params
 * Với .htaccess: /QLShopDT_API/controller/action/params
 * 
 * Lưu ý: API endpoints (/api/*.php) vẫn hoạt động độc lập, 
 *        không đi qua Router này.
 */

// Load bootstrap (session, config, database, core classes)
require_once __DIR__ . '/core/bootstrap.php';

// Khởi tạo Router
$router = new Router();

// Load routes
require_once BASE_PATH . '/config/routes.php';

// Lấy URI từ PATH_INFO hoặc REQUEST_URI
$uri = $_SERVER['PATH_INFO'] ?? '';

if (empty($uri)) {
    // Nếu không có PATH_INFO, parse từ REQUEST_URI
    $uri = $_SERVER['REQUEST_URI'];
    
    // Loại bỏ query string
    if (($pos = strpos($uri, '?')) !== false) {
        $uri = substr($uri, 0, $pos);
    }
    
    // Loại bỏ /QLShopDT_API và /app.php
    $uri = str_replace([BASE_URL, '/app.php'], '', $uri);
}

// Chuẩn hóa URI
$uri = '/' . trim($uri, '/');
if ($uri === '/') {
    $uri = '/';
}

// Dispatch request đến controller
$router->dispatch($uri);
