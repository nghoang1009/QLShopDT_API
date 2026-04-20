<?php
/**
 * Index.php - Entry point chính
 * 
 * Nếu mod_rewrite hoạt động: tự động chuyển đến app.php
 * Nếu không: redirect thủ công
 */

// Kiểm tra nếu được gọi trực tiếp (không qua .htaccess)
if (!isset($_SERVER['PATH_INFO']) && $_SERVER['REQUEST_URI'] === '/QLShopDT_API/' || $_SERVER['REQUEST_URI'] === '/QLShopDT_API/index.php') {
    // Redirect đến app.php
    header("Location: /QLShopDT_API/app.php");
    exit();
}

// Nếu có PATH_INFO, forward đến app.php
require_once __DIR__ . '/app.php';

