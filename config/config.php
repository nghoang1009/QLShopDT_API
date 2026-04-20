<?php
/**
 * Config - Cấu hình chung cho toàn bộ dự án
 */

// Ngăn truy cập trực tiếp
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// URL cơ sở
define('BASE_URL', '/QLShopDT_API');

// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'qlshopdienthoai');

// Cấu hình API URLs
define('API_BASE_URL', 'http://localhost' . BASE_URL . '/api');

// Các hằng số khác
define('DEFAULT_PAGINATION', 12);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

error_reporting(E_ALL);
ini_set('display_errors', 1);