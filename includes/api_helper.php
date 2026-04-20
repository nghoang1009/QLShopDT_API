<?php
/**
 * API Helper - Các hàm hỗ trợ gọi API và tiện ích
 */

// URL gốc của ứng dụng (dùng cho RESTful callAPI)
if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', 'http://localhost/QLShopDT_API');
}

// API URLs (RPC - dùng cho các module chưa migrate)
define('KHACHHANG_API_URL','http://localhost/QLShopDT_API/api/khachhang_api.php');
define('GIOHANG_API_URL',  'http://localhost/QLShopDT_API/api/giohang_api.php');
define('NHANVIEN_API_URL', 'http://localhost/QLShopDT_API/api/nhanvien_api.php');
define('PROFILE_API_URL',  'http://localhost/QLShopDT_API/api/profile_api.php');
define('VANCHUYEN_API_URL','http://localhost/QLShopDT_API/api/vanchuyen_api.php');

// ========== CSRF PROTECTION ==========

/**
 * Tạo CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Tạo hidden input field cho CSRF
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Kiểm tra CSRF token
 */
function verify_csrf($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate CSRF token (sau khi login hoặc submit form)
 */
function regenerate_csrf() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// ========== API CALL HELPERS ==========

/**
 * Gọi RESTful API với đầy đủ HTTP Method (GET, POST, PUT, DELETE).
 *
 * @param string $method   - HTTP method: GET | POST | PUT | DELETE
 * @param string $endpoint - Đường dẫn endpoint, ví dụ: /api/danhmuc hoặc /api/danhmuc/5
 * @param array  $data     - Dữ liệu gửi kèm (chỉ dùng cho POST / PUT)
 * @return array           - Mảng JSON đã decode; chứa 'status' false nếu lỗi
 */
function callAPI($method, $endpoint, $data = []) {
    $url    = APP_BASE_URL . $endpoint;
    $method = strtoupper($method);

    if ($method === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
    }

    // Lưu session ID trước khi nhả lock
    $sessionName = session_name();
    $sessionId   = session_id();

    $hadActiveSession = (session_status() === PHP_SESSION_ACTIVE);
    if ($hadActiveSession) {
        session_write_close();
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method);
    curl_setopt($ch, CURLOPT_TIMEOUT,        10);

    // Gửi JSON body cho POST / PUT
    if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($data)) {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json),
        ]);
    }

    // Chuyển tiếp session cookie để controller nhận diện người dùng
    if ($sessionName && $sessionId) {
        curl_setopt($ch, CURLOPT_COOKIE, $sessionName . '=' . $sessionId);
    }

    $response = curl_exec($ch);
    unset($ch); // curl_close() deprecated since PHP 8.4

    // Khôi phục session sau khi cURL xong
    if ($hadActiveSession && !headers_sent()) {
        session_start();
    }

    if ($response === false) {
        return ['status' => false, 'message' => 'Không thể kết nối đến API'];
    }

    return json_decode($response, true) ?? ['status' => false, 'message' => 'Phản hồi không hợp lệ từ API'];
}

/**
 * Gọi API RPC (legacy - POST JSON với trường 'action').
 * Dùng cho các module chưa migrate sang RESTful.
 *
 * @param string $url  - URL đầy đủ đến file API
 * @param array  $data - Payload bao gồm 'action'
 * @return array|null
 */
function callRpcAPI($url, $data) {
    $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => 'Content-Type: application/json; charset=utf-8',
            'content'       => json_encode($data),
            'ignore_errors' => true,
        ],
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return ['status' => false, 'message' => 'Không thể kết nối đến API'];
    }

    return json_decode($response, true);
}

/** Gọi API khách hàng */
function callKhachhangAPI($data) {
    return callRpcAPI(KHACHHANG_API_URL, $data);
}

/** Gọi API nhân viên */
function callNhanVienAPI($data) {
    return callRpcAPI(NHANVIEN_API_URL, $data);
}

/** Gọi API giỏ hàng */
function callGioHangAPI($data) {
    return callRpcAPI(GIOHANG_API_URL, $data);
}

/** Gọi API vận chuyển */
function callVanchuyenAPI($data) {
    return callRpcAPI(VANCHUYEN_API_URL, $data);
}

/** Gọi API profile */
function callProfileAPI($data) {
    return callRpcAPI(PROFILE_API_URL, $data);
}

// ========== ROLE & AUTH HELPERS ==========

function getCurrentRole() {
    return isset($_SESSION['role']) ? (int)$_SESSION['role'] : null;
}

function isAdminOrStaff() {
    $role = getCurrentRole();
    return $role === 1 || $role === 2;
}

function isAdmin() {
    return getCurrentRole() === 1;
}

function isStaff() {
    return getCurrentRole() === 2;
}

function isCustomer() {
    return getCurrentRole() === 0;
}

function requireLogin() {
    if (!isset($_SESSION['username'])) {
        header('Location: /QLShopDT_API/views/auth/login.php');
        exit();
    }
}

function requireRole($roles) {
    requireLogin();
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    if (!in_array(getCurrentRole(), $roles)) {
        http_response_code(403);
        echo "Bạn không có quyền truy cập trang này.";
        exit();
    }
}



// ========== UTILITY HELPERS ==========

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format số tiền
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Set flash message
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get flash message
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Check if flash message exists
 */
function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}
?>